<?php

require_once __DIR__ . '/../models/Venda.php';
require_once __DIR__ . '/../config/db.php';

class VendaController
{
    public function index(): void
    {
        $this->check();

        $vendaModel = new Venda();

        $vendas = $vendaModel->listarRecentes();

        $conn = Database::getConnection();


        // Lista de clientes
        $clientes = $conn->query("
            SELECT
                id,
                nome
            FROM cliente
            ORDER BY nome
        ")->fetchAll();


        // Lista de variações/produtos ativos
        $variacoes = $conn->query("
            SELECT
                v.id,
                v.sku,
                v.tamanho,
                v.cor,
                v.preco,
                p.nome AS produto_nome

            FROM variacao v

            INNER JOIN produto p
                ON p.id_produto = v.produto_id

            WHERE p.ativo = 1

            ORDER BY p.nome
        ")->fetchAll();


        // Abre a View
        require_once __DIR__ . '/../views/vendas.php';
    }


    public function salvar(): void
    {
        $this->check();


        // Cliente selecionado
        $clienteId = (int) (
            $_POST['cliente_id'] ?? 0
        );


        // Dados dos produtos
        $variacaoIds = $_POST['variacao_id'] ?? [];

        $quantidades = $_POST['quantidade'] ?? [];

        $precos = $_POST['preco_unitario'] ?? [];


        // Verifica se foi selecionado um cliente
        if ($clienteId <= 0) {

            die("Selecione um cliente.");

        }


        $itens = [];


        // Monta os itens da venda
        foreach ($variacaoIds as $i => $variacaoId) {

            $variacaoId = (int) $variacaoId;

            $quantidade = (int) (
                $quantidades[$i] ?? 0
            );

            $preco = (float) str_replace(
                ',',
                '.',
                $precos[$i] ?? '0'
            );


            // Só adiciona itens válidos
            if (
                $variacaoId > 0 &&
                $quantidade > 0
            ) {

                $itens[] = [

                    'variacao_id' => $variacaoId,

                    'quantidade' => $quantidade,

                    'preco_unitario' => $preco

                ];
            }
        }


        // Verifica se existe pelo menos um item
        if (empty($itens)) {

            die(
                "Informe ao menos um item válido para a venda."
            );

        }


        $vendaModel = new Venda();


        $usuarioId = (int) (
            $_SESSION['usuario_id']
        );


        try {

            // Registra a venda
            $vendaModel->registrar(
                $usuarioId,
                $clienteId,
                $itens
            );


        } catch (RuntimeException $e) {

            // Erro de negócio,
            // por exemplo: estoque insuficiente
            die(
                "Não foi possível concluir a venda: "
                . htmlspecialchars(
                    $e->getMessage()
                )
            );


        } catch (Throwable $e) {

            // Erro inesperado
            die(
                "Erro inesperado ao registrar a venda."
            );
        }


        // Volta para a página de vendas
        header(
            "Location: index.php?controller=venda&action=index"
        );

        exit;
    }


    private function check(): void
    {
        // Verifica se o usuário está logado
        if (!isset($_SESSION['usuario_id'])) {

            header(
                "Location: index.php?controller=auth&action=form"
            );

            exit;
        }
    }
}