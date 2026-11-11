
<?php

$tituloPagina = $tituloPagina ?? 'Magic Donuts';
$clienteEstaLogado = cliente_logado();
$nomeCliente = $_SESSION['cliente_nome'] ?? 'Minha conta';

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta
        name="description"
        content="Magic Donuts: donuts artesanais, deliciosos e feitos para adoçar seu dia."
    >

    <title><?= e($tituloPagina) ?></title>

    <link
        rel="stylesheet"
        href="/magicdonuts/public/assets/css/loja.css"
    >
</head>

<body class="site-body">

    <a class="pular-conteudo" href="#conteudo">
        Pular para o conteúdo
    </a>

    <header class="site-header">

        <div class="container site-header-container">

            <a
                href="<?= e(url_site('loja', 'index')) ?>"
                class="site-logo"
                aria-label="Magic Donuts - Página inicial"
            >
                <img
                    src="/magicdonuts/imagem/logo.jpg"
                    alt="Logo Magic Donuts"
                >

                <span>
                    <strong>Magic Donuts</strong>
                    <small>Um mundo de sabor!</small>
                </span>
            </a>

            <nav class="site-nav" aria-label="Navegação principal">

                <a href="<?= e(url_site('loja', 'index')) ?>">
                    Início
                </a>

                <a href="<?= e(url_site('loja', 'index')) ?>#cardapio">
                    Cardápio
                </a>

                <a href="<?= e(url_site('carrinho', 'index')) ?>">
                    Carrinho
                </a>

                <?php if ($clienteEstaLogado): ?>

                    <a href="<?= e(url_site('pedido', 'meus')) ?>">
                        Meus pedidos
                    </a>

                    <a href="<?= e(url_site('cliente', 'sair')) ?>">
                        Sair
                    </a>

                <?php else: ?>

                    <a href="<?= e(url_site('cliente', 'login')) ?>">
                        Entrar
                    </a>

                    <a href="<?= e(url_site('cliente', 'cadastro')) ?>">
                        Criar conta
                    </a>

                <?php endif; ?>

            </nav>

            <?php if ($clienteEstaLogado): ?>

                <a
                    href="<?= e(url_site('cliente', 'conta')) ?>"
                    class="site-boas-vindas"
                    title="Acessar minha conta"
                    aria-label="Olá, <?= e($nomeCliente) ?>! Acessar minha conta"
                >
                    <i class="fa-solid fa-circle-user" aria-hidden="true"></i>
                    Olá, <?= e($nomeCliente) ?>!
                </a>

            <?php endif; ?>

        </div>

    </header>

    <main id="conteudo" class="site-main">

        <?php if (!empty($_SESSION['site_erro'])): ?>

            <div class="container">
                <div class="aviso aviso-erro" role="alert">
                    <?= e($_SESSION['site_erro']) ?>
                </div>
            </div>

            <?php unset($_SESSION['site_erro']); ?>

        <?php endif; ?>

        <?php if (!empty($_SESSION['site_sucesso'])): ?>

            <div class="container">
                <div class="aviso aviso-sucesso" role="status">
                    <?= e($_SESSION['site_sucesso']) ?>
                </div>
            </div>

            <?php unset($_SESSION['site_sucesso']); ?>

        <?php endif; ?>
