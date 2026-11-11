
<?php

require_once __DIR__ . '/SiteBaseController.php';
require_once __DIR__ . '/../models/ClienteSite.php';

class ClienteController extends SiteBaseController
{
    private ClienteSite $clienteModel;

    public function __construct()
    {
        $this->clienteModel = new ClienteSite();
    }

    /**
     * Exibe a página de login.
     */
    public function login(): void
    {
        if (cliente_logado()) {
            $this->redirecionarSite('loja', 'index');
        }

        $this->renderSite('login', [
            'tituloPagina' => 'Entrar | Magic Donuts',
            'paginaAtual' => 'login',
            'erro' => $this->obterFlash('erro'),
            'sucesso' => $this->obterFlash('sucesso'),
            'email' => ''
        ]);
    }

    /**
     * Realiza o login do cliente.
     */
    public function autenticar(): void
    {
        $this->exigirPost();
        $this->validarFormulario();

        if (cliente_logado()) {
            $this->redirecionarSite('loja', 'index');
        }

        $email = strtolower(trim($_POST['email'] ?? ''));
        $senha = $_POST['senha'] ?? '';

        if (
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            $senha === ''
        ) {
            $this->flash(
                'erro',
                'Informe um e-mail e uma senha válidos.'
            );

            $this->redirecionarSite('cliente', 'login');
        }

        $cliente = $this->clienteModel->buscarPorEmail($email);

        if (
            !$cliente ||
            !password_verify($senha, $cliente['senha_hash'])
        ) {
            $this->flash(
                'erro',
                'E-mail ou senha incorretos.'
            );

            $this->redirecionarSite('cliente', 'login');
        }

        session_regenerate_id(true);

        $_SESSION['cliente_cpf'] = $cliente['cpf'];
        $_SESSION['cliente_nome'] = $cliente['nome'];
        $_SESSION['cliente_email'] = $cliente['email'];

        $this->flash(
            'sucesso',
            'Bem-vindo(a) de volta, ' . $cliente['nome'] . '!'
        );

        $destino = $_SESSION['destino_apos_login'] ?? null;

        unset($_SESSION['destino_apos_login']);

        if (
            is_string($destino) &&
            str_starts_with($destino, MD_URL_BASE . '/index.php?')
        ) {
            redirecionar($destino);
        }

        $this->redirecionarSite('loja', 'index');
    }

    /**
     * Exibe a página de cadastro.
     */
    public function cadastro(): void
    {
        if (cliente_logado()) {
            $this->redirecionarSite('loja', 'index');
        }

        $this->renderSite('cadastro', [
            'tituloPagina' => 'Criar conta | Magic Donuts',
            'paginaAtual' => 'cadastro',
            'erro' => $this->obterFlash('erro'),
            'sucesso' => $this->obterFlash('sucesso')
        ]);
    }

