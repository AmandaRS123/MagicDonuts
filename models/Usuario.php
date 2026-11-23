
<?php
require_once __DIR__ . '/../config/db.php';

class Usuario
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function buscarPorEmail($email)
    {
        $sql = "
            SELECT *
            FROM usuario
            WHERE email = :email
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':email' => $email]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function cadastrar(
        $nome,
        $email,
        $senha_hash,
        $perfil = 'vendedor',
        $ativo = 1
    ) {
        $sql = "
            INSERT INTO usuario (
                nome,
                email,
                senha,
                perfil,
                ativo
            ) VALUES (
                :nome,
                :email,
                :senha,
                :perfil,
                :ativo
            )
        ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':senha' => $senha_hash,
            ':perfil' => $perfil,
            ':ativo' => $ativo
        ]);
    }

    public function listarTodos(): array
    {
        $stmt = $this->conn->query("
            SELECT id, nome, email, perfil, ativo
            FROM usuario
            ORDER BY
                CASE WHEN perfil = 'admin' THEN 0 ELSE 1 END,
                nome ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function alterarStatus(int $id, int $ativo)
    {
        $stmt = $this->conn->prepare("
            SELECT id, perfil, ativo
            FROM usuario
            WHERE id = :id
            LIMIT 1
        ");

        $stmt->execute([':id' => $id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            return false;
        }

        // Não permite desativar o último administrador ativo.
        if (
            $ativo === 0 &&
            $usuario['perfil'] === 'admin' &&
            (int) $usuario['ativo'] === 1
        ) {
            $stmt = $this->conn->query("
                SELECT COUNT(*)
                FROM usuario
                WHERE perfil = 'admin'
                  AND ativo = 1
            ");

            $administradoresAtivos = (int) $stmt->fetchColumn();

            if ($administradoresAtivos <= 1) {
                return 'ultimo_admin';
            }
        }

        $stmt = $this->conn->prepare("
            UPDATE usuario
            SET ativo = :ativo
            WHERE id = :id
        ");

        return $stmt->execute([
            ':ativo' => $ativo,
            ':id' => $id
        ]);
    }
}
