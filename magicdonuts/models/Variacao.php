<?php

require_once __DIR__ . '/../config/db.php';

class Variacao
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    // Lista todas as variações de um produto
    public function listarPorProduto(int $produtoId): array
    {
        $stmt = $this->conn->prepare("
            SELECT
                id,
                produto_id,
                nome,
                sku,
                preco
            FROM variacao
            WHERE produto_id = :produto_id
            ORDER BY id DESC
        ");

        $stmt->execute([
            ':produto_id' => $produtoId
        ]);

        return $stmt->fetchAll();
    }

    // Busca uma variação pelo ID
    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT
                id,
                produto_id,
                nome,
                sku,
                preco
            FROM variacao
            WHERE id = :id
        ");

        $stmt->execute([
            ':id' => $id
        ]);

        $r = $stmt->fetch();

        return $r ?: null;
    }

    // Insere uma nova variação
    public function inserir(
        int $produtoId,
        string $nome,
        string $sku,
        float $preco
    ): int {
        $stmt = $this->conn->prepare("
            INSERT INTO variacao
            (
                produto_id,
                nome,
                sku,
                preco
            )
            VALUES
            (
                :produto_id,
                :nome,
                :sku,
                :preco
            )
        ");

        $stmt->execute([
            ':produto_id' => $produtoId,
            ':nome' => $nome,
            ':sku' => $sku,
            ':preco' => $preco
        ]);

        return (int) $this->conn->lastInsertId();
    }

    // Atualiza uma variação
    public function atualizar(
        int $id,
        string $nome,
        string $sku,
        float $preco
    ): void {
        $stmt = $this->conn->prepare("
            UPDATE variacao
            SET
                nome = :nome,
                sku = :sku,
                preco = :preco
            WHERE id = :id
        ");

        $stmt->execute([
            ':id' => $id,
            ':nome' => $nome,
            ':sku' => $sku,
            ':preco' => $preco
        ]);
    }

    // Exclui uma variação
    public function deletar(int $id): void
    {
        $stmt = $this->conn->prepare("
            DELETE FROM variacao
            WHERE id = :id
        ");

        $stmt->execute([
            ':id' => $id
        ]);
    }
}