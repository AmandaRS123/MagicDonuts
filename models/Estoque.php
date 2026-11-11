
<?php

require_once __DIR__ . '/../config/db.php';

class Estoque
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    /**
     * Garante que a variação tenha um registro de estoque.
     */
    public function garantirRegistro(
        int $variacaoId,
        int $minimo = 0
    ): void {
        $stmt = $this->conn->prepare("
            SELECT id
            FROM estoque
            WHERE variacao_id = :vid
        ");

        $stmt->execute([
            ':vid' => $variacaoId
        ]);

        if (!$stmt->fetch()) {
            $ins = $this->conn->prepare("
                INSERT INTO estoque (
                    variacao_id,
                    quantidade,
                    minimo
                )
                VALUES (
                    :vid,
                    0,
                    :minimo
                )
            ");

            $ins->execute([
                ':vid' => $variacaoId,
                ':minimo' => $minimo
            ]);
        }
    }

    /**
     * Busca o registro de estoque de uma variação.
     */
    public function buscarPorVariacao(
        int $variacaoId
    ): ?array {
        $stmt = $this->conn->prepare("
            SELECT *
            FROM estoque
            WHERE variacao_id = :vid
        ");

        $stmt->execute([
            ':vid' => $variacaoId
        ]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado ?: null;
    }

    /**
     * Ajusta a quantidade em estoque.
     * Positivo = entrada.
     * Negativo = saída.
     * Impede que o estoque fique negativo.
     */
    public function ajustar(
        int $variacaoId,
        int $delta
    ): void {
        $this->garantirRegistro($variacaoId);

        $stmt = $this->conn->prepare("
            SELECT quantidade
            FROM estoque
            WHERE variacao_id = :vid
            FOR UPDATE
        ");

        $stmt->execute([
            ':vid' => $variacaoId
        ]);

        $resultado = $stmt->fetchColumn();

        if ($resultado === false) {
            throw new RuntimeException(
                'Não foi possível localizar o registro de estoque.'
            );
        }

        $atual = (int) $resultado;
        $novaQuantidade = $atual + $delta;

        if ($novaQuantidade < 0) {
            throw new RuntimeException(
                'Estoque insuficiente para esta operação.'
            );
        }

        $up = $this->conn->prepare("
            UPDATE estoque
            SET quantidade = :q
            WHERE variacao_id = :vid
        ");

        $up->execute([
            ':q' => $novaQuantidade,
            ':vid' => $variacaoId
        ]);
    }

    /**
     * Lista variações com estoque abaixo ou igual ao mínimo.
     */
    public function listarBaixoEstoque(): array
    {
        $sql = "
            SELECT
                e.id,
                e.variacao_id,
                e.quantidade,
                e.minimo,
                v.tamanho AS variacao_nome,
                v.sku,
                p.nome AS produto_nome
            FROM estoque e
            INNER JOIN variacao v
                ON v.id = e.variacao_id
            INNER JOIN produto p
                ON p.id = v.produto_id
            WHERE e.quantidade <= e.minimo
            ORDER BY e.quantidade ASC
        ";

        return $this->conn
            ->query($sql)
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Conta os registros com estoque abaixo ou igual ao mínimo.
     */
    public function contarBaixoEstoque(): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM estoque
            WHERE quantidade <= minimo
        ";

        return (int) $this->conn
            ->query($sql)
            ->fetchColumn();
    }
}
