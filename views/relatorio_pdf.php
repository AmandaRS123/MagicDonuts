
<?php

$topProdutos = $topProdutos ?? [];
$fornecedores = $fornecedores ?? [];
$entradas = $entradas ?? [];
$baixoEstoque = $baixoEstoque ?? [];

$esc = static function ($valor): string {
    return htmlspecialchars(
        (string)($valor ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
};

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">

    <title>Relatório Gerencial - Magic Donuts</title>

    <style>
        @page {
            margin: 35px 38px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #403650;
            line-height: 1.5;
        }

        .cabecalho {
            padding-bottom: 17px;
            border-bottom: 3px solid #7652aa;
            margin-bottom: 22px;
        }

        .marca {
            color: #7652aa;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        h1 {
            margin: 7px 0 3px;
            color: #493174;
            font-size: 23px;
        }

        .subtitulo {
            margin: 0;
            color: #81748f;
            font-size: 10px;
        }

        .data-geracao {
            margin-top: 12px;
            color: #887c96;
            font-size: 9px;
        }

        .secao {
            margin-top: 23px;
            page-break-inside: avoid;
        }

        h2 {
            margin: 0 0 4px;
            padding-left: 9px;
            border-left: 4px solid #9a79c4;
            color: #50377c;
            font-size: 14px;
        }

        .descricao {
            margin: 0 0 10px;
            color: #887c96;
            font-size: 9px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 7px;
        }

        th {
            padding: 9px 8px;
            background-color: #eee5f8;
            color: #50377c;
            font-size: 9px;
            text-align: left;
            border: 1px solid #e0d5ed;
        }

        td {
            padding: 8px;
            border: 1px solid #ece6f1;
            color: #554b61;
            font-size: 9px;
        }

        tr {
            page-break-inside: avoid;
        }

        .valor {
            color: #65459b;
            font-weight: bold;
        }

        .quantidade {
            color: #6846a2;
            font-weight: bold;
        }

        .alerta h2 {
            color: #98602f;
            border-left-color: #dba36c;
        }

        .alerta th {
            background-color: #fff0dc;
            color: #98602f;
            border-color: #f0dfc9;
        }

        .vazio {
            padding: 14px;
            text-align: center;
            color: #8a8195;
        }

        .rodape {
            margin-top: 28px;
            padding-top: 9px;
            border-top: 1px solid #e7dfed;
            color: #93899e;
            font-size: 8px;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="cabecalho">
        <div class="marca">MAGIC DONUTS · GESTÃO</div>

        <h1>Relatório Gerencial</h1>

        <p class="subtitulo">
            Desempenho, fornecedores e movimentação de estoque.
        </p>

        <div class="data-geracao">
            Relatório gerado em:
            <?= date('d/m/Y às H:i') ?>
        </div>
    </div>


    <!-- PRODUTOS MAIS VENDIDOS -->
    <div class="secao">

        <h2>01. Produtos mais vendidos</h2>

        <p class="descricao">
            Produtos com maior quantidade de vendas registradas.
        </p>

        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade vendida</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($topProdutos)): ?>

                    <tr>
                        <td colspan="2" class="vazio">
                            Nenhuma venda registrada ainda.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($topProdutos as $p): ?>
                        <tr>
                            <td><?= $esc($p['nome'] ?? '') ?></td>

                            <td class="quantidade">
                                <?= (int)($p['total_vendido'] ?? 0) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                <?php endif; ?>
            </tbody>
        </table>

    </div>


    <!-- FORNECEDORES -->
    <div class="secao">

        <h2>02. Fornecedores</h2>

        <p class="descricao">
            Fornecedores com maior número de entradas registradas.
        </p>

        <table>
            <thead>
                <tr>
                    <th>Fornecedor</th>
                    <th>Entradas</th>
                    <th>Valor total</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($fornecedores)): ?>

                    <tr>
                        <td colspan="3" class="vazio">
                            Nenhum fornecedor encontrado.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($fornecedores as $f): ?>
                        <tr>
                            <td><?= $esc($f['nome'] ?? '') ?></td>

                            <td>
                                <?= (int)($f['total_entradas'] ?? 0) ?>
                            </td>

                            <td class="valor">
                                R$ <?= number_format(
                                    (float)($f['valor_total'] ?? 0),
                                    2,
                                    ',',
                                    '.'
                                ) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                <?php endif; ?>
            </tbody>
        </table>

    </div>


    <!-- ENTRADAS RECENTES -->
    <div class="secao">

        <h2>03. Entradas recentes</h2>

        <p class="descricao">
            Últimas mercadorias recebidas pela loja.
        </p>

        <table>
            <thead>
                <tr>
                    <th>Identificação</th>
                    <th>Fornecedor</th>
                    <th>Data</th>
                    <th>Valor</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($entradas)): ?>

                    <tr>
                        <td colspan="4" class="vazio">
                            Nenhuma entrada registrada ainda.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($entradas as $e): ?>
                        <tr>
                            <td>
                                #<?= (int)($e['id'] ?? 0) ?>
                            </td>

                            <td>
                                <?= $esc($e['fornecedor_nome'] ?? '') ?>
                            </td>

                            <td>
                                <?= !empty($e['data'])
                                    ? date('d/m/Y', strtotime($e['data']))
                                    : '—' ?>
                            </td>

                            <td class="valor">
                                R$ <?= number_format(
                                    (float)($e['valor_total'] ?? 0),
                                    2,
                                    ',',
                                    '.'
                                ) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                <?php endif; ?>
            </tbody>
        </table>

    </div>


    <!-- ESTOQUE BAIXO -->
    <div class="secao alerta">

        <h2>04. Estoque baixo</h2>

        <p class="descricao">
            Produtos com quantidade igual ou inferior ao estoque mínimo.
        </p>

        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>SKU</th>
                    <th>Quantidade atual</th>
                    <th>Estoque mínimo</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($baixoEstoque)): ?>

                    <tr>
                        <td colspan="4" class="vazio">
                            Nenhum item abaixo do estoque mínimo.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($baixoEstoque as $b): ?>
                        <tr>
                            <td>
                                <?= $esc($b['produto_nome'] ?? '') ?>
                            </td>

                            <td>
                                <?= $esc($b['sku'] ?? '') ?>
                            </td>

                            <td class="quantidade">
                                <?= (int)($b['quantidade'] ?? 0) ?>
                            </td>

                            <td>
                                <?= (int)($b['minimo'] ?? 0) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                <?php endif; ?>
            </tbody>
        </table>

    </div>


    <div class="rodape">
        Magic Donuts · Relatório Gerencial · Documento gerado pelo sistema.
    </div>

</body>
</html>
