<?php

require_once __DIR__ . '/../config/db.php';

class Variacao
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    // Lista as variações de um produto
    public function listarPorProduto(int $produtoId): array
    {
        $sql = "
            SELECT
                id,
                produto_id,
                tamanho,
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

    // Busca uma variação pelo ID
    public function buscarPorId(int $id): ?array
    {
        $sql = "
            SELECT
                id,
                produto_id,
                tamanho,
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

    // Insere uma nova variação
    public function inserir(
        int $produtoId,
        string $tamanho,
        string $sku,
        float $preco
    ): int {
        $sql = "
            INSERT INTO variacao (
                produto_id,
                tamanho,
                sku,
                preco
            )
            VALUES (
                :produto_id,
                :tamanho,
                :sku,
                :preco
            )
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':produto_id' => $produtoId,
            ':tamanho' => $tamanho,
            ':sku' => $sku,
            ':preco' => $preco
        ]);

        return (int) $this->conn->lastInsertId();
    }

    // Atualiza uma variação
    public function atualizar(
        int $id,
        string $tamanho,
        string $sku,
        float $preco
    ): void {
        $sql = "
            UPDATE variacao
            SET
                tamanho = :tamanho,
                sku = :sku,
                preco = :preco
            WHERE id = :id
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':id' => $id,
            ':tamanho' => $tamanho,
            ':sku' => $sku,
            ':preco' => $preco
        ]);
    }

    // Exclui uma variação
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