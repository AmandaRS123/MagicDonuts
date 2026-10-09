<?php

require_once __DIR__ . '/../models/Entrada.php';

class EntradaController
{
    private function check(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?controller=auth&action=form');
            exit;
        }
    }

    public function index(): void
    {
        $this->check();


        $model = new Entrada();

        $fornecedores = $model->listarFornecedores();
        $variacoes = $model->listarVariacoes();
        $entradas = $model->listarComFornecedor();

       

        $nomeUser = $_SESSION['nome'] ?? 'Usuário';

        require __DIR__ . '/../views/entradas.php';
    }

    public function salvar(): void
    {
        $this->check();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=entrada&action=index');
            exit;
        }

        $fornecedorId = (int) ($_POST['fornecedor_id'] ?? 0);
        $variacoes = $_POST['variacao_id'] ?? [];
        $quantidades = $_POST['quantidade'] ?? [];
        $custos = $_POST['custo_unitario'] ?? [];

        $itens = [];

        foreach ($variacoes as $i => $id) {
            $id = (int) $id;
            $quantidade = (int) ($quantidades[$i] ?? 0);

            if ($id <= 0 || $quantidade <= 0) {
                continue;
            }

            $custoTexto = str_replace(
                ',',
                '.',
                str_replace('.', '', (string) ($custos[$i] ?? '0'))
            );

            if (!is_numeric($custoTexto) || (float) $custoTexto < 0) {
                $_SESSION['erro_entrada'] = 'Custo unitário inválido.';
                header('Location: index.php?controller=entrada&action=index');
                exit;
            }

            $itens[] = [
                'variacao_id' => $id,
                'quantidade' => $quantidade,
                'custo_unitario' => (float) $custoTexto
            ];
        }

        try {
            if ($fornecedorId <= 0) {
                throw new InvalidArgumentException('Selecione um fornecedor.');
            }

            if (empty($itens)) {
                throw new InvalidArgumentException(
                    'Selecione uma variação e informe uma quantidade maior que zero.'
                );
            }

            $model = new Entrada();
            $model->registrar($fornecedorId, $itens);

            $_SESSION['sucesso_entrada'] = 'Entrada registrada com sucesso!';


        } catch (Throwable $e) {
    $_SESSION['erro_entrada'] = 'ERRO AO SALVAR: ' . $e->getMessage();
}


        header('Location: index.php?controller=entrada&action=index');
        exit;
    }
}