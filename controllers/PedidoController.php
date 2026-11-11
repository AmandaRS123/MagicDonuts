
<?php

require_once __DIR__ . '/SiteBaseController.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Carrinho.php';

class PedidoController extends SiteBaseController
{
    private PDO $conn;
    private Carrinho $carrinho;

    private const FRETE_GRATIS = 60.00;

    private const CIDADES_PROXIMAS = [
        'mesquita',
        'nilopolis',
        'belford roxo',
        'sao joao de meriti',
        'queimados',
        'japeri',
        'duque de caxias'
    ];

    public function __construct()
    {
        $this->conn = Database::getConnection();
        $this->carrinho = new Carrinho();
    }

    /**
     * Exibe a página de checkout.
     */
    public function checkout(): void
    {
        if (!cliente_logado()) {
            $this->redirecionarSite('cliente', 'login');
            return;
        }

        $itens = $this->carrinho->listar();

        if (empty($itens)) {
            $this->redirecionarSite('carrinho', 'index');
            return;
        }

        $cpf = preg_replace(
            '/\D/',
            '',
            $_SESSION['cliente_cpf'] ?? ''
        );

        $stmt = $this->conn->prepare("
            SELECT *
            FROM cliente_site
            WHERE cpf = :cpf
            LIMIT 1
        ");

        $stmt->execute([':cpf' => $cpf]);

        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cliente) {
            $_SESSION['site_erro'] = 'Não foi possível localizar seu cadastro.';
            $this->redirecionarSite('cliente', 'login');
            return;
        }

        $subtotal = (float) $this->carrinho->subtotal();

        if (isset($_GET['tipo_entrega'])) {
            $tipoRecebido = $_GET['tipo_entrega'];

            if (in_array($tipoRecebido, ['entrega', 'retirada'], true)) {
                $_SESSION['tipo_entrega'] = $tipoRecebido;
            }
        }

        $tipoEntrega = $_SESSION['tipo_entrega'] ?? 'entrega';

        $this->renderSite('checkout', [
            'itens' => $itens,
            'subtotal' => $subtotal,
            'cliente' => $cliente,
            'tipoEntrega' => $tipoEntrega,
            'freteAtual' => $_SESSION['frete_valor'] ?? null,
            'cidadeFrete' => $_SESSION['frete_cidade'] ?? '',
            'estadoFrete' => $_SESSION['frete_estado'] ?? ''
        ]);
    }

    /**
     * Consulta o CEP e calcula o frete estimado.
     */
    public function frete(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $cep = preg_replace(
            '/\D/',
            '',
            $_GET['cep'] ?? ''
        );

        if (strlen($cep) !== 8) {
            $this->responderJson([
                'valido' => false,
                'mensagem' => 'Informe um CEP válido com 8 números.'
            ]);
        }

        $dadosCep = $this->consultarCep($cep);

        if (!$dadosCep) {
            $this->responderJson([
                'valido' => false,
                'mensagem' => 'Não foi possível consultar esse CEP. Tente novamente.'
            ]);
        }

        if (($dadosCep['uf'] ?? '') !== 'RJ') {
            $this->responderJson([
                'valido' => false,
                'mensagem' => 'No momento, a Magic Donuts realiza entregas somente no estado do Rio de Janeiro.'
            ]);
        }

        $subtotal = (float) $this->carrinho->subtotal();

        if ($subtotal <= 0) {
            $this->responderJson([
                'valido' => false,
                'mensagem' => 'Seu carrinho está vazio.'
            ]);
        }

        $cidade = $dadosCep['localidade'] ?? '';

        $frete = $this->calcularValorFrete($cidade, $subtotal);
        $prazo = $this->calcularPrazo($cidade);

        $_SESSION['tipo_entrega'] = 'entrega';
        $_SESSION['frete_valor'] = $frete;
        $_SESSION['frete_cep'] = $cep;
        $_SESSION['frete_cidade'] = $cidade;
        $_SESSION['frete_estado'] = 'RJ';
        $_SESSION['frete_prazo'] = $prazo;

        $regiao = $this->identificarRegiao($cidade);

        $mensagem = $frete === 0.0
            ? 'Você ganhou frete grátis!'
            : 'Frete estimado para ' . $cidade . '/RJ.';

        $this->responderJson([
            'valido' => true,
            'mensagem' => $mensagem,
            'valor' => $frete,
            'valor_fmt' => $this->formatarMoeda($frete),
            'total_fmt' => $this->formatarMoeda($subtotal + $frete),
            'regiao' => $regiao,
            'cidade' => $cidade,
            'estado' => 'RJ',
            'cep' => $cep,
            'prazo_horas' => $prazo['horas'],
            'prazo_texto' => $prazo['texto']
        ]);
    }

