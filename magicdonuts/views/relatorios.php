<?php

$nomeUser = $_SESSION['nome'] ?? 'Usuário';

?>

<!doctype html>
<html lang="pt-br">

<head>

    <meta charset="utf-8">

    <title>Relatórios - Magic Donuts</title>

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <link
        rel="stylesheet"
        href="/magicdonuts/public/assets/css/style.css"
    >
    
    <link rel="icon" type="image/jpeg" href="/magicdonuts/imagem/logo.jpg">

</head>

<body class="relatorios-body">

    <div class="relatorios-container">

        <!-- =========================================
        TOPO
        ========================================= -->

        <header class="relatorios-topbar">

            <div class="relatorios-brand">

                <div class="relatorios-logo">
                    🍩
                </div>

                <div>

                    <h1>Relatórios</h1>

                    <span>
                        Indicadores da loja
                    </span>

                </div>

            </div>


            <!-- USUÁRIO -->

            <div class="relatorios-user">

                <div class="relatorios-user-info">

                    <span class="relatorios-user-label">
                        Olá,
                    </span>

                    <strong>
                        <?= htmlspecialchars($nomeUser) ?>
                    </strong>

                </div>

                <a
                    href="/magicdonuts/index.php?controller=auth&action=logout"
                    class="relatorios-logout"
                >
                    Sair
                </a>

            </div>

        </header>


        <!-- =========================================
        GRID DOS RELATÓRIOS
        ========================================= -->

        <main class="relatorios-grid">


            <!-- =====================================
            PRODUTOS MAIS VENDIDOS
            ===================================== -->

            <section class="relatorio-card">

                <div class="relatorio-card-title">

                    <div class="relatorio-icon">
                        🍩
                    </div>

                    <div>

                        <h2>
                            Produtos mais vendidos
                        </h2>

                        <p>
                            Produtos com maior número de vendas
                        </p>

                    </div>

                </div>


                <div class="relatorio-table-wrapper">

                    <table class="relatorio-table">

                        <thead>

                            <tr>

                                <th>
                                    Produto
                                </th>

                                <th>
                                    Qtde. vendida
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php foreach ($topProdutos as $p): ?>

                                <tr>

                                    <td>

                                        <span class="produto-nome">
                                            <?= htmlspecialchars($p['nome']) ?>
                                        </span>

                                    </td>

                                    <td>

                                        <span class="quantidade-badge">
                                            <?= (int) $p['total_vendido'] ?>
                                        </span>

                                    </td>

                                </tr>

                            <?php endforeach; ?>


                            <?php if (empty($topProdutos)): ?>

                                <tr>

                                    <td
                                        colspan="2"
                                        class="relatorio-vazio"
                                    >
                                        Nenhuma venda registrada ainda. 🍩
                                    </td>

                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </section>


            <!-- =====================================
            FORNECEDORES
            ===================================== -->

            <section class="relatorio-card">

                <div class="relatorio-card-title">

                    <div class="relatorio-icon">
                        📦
                    </div>

                    <div>

                        <h2>
                            Fornecedores
                        </h2>

                        <p>
                            Fornecedores com mais entradas
                        </p>

                    </div>

                </div>


                <div class="relatorio-table-wrapper">

                    <table class="relatorio-table">

                        <thead>

                            <tr>

                                <th>
                                    Fornecedor
                                </th>

                                <th>
                                    Entradas
                                </th>

                                <th>
                                    Valor total
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php foreach ($fornecedores as $f): ?>

                                <tr>

                                    <td>

                                        <?= htmlspecialchars($f['nome']) ?>

                                    </td>

                                    <td>

                                        <span class="quantidade-badge">
                                            <?= (int) $f['total_entradas'] ?>
                                        </span>

                                    </td>

                                    <td>

                                        <strong class="valor-relatorio">

                                            R$
                                            <?= number_format(
                                                (float) $f['valor_total'],
                                                2,
                                                ',',
                                                '.'
                                            ) ?>

                                        </strong>

                                    </td>

                                </tr>

                            <?php endforeach; ?>


                            <?php if (empty($fornecedores)): ?>

                                <tr>

                                    <td
                                        colspan="3"
                                        class="relatorio-vazio"
                                    >
                                        Nenhum fornecedor encontrado. 📦
                                    </td>

                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </section>


            <!-- =====================================
            ENTRADAS RECENTES
            ===================================== -->

            <section class="relatorio-card">

                <div class="relatorio-card-title">

                    <div class="relatorio-icon">
                        🚚
                    </div>

                    <div>

                        <h2>
                            Entradas recentes
                        </h2>

                        <p>
                            Últimas mercadorias recebidas
                        </p>

                    </div>

                </div>


                <div class="relatorio-table-wrapper">

                    <table class="relatorio-table">

                        <thead>

                            <tr>

                                <th>
                                    ID
                                </th>

                                <th>
                                    Fornecedor
                                </th>

                                <th>
                                    Data
                                </th>

                                <th>
                                    Valor
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php foreach ($entradas as $e): ?>

                                <tr>

                                    <td>

                                        <span class="id-badge">
                                            #<?= (int) $e['id'] ?>
                                        </span>

                                    </td>

                                    <td>

                                        <?= htmlspecialchars(
                                            $e['fornecedor_nome']
                                        ) ?>

                                    </td>

                                    <td>

                                        <?= htmlspecialchars(
                                            $e['data']
                                        ) ?>

                                    </td>

                                    <td>

                                        <strong class="valor-relatorio">

                                            R$
                                            <?= number_format(
                                                (float) $e['valor_total'],
                                                2,
                                                ',',
                                                '.'
                                            ) ?>

                                        </strong>

                                    </td>

                                </tr>

                            <?php endforeach; ?>


                            <?php if (empty($entradas)): ?>

                                <tr>

                                    <td
                                        colspan="4"
                                        class="relatorio-vazio"
                                    >
                                        Nenhuma entrada registrada ainda. 📦
                                    </td>

                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </section>


            <!-- =====================================
            ESTOQUE BAIXO
            ===================================== -->

            <section class="relatorio-card relatorio-card-alerta">

                <div class="relatorio-card-title">

                    <div class="relatorio-icon relatorio-icon-alerta">
                        ⚠️
                    </div>

                    <div>

                        <h2>
                            Estoque baixo
                        </h2>

                        <p>
                            Produtos que precisam de atenção
                        </p>

                    </div>

                </div>


                <div class="relatorio-table-wrapper">

                    <table class="relatorio-table">

                        <thead>

                            <tr>

                                <th>
                                    Produto
                                </th>

                                <th>
                                    SKU
                                </th>

                                <th>
                                    Qtde.
                                </th>

                                <th>
                                    Mínimo
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php foreach ($baixoEstoque as $b): ?>

                                <tr>

                                    <td>

                                        <strong>
                                            <?= htmlspecialchars(
                                                $b['produto_nome']
                                            ) ?>
                                        </strong>

                                    </td>

                                    <td>

                                        <span class="sku-badge">
                                            <?= htmlspecialchars(
                                                $b['sku']
                                            ) ?>
                                        </span>

                                    </td>

                                    <td>

                                        <span class="estoque-baixo-badge">
                                            <?= (int) $b['quantidade'] ?>
                                        </span>

                                    </td>

                                    <td>

                                        <?= (int) $b['minimo'] ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>


                            <?php if (empty($baixoEstoque)): ?>

                                <tr>

                                    <td
                                        colspan="4"
                                        class="relatorio-vazio"
                                    >
                                        Nenhum item abaixo do estoque mínimo. 📦
                                    </td>

                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </section>

        </main>


        <!-- =========================================
        BOTÃO VOLTAR
        ========================================= -->

        <div class="relatorios-footer">

            <a
                href="/magicdonuts/index.php?controller=auth&action=dashboard"
                class="relatorios-voltar"
            >

                <span>
                    ←
                </span>

                Voltar ao Dashboard

            </a>

        </div>

    </div>

</body>

</html>