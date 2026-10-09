<?php
// ==========================================================
// VARIAÇÃO - MAGIC DONUTS
// ==========================================================

// Produto selecionado
$produtoNome = $produto['nome'] ?? 'Produto';
$produtoId   = isset($produto['id']) ? (int)$produto['id'] : 0;

// Verifica se está editando uma variação
$estaEditando = !empty($editar);
?>

<!doctype html>
<html lang="pt-br">

<head>

    <meta charset="utf-8">

    <title>
        Variações - Magic Donuts
    </title>

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <link
        rel="stylesheet"
        href="/magicdonuts/public/assets/css/style.css"
    >

    <link
        rel="icon"
        type="image/jpeg"
        href="/magicdonuts/imagem/logo.jpg"
    >

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

</head>


<body class="produtos-body">


<!-- ==========================================
     TOPO
========================================== -->

<header class="produtos-header">

    <div class="produtos-header-inner">


        <!-- LOGO -->

        <a
            href="/magicdonuts/index.php?controller=auth&action=dashboard"
            class="produtos-brand"
        >

            <img
                src="/magicdonuts/imagem/logo.jpg"
                alt="Magic Donuts"
                class="produtos-logo"
            >

            <strong>Magic Donuts</strong>

        </a>


        <!-- USUÁRIO -->

        <div class="produtos-user">

            <span>

                Olá,

                <strong>
                    <?= htmlspecialchars($_SESSION['nome'] ?? 'Usuário') ?>
                </strong>

            </span>


            <a
                href="/magicdonuts/index.php?controller=auth&action=dashboard"
            >
                Sair
            </a>

        </div>

    </div>

</header>



<!-- ==========================================
     CONTEÚDO
========================================== -->

<main class="produtos-main">


    <!-- ======================================
         BOTÃO VOLTAR
    ======================================= -->

    <div style="margin-bottom: 20px;">


    </div>



    <!-- ======================================
         TÍTULO
    ======================================= -->

    <section class="produto-card">

        <div class="section-title">

            <h1>

                Variações do Produto

                <span class="title-sparkles">✦</span>

            </h1>

        </div>


        <!-- PRODUTO SELECIONADO -->

        <div
            style="
                padding: 15px;
                margin-bottom: 20px;
                border-radius: 10px;
                background: #f6efc7;
            "
        >

            <strong>Produto:</strong>

            <?= htmlspecialchars($produtoNome) ?>

            <span style="margin-left: 10px;">

                <strong>ID:</strong>

                #<?= $produtoId ?>

            </span>

        </div>



        <!-- ======================================
             CADASTRO / EDIÇÃO
        ======================================= -->

        <div class="section-title">

            <h2>

                <?= $estaEditando
                    ? 'Editar Variação'
                    : 'Cadastrar Variação'
                ?>

                <span class="title-sparkles">✦</span>

            </h2>

        </div>



        <form
            method="post"
            action="index.php?controller=variacao&action=salvar"
            class="produto-form"
        >


            <!-- ID DA VARIAÇÃO -->

            <input
                type="hidden"
                name="id"
                value="<?= $estaEditando
                    ? (int)$editar['id']
                    : 0
                ?>"
            >


            <!-- ID DO PRODUTO -->

            <input
                type="hidden"
                name="produto_id"
                value="<?= $produtoId ?>"
            >



            <!-- NOME DA VARIAÇÃO -->

            <div class="produto-form-group">

                <label for="nome">

                    Nome da Variação

                </label>


                <div class="produto-input-wrapper">

                    <div class="produto-input-icon">

                        <i class="fa-solid fa-tag"></i>

                    </div>


                    <input
                        class="produto-input"
                        id="nome"
                        type="text"
                        name="nome"
                        required
                        placeholder="Ex.: Pequeno, Médio, Grande"
                        value="<?= $estaEditando
                            ? htmlspecialchars($editar['nome'])
                            : ''
                        ?>"
                    >

                </div>

            </div>



            <!-- SKU -->

            <div class="produto-form-group">

                <label for="sku">

                    SKU

                </label>


                <div class="produto-input-wrapper">

                    <div class="produto-input-icon">

                        <i class="fa-solid fa-barcode"></i>

                    </div>


                    <input
                        class="produto-input"
                        id="sku"
                        type="text"
                        name="sku"
                        required
                        placeholder="Ex.: DON-001"
                        value="<?= $estaEditando
                            ? htmlspecialchars($editar['sku'])
                            : ''
                        ?>"
                    >

                </div>


                <small class="produto-help">

                    Código único para identificar esta variação.

                </small>

            </div>



            <!-- PREÇO -->

            <div class="produto-form-group">

                <label for="preco">

                    Preço

                </label>


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
                        placeholder="0,00"
                        value="<?= $estaEditando
                            ? htmlspecialchars($editar['preco'])
                            : ''
                        ?>"
                    >

                </div>

            </div>



            <!-- BOTÕES -->

            <div class="produto-actions">

            <a
        href="index.php?controller=produto&action=index"
        class="produto-btn produto-btn-secondary"
    >
        ← Voltar para Produtos
    </a>
                <button
                    class="produto-btn produto-btn-primary"
                    type="submit"
                >

                    <i class="fa-regular fa-floppy-disk"></i>

                    Salvar

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



    <!-- ======================================
         LISTA DE VARIAÇÕES
    ======================================= -->

    <section class="produto-card lista-produtos-card">

        <div class="section-title">

            <h2>

                Variações cadastradas

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

                            <i class="fa-solid fa-tag"></i>

                            Nome

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


                                <!-- ID -->

                                <td>

                                    #<?= (int)$v['id'] ?>

                                </td>



                                <!-- NOME -->

                                <td>

                                    <strong>

                                        <?= htmlspecialchars($v['nome']) ?>

                                    </strong>

                                </td>



                                <!-- SKU -->

                                <td>

                                    <span class="categoria-badge">

                                        <?= htmlspecialchars($v['sku']) ?>

                                    </span>

                                </td>



                                <!-- PREÇO -->

                                <td>

                                    R$

                                    <?= number_format(
                                        (float)$v['preco'],
                                        2,
                                        ',',
                                        '.'
                                    ) ?>

                                </td>



                                <!-- AÇÕES -->

                                <td>

                                    <div class="produto-table-actions">


                                        <!-- EDITAR -->

                                        <a
                                            class="table-btn table-btn-edit"
                                            href="index.php?controller=variacao&action=index&produto_id=<?= $produtoId ?>&id=<?= (int)$v['id'] ?>"
                                            title="Editar"
                                        >

                                            <i class="fa-solid fa-pen"></i>

                                        </a>



                                        <!-- EXCLUIR -->

                                        <a
                                            class="table-btn table-btn-delete"
                                            href="index.php?controller=variacao&action=deletar&id=<?= (int)$v['id'] ?>&produto_id=<?= $produtoId ?>"
                                            onclick="return confirm('⚠️ Excluir esta variação?')"
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

                            <td
                                colspan="5"
                                style="text-align: center; padding: 30px;"
                            >

                                <i class="fa-solid fa-circle-info"></i>

                                Nenhuma variação cadastrada para este produto.

                            </td>

                        </tr>


                    <?php endif; ?>


                </tbody>

            </table>

        </div>

    </section>


</main>


</body>

</html>