
<?php

require_once __DIR__ . '/../config/db.php';

class Variacao
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    // Lista as opções de um produto
    public function listarPorProduto(int $produtoId): array
    {
        $sql = "
            SELECT
                id,
                produto_id,
                opcao,
                sku,
                preco
            FROM variacao
            WHERE produto_id = :produto_id
            ORDER BY id DESC
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':produto_id' => $produtoId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Busca uma opção pelo ID
    public function buscarPorId(int $id): ?array
    {
        $sql = "
            SELECT
                id,
                produto_id,
                opcao,
                sku,
                preco
            FROM variacao
            WHERE id = :id
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        $variacao = $stmt->fetch(PDO::FETCH_ASSOC);

        return $variacao ?: null;
    }

    // Insere uma nova opção
    public function inserir(
        int $produtoId,
        string $opcao,
        string $sku,
        float $preco
    ): int {
        $sql = "
            INSERT INTO variacao (
                produto_id,
                opcao,
                sku,
                preco
            )
            VALUES (
                :produto_id,
                :opcao,
                :sku,
                :preco
            )
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':produto_id' => $produtoId,
            ':opcao' => $opcao,
            ':sku' => $sku,
            ':preco' => $preco
        ]);

        return (int) $this->conn->lastInsertId();
    }

    // Atualiza uma opção
    public function atualizar(
        int $id,
        string $opcao,
        string $sku,
        float $preco
    ): void {
        $sql = "
            UPDATE variacao
            SET
                opcao = :opcao,
                sku = :sku,
                preco = :preco
            WHERE id = :id
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':id' => $id,
            ':opcao' => $opcao,
            ':sku' => $sku,
            ':preco' => $preco
        ]);
    }

    // Exclui uma opção
    public function deletar(int $id): void
    {
        $sql = "
            DELETE FROM variacao
            WHERE id = :id
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);
    }
}
