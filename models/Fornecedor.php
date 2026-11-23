
<?php

require_once __DIR__ . '/../config/db.php';

class Fornecedor
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    /**
     * Lista todos os fornecedores.
     */
    public function listarTodos(): array
    {
        $sql = "
            SELECT
                id,
                nome,
                cnpj,
                telefone,
                email,
                endereco,
                cep,
                ativo
            FROM fornecedor
            ORDER BY nome
        ";

        return $this->conn
            ->query($sql)
            ->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Lista somente fornecedores ativos.
     */
    public function listarAtivos(): array
    {
        $sql = "
            SELECT
                id,
                nome
            FROM fornecedor
            WHERE ativo = 1
            ORDER BY nome
        ";

        return $this->conn
            ->query($sql)
            ->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Busca um fornecedor pelo ID.
     */
    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT *
            FROM fornecedor
            WHERE id = :id
        ");

        $stmt->execute([
            ':id' => $id
        ]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado ?: null;
    }


    /**
     * Insere um novo fornecedor.
     */
    public function inserir(
        string $nome,
        string $cnpj,
        ?string $telefone,
        ?string $email,
        ?string $endereco,
        ?string $cep
    ): int {

        $stmt = $this->conn->prepare("
            INSERT INTO fornecedor (
                nome,
                cnpj,
                telefone,
                email,
                endereco,
                cep,
                ativo
            )
            VALUES (
                :nome,
                :cnpj,
                :telefone,
                :email,
                :endereco,
                :cep,
                1
            )
        ");

        $stmt->execute([
            ':nome' => $nome,
            ':cnpj' => $cnpj,
            ':telefone' => $telefone,
            ':email' => $email,
            ':endereco' => $endereco,
            ':cep' => $cep
        ]);

        return (int) $this->conn->lastInsertId();
    }


    /**
     * Atualiza um fornecedor existente.
     */
    public function atualizar(
        int $id,
        string $nome,
        string $cnpj,
        ?string $telefone,
        ?string $email,
        ?string $endereco,
        ?string $cep
    ): void {

        $stmt = $this->conn->prepare("
            UPDATE fornecedor
            SET
                nome = :nome,
                cnpj = :cnpj,
                telefone = :telefone,
                email = :email,
                endereco = :endereco,
                cep = :cep
            WHERE id = :id
        ");

        $stmt->execute([
            ':id' => $id,
            ':nome' => $nome,
            ':cnpj' => $cnpj,
            ':telefone' => $telefone,
            ':email' => $email,
            ':endereco' => $endereco,
            ':cep' => $cep
        ]);
    }


    /**
     * Ativa ou inativa um fornecedor.
     */
    public function setAtivo(int $id, bool $ativo): void
    {
        $stmt = $this->conn->prepare("
            UPDATE fornecedor
            SET ativo = :ativo
            WHERE id = :id
        ");

        $stmt->execute([
            ':id' => $id,
            ':ativo' => $ativo ? 1 : 0
        ]);
    }
}
