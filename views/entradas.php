
<?php

$sucesso = $_SESSION['sucesso_entrada'] ?? null;
$erro = $_SESSION['erro_entrada'] ?? null;
$erroNota = $_SESSION['erro_nota_entrada'] ?? null;

unset(
    $_SESSION['sucesso_entrada'],
    $_SESSION['erro_entrada'],
    $_SESSION['erro_nota_entrada']
);

$fornecedores = $fornecedores ?? [];
$variacoes = $variacoes ?? [];
$entradas = $entradas ?? [];

$paginaAtual = 'entrada';
$tituloPagina = 'Entradas';
$descricaoPagina = 'Registre compras e acompanhe a reposição do estoque.';

require_once __DIR__ . '/layout/inicio.php';

?>

<div class="entradas-container">

    <!-- FORMULÁRIO DE NOVA ENTRADA -->
    <div class="entrada-card">

        <div class="entrada-card-title">
            <div class="entrada-icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 7L12 3L20 7L12 11L4 7Z"/>
                    <path d="M4 7V17L12 21L20 17V7"/>
                    <path d="M12 11V21"/>
                </svg>
            </div>

            <div>
                <h2>Registrar Nova Entrada</h2>
                <p>Informe o fornecedor e os produtos recebidos.</p>
            </div>
        </div>

        <!-- MENSAGENS -->
        <?php if ($sucesso): ?>
            <p class="entrada-mensagem entrada-sucesso">
                <?= htmlspecialchars($sucesso, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <?php if ($erro): ?>
            <p class="entrada-mensagem entrada-erro">
                <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <?php if ($erroNota): ?>
            <p class="entrada-mensagem entrada-erro">
                <?= htmlspecialchars($erroNota, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <!-- FORMULÁRIO -->
        <form method="post" action="index.php?controller=entrada&action=salvar">

            <!-- FORNECEDOR -->
            <div class="entrada-campo">
                <label for="fornecedor_id">Fornecedor</label>

                <div class="entrada-select">
                    <select
                        class="entrada-input"
                        name="fornecedor_id"
                        id="fornecedor_id"
                        required
                    >
                        <option value="">-- Selecione um fornecedor --</option>

                        <?php foreach ($fornecedores as $f): ?>
                            <option value="<?= (int) $f['id'] ?>">
                                <?= htmlspecialchars($f['nome'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>

                    </select>

                    <span class="entrada-seta">⌄</span>
                </div>

                <?php if (empty($fornecedores)): ?>
                    <p>Nenhum fornecedor cadastrado foi encontrado.</p>
                <?php endif; ?>
            </div>

            <!-- ITENS DA ENTRADA -->
            <div class="entrada-itens-titulo">
                <div class="entrada-mini-icon">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 13L13 20L4 11V4H11L20 13Z"/>
                        <circle cx="8" cy="8" r="1"/>
                    </svg>
                </div>

                <div>
                    <h3>Itens da entrada</h3>
                    <p>
                        Selecione a variação (SKU) recebida e informe
                        a quantidade e o custo unitário.
                    </p>
                </div>
            </div>

            <!-- TABELA DE ITENS -->
            <div class="entrada-table-wrapper">

                <table class="entrada-table" id="tabela-itens">

                    <thead>
                        <tr>
                            <th>Produto / Variação (SKU)</th>
                            <th class="entrada-col-quantidade">Quantidade</th>
                            <th class="entrada-col-custo">Custo Unitário (R$)</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php for ($linha = 0; $linha < 5; $linha++): ?>

                            <tr>

                                <td>
                                    <div class="entrada-select">

                                        <select
                                            class="entrada-input"
                                            name="variacao_id[]"
                                        >
                                            <option value="">
                                                -- Não usar esta linha --
                                            </option>

                                            <?php foreach ($variacoes as $v): ?>

                                                <option value="<?= (int) $v['id'] ?>">
                                                    <?= htmlspecialchars(
                                                        $v['produto_nome'] . ' — ' .
                                                        $v['opcao'] . ' - (' .
                                                        $v['sku'] . ')',
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </option>

                                            <?php endforeach; ?>

                                        </select>

                                        <span class="entrada-seta">⌄</span>

                                    </div>
                                </td>

                                <td>
                                    <input
                                        class="entrada-input entrada-numero"
                                        type="number"
                                        min="0"
                                        name="quantidade[]"
                                        value="0"
                                    >
                                </td>

                                <td>
                                    <div class="entrada-preco">

                                        <span>R$</span>

                                        <input
                                            class="entrada-input"
                                            type="text"
                                            name="custo_unitario[]"
                                            value="0,00"
                                            inputmode="decimal"
                                        >

                                    </div>
                                </td>

                            </tr>

                        <?php endfor; ?>

                    </tbody>

                </table>

                <?php if (empty($variacoes)): ?>
                    <p>
                        Nenhuma variação cadastrada foi encontrada.
                        Verifique se existem registros na tabela variacao.
                    </p>
                <?php endif; ?>

            </div>

            <!-- BOTÃO -->
            <div class="entrada-actions">

                <button class="entrada-btn-primary" type="submit">

                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 12L10 17L19 7"/>
                    </svg>

                    Confirmar Entrada

                </button>

            </div>

        </form>

    </div>

    <!-- HISTÓRICO -->
    <div class="entrada-card">

        <div class="entrada-card-title entrada-historico-title">

            <div class="entrada-icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="5" y="3" width="14" height="18" rx="2"/>
                    <path d="M9 3H15V6H9V3Z"/>
                    <path d="M8 10H16"/>
                    <path d="M8 14H16"/>
                    <path d="M8 18H13"/>
                </svg>
            </div>

            <div>
                <h2>Últimas Entradas</h2>
                <p>Confira as últimas entradas de mercadoria realizadas.</p>
            </div>

        </div>

        <div class="entrada-table-wrapper">

            <table class="entrada-table entrada-historico-table">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fornecedor</th>
                        <th>Produtos / Variações</th>
                        <th>Quantidade</th>
                        <th>Data</th>
                        <th>Status</th>
                        <th>Valor Total</th>
                        <th>Nota Fiscal</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($entradas)): ?>

                        <tr>
                            <td colspan="8">
                                Nenhuma entrada registrada.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($entradas as $e): ?>

                            <tr>

                                <!-- ID -->
                                <td>
                                    <span class="entrada-id">
                                        #<?= (int) $e['id'] ?>
                                    </span>
                                </td>

                                <!-- FORNECEDOR -->
                                <td>
                                    <?= htmlspecialchars(
                                        $e['fornecedor_nome'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </td>

                                <!-- PRODUTOS E VARIAÇÕES -->
                                <td>
                                    <span class="entrada-produtos">
                                        <?= htmlspecialchars(
                                            $e['produtos_variacoes'] ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>
                                </td>

                                <!-- QUANTIDADE TOTAL -->
                                <td>
                                    <strong>
                                        <?= (int) ($e['quantidade_total'] ?? 0) ?> un.
                                    </strong>
                                </td>

                                <!-- DATA -->
                                <td>
                                    <?= htmlspecialchars(
                                        $e['data'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </td>

                                <!-- STATUS -->
                                <td>
                                    <span class="entrada-status">
                                        <?= htmlspecialchars(
                                            $e['status'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>
                                </td>

                                <!-- VALOR TOTAL -->
                                <td>
                                    <span class="entrada-valor">
                                        R$ <?= number_format(
                                            (float) $e['valor_total'],
                                            2,
                                            ',',
                                            '.'
                                        ) ?>
                                    </span>
                                </td>

                                <!-- NOTA FISCAL -->
                                <td>
                                    <div class="entrada-acoes-nota">

                                        <?php if (!empty($e['nota_fiscal_id'])): ?>

                                            <!-- NOTA JÁ EMITIDA -->
                                            <a
                                                href="index.php?controller=entrada&action=verNotaFiscal&id=<?= (int) $e['id'] ?>"
                                                class="entrada-btn-nota"
                                            >
                                                Ver NF
                                            </a>

                                        <?php else: ?>

                                            <!-- EMITIR NOTA -->
                                            <form
                                                method="POST"
                                                action="index.php?controller=entrada&action=emitirNotaFiscal"
                                                onsubmit="return confirm('Deseja emitir a nota fiscal demonstrativa desta entrada?');"
                                            >
                                                <input
                                                    type="hidden"
                                                    name="entrada_id"
                                                    value="<?= (int) $e['id'] ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    class="entrada-btn-nota"
                                                >
                                                    Emitir NF
                                                </button>
                                            </form>

                                        <?php endif; ?>

                                    </div>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php require_once __DIR__ . '/layout/fim.php'; ?>
