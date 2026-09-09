<?php
$nome = $_SESSION['nome'] ?? 'Usuário';
$perfil = $_SESSION['perfil'] ?? 'vendedor';
?>

<!doctype html>
<html lang="pt-br">

<head>

    <meta charset="utf-8">

    <title>Magic Donuts - Dashboard</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="/magicdonuts/public/assets/css/style.css">

    <link rel="icon" type="image/jpeg" href="/magicdonuts/imagem/logo.jpg">


    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body class="dashboard-body">

<div class="dashboard-container">


    <!-- =================================
         TOPBAR
    ================================== -->

    <div class="topbar">

        <!-- LOGO -->
        <div class="dashboard-brand">

            <img
                src="/magicdonuts/imagem/logo.jpg"
                alt="Magic Donuts"
                class="dashboard-logo"
            >

            <h1>Magic Donuts</h1>

        </div>


        <!-- USUÁRIO -->
        <div class="dashboard-user">

            <span>
                Olá,
                <strong>
                    <?php echo htmlspecialchars($nome); ?>
                </strong>
            </span>

            <a href="/magicdonuts/index.php?controller=auth&action=logout">
                Sair
            </a>

        </div>

    </div>


    <!-- =================================
         CARD PRINCIPAL
    ================================== -->

    <div class="dashboard-main-card">


        <!-- TÍTULO -->

        <div class="dashboard-welcome">

            <h2>
                Bem-vindo(a),
                <?php echo htmlspecialchars($nome); ?>
                <span class="donut-emoji">🍩</span>
            </h2>

            <p>
                Escolha um módulo para continuar.
            </p>

        </div>


        <!-- =================================
             MÓDULOS
        ================================== -->

        <div class="dashboard-nav">


            <!-- PRODUTOS -->

            <a
                href="/magicdonuts/index.php?controller=produto&action=index"
                class="dashboard-nav-item"
            >

                <i class="fa-solid fa-circle-dot"></i>

                <span>Produtos</span>

            </a>


            <!-- ENTRADAS -->

            <a
                href="/magicdonuts/index.php?controller=entrada&action=index"
                class="dashboard-nav-item"
            >

                <i class="fa-regular fa-clipboard"></i>

                <span>Entradas</span>

            </a>


            <!-- VENDAS -->

            <a
                href="/magicdonuts/index.php?controller=venda&action=index"
                class="dashboard-nav-item"
            >

                <i class="fa-solid fa-cart-shopping"></i>

                <span>Vendas</span>

            </a>


            <!-- RELATÓRIOS -->

            <a
                href="/magicdonuts/index.php?controller=relatorio&action=index"
                class="dashboard-nav-item"
            >

                <i class="fa-solid fa-chart-column"></i>

                <span>Relatórios</span>

            </a>

        </div>


        <!-- =================================
             INDICADORES
        ================================== -->

        <div class="dashboard-kpis">


            <!-- VENDAS -->

            <div class="dashboard-kpi">

                <div class="kpi-icon">

                    <i class="fa-solid fa-arrow-trend-up"></i>

                </div>

                <div class="kpi-title">
                    Vendas (mês)
                </div>

                <div class="kpi-value">
                    0
                </div>

            </div>


            <!-- ENTRADAS -->

            <div class="dashboard-kpi">

                <div class="kpi-icon">

                    <i class="fa-solid fa-box-open"></i>

                </div>

                <div class="kpi-title">
                    Entradas
                </div>

                <div class="kpi-value">
                    0
                </div>

            </div>


            <!-- ESTOQUE -->

            <div class="dashboard-kpi">

                <div class="kpi-icon">

                    <i class="fa-solid fa-cube"></i>

                </div>

                <div class="kpi-title">
                    Estoque baixo
                </div>

                <div class="kpi-value">
                    0
                </div>

            </div>


            <!-- PRODUTOS -->

            <div class="dashboard-kpi">

                <div class="kpi-icon">

                    <i class="fa-solid fa-box"></i>

                </div>

                <div class="kpi-title">
                    Produtos
                </div>

                <div class="kpi-value">
                    8
                </div>

            </div>


        </div>

    </div>

</div>

</body>
</html>