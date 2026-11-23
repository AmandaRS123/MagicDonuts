
<?php

require_once __DIR__ . '/../config/db.php';

class Categoria
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    // Lista todas as categorias, ativas e inativas.
    public function listarTodas(): array
    {
        $sql = "SELECT id, nome, ativo
                FROM categoria
                ORDER BY nome";

        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Busca uma categoria pelo ID.
    public function buscarPorId(int $id): ?array
    {
        $sql = "SELECT id, nome, ativo
                FROM categoria
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);

        $categoria = $stmt->fetch(PDO::FETCH_ASSOC);

        return $categoria ?: null;
    }

    // Insere uma nova categoria.
    public function inserir(string $nome): bool
    {
        $sql = "INSERT INTO categoria (nome, ativo)
                VALUES (:nome, 1)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([':nome' => $nome]);
    }

    // Atualiza o nome da categoria.
    public function atualizar(int $id, string $nome): bool
    {
        $sql = "UPDATE categoria
                SET nome = :nome
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':nome' => $nome,
            ':id' => $id
        ]);
    }

    // Ativa ou inativa uma categoria.
    public function setAtivo(int $id, bool $ativo): bool
    {
        $sql = "UPDATE categoria
                SET ativo = :ativo
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':ativo' => $ativo ? 1 : 0,
            ':id' => $id
        ]);
    }

    // Mantém a consulta usada pelos outros módulos.
    public function listarAtivas(): array
    {
        $sql = "SELECT id, nome
                FROM categoria
                WHERE ativo = 1
                ORDER BY nome";

        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }


// Exclui uma categoria somente se não houver produtos vinculados.
public function excluir(int $id): string
{
    $sql = "SELECT COUNT(*)
            FROM produto
            WHERE categoria_id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute([':id' => $id]);

    $quantidadeProdutos = (int) $stmt->fetchColumn();

    if ($quantidadeProdutos > 0) {
        return 'vinculada';
    }

    $sql = "DELETE FROM categoria
            WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute([':id' => $id]);

    return $stmt->rowCount() > 0 ? 'excluida' : 'nao_encontrada';
}

}
