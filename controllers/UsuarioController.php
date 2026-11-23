
<?php
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController
{
    private function onlyAdmin(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?controller=auth&action=form');
            exit;
        }

        if (($_SESSION['perfil'] ?? '') !== 'admin') {
            http_response_code(403);
            exit('Acesso permitido somente para administradores.');
        }
    }

    public function index(): void
    {
        $this->onlyAdmin();

        $usuarioModel = new Usuario();
        $usuarios = $usuarioModel->listarTodos();

        require __DIR__ . '/../views/usuarios.php';
    }

    public function create(): void
    {
        $this->onlyAdmin();

        require __DIR__ . '/../views/cadastro.php';
    }

    public function store(): void
    {
        $this->onlyAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=usuario&action=create');
            exit;
        }

        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if ($nome === '' || $email === '' || $senha === '') {
            exit('Preencha todos os campos.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            exit('Informe um e-mail válido.');
        }

        if (strlen($senha) < 6) {
            exit('A senha deve possuir pelo menos 6 caracteres.');
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $usuarioModel = new Usuario();

        try {
            $usuarioModel->cadastrar(
                $nome,
                $email,
                $senhaHash,
                'vendedor',
                1
            );

            header('Location: index.php?controller=usuario&action=index&sucesso=cadastro');
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                exit('Este e-mail já está cadastrado.');
            }

            error_log('Erro ao cadastrar usuário: ' . $e->getMessage());
            exit('Não foi possível cadastrar o funcionário.');
        }
    }

    public function alterarStatus(): void
    {
        $this->onlyAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=usuario&action=index');
            exit;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $ativo = filter_input(INPUT_POST, 'ativo', FILTER_VALIDATE_INT);

        if (!$id || !in_array($ativo, [0, 1], true)) {
            header('Location: index.php?controller=usuario&action=index&erro=dados');
            exit;
        }

        $usuarioLogado = (int) $_SESSION['usuario_id'];

        if ($id === $usuarioLogado && $ativo === 0) {
            header('Location: index.php?controller=usuario&action=index&erro=propria_conta');
            exit;
        }

        try {
            $usuarioModel = new Usuario();

            $resultado = $usuarioModel->alterarStatus($id, $ativo);

            if ($resultado === 'ultimo_admin') {
                header('Location: index.php?controller=usuario&action=index&erro=ultimo_admin');
                exit;
            }

            if ($resultado === false) {
                header('Location: index.php?controller=usuario&action=index&erro=nao_encontrado');
                exit;
            }

            header('Location: index.php?controller=usuario&action=index&sucesso=status');
            exit;

        } catch (Throwable $e) {
            error_log('Erro ao alterar status do usuário: ' . $e->getMessage());
            header('Location: index.php?controller=usuario&action=index&erro=operacao');
            exit;
        }
    }
}
