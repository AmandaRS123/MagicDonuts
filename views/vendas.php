<?php

$nomeUser = $_SESSION['nome'] ?? 'Usuário';

?>

<!doctype html>
<html lang="pt-br">

<head>

    <meta charset="utf-8">

    <title>Vendas - Magic Donuts</title>

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

<body class="vendas-body">

<div class="vendas-container">


    <!-- =========================================
         TOPO
    ========================================== -->

    <header class="vendas-topbar">

        <div class="vendas-brand">

            <div class="vendas-logo">
              <img
                src="/magicdonuts/imagem/logo.jpg"
                alt="Logo Magic Donuts"> 
            </div>

            <div>

                <h1>Magic Donuts</h1>

                <span>Vendas</span>

                <small>
                    Registro de vendas do balcão
                </small>

            </div>

        </div>


        <div class="vendas-user">

            <div class="vendas-user-info">

                <span class="vendas-user-icon">
                    👤
                </span>

                <span>
                    Olá,
                    <strong>
                        <?= htmlspecialchars($nomeUser) ?>
                    </strong>
                </span>

            </div>

            <a
                class="vendas-sair"
                href="/magicdonuts/index.php?controller=auth&action=logout"
            >
                Sair
            </a>

        </div>

    </header>



    <!-- =========================================
         NOVA VENDA
    ========================================== -->

    <section class="venda-card">


        <!-- CABEÇALHO -->

        <div class="venda-card-header">

            <div class="venda-header-icon">
                🛍️
            </div>

            <div>

                <h2>Nova venda</h2>

                <p>
                    Selecione o cliente e adicione os itens da venda
                </p>

            </div>

        </div>



        <form
            method="post"
            action="index.php?controller=venda&action=salvar"
        >


            <!-- =====================================
                 CLIENTE
            ====================================== -->

            <div class="cliente-box">

                <div class="cliente-icon">
                    👤
                </div>

                <div class="cliente-field">

                    <label for="cliente_id">
                        Cliente
                    </label>

                    <select
                        id="cliente_id"
                        name="cliente_id"
                        class="venda-select"
                        required
                    >

                        <option value="">
                            Selecione um cliente...
                        </option>

                        <?php foreach ($clientes as $c): ?>

                            <option
                                value="<?= (int)$c['id'] ?>"
                            >
                                <?= htmlspecialchars($c['nome']) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

            </div>



            <!-- =====================================
                 TÍTULO ITENS
            ====================================== -->

            <div class="itens-titulo">

                <div>

                    <h3>Itens da venda</h3>

                    <p>
                        Escolha os produtos e informe a quantidade
                    </p>

                </div>

                <span class="itens-contador">
                    5 itens disponíveis
                </span>

            </div>



            <!-- =====================================
                 PRODUTOS
            ====================================== -->

            <div class="venda-itens-grid">

                <?php for ($linha = 0; $linha < 5; $linha++): ?>

                    <div class="venda-item-card">


                        <!-- CABEÇALHO DO PRODUTO -->

                        <div class="venda-item-top">

                            <div class="venda-produto-icon">
                                🍩
                            </div>

                            <div class="venda-produto-info">

                                <strong>
                                    Item <?= $linha + 1 ?>
                                </strong>

                                <span>
                                    Selecione uma variação
                                </span>

                            </div>

                            <div class="venda-item-number">
                                <?= $linha + 1 ?>
                            </div>

                        </div>



                        <!-- VARIAÇÃO -->

                        <div class="venda-item-select">

                            <label>
                                Variação / Produto
                            </label>

                            <select
                                class="venda-select variacao-select"
                                name="variacao_id[]"
                                data-linha="<?= $linha ?>"
                            >

                                <option value="">
                                    — não usar esta linha —
                                </option>

                                <?php foreach ($variacoes as $v): ?>

                                    <option
                                        value="<?= (int)$v['id'] ?>"
                                        data-preco="<?= (float)$v['preco'] ?>"
                                    >

                                        <?= htmlspecialchars($v['produto_nome']) ?>

                                        —

                                        <?= htmlspecialchars($v['sku']) ?>

                                        —

                                        <?= htmlspecialchars($v['tamanho']) ?>

                                        /

                                        <?= htmlspecialchars($v['cor']) ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>



                        <!-- QUANTIDADE / PREÇO -->

                        <div class="venda-item-fields">


                            <div class="venda-field">

                                <label>
                                    Quantidade
                                </label>

                                <input
                                    class="venda-input quantidade-input"
                                    type="number"
                                    min="0"
                                    name="quantidade[]"
                                    value="0"
                                >

                            </div>


                            <div class="venda-field">

                                <label>
                                    Preço unitário
                                </label>

                                <div class="preco-wrapper">

                                    <span>R$</span>

                                    <input
                                        class="venda-input preco-input"
                                        type="text"
                                        name="preco_unitario[]"
                                        value="0,00"
                                    >

                                </div>

                            </div>


                        </div>



                        <!-- SUBTOTAL -->

                        <div class="venda-subtotal">

                            <span>
                                Subtotal
                            </span>

                            <strong>
                                R$ <span class="subtotal-valor">0,00</span>
                            </strong>

                        </div>

                    </div>

                <?php endfor; ?>

            </div>



            <!-- =====================================
                 DICA
            ====================================== -->

            <div class="venda-dica">

                <span class="dica-icon">
                    💡
                </span>

                <p>
                    O preço é sugerido a partir do cadastro da
                    variação, mas pode ser ajustado manualmente
                    para promoções ou alterações pontuais.
                </p>

            </div>



            <!-- =====================================
                 RESUMO
            ====================================== -->

            <div class="venda-resumo">


                <div class="resumo-info">

                    <div class="resumo-icon">
                        🧾
                    </div>

                    <div>

                        <span>
                            Itens adicionados
                        </span>

                        <strong id="quantidade-itens">
                            0 produtos
                        </strong>

                    </div>

                </div>


                <div class="resumo-separador"></div>


                <div class="resumo-total">

                    <div class="resumo-total-icon">
                        💰
                    </div>

                    <div>

                        <span>
                            Total da venda
                        </span>

                        <strong>
                            R$
                            <span id="valor-total">
                                0,00
                            </span>
                        </strong>

                    </div>

                </div>


                <button
                    class="finalizar-venda-btn"
                    type="submit"
                >

                    <span>
                        🛒
                    </span>

                    Finalizar venda

                    <span>
                        →
                    </span>

                </button>


            </div>


        </form>

    </section>



    <!-- =========================================
         VENDAS RECENTES
    ========================================== -->

    <section class="vendas-recentes-card">


        <div class="vendas-recentes-header">

            <div class="vendas-recentes-icon">
                🕐
            </div>

            <div>

                <h2>
                    Vendas recentes
                </h2>

                <p>
                    Últimas vendas realizadas no balcão
                </p>

            </div>

        </div>



        <div class="vendas-tabela-wrapper">

            <table class="vendas-tabela">

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Cliente</th>

                        <th>Data</th>

                        <th>Valor total</th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach ($vendas as $v): ?>

                    <tr>

                        <td>

                            <span class="venda-id">
                                #<?= (int)$v['id'] ?>
                            </span>

                        </td>


                        <td>

                            <span class="cliente-nome-tabela">

                                <span class="mini-avatar">
                                    👤
                                </span>

                                <?= htmlspecialchars(
                                    $v['cliente_nome']
                                ) ?>

                            </span>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $v['data']
                            ) ?>

                        </td>


                        <td>

                            <strong class="valor-tabela">

                                R$

                                <?= number_format(
                                    (float)$v['valor_total'],
                                    2,
                                    ',',
                                    '.'
                                ) ?>

                            </strong>

                        </td>

                    </tr>

                <?php endforeach; ?>


                <?php if (empty($vendas)): ?>

                    <tr>

                        <td
                            colspan="4"
                            class="vendas-vazio"
                        >

                            🧾 Nenhuma venda registrada ainda.

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>



    <!-- =========================================
         VOLTAR
    ========================================== -->

    <div class="vendas-voltar">

        <a
            href="/magicdonuts/index.php?controller=auth&action=dashboard"
            class="voltar-painel-btn"
        >

            <span>
                ←
            </span>

            Voltar ao painel

        </a>

    </div>


