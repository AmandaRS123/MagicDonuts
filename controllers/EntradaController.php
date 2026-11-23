
<?php

require_once __DIR__ . '/../models/Entrada.php';
require_once __DIR__ . '/../models/NotaFiscal.php';

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
                throw new InvalidArgumentException(
                    'Selecione um fornecedor.'
                );
            }

            if (empty($itens)) {
                throw new InvalidArgumentException(
                    'Selecione uma variação e informe uma quantidade maior que zero.'
                );
            }

            $model = new Entrada();
            $model->registrar($fornecedorId, $itens);

            $_SESSION['sucesso_entrada'] =
                'Entrada registrada com sucesso!';

        } catch (Throwable $e) {
    error_log('Erro ao emitir nota fiscal: ' . $e->getMessage());

    $_SESSION['erro_entrada'] =
        $e instanceof InvalidArgumentException
            ? $e->getMessage()
            : 'Não foi possível emitir a nota fiscal demonstrativa.';
}

        header('Location: index.php?controller=entrada&action=index');
        exit;
    }

    /**
     * Gera e registra automaticamente a nota demonstrativa da entrada.
     */
    public function emitirNotaFiscal(): void
    {
        $this->check();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=entrada&action=index');
            exit;
        }

        $entradaId = (int) ($_POST['entrada_id'] ?? 0);

        try {
            if ($entradaId <= 0) {
                throw new InvalidArgumentException(
                    'Identificador de entrada inválido.'
                );
            }

            $entradaModel = new Entrada();
            $notaFiscalModel = new NotaFiscal();

            $entrada = $entradaModel->buscarPorId($entradaId);

            if (!$entrada) {
                throw new InvalidArgumentException(
                    'Entrada não encontrada.'
                );
            }

            if ($notaFiscalModel->buscarPorEntrada($entradaId)) {
                throw new InvalidArgumentException(
                    'Esta entrada já possui uma nota fiscal.'
                );
            }

            $notaFiscalModel->cadastrarEntrada([
                'entrada_id' => $entradaId,
                'modelo' => 'DEMO',
                'serie' => '1',
                'numero' => (string) $entradaId,
                'chave_acesso' => str_pad(
    (string) $entradaId,
    44,
    '0',
    STR_PAD_LEFT
),
                'data_emissao' => date('Y-m-d'),
                'valor_total' => (float) $entrada['valor_total']
            ]);

            $_SESSION['sucesso_entrada'] =
                'Nota fiscal demonstrativa emitida com sucesso!';

        } catch (Throwable $e) {
            error_log('Erro ao emitir nota fiscal: ' . $e->getMessage());

            $_SESSION['erro_entrada'] =
                $e instanceof InvalidArgumentException
                    ? $e->getMessage()
                    : 'Não foi possível emitir a nota fiscal demonstrativa.';
        }

        header('Location: index.php?controller=entrada&action=index');
        exit;
    }

    /**
     * Exibe a nota fiscal demonstrativa já cadastrada.
     */
    public function verNotaFiscal(): void
    {
        $this->check();

        $entradaId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$entradaId || $entradaId <= 0) {
            $_SESSION['erro_entrada'] = 'Identificador de entrada inválido.';
            header('Location: index.php?controller=entrada&action=index');
            exit;
        }

        $entradaModel = new Entrada();
        $notaFiscalModel = new NotaFiscal();

        $entrada = $entradaModel->buscarPorId($entradaId);
        $notaFiscal = $notaFiscalModel->buscarPorEntrada($entradaId);

        if (!$entrada || !$notaFiscal) {
            $_SESSION['erro_entrada'] =
                'Entrada ou nota fiscal não encontrada.';
            header('Location: index.php?controller=entrada&action=index');
            exit;
        }

        $itens = $entradaModel->buscarItensPorEntrada($entradaId);

        $nomeUser = $_SESSION['nome'] ?? 'Usuário';

        require __DIR__ . '/../views/ver_nota_fiscal_entrada.php';
    }
}
