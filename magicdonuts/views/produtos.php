<?php

// Helper para resolver imagem por ID sem usar banco
function imagemProdutoUrl(int $produtoId): string
{
    $baseFs = __DIR__ . "/../public/uploads/produtos/";
    $baseUrl = "/magicdonuts/public/uploads/produtos/";

    foreach (['jpg', 'png', 'webp'] as $ext) {

        if (file_exists($baseFs . $produtoId . '.' . $ext)) {
            return $baseUrl . $produtoId . '.' . $ext;
        }

    }

    return "/magicdonuts/imagem/produto_sem_foto.png";
}

?>

<!doctype html>

<html lang="pt-br">

<head>

    <meta charset="utf-8">

    <title>Produtos - Magic Donuts</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link
        rel="stylesheet"
        href="/magicdonuts/public/assets/css/style.css"
    >

    <link rel="icon" type="image/jpeg" href="/magicdonuts/imagem/logo.jpg">


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

            <a href="/magicdonuts/index.php?controller=auth&action=dashboard">Sair</a>

        </div>

    </div>

</header>



<!-- ==========================================
     CONTEÚDO
========================================== -->

<main class="produtos-main">


    <!-- ======================================
         CADASTRO DO PRODUTO
    ======================================= -->

    <section class="produto-card">

        <div class="section-title">

            <h1>

                <?= $editar
                    ? "Editar Produto #" . (int)$editar['id']
                    : "Cadastrar Produto"
                ?>

                <span class="title-sparkles">✦</span>

            </h1>

        </div>


        <form
            method="post"
            action="index.php?controller=produto&action=salvar"
            enctype="multipart/form-data"
            class="produto-form"
        >

            <input
                type="hidden"
                name="id"
                value="<?= $editar ? (int)$editar['id'] : 0 ?>"
            >


            <!-- CATEGORIA -->

            <div class="produto-form-group">

                <label for="categoria_id">
                    Categoria
                </label>

                <div class="produto-input-wrapper">

                    <div class="produto-input-icon">
                        <i class="fa-solid fa-table-cells"></i>
                    </div>

                    <select
                        class="produto-input produto-select"
                        id="categoria_id"
                        name="categoria_id"
                        required
                    >

                        <option value="">
                            Selecione...
                        </option>

                        <?php foreach ($categorias as $c): ?>

                            <option
                                value="<?= (int)$c['id'] ?>"
                                <?= $editar &&
                                    (int)$editar['categoria_id'] === (int)$c['id']
                                    ? 'selected'
                                    : ''
                                ?>
                            >

                                <?= htmlspecialchars($c['nome']) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

            </div>



            <!-- NOME -->

            <div class="produto-form-group">

                <label for="nome">
                    Nome
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
                        value="<?= $editar
                            ? htmlspecialchars($editar['nome'])
                            : ''
                        ?>"
                    >

                </div>

            </div>



            <!-- DESCRIÇÃO -->

            <div class="produto-form-group">

                <label for="descricao">
                    Descrição (opcional)
                </label>

                <div class="produto-input-wrapper produto-textarea-wrapper">

                    <div class="produto-input-icon">
                        <i class="fa-regular fa-file-lines"></i>
                    </div>

                    <textarea
                        class="produto-input produto-textarea"
                        id="descricao"
                        name="descricao"
                        rows="3"
                    ><?= $editar
                        ? htmlspecialchars($editar['descricao'] ?? '')
                        : ''
                    ?></textarea>

                </div>

            </div>



            <!-- IMAGEM -->

            <div class="produto-form-group">

                <label for="imagem">
                    Imagem do produto (opcional)
                </label>

                <div class="produto-input-wrapper arquivo-wrapper">

                    <div class="produto-input-icon">
                        <i class="fa-regular fa-image"></i>
                    </div>

                    <input
                        class="produto-input-file"
                        id="imagem"
                        type="file"
                        name="imagem"
                        accept="image/png, image/jpeg, image/webp"
                    >

                </div>

                <small class="produto-help">
                    Formatos: JPG, PNG, WEBP (até 4MB).
                    Salva como ID do produto.
                </small>

            </div>



            <!-- BOTÕES -->

            <div class="produto-actions">

                <button
                    class="produto-btn produto-btn-primary"
                    type="submit"
                >

                    <i class="fa-regular fa-floppy-disk"></i>

                    Salvar

                </button>


                <a
                    class="produto-btn produto-btn-secondary"
                    href="index.php?controller=produto&action=index"
                >

                    <i class="fa-solid fa-rotate"></i>

                    Limpar

                </a>

            </div>

        </form>

    </section>



    <!-- ======================================
         LISTA DE PRODUTOS
    ======================================= -->

    <section class="produto-card lista-produtos-card">

        <div class="section-title">

            <h2>

                Lista de Produtos

                <span class="title-sparkles">✦</span>

            </h2>

        </div>


        <div class="table-wrapper">

            <table class="produtos-table">

                <thead>

                    <tr>

                        <th>
                            <i class="fa-regular fa-image"></i>
                            Imagem
                        </th>

                        <th>
                            <i class="fa-solid fa-hashtag"></i>
                            ID
                        </th>

                        <th>
                            <i class="fa-solid fa-tag"></i>
                            Nome
                        </th>

                        <th>
                            <i class="fa-solid fa-table-cells"></i>
                            Categoria
                        </th>

                        <th>
                            <i class="fa-regular fa-circle-check"></i>
                            Status
                        </th>

                        <th>
                            <i class="fa-solid fa-gear"></i>
                            Ações
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php foreach ($produtos as $p): ?>

                        <tr>


                            <!-- IMAGEM -->

                            <td>

                                <img
                                    class="produto-thumb"
                                    src="<?= imagemProdutoUrl((int)$p['id']) ?>"
                                    alt="<?= htmlspecialchars($p['nome']) ?>"
                                >

                            </td>


                            <!-- ID -->

                            <td>

                                #<?= (int)$p['id'] ?>

                            </td>


                            <!-- NOME -->

                            <td>

                                <strong>
                                    <?= htmlspecialchars($p['nome']) ?>
                                </strong>

                            </td>


                            <!-- CATEGORIA -->

                            <td>

                                <span class="categoria-badge">

                                    <?= htmlspecialchars($p['categoria_nome']) ?>

                                </span>

                            </td>


                            <!-- STATUS -->

                            <td>

                                <?=
                                    ((int)$p['ativo'] === 1)

                                    ? '<span class="tag ok">Ativo</span>'

                                    : '<span class="tag off">Inativo</span>'
                                ?>

                            </td>


                            <!-- AÇÕES -->

                            <td>

                                <div class="produto-table-actions">


                                    <!-- EDITAR -->

                                    <a
                                        class="table-btn table-btn-edit"
                                        href="index.php?controller=produto&action=index&id=<?= (int)$p['id'] ?>"
                                        title="Editar"
                                    >

                                        <i class="fa-solid fa-pen"></i>

                                    </a>


                                    <!-- ATIVAR / INATIVAR -->

                                    <?php if ((int)$p['ativo'] === 1): ?>

                                        <a
                                            class="table-btn table-btn-danger"
                                            href="index.php?controller=produto&action=toggle&id=<?= (int)$p['id'] ?>&ativo=0"
                                            onclick="return confirm('Inativar este produto?')"
                                            title="Inativar"
                                        >

                                            <i class="fa-solid fa-ban"></i>

                                        </a>

                                    <?php else: ?>

                                        <a
                                            class="table-btn table-btn-success"
                                            href="index.php?controller=produto&action=toggle&id=<?= (int)$p['id'] ?>&ativo=1"
                                            title="Ativar"
                                        >

                                            <i class="fa-solid fa-check"></i>

                                        </a>

                                    <?php endif; ?>


                                    <!-- EXCLUIR -->

                                    <a
                                        class="table-btn table-btn-delete"
                                        href="index.php?controller=produto&action=deletar&id=<?= (int)$p['id'] ?>"
                                        onclick="return confirm('⚠️ DELETAR permanentemente? Não há volta!')"
                                        title="Excluir"
                                    >

                                        <i class="fa-solid fa-trash"></i>

                                    </a>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </section>

</main>

</body>
</html>