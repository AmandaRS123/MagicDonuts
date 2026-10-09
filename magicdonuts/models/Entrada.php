<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/Estoque.php';

class Entrada
{
    private PDO $conn;
    private Estoque $estoqueModel;

    public function __construct()
    {
        $this->conn = Database::getConnection();
        $this->estoqueModel = new Estoque();
    }

    public function listarFornecedores(): array
    {
        $sql = "
            SELECT id, nome
            FROM fornecedor
            ORDER BY nome ASC
        ";

        return $this->conn
            ->query($sql)
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarVariacoes(): array
    {
        $sql = "
            SELECT id, nome, sku, preco
            FROM variacao
            ORDER BY nome ASC
        ";

        return $this->conn
            ->query($sql)
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarComFornecedor(): array
    {
        $sql = "
            SELECT
                em.id,
                em.data,
                em.status,
                em.valor_total,
                f.nome AS fornecedor_nome
            FROM entrada_mercadoria em
            INNER JOIN fornecedor f
                ON f.id = em.fornecedor_id
            ORDER BY em.id DESC
        ";

        return $this->conn
            ->query($sql)
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrar(int $fornecedorId, array $itens): int
    {
        if ($fornecedorId <= 0 || empty($itens)) {
            throw new InvalidArgumentException(
                'Informe um fornecedor e pelo menos um item.'
            );
        }

        $valorTotal = 0;

        foreach ($itens as $item) {
            if (
                (int) $item['variacao_id'] <= 0 ||
                (int) $item['quantidade'] <= 0 ||
                (float) $item['custo_unitario'] < 0
            ) {
                throw new InvalidArgumentException(
                    'Existe um item com dados inválidos.'
                );
            }

            $valorTotal +=
                (int) $item['quantidade'] *
                (float) $item['custo_unitario'];
        }

        $this->conn->beginTransaction();

        try {
            $stmt = $this->conn->prepare("
                SELECT id
                FROM fornecedor
                WHERE id = :id
            ");
            $stmt->execute(['id' => $fornecedorId]);

            if (!$stmt->fetchColumn()) {
                throw new InvalidArgumentException(
                    'O fornecedor selecionado não existe.'
                );
            }

            $stmt = $this->conn->prepare("
                INSERT INTO entrada_mercadoria
                    (fornecedor_id, data, status, valor_total)
                VALUES
                    (:fornecedor_id, CURDATE(), 'confirmar', :valor_total)
            ");

            $stmt->execute([
                'fornecedor_id' => $fornecedorId,
                'valor_total' => $valorTotal
            ]);

            $entradaId = (int) $this->conn->lastInsertId();

            $stmtVariacao = $this->conn->prepare("
                SELECT id
                FROM variacao
                WHERE id = :id
            ");

            $stmtItem = $this->conn->prepare("
                INSERT INTO entrada_item
                    (entrada_id, variacao_id, quantidade, custo_unitario)
                VALUES
                    (:entrada_id, :variacao_id, :quantidade, :custo_unitario)
            ");

           $stmtMovimento = $this->conn->prepare(
    "INSERT INTO movimento_estoque
        (variacao_id, tipo, quantidade, origem, observacao, data)
     VALUES
        (:variacao_id, 'entrada', :quantidade, 'entrada', :observacao, NOW())"
);

            foreach ($itens as $item) {
                $variacaoId = (int) $item['variacao_id'];
                $quantidade = (int) $item['quantidade'];
                $custo = (float) $item['custo_unitario'];

                $stmtVariacao->execute(['id' => $variacaoId]);

                if (!$stmtVariacao->fetchColumn()) {
                    throw new InvalidArgumentException(
                        'Uma das variações selecionadas não existe.'
                    );
                }

                $stmtItem->execute([
                    'entrada_id' => $entradaId,
                    'variacao_id' => $variacaoId,
                    'quantidade' => $quantidade,
                    'custo_unitario' => $custo
                ]);

                $entradaItemId = (int) $this->conn->lastInsertId();

                $stmtMovimento->execute([
    'variacao_id' => $variacaoId,
    'quantidade' => $quantidade,
    'observacao' => 'Entrada de mercadoria nº ' . $entradaId
]);

                $this->estoqueModel->ajustar(
                    $variacaoId,
                    $quantidade
                );
            }

            $this->conn->commit();

            return $entradaId;

        } catch (Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            throw $e;
        }
    }
}