</div>



<!-- =========================================
     JAVASCRIPT
========================================== -->

<script>

document.addEventListener('DOMContentLoaded', function () {


    const selects = document.querySelectorAll(
        '.variacao-select'
    );


    const quantidades = document.querySelectorAll(
        '.quantidade-input'
    );


    const precos = document.querySelectorAll(
        '.preco-input'
    );


    /*
     * Atualiza o subtotal de cada produto
     */

    function atualizarLinha(linha) {

        const quantidadeInput =
            linha.querySelector('.quantidade-input');

        const precoInput =
            linha.querySelector('.preco-input');

        const subtotal =
            linha.querySelector('.subtotal-valor');


        let quantidade =
            parseFloat(
                quantidadeInput.value
            ) || 0;


        let precoTexto =
            precoInput.value
                .replace('R$', '')
                .replace(/\./g, '')
                .replace(',', '.')
                .trim();


        let preco =
            parseFloat(precoTexto) || 0;


        let valor =
            quantidade * preco;


        subtotal.textContent =
            valor.toFixed(2)
                .replace('.', ',');

    }



    /*
     * Atualiza o total geral
     */

    function atualizarTotal() {

        let total = 0;

        let itens = 0;


        document
            .querySelectorAll('.venda-item-card')
            .forEach(function (card) {


                const quantidadeInput =
                    card.querySelector(
                        '.quantidade-input'
                    );


                const precoInput =
                    card.querySelector(
                        '.preco-input'
                    );


                let quantidade =
                    parseFloat(
                        quantidadeInput.value
                    ) || 0;


                let precoTexto =
                    precoInput.value
                        .replace('R$', '')
                        .replace(/\./g, '')
                        .replace(',', '.')
                        .trim();


                let preco =
                    parseFloat(precoTexto) || 0;


                if (quantidade > 0) {

                    itens += quantidade;

                }


                total += quantidade * preco;


            });


        document.getElementById(
            'valor-total'
        ).textContent =
            total.toFixed(2)
                .replace('.', ',');


        document.getElementById(
            'quantidade-itens'
        ).textContent =
            itens +
            (itens === 1
                ? ' produto'
                : ' produtos');

    }



    /*
     * Quando escolher um produto,
     * preenche o preço automaticamente.
     */

    selects.forEach(function (select) {

        select.addEventListener(
            'change',
            function () {


                const opcao =
                    this.options[
                        this.selectedIndex
                    ];


                const preco =
                    opcao.getAttribute(
                        'data-preco'
                    );


                const card =
                    this.closest(
                        '.venda-item-card'
                    );


                const inputPreco =
                    card.querySelector(
                        '.preco-input'
                    );


                const nomeProduto =
                    card.querySelector(
                        '.venda-produto-info strong'
                    );


                const textoProduto =
                    opcao.textContent.trim();


                if (preco) {

                    inputPreco.value =
                        parseFloat(preco)
                            .toFixed(2)
                            .replace('.', ',');


                    nomeProduto.textContent =
                        textoProduto
                            .split('—')[0]
                            .trim();

                } else {

                    inputPreco.value =
                        '0,00';


                    nomeProduto.textContent =
                        'Item ' +
                        (
                            parseInt(
                                this.dataset.linha
                            ) + 1
                        );

                }


                atualizarLinha(card);

                atualizarTotal();

            }
        );

    });



    /*
     * Atualiza ao alterar quantidade
     */

    quantidades.forEach(function (input) {

        input.addEventListener(
            'input',
            function () {

                const card =
                    this.closest(
                        '.venda-item-card'
                    );


                atualizarLinha(card);

                atualizarTotal();

            }
        );

    });



    /*
     * Atualiza ao alterar preço
     */

    precos.forEach(function (input) {

        input.addEventListener(
            'input',
            function () {

                const card =
                    this.closest(
                        '.venda-item-card'
                    );


                atualizarLinha(card);

                atualizarTotal();

            }
        );

    });


    /*
     * Estado inicial
     */

    document
        .querySelectorAll('.venda-item-card')
        .forEach(function (card) {

            atualizarLinha(card);

        });


    atualizarTotal();


});

</script>


</body>

</html>