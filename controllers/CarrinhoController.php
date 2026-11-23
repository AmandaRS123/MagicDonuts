
<?php

require_once __DIR__ . '/SiteBaseController.php';
require_once __DIR__ . '/../models/Carrinho.php';

class CarrinhoController extends SiteBaseController
{
    private Carrinho $carrinho;

    public function __construct()
    {
        $this->carrinho = new Carrinho();
    }

    /*
    |--------------------------------------------------------------------------
    | EXIBIR CARRINHO
    |--------------------------------------------------------------------------
    */

    public function index(): void
    {
        $itens = $this->carrinho->listar();

        $subtotal = $this->carrinho->subtotal();

        $quantidadeItens = $this->carrinho->quantidadeTotal();

        $this->renderSite('carrinho', [
            'tituloPagina' => 'Meu carrinho | Magic Donuts',
            'paginaAtual' => 'carrinho',
            'itens' => $itens,
            'subtotal' => $subtotal,
            'quantidadeItens' => $quantidadeItens
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ADICIONAR PRODUTO
    |--------------------------------------------------------------------------
    */

    public function adicionar(): void
    {
        $this->exigirPost();
        $this->validarFormulario();

        $variacaoId = (int) (
            $_POST['variacao_id']
            ?? $_POST['id_variacao']
            ?? 0
        );

        $quantidade = (int) ($_POST['quantidade'] ?? 1);

        if ($this->carrinho->adicionar($variacaoId, $quantidade)) {

            $this->flash(
                'sucesso',
                'Produto adicionado ao carrinho!'
            );

        } else {

            $this->flash(
                'erro',
                'Não foi possível adicionar o produto. Verifique o estoque e a quantidade disponível.'
            );
        }

        $this->redirecionarSite('carrinho');
    }

    /*
    |--------------------------------------------------------------------------
    | ATUALIZAR QUANTIDADES
    |--------------------------------------------------------------------------
    */

    public function atualizar(): void
    {
        $this->exigirPost();
        $this->validarFormulario();

        $quantidades = $_POST['quantidade'] ?? [];

        if (!is_array($quantidades)) {
            $this->flash(
                'erro',
                'Dados de quantidade inválidos.'
            );

            $this->redirecionarSite('carrinho');
        }

        $this->carrinho->atualizar($quantidades);

        $this->flash(
            'sucesso',
            'Carrinho atualizado com sucesso!'
        );

        $this->redirecionarSite('carrinho');
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVER PRODUTO
    |--------------------------------------------------------------------------
    */

    public function remover(): void
    {
        $this->exigirPost();
        $this->validarFormulario();

        $variacaoId = (int) ($_POST['variacao_id'] ?? 0);

        if ($variacaoId <= 0) {

            $this->flash(
                'erro',
                'Produto inválido.'
            );

            $this->redirecionarSite('carrinho');
        }

        $this->carrinho->remover($variacaoId);

        $this->flash(
            'sucesso',
            'Produto removido do carrinho.'
        );

        $this->redirecionarSite('carrinho');
    }

    /*
    |--------------------------------------------------------------------------
    | LIMPAR CARRINHO
    |--------------------------------------------------------------------------
    */

    public function limpar(): void
    {
        $this->exigirPost();
        $this->validarFormulario();

        $this->carrinho->limpar();

        $this->flash(
            'sucesso',
            'Seu carrinho foi esvaziado.'
        );

        $this->redirecionarSite('carrinho');
    }
}
