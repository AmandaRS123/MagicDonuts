
<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/Estoque.php';

class Venda
{
    private PDO $conn;
    private Estoque $estoqueModel;

    public function __construct()
    {
        $this->conn = Database::getConnection();
        $this->estoqueModel = new Estoque();
    }

    /**
     * Lista as vendas mais recentes.
     */
    public function listarRecentes(int $limite = 20): array
    {
        $sql = "
            SELECT
                v.id,
                v.data,
                v.status,
                v.valor_total,
                c.nome AS cliente_nome,
                u.nome AS vendedor_nome
            FROM venda v
            INNER JOIN cliente c ON c.id = v.cliente_id
            INNER JOIN usuario u ON u.id = v.usuario_id
            ORDER BY v.id DESC
            LIMIT :limite
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Registra uma venda completa e atualiza o estoque.
     */
    public function registrar(
        int $usuarioId,
        int $clienteId,
        array $itens
    ): int {
        if (empty($itens)) {
            throw new InvalidArgumentException(
                "A venda precisa ter ao menos um item."
            );
        }

        // Valida os itens e calcula o total
        $valorTotal = 0.0;

        foreach ($itens as $item) {
            $quantidade = (int) ($item['quantidade'] ?? 0);
            $preco = (float) ($item['preco_unitario'] ?? 0);

            if (
                (int) ($item['variacao_id'] ?? 0) <= 0 ||
                $quantidade <= 0 ||
                $preco < 0
            ) {
                throw new InvalidArgumentException(
                    "Existe um item com dados inválidos."
                );
            }

            $valorTotal += $quantidade * $preco;
        }

        $this->conn->beginTransaction();

        try {
            // Cria o cabeçalho da venda
            $stmt = $this->conn->prepare("
                INSERT INTO venda (
                    usuario_id,
                    cliente_id,
                    data,
                    status,
                    valor_total
                )
                VALUES (
                    :usuario_id,
                    :cliente_id,
                    CURDATE(),
                    'finalizada',
                    :valor_total
                )
            ");

            $stmt->execute([
                ':usuario_id' => $usuarioId,
                ':cliente_id' => $clienteId,
                ':valor_total' => $valorTotal
            ]);

            $vendaId = (int) $this->conn->lastInsertId();

            // Prepara o cadastro dos itens da venda
            $stmtItem = $this->conn->prepare("
                INSERT INTO venda_item (
                    venda_id,
                    variacao_id,
                    quantidade,
                    preco_unitario
                )
                VALUES (
                    :venda_id,
                    :variacao_id,
                    :quantidade,
                    :preco_unitario
                )
            ");

            // Prepara o movimento de saída
            $stmtMov = $this->conn->prepare("
                INSERT INTO movimento_estoque (
                    variacao_id,
                    tipo,
                    quantidade,
                    origem,
                    observacao,
                    data
                )
                VALUES (
                    :variacao_id,
                    'saida',
                    :quantidade,
                    'venda',
                    :observacao,
                    NOW()
                )
            ");

            // Processa cada item da venda
            foreach ($itens as $item) {
                $variacaoId = (int) $item['variacao_id'];
                $quantidade = (int) $item['quantidade'];
                $precoUnitario = (float) $item['preco_unitario'];

                // Diminui o estoque
                $this->estoqueModel->ajustar(
                    $variacaoId,
                    -$quantidade
                );

                // Registra o item vendido
                $stmtItem->execute([
                    ':venda_id' => $vendaId,
                    ':variacao_id' => $variacaoId,
                    ':quantidade' => $quantidade,
                    ':preco_unitario' => $precoUnitario
                ]);

                // Registra o movimento de saída
                $stmtMov->execute([
                    ':variacao_id' => $variacaoId,
                    ':quantidade' => $quantidade,
                    ':observacao' => 'Venda nº ' . $vendaId
                ]);
            } // Fecha o foreach

            // Confirma a transação somente após todos os itens
            $this->conn->commit();

            return $vendaId;

        } catch (Throwable $e) {
            // Cancela todas as alterações em caso de erro
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            throw $e;
        }
    }
}