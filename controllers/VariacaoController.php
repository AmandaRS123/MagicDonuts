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
            die('Produto inválido.');
        }

        $produtoModel = new Produto();
        $variacaoModel = new Variacao();

        $produto = $produtoModel->buscarPorId($produtoId);

        if (!$produto) {
            die('Produto não encontrado.');
        }

        $variacoes = $variacaoModel->listarPorProduto($produtoId);

        $editar = null;

        if (isset($_GET['id'])) {
            $editar = $variacaoModel->buscarPorId(
                (int) $_GET['id']
            );

            if (
                $editar &&
                (int) $editar['produto_id'] !== $produtoId
            ) {
                $editar = null;
            }
        }

        require __DIR__ . '/../views/variacao.php';
    }

    public function salvar(): void
    {
        $this->check();
        $this->onlyAdmin();

        $id = (int) ($_POST['id'] ?? 0);
        $produtoId = (int) ($_POST['produto_id'] ?? 0);

        $tamanho = trim($_POST['tamanho'] ?? '');
        $sku = trim($_POST['sku'] ?? '');
        $preco = filter_var(
            $_POST['preco'] ?? null,
            FILTER_VALIDATE_FLOAT
        );

        if (
            $produtoId <= 0 ||
            $tamanho === '' ||
            $sku === '' ||
            $preco === false ||
            $preco < 0
        ) {
            die('Dados inválidos.');
        }

        $produtoModel = new Produto();
        $produto = $produtoModel->buscarPorId($produtoId);

        if (!$produto) {
            die('Produto não encontrado.');
        }

        $variacaoModel = new Variacao();

        if ($id > 0) {
            $variacaoExistente = $variacaoModel->buscarPorId($id);

            if (
                !$variacaoExistente ||
                (int) $variacaoExistente['produto_id'] !== $produtoId
            ) {
                die('Variação inválida.');
            }

            $variacaoModel->atualizar(
                $id,
                $tamanho,
                $sku,
                (float) $preco
            );
        } else {
            $variacaoModel->inserir(
                $produtoId,
                $tamanho,
                $sku,
                (float) $preco
            );
        }

        header(
            'Location: index.php?controller=variacao&action=index&produto_id='
            . $produtoId
        );

        exit;
    }

    public function deletar(): void
    {
        $this->check();
        $this->onlyAdmin();

        $id = (int) ($_GET['id'] ?? 0);
        $produtoId = (int) ($_GET['produto_id'] ?? 0);

        if ($id <= 0 || $produtoId <= 0) {
            die('Dados inválidos.');
        }

        $variacaoModel = new Variacao();
        $variacao = $variacaoModel->buscarPorId($id);

        if (
            !$variacao ||
            (int) $variacao['produto_id'] !== $produtoId
        ) {
            die('Variação inválida.');
        }

        $variacaoModel->deletar($id);

        header(
            'Location: index.php?controller=variacao&action=index&produto_id='
            . $produtoId
        );

        exit;
    }

    private function check(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            header(
                'Location: index.php?controller=auth&action=form'
            );

            exit;
        }
    }

    private function onlyAdmin(): void
    {
        if (($_SESSION['perfil'] ?? '') !== 'admin') {
            die('Acesso negado.');
        }
    }
}