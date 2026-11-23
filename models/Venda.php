
<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/Estoque.php';
require_once __DIR__ . '/NotaFiscal.php';

class Venda
{
    private PDO $conn;
    private Estoque $estoqueModel;

    public function __construct()
    {
        $this->conn = Database::getConnection();
        $this->estoqueModel = new Estoque();
    }

    public function listarRecentes(int $limite = 20): array
    {
        $stmt = $this->conn->prepare("
            SELECT
                v.id,
                v.data,
                v.status,
                v.valor_total,
                v.nf_emitida,
                COALESCE(c.nome, cs.nome, 'Não informado') AS cliente_nome,
                u.nome AS vendedor_nome,
                (
                    SELECT GROUP_CONCAT(
                        DISTINCT p.nome
                        ORDER BY p.nome
                        SEPARATOR ', '
                    )
                    FROM venda_item vi
                    INNER JOIN variacao va ON va.id = vi.variacao_id
                    INNER JOIN produto p ON p.id = va.produto_id
                    WHERE vi.venda_id = v.id
                ) AS produtos_nome,
                (
                    SELECT COALESCE(SUM(vi.quantidade), 0)
                    FROM venda_item vi
                    WHERE vi.venda_id = v.id
                ) AS quantidade_itens
            FROM venda v
            LEFT JOIN cliente c ON c.id = v.cliente_id
            LEFT JOIN cliente_site cs ON cs.cpf = v.cliente_site_cpf
            INNER JOIN usuario u ON u.id = v.usuario_id
            ORDER BY v.id DESC
            LIMIT :limite
        ");

        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrar(
        int $usuarioId,
        ?int $clienteId,
        ?string $clienteCpf,
        array $itens
    ): int {
        if ($usuarioId <= 0) {
            throw new InvalidArgumentException('Usuário inválido.');
        }

        $clienteCpf = $clienteCpf !== null
            ? preg_replace('/\D/', '', $clienteCpf)
            : null;

        if ($clienteId !== null && $clienteId <= 0) {
            throw new InvalidArgumentException('Cliente inválido.');
        }

        if ($clienteId !== null && $clienteCpf !== null) {
            throw new InvalidArgumentException(
                'Informe somente um tipo de cliente.'
            );
        }

        if ($clienteId === null && $clienteCpf === null) {
            throw new InvalidArgumentException('Selecione um cliente.');
        }

        if ($clienteCpf !== null && strlen($clienteCpf) !== 11) {
            throw new InvalidArgumentException('CPF inválido.');
        }

        if (empty($itens)) {
            throw new InvalidArgumentException(
                'A venda precisa ter pelo menos um item.'
            );
        }

        if ($clienteCpf !== null) {
            $stmtCliente = $this->conn->prepare("
                SELECT cpf
                FROM cliente_site
                WHERE cpf = :cpf
                LIMIT 1
            ");

            $stmtCliente->execute([':cpf' => $clienteCpf]);

            if (!$stmtCliente->fetchColumn()) {
                throw new InvalidArgumentException(
                    'Cliente da loja virtual não encontrado.'
                );
            }
        }

        $valorTotal = 0.0;

        foreach ($itens as $item) {
            $variacaoId = (int) ($item['variacao_id'] ?? 0);
            $quantidade = (int) ($item['quantidade'] ?? 0);
            $precoUnitario = (float) ($item['preco_unitario'] ?? -1);

            if ($variacaoId <= 0) {
                throw new InvalidArgumentException('Variação inválida.');
            }

            if ($quantidade <= 0) {
                throw new InvalidArgumentException(
                    'A quantidade deve ser maior que zero.'
                );
            }

            if (!is_finite($precoUnitario) || $precoUnitario < 0) {
                throw new InvalidArgumentException(
                    'O preço unitário é inválido.'
                );
            }

            $valorTotal += $quantidade * $precoUnitario;
        }

        try {
            $this->conn->beginTransaction();

            $stmtVenda = $this->conn->prepare("
                INSERT INTO venda (
                    usuario_id,
                    cliente_id,
                    cliente_site_cpf,
                    data,
                    status,
                    valor_total
                ) VALUES (
                    :usuario_id,
                    :cliente_id,
                    :cliente_site_cpf,
                    NOW(),
                    'finalizada',
                    :valor_total
                )
            ");

            $stmtVenda->execute([
                ':usuario_id' => $usuarioId,
                ':cliente_id' => $clienteId,
                ':cliente_site_cpf' => $clienteCpf,
                ':valor_total' => number_format(
                    $valorTotal,
                    2,
                    '.',
                    ''
                )
            ]);

            $vendaId = (int) $this->conn->lastInsertId();

            $stmtItem = $this->conn->prepare("
                INSERT INTO venda_item (
                    venda_id,
                    variacao_id,
                    quantidade,
                    preco_unitario
                ) VALUES (
                    :venda_id,
                    :variacao_id,
                    :quantidade,
                    :preco_unitario
                )
            ");

            $stmtMovimento = $this->conn->prepare("
                INSERT INTO movimento_estoque (
                    variacao_id,
                    tipo,
                    quantidade,
                    origem,
                    observacao,
                    data
                ) VALUES (
                    :variacao_id,
                    'saida',
                    :quantidade,
                    'venda',
                    :observacao,
                    NOW()
                )
            ");

            foreach ($itens as $item) {
                $variacaoId = (int) $item['variacao_id'];
                $quantidade = (int) $item['quantidade'];
                $precoUnitario = (float) $item['preco_unitario'];

                $stmtItem->execute([
                    ':venda_id' => $vendaId,
                    ':variacao_id' => $variacaoId,
                    ':quantidade' => $quantidade,
                    ':preco_unitario' => number_format(
                        $precoUnitario,
                        2,
                        '.',
                        ''
                    )
                ]);

                $stmtMovimento->execute([
                    ':variacao_id' => $variacaoId,
                    ':quantidade' => $quantidade,
                    ':observacao' => 'Saída referente à venda nº ' . $vendaId
                ]);

                $this->estoqueModel->ajustar(
                    $variacaoId,
                    -$quantidade
                );
            }

            $this->conn->commit();

            return $vendaId;

        } catch (Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            throw $e;
        }
    }

    public function emitirNotaFiscal(int $vendaId): bool
    {
        if ($vendaId <= 0) {
            return false;
        }

        $stmt = $this->conn->prepare("
            SELECT id, status, nf_emitida, valor_total
            FROM venda
            WHERE id = :id
            LIMIT 1
        ");

        $stmt->execute([':id' => $vendaId]);
        $venda = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$venda || $venda['status'] !== 'finalizada') {
            return false;
        }

        $notaFiscalModel = new NotaFiscal();

        $notaExistente = $notaFiscalModel->buscarPorVenda($vendaId);

        if (!$notaExistente) {
            $dadosNota = [
                'venda_id' => $vendaId,
                'modelo' => 'DEMO',
                'serie' => '1',
                'numero' => str_pad(
                    (string) $vendaId,
                    6,
                    '0',
                    STR_PAD_LEFT
                ),
                'data_emissao' => date('Y-m-d'),
                'valor_total' => (float) $venda['valor_total']
            ];

            if (!$notaFiscalModel->cadastrarVenda($dadosNota)) {
                return false;
            }
        }

        if ((int) $venda['nf_emitida'] === 0) {
            $stmt = $this->conn->prepare("
                UPDATE venda
                SET nf_emitida = 1
                WHERE id = :id
                  AND nf_emitida = 0
            ");

            $stmt->execute([':id' => $vendaId]);
        }

        return true;
    }

    public function buscarParaNotaFiscal(int $vendaId): ?array
    {
        if ($vendaId <= 0) {
            return null;
        }

        $stmt = $this->conn->prepare("
            SELECT
                v.id,
                v.data,
                v.status,
                v.valor_total,
                v.nf_emitida,
                COALESCE(c.nome, cs.nome, 'Não informado') AS cliente_nome
            FROM venda v
            LEFT JOIN cliente c ON c.id = v.cliente_id
            LEFT JOIN cliente_site cs ON cs.cpf = v.cliente_site_cpf
            WHERE v.id = :id
            LIMIT 1
        ");

        $stmt->execute([':id' => $vendaId]);
        $venda = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$venda) {
            return null;
        }

        $stmtItens = $this->conn->prepare("
            SELECT
                vi.variacao_id,
                vi.quantidade,
                vi.preco_unitario,
                va.sku,
                va.opcao AS variacao_nome,
                p.nome AS produto_nome
            FROM venda_item vi
            INNER JOIN variacao va ON va.id = vi.variacao_id
            INNER JOIN produto p ON p.id = va.produto_id
            WHERE vi.venda_id = :venda_id
            ORDER BY vi.variacao_id
        ");

        $stmtItens->execute([
            ':venda_id' => $vendaId
        ]);

        $venda['itens'] = $stmtItens->fetchAll(PDO::FETCH_ASSOC);

        return $venda;
    }
}
