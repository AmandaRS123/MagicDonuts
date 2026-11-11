
<?php

class ClienteSite
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function buscarPorEmail(string $email): ?array
    {
        $sql = "
            SELECT *
            FROM cliente_site
            WHERE email = :email
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => trim($email)]);

        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        return $cliente ?: null;
    }

    public function buscarPorCpf(string $cpf): ?array
    {
        $cpf = preg_replace('/\D/', '', $cpf);

        $sql = "
            SELECT *
            FROM cliente_site
            WHERE cpf = :cpf
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':cpf' => $cpf]);

        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        return $cliente ?: null;
    }

    public function cadastrar(array $dados): bool
    {
        $cpf = preg_replace('/\D/', '', $dados['cpf'] ?? '');
        $nome = trim($dados['nome'] ?? '');
        $email = trim($dados['email'] ?? '');
        $telefone = preg_replace('/\D/', '', $dados['telefone'] ?? '');
        $endereco = trim($dados['endereco'] ?? '');
        $senha = $dados['senha'] ?? '';

        if (!$cpf || !$nome || !$email || !$senha) {
            return false;
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "
            INSERT INTO cliente_site (
                cpf, nome, email, senha_hash, endereco, telefone
            ) VALUES (
                :cpf, :nome, :email, :senha_hash, :endereco, :telefone
            )
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':cpf' => $cpf,
            ':nome' => $nome,
            ':email' => $email,
            ':senha_hash' => $senhaHash,
            ':endereco' => $endereco,
            ':telefone' => $telefone
        ]);
    }

    public function atualizarEndereco(string $cpf, string $endereco): bool
    {
        $cpf = preg_replace('/\D/', '', $cpf);

        $sql = "
            UPDATE cliente_site
            SET endereco = :endereco
            WHERE cpf = :cpf
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':endereco' => trim($endereco),
            ':cpf' => $cpf
        ]);
    }

    public function atualizarDados(
        string $cpf,
        string $nome,
        string $email,
        string $telefone,
        string $endereco
    ): bool {
        $cpf = preg_replace('/\D/', '', $cpf);
        $telefone = preg_replace('/\D/', '', $telefone);

        $sql = "
            UPDATE cliente_site
            SET
                nome = :nome,
                email = :email,
                telefone = :telefone,
                endereco = :endereco
            WHERE cpf = :cpf
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':nome' => trim($nome),
            ':email' => trim($email),
            ':telefone' => $telefone,
            ':endereco' => trim($endereco),
            ':cpf' => $cpf
        ]);
    }

    public function atualizarSenha(string $cpf, string $novaSenha): bool
    {
        $cpf = preg_replace('/\D/', '', $cpf);
        $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

        $sql = "
            UPDATE cliente_site
            SET senha_hash = :senha_hash
            WHERE cpf = :cpf
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':senha_hash' => $senhaHash,
            ':cpf' => $cpf
        ]);
    }

    public function excluir(string $cpf): bool
    {
        $cpf = preg_replace('/\D/', '', $cpf);

        $sql = "
            DELETE FROM cliente_site
            WHERE cpf = :cpf
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':cpf' => $cpf
        ]);
    }
}