    /**
     * Consulta um CEP utilizando o ViaCEP.
     */
    private function consultarCep(string $cep): ?array
    {
        $url = 'https://viacep.com.br/ws/' . $cep . '/json/';

        $resposta = false;

        if (function_exists('curl_init')) {
            $curl = curl_init($url);

            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_HTTPHEADER => [
                    'Accept: application/json'
                ]
            ]);

            $resposta = curl_exec($curl);
            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);

            if ($status !== 200 || $resposta === false) {
                return null;
            }
        } else {
            $contexto = stream_context_create([
                'http' => [
                    'timeout' => 10
                ]
            ]);

            $resposta = @file_get_contents($url, false, $contexto);
        }

        if (!$resposta) {
            return null;
        }

        $dados = json_decode($resposta, true);

        if (
            !is_array($dados) ||
            !empty($dados['erro']) ||
            empty($dados['localidade']) ||
            empty($dados['uf'])
        ) {
            return null;
        }

        return $dados;
    }

    /**
     * Calcula o valor do frete conforme a cidade.
     */
    private function calcularValorFrete(
        string $cidade,
        float $subtotal
    ): float {
        if ($subtotal >= self::FRETE_GRATIS) {
            return 0.00;
        }

        $cidadeNormalizada = $this->normalizarTexto($cidade);

        if ($cidadeNormalizada === 'nova iguacu') {
            return 5.00;
        }

        if (in_array(
            $cidadeNormalizada,
            self::CIDADES_PROXIMAS,
            true
        )) {
            return 8.00;
        }

        return 12.00;
    }

    /**
     * Identifica a região.
     */
    private function identificarRegiao(string $cidade): string
    {
        $cidadeNormalizada = $this->normalizarTexto($cidade);

        if ($cidadeNormalizada === 'nova iguacu') {
            return 'Nova Iguaçu';
        }

        if (in_array(
            $cidadeNormalizada,
            self::CIDADES_PROXIMAS,
            true
        )) {
            return 'Cidade próxima';
        }

        return 'Demais cidades do RJ';
    }

    /**
     * Prazo estimado de entrega em horas.
     */
    private function calcularPrazo(string $cidade): array
    {
        $regiao = $this->identificarRegiao($cidade);

        $prazos = [
            'Nova Iguaçu' => [
                'horas' => 2,
                'texto' => 'Prazo estimado: 2 a 4 horas.'
            ],
            'Cidade próxima' => [
                'horas' => 3,
                'texto' => 'Prazo estimado: 3 a 5 horas.'
            ],
            'Demais cidades do RJ' => [
                'horas' => 5,
                'texto' => 'Prazo estimado: 5 a 8 horas.'
            ]
        ];

        return $prazos[$regiao] ?? $prazos['Demais cidades do RJ'];
    }

    /**
     * Remove acentos e padroniza o texto.
     */
    private function normalizarTexto(string $texto): string
    {
        $texto = trim(mb_strtolower($texto, 'UTF-8'));

        if (function_exists('iconv')) {
            $normalizado = iconv(
                'UTF-8',
                'ASCII//TRANSLIT//IGNORE',
                $texto
            );

            if ($normalizado !== false) {
                $texto = $normalizado;
            }
        }

        return strtolower($texto);
    }

    /**
     * Formata valores em reais.
     */
    private function formatarMoeda(float $valor): string
    {
        return 'R$ ' . number_format($valor, 2, ',', '.');
    }

    /**
     * Envia uma resposta JSON e encerra a execução.
     */
    private function responderJson(array $dados): void
    {
        echo json_encode(
            $dados,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        exit;
    }

    /**
     * Finaliza o pedido e dá baixa no estoque.
     */
    public function finalizar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirecionarSite('carrinho', 'index');
            return;
        }

        if (!cliente_logado()) {
            $this->redirecionarSite('cliente', 'login');
            return;
        }

        if (
            function_exists('csrf_validar') &&
            !csrf_validar()
        ) {
            $_SESSION['site_erro'] = 'Sua sessão expirou. Tente novamente.';
            $this->redirecionarSite('pedido', 'checkout');
            return;
        }

        $itens = $this->carrinho->listar();

        if (empty($itens)) {
            $this->redirecionarSite('carrinho', 'index');
            return;
        }

        $cpf = preg_replace(
            '/\D/',
            '',
            $_SESSION['cliente_cpf'] ?? ''
        );

        $tipoEntrega = $_POST['tipo_entrega'] ?? 'entrega';

        if (!in_array($tipoEntrega, ['entrega', 'retirada'], true)) {
            $tipoEntrega = 'entrega';
        }

        $formaPagamento = $_POST['forma_pagamento'] ?? 'pix';

        $formasPermitidas = ['pix', 'cartao', 'boleto'];

        if (!in_array($formaPagamento, $formasPermitidas, true)) {
            $formaPagamento = 'pix';
        }

        $enderecoCompleto = null;
        $cep = null;
        $frete = 0.00;
        $prazoFrete = null;
        $cidade = '';

        if ($tipoEntrega === 'entrega') {

            $cepInformado = preg_replace(
                '/\D/',
                '',
                $_POST['cep'] ?? ''
            );

            if (strlen($cepInformado) !== 8) {
                $_SESSION['site_erro'] = 'Informe um CEP válido.';
                $this->redirecionarSite('pedido', 'checkout');
                return;
            }

            // Consulta novamente o CEP no servidor.
            $dadosCep = $this->consultarCep($cepInformado);

            if (!$dadosCep) {
                $_SESSION['site_erro'] = 'Não foi possível validar o CEP informado.';
                $this->redirecionarSite('pedido', 'checkout');
                return;
            }

            $cidade = $dadosCep['localidade'] ?? '';
            $estado = strtoupper($dadosCep['uf'] ?? '');

            if ($estado !== 'RJ') {
                $_SESSION['site_erro'] = 'No momento, entregamos somente no estado do Rio de Janeiro.';
                $this->redirecionarSite('pedido', 'checkout');
                return;
            }

            $tipoEndereco = $_POST['endereco_tipo'] ?? 'outro';

            if (!in_array($tipoEndereco, ['cadastrado', 'outro'], true)) {
                $tipoEndereco = 'outro';
            }

            if ($tipoEndereco === 'cadastrado') {

                // Busca o endereço original diretamente no banco.
                $stmtCliente = $this->conn->prepare("
                    SELECT endereco
                    FROM cliente_site
                    WHERE cpf = :cpf
                    LIMIT 1
                ");

                $stmtCliente->execute([
                    ':cpf' => $cpf
                ]);

                $cliente = $stmtCliente->fetch(PDO::FETCH_ASSOC);

                $enderecoSalvo = trim($cliente['endereco'] ?? '');

                if ($enderecoSalvo === '') {
                    $_SESSION['site_erro'] = 'Seu cadastro não possui um endereço. Escolha outro endereço para a entrega.';
                    $this->redirecionarSite('pedido', 'checkout');
                    return;
                }

                $enderecoCompleto = $enderecoSalvo . ', CEP: ' . $cepInformado;

            } else {

                $endereco = trim($_POST['endereco'] ?? '');
                $numero = trim($_POST['numero'] ?? '');
                $complemento = trim($_POST['complemento'] ?? '');
                $bairro = trim($_POST['bairro'] ?? '');

                if (
                    $endereco === '' ||
                    $numero === '' ||
                    $bairro === ''
                ) {
                    $_SESSION['site_erro'] = 'Preencha corretamente rua, número e bairro.';
                    $this->redirecionarSite('pedido', 'checkout');
                    return;
                }

                $enderecoCompleto = $endereco . ', nº ' . $numero;

                if ($complemento !== '') {
                    $enderecoCompleto .= ', ' . $complemento;
                }

                $enderecoCompleto .= ', ' . $bairro;
                $enderecoCompleto .= ', ' . $cidade . ' - RJ';
                $enderecoCompleto .= ', CEP: ' . $cepInformado;
            }

            $cep = $cepInformado;

        } else {

            $enderecoCompleto =
                'Retirada na loja - Magic Donuts, Nova Iguaçu/RJ';
        }

        $subtotal = 0;
        $itensValidados = [];

        try {
            $this->conn->beginTransaction();

            foreach ($itens as $item) {

                $variacaoId = (int) ($item['variacao_id'] ?? 0);
                $quantidade = (int) ($item['quantidade'] ?? 0);

                if ($variacaoId <= 0 || $quantidade <= 0) {
                    throw new RuntimeException('Item inválido no carrinho.');
                }

                $stmtEstoque = $this->conn->prepare("
                    SELECT
                        v.id,
                        v.preco,
                        COALESCE(e.quantidade, 0) AS estoque
                    FROM variacao v
                    LEFT JOIN estoque e
                        ON e.variacao_id = v.id
                    WHERE v.id = :id
                    FOR UPDATE
                ");

                $stmtEstoque->execute([
                    ':id' => $variacaoId
                ]);

                $variacao = $stmtEstoque->fetch(PDO::FETCH_ASSOC);

                if (!$variacao) {
                    throw new RuntimeException(
                        'Um produto do carrinho não está mais disponível.'
                    );
                }

                if ((int) $variacao['estoque'] < $quantidade) {
                    throw new RuntimeException(
                        'Estoque insuficiente para um dos produtos.'
                    );
                }

                $preco = (float) $variacao['preco'];

                $subtotal += $preco * $quantidade;

                $itensValidados[] = [
                    'variacao_id' => $variacaoId,
                    'quantidade' => $quantidade,
                    'preco_unitario' => $preco
                ];
            }

            // Calcula o frete com o subtotal real do banco.
            if ($tipoEntrega === 'entrega') {
                $frete = $this->calcularValorFrete(
                    $cidade,
                    $subtotal
                );

                $prazoFrete = $this->calcularPrazo($cidade);
            }

            $valorTotal = $subtotal + $frete;

            $stmtPedido = $this->conn->prepare("
                INSERT INTO pedido (
                    cliente_cpf,
                    tipo_entrega,
                    endereco_entrega,
                    cep_entrega,
                    valor_frete,
                    prazo_frete_dias,
                    forma_pagamento,
                    valor_total
                ) VALUES (
                    :cpf,
                    :tipo_entrega,
                    :endereco,
                    :cep,
                    :frete,
                    :prazo,
                    :pagamento,
                    :total
                )
            ");

            $stmtPedido->execute([
                ':cpf' => $cpf,
                ':tipo_entrega' => $tipoEntrega,
                ':endereco' => $enderecoCompleto,
                ':cep' => $cep,
                ':frete' => $frete,
                ':prazo' => $prazoFrete !== null
                    ? $prazoFrete['horas']
                    : null,
                ':pagamento' => $formaPagamento,
                ':total' => $valorTotal
            ]);

            $pedidoId = (int) $this->conn->lastInsertId();

            $stmtItem = $this->conn->prepare("
                INSERT INTO pedido_item (
                    pedido_id,
                    variacao_id,
                    quantidade,
                    preco_unitario
                ) VALUES (
                    :pedido_id,
                    :variacao_id,
                    :quantidade,
                    :preco
                )
            ");

            $stmtBaixaEstoque = $this->conn->prepare("
                UPDATE estoque
                SET quantidade = quantidade - :quantidade
                WHERE variacao_id = :variacao_id
                  AND quantidade >= :quantidade_validacao
            ");

            foreach ($itensValidados as $item) {

                $stmtItem->execute([
                    ':pedido_id' => $pedidoId,
                    ':variacao_id' => $item['variacao_id'],
                    ':quantidade' => $item['quantidade'],
                    ':preco' => $item['preco_unitario']
                ]);

                $stmtBaixaEstoque->execute([
                    ':quantidade' => $item['quantidade'],
                    ':variacao_id' => $item['variacao_id'],
                    ':quantidade_validacao' => $item['quantidade']
                ]);

                if ($stmtBaixaEstoque->rowCount() !== 1) {
                    throw new RuntimeException(
                        'Não foi possível atualizar o estoque.'
                    );
                }
            }

            $this->conn->commit();

            $this->carrinho->limpar();

            unset(
                $_SESSION['frete_valor'],
                $_SESSION['frete_cep'],
                $_SESSION['frete_cidade'],
                $_SESSION['frete_estado'],
                $_SESSION['frete_prazo'],
                $_SESSION['tipo_entrega']
            );

            $this->redirecionarSite(
                'pedido',
                'pagamento',
                ['id' => $pedidoId]
            );

        } catch (Throwable $e) {

            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            $_SESSION['site_erro'] = $e instanceof RuntimeException
                ? $e->getMessage()
                : 'Não foi possível finalizar seu pedido. Tente novamente.';

            $this->redirecionarSite('pedido', 'checkout');
        }
    }

    /**
     * Exibe a página de pagamento simulado.
     */
    public function pagamento(): void
    {
        if (!cliente_logado()) {
            $this->redirecionarSite('cliente', 'login');
            return;
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->renderSite('nao_encontrado');
            return;
        }

        $cpf = preg_replace(
            '/\D/',
            '',
            $_SESSION['cliente_cpf'] ?? ''
        );

        $stmt = $this->conn->prepare("
            SELECT *
            FROM pedido
            WHERE id = :id
              AND cliente_cpf = :cpf
            LIMIT 1
        ");

        $stmt->execute([
            ':id' => $id,
            ':cpf' => $cpf
        ]);

        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pedido) {
            $this->renderSite('nao_encontrado');
            return;
        }

        if ($pedido['status'] !== 'pendente') {
            $this->redirecionarSite(
                'pedido',
                'confirmacao',
                ['id' => $id]
            );
            return;
        }

        $this->renderSite('pagamento', [
            'pedido' => $pedido
        ]);
    }

    /**
     * Simula a confirmação do pagamento.
     * Não realiza cobrança real.
     */
    public function simularPagamento(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirecionarSite('loja', 'index');
            return;
        }

        if (!cliente_logado()) {
            $this->redirecionarSite('cliente', 'login');
            return;
        }

        if (
            function_exists('csrf_validar') &&
            !csrf_validar()
        ) {
            $_SESSION['site_erro'] = 'Sua sessão expirou. Tente novamente.';
            $this->redirecionarSite('loja', 'index');
            return;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->renderSite('nao_encontrado');
            return;
        }

        $cpf = preg_replace(
            '/\D/',
            '',
            $_SESSION['cliente_cpf'] ?? ''
        );

        $stmt = $this->conn->prepare("
            UPDATE pedido
            SET status = 'pago'
            WHERE id = :id
              AND cliente_cpf = :cpf
              AND status = 'pendente'
        ");

        $stmt->execute([
            ':id' => $id,
            ':cpf' => $cpf
        ]);

        if ($stmt->rowCount() !== 1) {
            $_SESSION['site_erro'] = 'Não foi possível simular o pagamento.';

            $this->redirecionarSite(
                'pedido',
                'pagamento',
                ['id' => $id]
            );

            return;
        }

        $_SESSION['site_sucesso'] = 'Pagamento simulado com sucesso!';

        $this->redirecionarSite(
            'pedido',
            'confirmacao',
            ['id' => $id]
        );
    }

    /**
     * Exibe a confirmação e os detalhes do pedido.
     */
    public function confirmacao(): void
    {
        if (!cliente_logado()) {
            $this->redirecionarSite('cliente', 'login');
            return;
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->renderSite('nao_encontrado');
            return;
        }

        $cpf = preg_replace(
            '/\D/',
            '',
            $_SESSION['cliente_cpf'] ?? ''
        );

        $stmt = $this->conn->prepare("
            SELECT *
            FROM pedido
            WHERE id = :id
              AND cliente_cpf = :cpf
            LIMIT 1
        ");

        $stmt->execute([
            ':id' => $id,
            ':cpf' => $cpf
        ]);

        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pedido) {
            $this->renderSite('nao_encontrado');
            return;
        }

        $stmtItens = $this->conn->prepare("
            SELECT
                pi.quantidade,
                pi.preco_unitario,
                p.nome AS produto_nome,
                v.tamanho AS tamanho
            FROM pedido_item pi
            INNER JOIN variacao v
                ON v.id = pi.variacao_id
            INNER JOIN produto p
                ON p.id = v.produto_id
            WHERE pi.pedido_id = :pedido_id
        ");

        $stmtItens->execute([
            ':pedido_id' => $id
        ]);

        $itens = $stmtItens->fetchAll(PDO::FETCH_ASSOC);

        $this->renderSite('confirmacao', [
            'pedido' => $pedido,
            'itens' => $itens
        ]);
    }


    /**
     * Exibe a nota demonstrativa de um pedido.
     */
    
/**
 * Exibe a nota demonstrativa de um pedido.
 */
public function notaFiscal(): void
{
    if (!cliente_logado()) {
        $this->redirecionarSite('cliente', 'login');
        return;
    }

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id) {
        $this->renderSite('nao_encontrado');
        return;
    }

    $cpf = preg_replace(
        '/\D/',
        '',
        $_SESSION['cliente_cpf'] ?? ''
    );

    // Verifica se o pedido pertence ao cliente.
    $stmt = $this->conn->prepare("
        SELECT *
        FROM pedido
        WHERE id = :id
          AND cliente_cpf = :cpf
        LIMIT 1
    ");

    $stmt->execute([
        ':id' => $id,
        ':cpf' => $cpf
    ]);

    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pedido) {
        $this->renderSite('nao_encontrado');
        return;
    }

    // Busca os dados do cliente.
    $stmtCliente = $this->conn->prepare("
        SELECT *
        FROM cliente_site
        WHERE cpf = :cpf
        LIMIT 1
    ");

    $stmtCliente->execute([
        ':cpf' => $cpf
    ]);

    $cliente = $stmtCliente->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        $this->renderSite('nao_encontrado');
        return;
    }

    // Busca os produtos do pedido.
    $stmtItens = $this->conn->prepare("
        SELECT
            pi.quantidade,
            pi.preco_unitario,
            v.id AS variacao_id,
            v.tamanho AS variacao_nome,
            p.nome AS produto_nome
        FROM pedido_item pi
        INNER JOIN variacao v
            ON v.id = pi.variacao_id
        INNER JOIN produto p
            ON p.id = v.produto_id
        WHERE pi.pedido_id = :pedido_id
    ");

    $stmtItens->execute([
        ':pedido_id' => $id
    ]);

    $pedido['itens'] = $stmtItens->fetchAll(PDO::FETCH_ASSOC);

    // Caminho da nota fiscal.
    $caminho = __DIR__ . '/../views/site/nota_fiscal_pedido.php';

    if (!is_file($caminho)) {
        http_response_code(404);
        die('Arquivo da nota fiscal não encontrado.');
    }

    // Carrega a nota como documento independente,
    // sem duplicar o cabeçalho e rodapé da loja.
    extract([
        'pedido' => $pedido,
        'cliente' => $cliente
    ], EXTR_SKIP);

    require $caminho;
}


    /**
     * Lista os pedidos realizados pelo cliente.
     */
    public function meus(): void
    {
        if (!cliente_logado()) {
            $this->redirecionarSite('cliente', 'login');
            return;
        }

        $cpf = preg_replace(
            '/\D/',
            '',
            $_SESSION['cliente_cpf'] ?? ''
        );

        $stmt = $this->conn->prepare("
            SELECT
                id,
                data,
                status,
                valor_total,
                forma_pagamento,
                tipo_entrega,
                valor_frete,
                prazo_frete_dias AS prazo_horas
            FROM pedido
            WHERE cliente_cpf = :cpf
            ORDER BY data DESC
        ");

        $stmt->execute([
            ':cpf' => $cpf
        ]);

        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->renderSite('meus_pedidos', [
            'pedidos' => $pedidos,
            'clienteChave' => hash('sha256', $cpf)
        ]);
    }
}
