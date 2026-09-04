<?php
require_once __DIR__ . '/../config/db.php';

class Produto
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function listarComCategoria(bool $somenteAtivos = false): array
    {
        $sql = "
            SELECT p.id_produto, p.categoria_id, p.nome, p.descricao, p.preco, p.estoque, p.ativo,
                   c.nome AS categoria_nome
            FROM produto p
            INNER JOIN categoria c ON c.id_categoria = p.categoria_id
        ";

        if ($somenteAtivos) {
            $sql .= " WHERE c.ativo = 1 ";
        }

        $sql .= " ORDER BY c.nome ASC, p.nome ASC";
        
        return $this->conn->query($sql)->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM produto WHERE id_produto = :id");
        $stmt->execute([':id' => $id]);
        $r = $stmt->fetch();
        return $r ?: null;
    }

    public function inserir(int $categoriaId, string $nome, ?string $descricao, float $preco, int $estoque): int
    {
        $stmt = $this->conn->prepare("
            INSERT INTO produto (categoria_id, nome, descricao, preco, estoque, ativo)
            VALUES (:categoria_id, :nome, :descricao, :preco, :estoque, 1)
        ");
        $stmt->execute([
            ':categoria_id' => $categoriaId,
            ':nome'         => $nome,
            ':descricao'    => $descricao,
            ':preco'        => $preco,
            ':estoque'      => $estoque
        ]);
        return (int)$this->conn->lastInsertId();
    }

    public function atualizar(int $id, int $categoriaId, string $nome, ?string $descricao, float $preco, int $estoque): void
    {
        $stmt = $this->conn->prepare("
            UPDATE produto
            SET categoria_id = :categoria_id, nome = :nome, descricao = :descricao, preco = :preco, estoque = :estoque
            WHERE id_produto = :id
        ");
        $stmt->execute([
            ':id'           => $id,
            ':categoria_id' => $categoriaId,
            ':nome'         => $nome,
            ':descricao'    => $descricao,
            ':preco'        => $preco,
            ':estoque'      => $estoque
        ]);
    }

    public function setAtivo(int $id, bool $ativo): void
    {
        $stmt = $this->conn->prepare("UPDATE produto SET ativo = :ativo WHERE id_produto = :id");
        
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        $stmt->bindValue(':ativo', $ativo ? 1 : 0, PDO::PARAM_INT);
        
        $stmt->execute();
    }

    public function deletar(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM produto WHERE id_produto = :id");
        return $stmt->execute([':id' => $id]);
    }
}