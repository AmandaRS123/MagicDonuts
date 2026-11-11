<?php

$paginaAtual = $paginaAtual ?? ($_GET['controller'] ?? 'dashboard');
$tituloPagina = $tituloPagina ?? 'Magic Donuts';
$descricaoPagina = $descricaoPagina ?? 'Gerencie sua loja com facilidade.';

$nomeUsuario = $_SESSION['nome'] ?? 'Usuário';
$perfilUsuario = $_SESSION['perfil'] ?? '';
$isAdmin = ($perfilUsuario === 'admin');

$menu = [
    [
        'controller' => 'auth',
        'action' => 'dashboard',
        'titulo' => 'Dashboard',
        'icone' => 'fa-solid fa-house',
        'admin' => false
    ],
    [
        'controller' => 'produto',
        'action' => 'index',
        'titulo' => 'Produtos',
        'icone' => 'fa-solid fa-box',
        'admin' => false
    ],
    [
        'controller' => 'entrada',
        'action' => 'index',
        'titulo' => 'Entradas',
        'icone' => 'fa-solid fa-truck-ramp-box',
        'admin' => false
    ],
    [
        'controller' => 'fornecedor',
        'action' => 'index',
        'titulo' => 'Fornecedores',
        'icone' => 'fa-solid fa-truck',
        'admin' => true
    ],
    [
        'controller' => 'venda',
        'action' => 'index',
        'titulo' => 'Vendas',
        'icone' => 'fa-solid fa-cash-register',
        'admin' => false
    ],
    [
        'controller' => 'relatorio',
        'action' => 'index',
        'titulo' => 'Relatórios',
        'icone' => 'fa-solid fa-chart-column',
        'admin' => true
    ],
    [
        'controller' => 'loja',
        'action' => 'index',
        'titulo' => 'Visitar Site',
        'icone' => 'fa-solid fa-store',
        'admin' => false
    ],
];

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= htmlspecialchars($tituloPagina) ?> - Magic Donuts
    </title>

    <link
        rel="stylesheet"
        href="public/assets/css/style.css"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >
</head>

<body class="dashboard-body painel-dashboard-body">

<div class="painel-layout">

    <!-- MENU LATERAL -->
    <aside class="painel-sidebar">

        <div class="painel-brand">

            <a href="index.php?controller=auth&action=dashboard">
                <img
                    src="imagem/logo.jpg"
                    alt="Magic Donuts"
                >
            </a>

            <h1>Magic Donuts</h1>

        </div>

        <div class="painel-menu-label">
            Menu principal
        </div>

        <nav class="painel-nav">

            <?php foreach ($menu as $item): ?>

                <?php if (!$item['admin'] || $isAdmin): ?>

                    <?php
                    $ativo = ($paginaAtual === $item['controller']);

                    $url = 'index.php?controller='
                        . urlencode($item['controller'])
                        . '&action='
                        . urlencode($item['action']);
                    ?>

                    <a
    href="<?= htmlspecialchars($url) ?>"
    class="<?= $ativo ? 'active' : '' ?>"
    <?= $item['controller'] === 'loja' ? 'target="_blank" rel="noopener noreferrer"' : '' ?>
>

                        <i class="<?= htmlspecialchars($item['icone']) ?>"></i>

                        <span>
                            <?= htmlspecialchars($item['titulo']) ?>
                        </span>

                    </a>

                <?php endif; ?>

            <?php endforeach; ?>

        </nav>

        <!-- SAIR -->
        <div class="painel-sidebar-bottom">

            <a
                href="index.php?controller=auth&action=logout"
                class="painel-logout-link"
            >

                <i class="fa-solid fa-right-from-bracket"></i>

                <span>Sair</span>

            </a>

        </div>

    </aside>

    <!-- ÁREA PRINCIPAL -->
    <div class="painel-area-principal">

       
<!-- CABEÇALHO -->
<header class="painel-topbar">

    <!-- TÍTULO E DESCRIÇÃO -->
    <div class="painel-topbar-info">

        <h2>
            <?= htmlspecialchars($tituloPagina) ?>
        </h2>

        <p>
            <?= htmlspecialchars($descricaoPagina) ?>
        </p>

    </div>

    <!-- USUÁRIO LOGADO -->
    <div class="painel-topbar-user">

        <!-- ÍCONE -->
        <div class="painel-user-avatar">
            <i class="fa-solid fa-circle-user"></i>
        </div>

        <!-- NOME E PERFIL -->
        <div class="painel-user-info">

            <strong>
                <?= htmlspecialchars($nomeUsuario) ?>
            </strong>

            <span>
                <?= $isAdmin ? 'Administrador' : 'Vendedor' ?>
            </span>

        </div>

    </div>

</header>


        <!-- O CONTEÚDO DE CADA PÁGINA COMEÇA AQUI -->
        <main class="painel-content">