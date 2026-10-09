<?php

require_once __DIR__ . '/../models/Variacao.php';
require_once __DIR__ . '/../models/Produto.php';

class VariacaoController
{
    public function index(): void
    {
        $this->check();

        $produtoId = (int) ($_GET['produto_id'] ?? 0);

        if ($produtoId <= 0) {
            die("Produto inválido.");
        }

        $produtoModel = new Produto();
        $variacaoModel = new Variacao();

        // Busca o produto
        $produto = $produtoModel->buscarPorId($produtoId);

        if (!$produto) {
            die("Produto não encontrado.");
        }

        // Lista as variações do produto
        $variacoes = $variacaoModel->listarPorProduto($produtoId);

        // Verifica se existe uma variação sendo editada
        $editar = null;

        if (isset($_GET['id'])) {

            $editar = $variacaoModel->buscarPorId(
                (int) $_GET['id']
            );

            // Impede editar uma variação
            // que pertença a outro produto
            if (
                $editar &&
                (int) $editar['produto_id'] !== $produtoId
            ) {
                $editar = null;
            }
        }

        require_once __DIR__ . '/../views/variacao.php';
    }

    public function salvar(): void
    {
        $this->check();
        $this->onlyAdmin();

        $id = (int) ($_POST['id'] ?? 0);

        $produtoId = (int) (
            $_POST['produto_id'] ?? 0
        );

        $nome = trim(
            $_POST['nome'] ?? ''
        );

        $sku = trim(
            $_POST['sku'] ?? ''
        );

        $preco = (float) (
            $_POST['preco'] ?? 0
        );

        if (
            $produtoId <= 0 ||
            $nome === '' ||
            $sku === '' ||
            $preco < 0
        ) {
            die("Dados inválidos.");
        }

        $variacaoModel = new Variacao();

        if ($id > 0) {

            // Atualizar variação
            $variacaoModel->atualizar(
                $id,
                $nome,
                $sku,
                $preco
            );

        } else {

            // Inserir nova variação
            $variacaoModel->inserir(
                $produtoId,
                $nome,
                $sku,
                $preco
            );
        }

        // Volta para a página de variações
        header(
            "Location: index.php?controller=variacao&action=index&produto_id="
            . $produtoId
        );

        exit;
    }

    public function deletar(): void
    {
        $this->check();
        $this->onlyAdmin();

        $id = (int) (
            $_GET['id'] ?? 0
        );

        $produtoId = (int) (
            $_GET['produto_id'] ?? 0
        );

        if (
            $id <= 0 ||
            $produtoId <= 0
        ) {
            die("Dados inválidos.");
        }

        $variacaoModel = new Variacao();

        // Busca a variação
        $variacao = $variacaoModel->buscarPorId($id);

        if (!$variacao) {
            die("Variação não encontrada.");
        }

        // Confirma que a variação pertence
        // ao produto informado
        if (
            (int) $variacao['produto_id']
            !== $produtoId
        ) {
            die("Variação inválida.");
        }

        $variacaoModel->deletar($id);

        // Volta para as variações do produto
        header(
            "Location: index.php?controller=variacao&action=index&produto_id="
            . $produtoId
        );

        exit;
    }

    // Verifica se o usuário está logado
    private function check(): void
    {
        if (
            !isset($_SESSION['usuario_id'])
        ) {
            header(
                "Location: index.php?controller=auth&action=form"
            );

            exit;
        }
    }

    // Verifica se o usuário é administrador
    private function onlyAdmin(): void
    {
        if (
            ($_SESSION['perfil'] ?? '')
            !== 'admin'
        ) {
            die("Acesso negado.");
        }
    }
}