
<?php

require_once __DIR__ . '/SiteBaseController.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Vitrine.php';
require_once __DIR__ . '/../models/Carrinho.php';

class LojaController extends SiteBaseController
{
    private PDO $conn;
    private Vitrine $vitrine;

    public function __construct()
    {
        $this->conn = Database::getConnection();
        $this->vitrine = new Vitrine();
    }

    /*
    |--------------------------------------------------------------------------
    | PÁGINA INICIAL DA LOJA
    |--------------------------------------------------------------------------
    */

    public function index(): void
    {
        $categoriaId = filter_input(
            INPUT_GET,
            'categoria',
            FILTER_VALIDATE_INT
        );

        if (!$categoriaId || $categoriaId < 1) {
            $categoriaId = null;
        }

        $busca = trim($_GET['q'] ?? '');

        $ordem = $_GET['ordem'] ?? 'relevancia';

        $ordensPermitidas = [
            'relevancia',
            'menor',
            'maior',
            'nome'
        ];

        if (!in_array($ordem, $ordensPermitidas, true)) {
            $ordem = 'relevancia';
        }

        // Produtos com os campos esperados pelo home.php
        $produtos = $this->vitrine->listarProdutos(
            $categoriaId,
            $busca,
            $ordem
        );

        // Categorias com a quantidade de produtos
        $categorias = $this->vitrine->listarCategorias();

        // Produtos em destaque
        $destaques = $this->vitrine->listarDestaques(4);

        // Categoria selecionada
        $categoriaAtual = null;

        if ($categoriaId !== null) {
            foreach ($categorias as $categoria) {

                if ((int) $categoria['id'] === $categoriaId) {
                    $categoriaAtual = $categoria;
                    break;
                }
            }
        }

        // Verifica se existem filtros ativos
        $filtrando = (
            $categoriaId !== null ||
            $busca !== ''
        );

        $this->renderSite('home', [
            'produtos' => $produtos,
            'categorias' => $categorias,
            'destaques' => $destaques,
            'categoriaId' => $categoriaId,
            'categoriaAtual' => $categoriaAtual,
            'busca' => $busca,
            'ordem' => $ordem,
            'filtrando' => $filtrando
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DETALHES DO PRODUTO
    |--------------------------------------------------------------------------
    */

    public function produto(): void
    {
        $id = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        if (!$id || $id < 1) {
            $this->renderSite('nao_encontrado');
            return;
        }

        // Busca produto e suas variações
        $produto = $this->vitrine->buscarProduto($id);

        if (!$produto) {
            $this->renderSite('nao_encontrado');
            return;
        }

        // Busca produtos da mesma categoria
        $relacionados = $this->vitrine->listarRelacionados(
            (int) $produto['id'],
            (int) $produto['categoria_id'],
            4
        );

        $this->renderSite('produto', [
            'produto' => $produto,
            'relacionados' => $relacionados
        ]);
    }
}
