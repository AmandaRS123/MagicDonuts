<?php


// ============================================================================
// CONCEITO MVC: MODEL (CAMADA DE DADOS E REGRAS DE NEGÓCIO)
// O Model é responsável por se comunicar diretamente com o banco de dados (SQL).
// Ele isola toda a lógica de persistência para que o Controller não precise
// saber como os dados são gravados, buscados ou atualizados.
// ============================================================================


// Importa a classe de conexão com o banco de dados.
require_once __DIR__ . '/../config/db.php';


/**
 * CLASSE MODEL: Fornecedor
 * Funciona como um "molde" que agrupa todas as operações do BD referentes a fornecedores.
 */
class Fornecedor
{
    // PROPRIEDADE PRIVADA (Encapsulamento):
 // Armazena o objeto de conexão PDO. Sendo 'private', ela só pode ser acessada
    // internamente dentro desta própria classe através de $this->conn.
    private PDO $conn;


    /**
     * CONSTRUTOR (__construct)
     * Método mágico executado automaticamente quando instanciamos a classe (new Fornecedor()).
     * Garante que a classe já nasça com a conexão de banco pronta para uso.
     */
    public function __construct()
    {
        // Obtém a conexão ativa gerenciada pela classe Database.
        $this->conn = Database::getConnection();
    }


    /**
     * MÉTODO: listarTodos()
     * Retorna um array com todos os fornecedores cadastrados na tabela.
     * O tipo de retorno ': array' garante a integridade do tipo de dado devolvido.
     */
    public function listarTodos(): array
    {
        // Query SQL pura selecionando campos específicos ordenados por nome.
        $sql = "SELECT id, nome, cnpj, telefone, email, endereco, ativo
                FROM fornecedor
                ORDER BY nome";


        // query(): Método do PDO para consultas estáticas diretas (sem entrada de usuários).
        // fetchAll(): Extrai todos os resultados do banco e converte em um array do PHP.
        return $this->conn->query($sql)->fetchAll();
    }


    /**
     * MÉTODO: listarAtivos()
     * Traz apenas os fornecedores com status ativo (ativo = 1).
     */
    public function listarAtivos(): array
    {
        $sql = "SELECT id, nome FROM fornecedor WHERE ativo = 1 ORDER BY nome";
        return $this->conn->query($sql)->fetchAll();
    }


    /**
     * MÉTODO: buscarPorId()
     * Busca os dados de um único fornecedor pelo seu ID.
     * Retorno ': ?array' significa que aceita devolver um Array (sucesso) ou NULL (não encontrado).
     */
    public function buscarPorId(int $id): ?array
    {
        // PREPARED STATEMENTS (Segurança contra SQL Injection):
        // Usamos prepare() com um placeholder (:id) em vez de concatenar variáveis no SQL.
        $stmt = $this->conn->prepare(
            "SELECT * FROM fornecedor WHERE id = :id"
        );


        // Subtitui o placeholder ':id' pelo valor real sanitizado durante a execução.
        $stmt->execute([':id' => $id]);


        // fetch(): Traz apenas a PRIMEIRA linha correspondente do resultado.
        $r = $stmt->fetch();


        // Operador Ternário Curto (?:): Retorna os dados se existirem, ou null se vier falso.
        return $r ?: null;
    }


    /**
     * MÉTODO: inserir()
     * Cadastra um novo fornecedor na tabela.
     * Tipagem com '?' (ex: ?string) indica parâmetros opcionais que aceitam valor NULL.
     */
    public function inserir(string $nome, ?string $cnpj, ?string $telefone, ?string $email, ?string $endereco): int
    {
        // Prepara a instrução SQL de inserção.
        // O campo 'ativo' recebe 1 diretamente no SQL (novo fornecedor nasce ativo por padrão).
        $stmt = $this->conn->prepare("
            INSERT INTO fornecedor (nome, cnpj, telefone, email, endereco, ativo)
            VALUES (:nome, :cnpj, :telefone, :email, :endereco, 1)
        ");


        // Executa passando o mapa de dados para os placeholders de forma segura.
        $stmt->execute([
            ':nome'     => $nome,
            ':cnpj'     => $cnpj,
            ':telefone' => $telefone,
            ':email'    => $email,
            ':endereco' => $endereco,
        ]);


        // lastInsertId(): Captura a chave primária (ID auto-increment) gerada para este novo registro.
        return (int) $this->conn->lastInsertId();
    }


    /**
     * MÉTODO: atualizar()
     * Altera as informações de um fornecedor já existente.
     * Retorno ': void' sinaliza que o método executa uma instrução mas não devolve valor.
     */
    public function atualizar(int $id, string $nome, ?string $cnpj, ?string $telefone, ?string $email, ?string $endereco): void
    {
        // Prepara a consulta SQL de atualização filtrando pelo ID.
        $stmt = $this->conn->prepare("
            UPDATE fornecedor
            SET nome = :nome, cnpj = :cnpj, telefone = :telefone,
                email = :email, endereco = :endereco
            WHERE id = :id
        ");


        // Envia os dados parametrizados protegendo a consulta.
        $stmt->execute([
            ':id'       => $id,
            ':nome'     => $nome,
            ':cnpj'     => $cnpj,
            ':telefone' => $telefone,
            ':email'    => $email,
            ':endereco' => $endereco,
        ]);
    }


    /**
     * MÉTODO: setAtivo()
     * Altera apenas a coluna 'ativo' (Ativar / Inativar).
     * Conceito de EXCLUSÃO LÓGICA (Soft Delete): O registro não é apagado fisicamente
     * do banco de dados, preservando o histórico do sistema.
     */
    public function setAtivo(int $id, bool $ativo): void
    {
        $stmt = $this->conn->prepare(
            "UPDATE fornecedor SET ativo = :ativo WHERE id = :id"
        );


        // Operador Ternário ($ativo ? 1 : 0): Converte o tipo Boolean do PHP (true/false)
        // para o tipo Inteiro (1/0) armazenado na coluna do banco de dados.
        $stmt->execute([
            ':id'    => $id,
            ':ativo' => $ativo ? 1 : 0,
        ]);
    }
}


                                                                                                    