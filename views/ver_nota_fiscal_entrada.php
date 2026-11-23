
<?php

$esc = static function ($valor): string {
    return htmlspecialchars(
        (string) ($valor ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
};

$entradaId = (int) $entrada['id'];

$fornecedorNome = $entrada['fornecedor_nome'] ?? 'Não informado';

$dataEntrada = !empty($entrada['data'])
    ? date('d/m/Y', strtotime($entrada['data']))
    : 'Não informada';

$dataEmissao = !empty($notaFiscal['data_emissao'])
    ? date('d/m/Y', strtotime($notaFiscal['data_emissao']))
    : 'Não informada';

$valorTotal = (float) $notaFiscal['valor_total'];

?>

<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Nota de Entrada #<?= $entradaId ?> | Magic Donuts</title>

    <link
        rel="stylesheet"
        href="/magicdonuts/public/assets/css/nota.css"
    >
</head>

<body>

<div class="nf-container">

    <!-- CABEÇALHO -->
    <header class="nf-cabecalho">

        <div class="nf-emitente">
            <h1 class="nf-emitente-nome">MAGIC DONUTS</h1>

            <p class="nf-emitente-documento">
                Documento fiscal demonstrativo
            </p>

            <p class="nf-emitente-endereco">
                Sistema de gerenciamento da loja
            </p>
        </div>

        <div class="nf-documento-info">

            <h2 class="nf-documento-titulo">
                NOTA DE ENTRADA
            </h2>

            <div class="nf-documento-dados">

                <p class="nf-dado-linha">
                    <strong>ENTRADA:</strong>
                    #<?= $entradaId ?>
                </p>

                <p class="nf-dado-linha">
                    <strong>MODELO:</strong>
                    <?= $esc($notaFiscal['modelo']) ?>
                </p>

                <p class="nf-dado-linha">
                    <strong>NÚMERO:</strong>
                    <?= $esc($notaFiscal['numero']) ?>
                </p>

                <p class="nf-dado-linha">
                    <strong>SÉRIE:</strong>
                    <?= $esc($notaFiscal['serie']) ?>
                </p>

            </div>

        </div>

    </header>

    <!-- DADOS DA ENTRADA -->
    <section class="nf-emissao">

        <div class="nf-emissao-bloco">
            <span class="nf-emissao-label">FORNECEDOR</span>
            <span class="nf-emissao-valor">
                <?= $esc($fornecedorNome) ?>
            </span>
        </div>

        <div class="nf-emissao-bloco">
            <span class="nf-emissao-label">DATA DA ENTRADA</span>
            <span class="nf-emissao-valor">
                <?= $esc($dataEntrada) ?>
            </span>
        </div>

        <div class="nf-emissao-bloco">
            <span class="nf-emissao-label">DATA DE EMISSÃO</span>
            <span class="nf-emissao-valor">
                <?= $esc($dataEmissao) ?>
            </span>
        </div>

        <div class="nf-emissao-bloco">
            <span class="nf-emissao-label">VALOR TOTAL</span>
            <span class="nf-emissao-valor">
                R$ <?= number_format($valorTotal, 2, ',', '.') ?>
            </span>
        </div>

    </section>

    <!-- PRODUTOS RECEBIDOS -->
    <main class="nf-formulario">

        <h3>Produtos recebidos</h3>

        <div class="nf-tabela-wrapper">

            <table class="nf-tabela">

                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Variação</th>
                        <th>SKU</th>
                        <th>Quantidade</th>
                        <th>Custo unitário</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($itens)): ?>

                        <tr>
                            <td colspan="6">
                                Nenhum produto encontrado nesta entrada.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($itens as $item): ?>

                            <tr>

                                <td>
                                    <?= $esc($item['produto_nome']) ?>
                                </td>

                                <td>
                                    <?= $esc($item['variacao']) ?>
                                </td>

                                <td>
                                    <?= $esc($item['sku']) ?>
                                </td>

                                <td>
                                    <?= (int) $item['quantidade'] ?>
                                </td>

                                <td>
                                    R$ <?= number_format(
                                        (float) $item['custo_unitario'],
                                        2,
                                        ',',
                                        '.'
                                    ) ?>
                                </td>

                                <td>
                                    R$ <?= number_format(
                                        (float) $item['subtotal'],
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

        <div class="nf-form-aviso">
            Documento demonstrativo gerado automaticamente pelo
            sistema Magic Donuts. Não representa uma NF-e oficial
            emitida ou autorizada pela SEFAZ.
        </div>

        <div class="nf-form-botoes no-print">

            <button
                type="button"
                class="nf-btn-salvar"
                onclick="window.print()"
            >
                Imprimir
            </button>

            <a
                href="index.php?controller=entrada&action=index"
                class="nf-btn-cancelar"
            >
                Voltar
            </a>

        </div>

    </main>

</div>

</body>
</html>
