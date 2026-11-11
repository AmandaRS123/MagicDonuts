<?php

require_once __DIR__ . '/../config/helpers.php';

class SiteBaseController
{
    /**
     * Renderiza uma página do site com cabeçalho e rodapé.
     */
    protected function renderSite(
        string $pagina,
        array $dados = []
    ): void {

        $caminho = __DIR__ . '/../views/site/' . $pagina . '.php';

        if (!is_file($caminho)) {
            http_response_code(404);
            die('Página não encontrada.');
        }

        extract($dados, EXTR_SKIP);

        $tituloPagina = $tituloPagina ?? 'Magic Donuts';
        $paginaAtual = $paginaAtual ?? '';

        // Carrega o cabeçalho
        require __DIR__ . '/../views/site/_header.php';

        // Carrega o conteúdo da página
        require $caminho;

        // Carrega o rodapé
        require __DIR__ . '/../views/site/_footer.php';
    }

    /**
     * Redireciona para outra página do site.
     */
    protected function redirecionarSite(
        string $controller,
        string $action = 'index',
        array $params = []
    ): void {

        redirecionar(
            url_site($controller, $action, $params)
        );
    }

    /**
     * Verifica se a requisição é POST.
     */
    protected function exigirPost(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            http_response_code(405);
            die('Método não permitido.');
        }
    }

    /**
     * Verifica o token de segurança dos formulários.
     */
    protected function validarFormulario(): void
    {
        if (!csrf_validar()) {
            http_response_code(403);
            die('Sua sessão expirou. Atualize a página e tente novamente.');
        }
    }

    /**
     * Armazena uma mensagem temporária.
     */
    protected function flash(
        string $tipo,
        string $mensagem
    ): void {

        mensagem_site($tipo, $mensagem);
    }

    /**
     * Recupera uma mensagem temporária.
     */
    protected function obterFlash(string $tipo): ?string
    {
        return mensagem_site($tipo);
    }

    /**
     * Garante que o cliente esteja autenticado.
     */
    protected function exigirCliente(): void
    {
        if (!cliente_logado()) {

            $this->flash(
                'aviso',
                'Entre na sua conta para continuar.'
            );

            $this->redirecionarSite(
                'cliente',
                'login'
            );

            exit;
        }
    }

    /**
     * Retorna para a página anterior ou para um destino padrão.
     */
    protected function voltarOuRedirecionar(
        string $padrao
    ): void {

        $referencia = $_SERVER['HTTP_REFERER'] ?? '';

        $base = MD_URL_BASE;

        if (
            $referencia !== '' &&
            str_starts_with($referencia, $base . '/')
        ) {
            redirecionar($referencia);
            exit;
        }

        redirecionar($padrao);
        exit;
    }
}