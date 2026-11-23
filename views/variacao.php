
<?php

// Produto selecionado
$produtoNome = $produto['nome'] ?? 'Produto';
$produtoId = isset($produto['id']) ? (int) $produto['id'] : 0;

// Verifica se está editando uma variação
$estaEditando = !empty($editar);

// Configura o layout compartilhado
$paginaAtual = 'variacao';
$tituloPagina = 'Variações';
$descricaoPagina = 'Gerencie as opções, códigos e preços dos produtos.';

require_once __DIR__ . '/layout/inicio.php';

$esc = static function ($valor): string {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
};

?>

<div class="produtos-main">

    <!-- Título e informações do produto -->
    <section class="produto-card">

        <div class="section-title">
            <h1>
                Variações do Produto
                <span class="title-sparkles">✦</span>
            </h1>
        </div>

        <div style="padding: 15px; margin-bottom: 20px; border-radius: 10px; background: #f6efc7;">
            <strong>Produto:</strong>
            <?= $esc($produtoNome) ?>

            <span style="margin-left: 10px;">
                <strong>ID:</strong>
                #<?= $produtoId ?>
            </span>
        </div>

        <!-- Cadastro ou edição -->
        <div class="section-title">
            <h2>
                <?= $estaEditando ? 'Editar Variação' : 'Cadastrar Variação' ?>
                <span class="title-sparkles">✦</span>
            </h2>
        </div>

        <form
            method="post"
            action="index.php?controller=variacao&action=salvar"
            class="produto-form"
        >

            <input
                type="hidden"
                name="id"
                value="<?= $estaEditando ? (int) $editar['id'] : 0 ?>"
            >

            <input
                type="hidden"
                name="produto_id"
                value="<?= $produtoId ?>"
            >

            <!-- Opção da variação -->
            <div class="produto-form-group">

                <label for="opcao">Opção da variação</label>

                <div class="produto-input-wrapper">

                    <div class="produto-input-icon">
                        <i class="fa-solid fa-tags"></i>
                    </div>

                    <input
                        class="produto-input"
                        id="opcao"
                        type="text"
                        name="opcao"
                        maxlength="100"
                        required
                        placeholder="Ex.: Pequeno, Médio, Grande, Uva, Zero..."
                        value="<?= $estaEditando ? $esc($editar['opcao']) : '' ?>"
                    >

                </div>

                <small class="produto-help">
                    Informe o tamanho, sabor ou característica desta opção.
                </small>

            </div>

            <!-- SKU -->
            <div class="produto-form-group">

                <label for="sku">SKU</label>

                <div class="produto-input-wrapper">

                    <div class="produto-input-icon">
                        <i class="fa-solid fa-barcode"></i>
                    </div>

                    <input
                        class="produto-input"
                        id="sku"
                        type="text"
                        name="sku"
                        maxlength="100"
                        required
                        placeholder="Ex.: DON-001 ou BEB-UVA"
                        value="<?= $estaEditando ? $esc($editar['sku']) : '' ?>"
                    >

                </div>

                <small class="produto-help">
                    Código único para identificar esta opção do produto.
                </small>

            </div>

            <!-- Preço -->
            <div class="produto-form-group">

                <label for="preco">Preço</label>

                <div class="produto-input-wrapper">

                    <div class="produto-input-icon">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>

                    <input
                        class="produto-input"
                        id="preco"
                        type="number"
                        name="preco"
                        step="0.01"
                        min="0"
                        required
                        placeholder="0.00"
                        value="<?= $estaEditando ? $esc($editar['preco']) : '' ?>"
                    >

                </div>

            </div>

            <!-- Botões -->
            <div class="produto-actions">

                <a
                    href="index.php?controller=produto&action=index"
                    class="produto-btn produto-btn-secondary"
                >
                    <i class="fa-solid fa-arrow-left"></i>
                    Voltar para Produtos
                </a>

                <button
                    class="produto-btn produto-btn-primary"
                    type="submit"
                >
                    <i class="fa-regular fa-floppy-disk"></i>
                    <?= $estaEditando ? 'Salvar alterações' : 'Salvar' ?>
                </button>

                <a
                    class="produto-btn produto-btn-secondary"
                    href="index.php?controller=variacao&action=index&produto_id=<?= $produtoId ?>"
                >
                    <i class="fa-solid fa-rotate"></i>
                    Limpar
                </a>

            </div>

        </form>

    </section>

    <!-- Lista de variações -->
    <section class="produto-card lista-produtos-card">

        <div class="section-title">
            <h2>
                Opções cadastradas
                <span class="title-sparkles">✦</span>
            </h2>
        </div>

        <div class="table-wrapper">

            <table class="produtos-table">

                <thead>
                    <tr>
                        <th>
                            <i class="fa-solid fa-hashtag"></i>
                            ID
                        </th>

                        <th>
                            <i class="fa-solid fa-tags"></i>
                            Opção
                        </th>

                        <th>
                            <i class="fa-solid fa-barcode"></i>
                            SKU
                        </th>

                        <th>
                            <i class="fa-solid fa-money-bill-wave"></i>
                            Preço
                        </th>

                        <th>
                            <i class="fa-solid fa-gear"></i>
                            Ações
                        </th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($variacoes)): ?>

                        <?php foreach ($variacoes as $v): ?>

                            <tr>

                                <td>
                                    #<?= (int) $v['id'] ?>
                                </td>

                                <td>
                                    <strong>
                                        <?= $esc($v['opcao']) ?>
                                    </strong>
                                </td>

                                <td>
                                    <span class="categoria-badge">
                                        <?= $esc($v['sku']) ?>
                                    </span>
                                </td>

                                <td>
                                    R$ <?= number_format(
                                        (float) $v['preco'],
                                        2,
                                        ',',
                                        '.'
                                    ) ?>
                                </td>

                                <td>

                                    <div class="produto-table-actions">

                                        <!-- EDITAR -->
                                        <a
                                            class="table-btn table-btn-edit"
                                            href="index.php?controller=variacao&action=index&produto_id=<?= $produtoId ?>&id=<?= (int) $v['id'] ?>"
                                            title="Editar"
                                        >
                                            <i class="fa-solid fa-pen"></i>
                                        </a>

                                        <!-- EXCLUIR -->
                                        <a
                                            class="table-btn table-btn-delete"
                                            href="index.php?controller=variacao&action=deletar&id=<?= (int) $v['id'] ?>&produto_id=<?= $produtoId ?>"
                                            onclick="return confirm('Excluir esta opção do produto?')"
                                            title="Excluir"
                                        >
                                            <i class="fa-solid fa-trash"></i>
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px;">
                                <i class="fa-solid fa-circle-info"></i>
                                Nenhuma opção cadastrada para este produto.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

</div>

<?php require_once __DIR__ . '/layout/fim.php'; ?>
