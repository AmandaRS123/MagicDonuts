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

        $clientes = $conn->query("
            SELECT id, nome
            FROM cliente
            ORDER BY nome
        ")->fetchAll(PDO::FETCH_ASSOC);

        $variacoes = $conn->query("
            SELECT
                v.id,
                v.produto_id,
                v.tamanho AS variacao_nome,
                v.sku,
                v.preco,
                p.nome AS produto_nome,
                COALESCE(e.quantidade, 0) AS estoque_quantidade
            FROM variacao v
            INNER JOIN produto p
                ON p.id = v.produto_id
            LEFT JOIN estoque e
                ON e.variacao_id = v.id
            WHERE p.ativo = 1
            ORDER BY p.nome, v.tamanho
        ")->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/vendas.php';
    }

    public function salvar(): void
    {
        $this->check();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=venda&action=index');
            exit;
        }

        $clienteId = (int) ($_POST['cliente_id'] ?? 0);
        $variacaoIds = $_POST['variacao_id'] ?? [];
        $quantidades = $_POST['quantidade'] ?? [];
        $precos = $_POST['preco_unitario'] ?? [];

        if ($clienteId <= 0) {
            die('Selecione um cliente.');
        }

        $itens = [];

        foreach ($variacaoIds as $i => $variacaoId) {
            $variacaoId = (int) $variacaoId;
            $quantidade = (int) ($quantidades[$i] ?? 0);

            $precoTexto = str_replace(
                ',',
                '.',
                (string) ($precos[$i] ?? '0')
            );

            if (!is_numeric($precoTexto)) {
                die('Informe um preço válido.');
            }

            $preco = (float) $precoTexto;

            if ($preco < 0) {
                die('O preço não pode ser negativo.');
            }

            if ($variacaoId > 0 && $quantidade > 0) {
                $itens[] = [
                    'variacao_id' => $variacaoId,
                    'quantidade' => $quantidade,
                    'preco_unitario' => $preco
                ];
            }
        }

        if (empty($itens)) {
            die('Informe ao menos um item válido para a venda.');
        }

        $vendaModel = new Venda();
        $usuarioId = (int) ($_SESSION['usuario_id'] ?? 0);

        if ($usuarioId <= 0) {
            die('Usuário não identificado.');
        }

        try {
            $vendaId = $vendaModel->registrar(
                $usuarioId,
                $clienteId,
                $itens
            );

            $_SESSION['venda_sucesso'] =
                'Venda nº ' . $vendaId . ' registrada com sucesso!';

        } catch (InvalidArgumentException | RuntimeException $e) {
            die(
                'Não foi possível concluir a venda: ' .
                htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
            );

        } catch (Throwable $e) {
            error_log('Erro ao registrar venda: ' . $e->getMessage());
            die('Erro inesperado ao registrar a venda.');
        }

        header('Location: index.php?controller=venda&action=index');
        exit;
    }

    /**
     * Ação acionada pelo botão Emitir nota.
     */
    public function emitirNota(): void
    {
        $this->check();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=venda&action=index');
            exit;
        }

        $vendaId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$vendaId || $vendaId <= 0) {
            $_SESSION['venda_erro'] = 'Identificador de venda inválido.';
            header('Location: index.php?controller=venda&action=index');
            exit;
        }

        try {
            $vendaModel = new Venda();

            if (!$vendaModel->emitirNotaFiscal((int) $vendaId)) {
                $_SESSION['venda_erro'] =
                    'Não foi possível emitir a nota desta venda.';
            } else {
                $_SESSION['venda_sucesso'] =
                    'Emissão da NF concluída com sucesso!';
            }

        } catch (Throwable $e) {
            error_log('Erro ao emitir nota: ' . $e->getMessage());

            $_SESSION['venda_erro'] =
                'Ocorreu um erro ao emitir a nota.';
        }

        header('Location: index.php?controller=venda&action=index');
        exit;
    }

    /**
     * Exibe a nota somente depois da emissão.
     */
    public function notaFiscal(): void
    {
        $this->check();

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id || $id <= 0) {
            http_response_code(400);
            exit('Identificador de venda inválido.');
        }

        $vendaModel = new Venda();
        $venda = $vendaModel->buscarParaNotaFiscal((int) $id);

        if (!$venda) {
            http_response_code(404);
            exit('Venda não encontrada.');
        }

        if ((int) $venda['nf_emitida'] !== 1) {
            header('Location: index.php?controller=venda&action=index');
            exit;
        }

        $cliente = [
            'nome' => $venda['cliente_nome'] ?? 'Não informado'
        ];

        require __DIR__ . '/../views/nota_fiscal_venda.php';
    }

    private function check(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?controller=auth&action=form');
            exit;
        }
    }
}