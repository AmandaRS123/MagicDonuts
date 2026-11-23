
<?php

require_once __DIR__ . '/../models/Fornecedor.php';

class FornecedorController
{
    /**
     * Exibe a tela de fornecedores.
     */
    public function index(): void
    {
        $this->check();
        $this->onlyAdmin();

        $fornecedorModel = new Fornecedor();

        // Lista todos os fornecedores.
        $fornecedores = $fornecedorModel->listarTodos();

        // Fornecedor selecionado para edição.
        $editar = null;

        if (isset($_GET['id'])) {

            $id = (int) $_GET['id'];

            if ($id > 0) {
                $editar = $fornecedorModel->buscarPorId($id);
            }

        }

        require_once __DIR__ . '/../views/fornecedores.php';
    }


    /**
     * Cadastra ou atualiza um fornecedor.
     */
    public function salvar(): void
    {
        $this->check();
        $this->onlyAdmin();

        $id = (int) ($_POST['id'] ?? 0);

        $nome = trim($_POST['nome'] ?? '');
        $cnpj = trim($_POST['cnpj'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $endereco = trim($_POST['endereco'] ?? '');
        $cep = trim($_POST['cep'] ?? '');

        // Remove caracteres especiais do CNPJ.
        $cnpj = preg_replace('/\D/', '', $cnpj);

        // CNPJ é obrigatório, mas pode ser fictício.
if ($cnpj === '') {
    die('O CNPJ é obrigatório.');
}

        // Nome também é obrigatório.
        if ($nome === '') {
            die('Informe o nome do fornecedor.');
        }

        // Verifica o formato do e-mail, caso tenha sido informado.
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            die('Informe um endereço de e-mail válido.');
        }

        // Campos opcionais vazios são enviados como NULL.
        $telefone = $telefone === '' ? null : $telefone;
        $email = $email === '' ? null : $email;
        $endereco = $endereco === '' ? null : $endereco;

        // Remove caracteres não numéricos do CEP.
        $cep = preg_replace('/\D/', '', $cep);

        if ($cep !== '' && strlen($cep) !== 8) {
            die('Informe um CEP válido com 8 dígitos.');
        }

        // Formata o CEP.
        if ($cep !== '') {
            $cep = substr($cep, 0, 5) . '-' . substr($cep, 5, 3);
        } else {
            $cep = null;
        }

        $fornecedorModel = new Fornecedor();

        if ($id > 0) {

            // Atualiza o fornecedor existente.
            $fornecedorModel->atualizar(
                $id,
                $nome,
                $cnpj,
                $telefone,
                $email,
                $endereco,
                $cep
            );

        } else {

            // Cadastra um novo fornecedor.
            $fornecedorModel->inserir(
                $nome,
                $cnpj,
                $telefone,
                $email,
                $endereco,
                $cep
            );

        }

        header('Location: index.php?controller=fornecedor&action=index');
        exit;
    }


    /**
     * Ativa ou inativa um fornecedor.
     */
    public function toggle(): void
    {
        $this->check();
        $this->onlyAdmin();

        $id = (int) ($_GET['id'] ?? 0);
        $ativo = (int) ($_GET['ativo'] ?? 1);

        if ($id <= 0) {
            die('ID inválido.');
        }

        $fornecedorModel = new Fornecedor();

        $fornecedorModel->setAtivo(
            $id,
            $ativo === 1
        );

        header('Location: index.php?controller=fornecedor&action=index');
        exit;
    }


    /**
     * Verifica se o usuário está autenticado.
     */
    private function check(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?controller=auth&action=form');
            exit;
        }
    }


    /**
     * Permite alterações apenas ao administrador.
     */
    private function onlyAdmin(): void
    {
        if (($_SESSION['perfil'] ?? '') !== 'admin') {
            die('Acesso negado.');
        }
    }
}