    /**
     * Cadastra um novo cliente.
     */
    public function cadastrar(): void
    {
        $this->exigirPost();
        $this->validarFormulario();

        if (cliente_logado()) {
            $this->redirecionarSite('loja', 'index');
        }

        $nome = trim($_POST['nome'] ?? '');
        $cpf = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));

        $senha = $_POST['senha'] ?? '';
        $confirmarSenha = $_POST['confirmar_senha'] ?? '';

        $telefone = preg_replace('/\D/', '', $_POST['telefone'] ?? '');
        $endereco = trim($_POST['endereco'] ?? '');

        $dados = [
            'nome' => $nome,
            'cpf' => $cpf,
            'email' => $email,
            'telefone' => $telefone,
            'endereco' => $endereco,
            'senha' => $senha
        ];

        if (
            $nome === '' ||
            mb_strlen($nome) < 3 ||
            mb_strlen($nome) > 100
        ) {
            $this->erroCadastro('Informe um nome válido.');
        }

        // Exige 11 números, sem validar os dígitos verificadores.
        if (strlen($cpf) !== 11) {
            $this->erroCadastro('Informe um CPF com 11 números.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->erroCadastro('Informe um e-mail válido.');
        }

        if (strlen($senha) < 8) {
            $this->erroCadastro(
                'Sua senha precisa ter pelo menos 8 caracteres.'
            );
        }

        if ($senha !== $confirmarSenha) {
            $this->erroCadastro(
                'As senhas informadas não são iguais.'
            );
        }

        if ($endereco === '') {
            $this->erroCadastro(
                'Informe seu endereço de entrega.'
            );
        }

        if ($this->clienteModel->buscarPorEmail($email)) {
            $this->erroCadastro(
                'Este e-mail já está cadastrado.'
            );
        }

        if ($this->clienteModel->buscarPorCpf($cpf)) {
            $this->erroCadastro(
                'Este CPF já possui uma conta.'
            );
        }

        $cadastrado = $this->clienteModel->cadastrar($dados);

        if (!$cadastrado) {
            $this->erroCadastro(
                'Não foi possível realizar seu cadastro. Tente novamente.'
            );
        }

        $cliente = $this->clienteModel->buscarPorCpf($cpf);

        if (!$cliente) {
            $this->erroCadastro(
                'Sua conta foi criada, mas não foi possível iniciar sua sessão. Faça login.'
            );
        }

        session_regenerate_id(true);

        $_SESSION['cliente_cpf'] = $cliente['cpf'];
        $_SESSION['cliente_nome'] = $cliente['nome'];
        $_SESSION['cliente_email'] = $cliente['email'];

        $this->flash(
            'sucesso',
            'Cadastro concluído com sucesso! Seja bem-vindo(a) à Magic Donuts, ' . $cliente['nome'] . '!'
        );

        $this->redirecionarSite('loja', 'index');
    }

    /**
     * Exibe a página Minha conta.
     */
    public function conta(): void
    {
        $this->exigirCliente();

        $cliente = $this->clienteModel->buscarPorCpf(
            $_SESSION['cliente_cpf']
        );

        if (!$cliente) {
            $this->sair();
            return;
        }

        $this->renderSite('meus_dados', [
            'tituloPagina' => 'Minha conta | Magic Donuts',
            'paginaAtual' => 'conta',
            'cliente' => $cliente,
            'erro' => $this->obterFlash('erro'),
            'sucesso' => $this->obterFlash('sucesso')
        ]);
    }

    /**
     * Atualiza os dados pessoais do cliente.
     */
    public function atualizarDados(): void
    {
        $this->exigirPost();
        $this->validarFormulario();
        $this->exigirCliente();

        $cpf = $_SESSION['cliente_cpf'];

        $nome = trim($_POST['nome'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $telefone = preg_replace('/\D/', '', $_POST['telefone'] ?? '');
        $endereco = trim($_POST['endereco'] ?? '');

        if (
            $nome === '' ||
            mb_strlen($nome) < 3 ||
            mb_strlen($nome) > 100
        ) {
            $this->flash('erro', 'Informe um nome válido.');
            $this->redirecionarSite('cliente', 'conta');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('erro', 'Informe um e-mail válido.');
            $this->redirecionarSite('cliente', 'conta');
        }

        if ($endereco === '') {
            $this->flash('erro', 'Informe seu endereço.');
            $this->redirecionarSite('cliente', 'conta');
        }

        $outroCliente = $this->clienteModel->buscarPorEmail($email);

        if (
            $outroCliente &&
            preg_replace('/\D/', '', $outroCliente['cpf']) !==
            preg_replace('/\D/', '', $cpf)
        ) {
            $this->flash(
                'erro',
                'Este e-mail já está sendo utilizado.'
            );

            $this->redirecionarSite('cliente', 'conta');
        }

        $atualizado = $this->clienteModel->atualizarDados(
            $cpf,
            $nome,
            $email,
            $telefone,
            $endereco
        );

        if (!$atualizado) {
            $this->flash(
                'erro',
                'Não foi possível atualizar seus dados.'
            );

            $this->redirecionarSite('cliente', 'conta');
        }

        $_SESSION['cliente_nome'] = $nome;
        $_SESSION['cliente_email'] = $email;

        $this->flash(
            'sucesso',
            'Seus dados foram atualizados com sucesso!'
        );

        $this->redirecionarSite('cliente', 'conta');
    }

    /**
     * Altera a senha do cliente.
     */
    public function alterarSenha(): void
    {
        $this->exigirPost();
        $this->validarFormulario();
        $this->exigirCliente();

        $cpf = $_SESSION['cliente_cpf'];

        $senhaAtual = $_POST['senha_atual'] ?? '';
        $novaSenha = $_POST['nova_senha'] ?? '';
        $confirmarSenha = $_POST['confirmar_senha'] ?? '';

        $cliente = $this->clienteModel->buscarPorCpf($cpf);

        if (
            !$cliente ||
            !password_verify($senhaAtual, $cliente['senha_hash'])
        ) {
            $this->flash(
                'erro',
                'Sua senha atual está incorreta.'
            );

            $this->redirecionarSite('cliente', 'conta');
        }

        if (strlen($novaSenha) < 8) {
            $this->flash(
                'erro',
                'A nova senha precisa ter pelo menos 8 caracteres.'
            );

            $this->redirecionarSite('cliente', 'conta');
        }

        if ($novaSenha !== $confirmarSenha) {
            $this->flash(
                'erro',
                'As novas senhas não coincidem.'
            );

            $this->redirecionarSite('cliente', 'conta');
        }

        $atualizado = $this->clienteModel->atualizarSenha(
            $cpf,
            $novaSenha
        );

        if (!$atualizado) {
            $this->flash(
                'erro',
                'Não foi possível alterar sua senha.'
            );

            $this->redirecionarSite('cliente', 'conta');
        }

        $this->flash(
            'sucesso',
            'Sua senha foi alterada com sucesso!'
        );

        $this->redirecionarSite('cliente', 'conta');
    }

    /**
     * Exclui a conta do cliente.
     */
    public function excluirConta(): void
    {
        $this->exigirPost();
        $this->validarFormulario();
        $this->exigirCliente();

        $cpf = $_SESSION['cliente_cpf'];
        $senha = $_POST['senha_exclusao'] ?? '';

        $cliente = $this->clienteModel->buscarPorCpf($cpf);

        if (
            !$cliente ||
            !password_verify($senha, $cliente['senha_hash'])
        ) {
            $this->flash(
                'erro',
                'Senha incorreta. A conta não foi excluída.'
            );

            $this->redirecionarSite('cliente', 'conta');
        }

        $excluido = $this->clienteModel->excluir($cpf);

        if (!$excluido) {
            $this->flash(
                'erro',
                'Não foi possível excluir sua conta.'
            );

            $this->redirecionarSite('cliente', 'conta');
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $parametros = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $parametros['path'],
                $parametros['domain'],
                $parametros['secure'],
                $parametros['httponly']
            );
        }

        session_destroy();

        $this->redirecionarSite('loja', 'index');
    }

    /**
     * Exibe erros de cadastro e retorna ao formulário.
     */
    private function erroCadastro(string $mensagem): void
    {
        $this->flash('erro', $mensagem);

        $this->redirecionarSite('cliente', 'cadastro');
    }

    /**
     * Valida os dígitos verificadores do CPF.
     * Mantido como método auxiliar, mas não é usado no cadastro.
     */
    private function cpfValido(string $cpf): bool
    {
        if (
            strlen($cpf) !== 11 ||
            preg_match('/^(\d)\1{10}$/', $cpf)
        ) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $soma = 0;

            for ($i = 0; $i < $t; $i++) {
                $soma += (int) $cpf[$i] * (($t + 1) - $i);
            }

            $resto = ($soma * 10) % 11;
            $digito = $resto === 10 ? 0 : $resto;

            if ((int) $cpf[$t] !== $digito) {
                return false;
            }
        }

        return true;
    }
}
