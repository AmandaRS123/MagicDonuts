<?php


// ============================================================================
// CONCEITO MVC: CONTROLLER (CONTROLADOR)
// O Controller atua como "garçom" do sistema: recebe as requisições do usuário,
// aciona as regras de negócio no Model e envia a resposta para a View (tela).
// ============================================================================


// Importa o Model necessário. Sem ele, este Controller não consegue interagir com o banco de dados.
require_once __DIR__ . '/../models/Fornecedor.php';
 
class FornecedorController
{
    /**
     * AÇÃO: index()
     * Responsável por montar a página principal de gestão de fornecedores.
     */
    public function index(): void
    {
        // 1. AUTENTICAÇÃO E AUTORIZAÇÃO: Valida se quem está acessando pode ver esta tela.
        $this->check();      // O usuário fez login?
        $this->onlyAdmin();  // O usuário é um administrador?
 
        // 2. BUSCA DE DADOS: Instancia o Model e busca todos os fornecedores no banco.
        $fornecedorModel = new Fornecedor();
        $fornecedores = $fornecedorModel->listarTodos();
 
        // 3. LÓGICA DE EDIÇÃO:
        // Se a URL contiver o parâmetro 'id' (ex: index.php?id=5), busca o fornecedor
        // correspondente para carregar seus dados no formulário de alteração.
        $editar = null;
        if (isset($_GET['id'])) {
            $editar = $fornecedorModel->buscarPorId((int) $_GET['id']);
        }
 
        // 4. CARREGAMENTO DA VIEW:
        // As variáveis criadas acima ($fornecedores e $editar) ficam automaticamente
        // disponíveis para serem exibidas dentro do arquivo da View abaixo.
        require_once __DIR__ . '/../views/fornecedores.php';
    }
 
    /**
     * AÇÃO: salvar()
     * Processa os dados de formulários enviados via método POST.
     * Trata tanto a inclusão de NOVOS fornecedores quanto a ATUALIZAÇÃO dos existentes.
     */
    public function salvar(): void
    {
        // 1. BARREIRA DE SEGURANÇA
        $this->check();
        $this->onlyAdmin();
 
        // 2. CAPTURA E SANITIZAÇÃO BÁSICA DOS DADOS ($_POST)
        // - (int): Força a conversão para inteiro por segurança (Casting).
        // - ?? '': Operador Null Coalescing (evita erro caso a chave não exista na requisição).
        // - trim(): Remove espaços em branco acidentais no início e fim do texto.
        $id       = (int) ($_POST['id'] ?? 0);
        $nome     = trim($_POST['nome'] ?? '');
        $cnpj     = trim($_POST['cnpj'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $endereco = trim($_POST['endereco'] ?? '');
 
        // 3. TRATAMENTO DE CAMPOS OPCIONAIS:
        // Se o campo for enviado em branco (''), convertemos para 'null' para salvar
        // o tipo nulo correto no banco de dados em vez de um texto vazio.
        $cnpj     = $cnpj === '' ? null : $cnpj;
        $telefone = $telefone === '' ? null : $telefone;
        $email    = $email === '' ? null : $email;
        $endereco = $endereco === '' ? null : $endereco;
 
        // 4. VALIDAÇÃO DE CAMPO OBRIGATÓRIO:
        // Interrompe o processo imediatamente se o nome estiver em branco.
        if ($nome === '') {
            die("Nome do fornecedor é obrigatório.");
        }
 
        // 5. REGRA DE NEGÓCIO (Inserção vs Atualização)
        $fornecedorModel = new Fornecedor();
 
        // Se o ID for > 0, sabemos que o registro já existe no banco (Edição).
        // Caso contrário (ID == 0), trata-se de um registro inédito (Novo Cadastro).
        if ($id > 0) {
            $fornecedorModel->atualizar($id, $nome, $cnpj, $telefone, $email, $endereco);
        } else {
            $fornecedorModel->inserir($nome, $cnpj, $telefone, $email, $endereco);
        }
 
        // 6. REDIRECIONAMENTO (Padrão PRG: Post-Redirect-Get)
        // Redireciona o usuário de volta para a lista. Isso evita o reenvio de dados
        // duplicados se ele atualizar a página (F5) após salvar.
        header("Location: index.php?controller=fornecedor&action=index");
        exit; // O exit obriga o script a parar imediatamente após enviar o cabeçalho.
    }
 
    /**
     * AÇÃO: toggle()
     * Alterna o status do fornecedor entre Ativo (1) e Inativo (0).
     * Técnica conhecida como "Exclusão Lógica" (Soft Delete).
     */
    public function toggle(): void
    {
        $this->check();
        $this->onlyAdmin();
 
        // Recebe os dados passados diretamente pela URL ($_GET)
        $id    = (int) ($_GET['id'] ?? 0);
        $ativo = (int) ($_GET['ativo'] ?? 1);
 
        // Validação preventiva
        if ($id <= 0) die("ID inválido.");
 
        // Executa a alteração. O comparativo ($ativo === 1) envia um booleano (true/false) para o Model.
        $fornecedorModel = new Fornecedor();
        $fornecedorModel->setAtivo($id, $ativo === 1);
 
        // Redireciona para a listagem atualizada
        header("Location: index.php?controller=fornecedor&action=index");
        exit;
    }
 
    /**
     * MÉTODOS AUXILIARES (PRIVADOS)
     * Métodos declarados como 'private' só podem ser executados internamente pela própria classe.
     */


    // Verifica se o usuário tem uma sessão válida (se está autenticado no sistema).
    private function check(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            // Caso não esteja logado, redireciona diretamente para a tela de login.
            header("Location: index.php?controller=auth&action=form");
            exit;
        }
    }
 
    // Verifica o nível de permissão (Autorização).
    private function onlyAdmin(): void
    {
        if (($_SESSION['perfil'] ?? '') !== 'admin') {
            // Bloqueia a execução com mensagem de erro caso o perfil não seja 'admin'.
            die("Acesso negado.");
        }
    }
}

