<?php

$nome = $_SESSION['nome'] ?? 'Usuário';
$perfil = $_SESSION['perfil'] ?? 'vendedor';
$isAdmin = ($perfil === 'admin');

// Valores padrão caso o controller não envie os indicadores.
$vendasMes = $vendasMes ?? 0;
$entradasRegistradas = $entradasRegistradas ?? 0;
$estoqueTotal = $estoqueTotal ?? 0;
$produtosCadastrados = $produtosCadastrados ?? 0;

?>

<!-- BOAS-VINDAS -->
<section class="painel-welcome">

    <div class="painel-welcome-text">

        <span class="painel-eyebrow">
            BEM-VINDO(A) À MAGIC DONUTS
        </span>

        <h1>Vamos deixar o dia mais doce?</h1>

        <p>
            Acesse os módulos pelo menu lateral para gerenciar
            os produtos, acompanhar as vendas e organizar a loja.
        </p>

    </div>

    <div class="painel-welcome-icon" aria-hidden="true">
        <i class="fa-solid fa-cookie-bite"></i>
    </div>

</section>


<!-- ATALHOS -->
<section class="painel-section">

    <div class="painel-section-heading">

        <div>
            <p class="painel-eyebrow">ACESSO RÁPIDO</p>
            <h2>Atalhos</h2>
        </div>

    </div>

    <div class="painel-shortcuts">

        <!-- PRODUTOS -->
        <a
            class="painel-shortcut"
            href="index.php?controller=produto&action=index"
        >
            <span class="painel-shortcut-icon">
                <i class="fa-solid fa-box-open"></i>
            </span>

            <span class="painel-shortcut-text">
                <h3>Produtos</h3>
                <p>Consultar e cadastrar produtos</p>
            </span>

            <i class="fa-solid fa-chevron-right painel-shortcut-arrow"></i>
        </a>


        <!-- ENTRADAS -->
        <a
            class="painel-shortcut"
            href="index.php?controller=entrada&action=index"
        >
            <span class="painel-shortcut-icon">
                <i class="fa-solid fa-truck-ramp-box"></i>
            </span>

            <span class="painel-shortcut-text">
                <h3>Entradas</h3>
                <p>Registrar recebimentos</p>
            </span>

            <i class="fa-solid fa-chevron-right painel-shortcut-arrow"></i>
        </a>


        <!-- VENDAS -->
        <a
            class="painel-shortcut"
            href="index.php?controller=venda&action=index"
        >
            <span class="painel-shortcut-icon">
                <i class="fa-solid fa-cash-register"></i>
            </span>

            <span class="painel-shortcut-text">
                <h3>Vendas</h3>
                <p>Acessar as vendas</p>
            </span>

            <i class="fa-solid fa-chevron-right painel-shortcut-arrow"></i>
        </a>


        <?php if ($isAdmin): ?>

            <!-- FORNECEDORES -->
            <a
                class="painel-shortcut"
                href="index.php?controller=fornecedor&action=index"
            >
                <span class="painel-shortcut-icon">
                    <i class="fa-solid fa-truck-field"></i>
                </span>

                <span class="painel-shortcut-text">
                    <h3>Fornecedores</h3>
                    <p>Consultar fornecedores</p>
                </span>

                <i class="fa-solid fa-chevron-right painel-shortcut-arrow"></i>
            </a>


            <!-- RELATÓRIOS -->
            <a
                class="painel-shortcut"
                href="index.php?controller=relatorio&action=index"
            >
                <span class="painel-shortcut-icon">
                    <i class="fa-solid fa-chart-column"></i>
                </span>

                <span class="painel-shortcut-text">
                    <h3>Relatórios</h3>
                    <p>Visualizar informações da loja</p>
                </span>

                <i class="fa-solid fa-chevron-right painel-shortcut-arrow"></i>
            </a>

        <?php endif; ?>

    </div>

</section>


<!-- INDICADORES DA LOJA -->
<section class="painel-section">

    <div class="painel-section-heading">

        <div>
            <p class="painel-eyebrow">RESUMO</p>
            <h2>Indicadores da loja</h2>
        </div>

    </div>

    <div class="painel-kpis">

        <!-- VENDAS -->
        <article class="painel-kpi">

            <div class="painel-kpi-top">
                <span class="painel-kpi-icon pink">
                    <i class="fa-solid fa-bag-shopping"></i>
                </span>
            </div>

            <p class="painel-kpi-title">Vendas no mês</p>

            <p class="painel-kpi-value">
                <?= number_format((int) $vendasMes, 0, ',', '.') ?>
            </p>

            <p class="painel-kpi-note">
                Vendas finalizadas neste mês
            </p>

        </article>


        <!-- ENTRADAS -->
        <article class="painel-kpi">

            <div class="painel-kpi-top">
                <span class="painel-kpi-icon purple">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </span>
            </div>

            <p class="painel-kpi-title">Entradas registradas</p>

            <p class="painel-kpi-value">
                <?= number_format((int) $entradasRegistradas, 0, ',', '.') ?>
            </p>

            <p class="painel-kpi-note">
                Entradas confirmadas
            </p>

        </article>


        <!-- ESTOQUE DISPONÍVEL -->
        <article class="painel-kpi">

            <div class="painel-kpi-top">
                <span class="painel-kpi-icon orange">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </span>
            </div>

            <p class="painel-kpi-title">Estoque disponível</p>

            <p class="painel-kpi-value">
                <?= number_format((int) $estoqueTotal, 0, ',', '.') ?>
            </p>

            <p class="painel-kpi-note">
                Unidades disponíveis atualmente
            </p>

        </article>


        <!-- PRODUTOS -->
        <article class="painel-kpi">

            <div class="painel-kpi-top">
                <span class="painel-kpi-icon blue">
                    <i class="fa-solid fa-cube"></i>
                </span>
            </div>

            <p class="painel-kpi-title">Produtos cadastrados</p>

            <p class="painel-kpi-value">
                <?= number_format((int) $produtosCadastrados, 0, ',', '.') ?>
            </p>

            <p class="painel-kpi-note">
                Produtos ativos no sistema
            </p>

        </article>

    </div>

</section>


<!-- RODAPÉ -->
<footer class="painel-footer">
    Magic Donuts · Painel de gestão
</footer>