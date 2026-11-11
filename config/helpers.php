<?php

/*
|--------------------------------------------------------------------------
| CONFIGURAÇÕES DO SITE
|--------------------------------------------------------------------------
*/

if (!defined('MD_FRETE_GRATIS_A_PARTIR')) {
    define('MD_FRETE_GRATIS_A_PARTIR', 60.00);
}

if (!defined('MD_URL_BASE')) {
    define('MD_URL_BASE', '/magicdonuts');
}

/*
|--------------------------------------------------------------------------
| SEGURANÇA E FORMATAÇÃO
|--------------------------------------------------------------------------
*/

function e($valor): string
{
    return htmlspecialchars(
        (string) ($valor ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

function moeda($valor): string
{
    return 'R$ ' . number_format(
        (float) $valor,
        2,
        ',',
        '.'
    );
}

/*
|--------------------------------------------------------------------------
| URLS DO SITE
|--------------------------------------------------------------------------
*/

function url_site(
    string $controller,
    string $action = 'index',
    array $params = []
): string {

    $query = array_merge(
        [
            'controller' => $controller,
            'action' => $action
        ],
        $params
    );

    return MD_URL_BASE . '/index.php?' . http_build_query($query);
}

/*
|--------------------------------------------------------------------------
| SESSÃO DO CLIENTE
|--------------------------------------------------------------------------
*/

function cliente_logado(): bool
{
    return !empty($_SESSION['cliente_cpf']);
}

function cliente_atual(): ?array
{
    if (!cliente_logado()) {
        return null;
    }

    return [
        'cpf' => $_SESSION['cliente_cpf'],
        'nome' => $_SESSION['cliente_nome'] ?? '',
        'email' => $_SESSION['cliente_email'] ?? ''
    ];
}

/*
|--------------------------------------------------------------------------
| PROTEÇÃO CSRF
|--------------------------------------------------------------------------
*/

function csrf_token(): string
{
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(
            random_bytes(32)
        );
    }

    return $_SESSION['_csrf_token'];
}

function csrf_campo(): string
{
    return '<input type="hidden" name="_token" value="' .
        e(csrf_token()) .
        '">';
}

function csrf_validar(): bool
{
    $token = $_POST['_token'] ?? '';

    return is_string($token)
        && isset($_SESSION['_csrf_token'])
        && hash_equals($_SESSION['_csrf_token'], $token);
}

/*
|--------------------------------------------------------------------------
| IMAGENS DOS PRODUTOS
|--------------------------------------------------------------------------
*/

function imagem_produto(
    int $produtoId,
    bool $fallback = true
): ?string {

    $pastaFisica = __DIR__ .
        '/../public/uploads/produtos/';

    $pastaUrl = MD_URL_BASE .
        '/public/uploads/produtos/';

    foreach (['webp', 'jpg', 'png', 'jpeg'] as $extensao) {

        $arquivo = $pastaFisica .
            $produtoId . '.' . $extensao;

        if (is_file($arquivo)) {
            return $pastaUrl .
                $produtoId . '.' . $extensao;
        }
    }

    if (!$fallback) {
        return null;
    }

    return MD_URL_BASE .
        '/imagem/produto_sem_foto.png';
}

/*
|--------------------------------------------------------------------------
| REDIRECIONAMENTO
|--------------------------------------------------------------------------
*/

function redirecionar(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/*
|--------------------------------------------------------------------------
| MENSAGENS TEMPORÁRIAS
|--------------------------------------------------------------------------
*/

function mensagem_site(
    string $tipo,
    ?string $mensagem = null
): ?string {

    if ($mensagem !== null) {
        $_SESSION['_mensagens_site'][$tipo] = $mensagem;
        return null;
    }

    $texto = $_SESSION['_mensagens_site'][$tipo] ?? null;

    unset($_SESSION['_mensagens_site'][$tipo]);

    return $texto;
}