
<?php
// Nota fiscal demonstrativa da venda.
// Os dados devem ser carregados pelo controller antes desta view.

$esc = static function ($valor): string {
    return htmlspecialchars(
        (string) ($valor ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
};

$idVenda = (int) ($venda['id'] ?? 0);

$timestampVenda = !empty($venda['data'])
    ? strtotime($venda['data'])
    : false;

$dataVendaFormatada = $timestampVenda
    ? date('d/m/Y', $timestampVenda)
    : 'Não informada';

$horaVendaFormatada = $timestampVenda
    ? date('H:i:s', $timestampVenda)
    : '--:--:--';

$itensVenda = (
    isset($venda['itens']) && is_array($venda['itens'])
) ? $venda['itens'] : [];

$valorTotalVenda = (float) ($venda['valor_total'] ?? 0);

// Número demonstrativo baseado no ID da venda.
$numeroNota = str_pad((string) $idVenda, 6, '0', STR_PAD_LEFT);
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Nota de Venda - <?= $esc($numeroNota) ?> | Magic Donuts</title>

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
                Documento demonstrativo de venda
            </p>

            <p class="nf-emitente-endereco">
                Sistema de gerenciamento da loja
            </p>
        </div>

        <div class="nf-documento-info">
            <h2 class="nf-documento-titulo">NOTA DE VENDA</h2>

            <div class="nf-documento-dados">
                <p class="nf-dado-linha">
                    <strong>NÚMERO:</strong>
                    <?= $esc($numeroNota) ?>
                </p>

                <p class="nf-dado-linha">
                    <strong>SÉRIE:</strong> 1
                </p>

                <p class="nf-dado-linha">
                    <strong>REFERÊNCIA:</strong>
                    #<?= $idVenda ?>
                </p>
            </div>
        </div>
    </header>

    <!-- DATA E IDENTIFICAÇÃO -->
    <section class="nf-emissao">
        <div class="nf-emissao-bloco">
            <span class="nf-emissao-label">DATA DA VENDA</span>
            <span class="nf-emissao-valor">
                <?= $esc($dataVendaFormatada) ?>
            </span>
        </div>

        <div class="nf-emissao-bloco">
            <span class="nf-emissao-label">HORA DA VENDA</span>
            <span class="nf-emissao-valor">
                <?= $esc($horaVendaFormatada) ?>
            </span>
        </div>

        <div class="nf-emissao-bloco">
            <span class="nf-emissao-label">CÓDIGO DA VENDA</span>
            <span class="nf-emissao-valor">#<?= $idVenda ?></span>
        </div>
    </section>

    <!-- CLIENTE, QUANDO DISPONÍVEL -->
    <?php if (!empty($cliente) && is_array($cliente)): ?>
        <section class="nf-emissao">
            <div class="nf-emissao-bloco nf-destinatario">
                <span class="nf-emissao-label">CLIENTE</span>

                <span class="nf-emissao-valor">
                    <?= $esc($cliente['nome'] ?? 'Não informado') ?>
                </span>

                <?php if (!empty($cliente['cpf'])): ?>
                    <p class="nf-emitente-endereco">
                        CPF: <?= $esc($cliente['cpf']) ?>
                    </p>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- PRODUTOS VENDIDOS -->
    <main class="nf-conteudo">
        <table class="nf-tabela-produtos">
            <thead>
                <tr>
                    <th class="nf-th-codigo">CÓDIGO / SKU</th>
                    <th class="nf-th-descricao">PRODUTO</th>
                    <th class="nf-th-qtd">QTD.</th>
                    <th class="nf-th-valor-uni">V. UNITÁRIO</th>
                    <th class="nf-th-valor-tot">V. TOTAL</th>
                </tr>
            </thead>

            <tbody>
            <?php if (!empty($itensVenda)): ?>

                <?php foreach ($itensVenda as $item): ?>
                    <?php
                    $quantidade = (int) ($item['quantidade'] ?? 0);
                    $precoUnitario = (float) (
                        $item['preco_unitario'] ?? 0
                    );

                    $subtotal = $quantidade * $precoUnitario;

                    $nomeProduto = $item['produto_nome']
                        ?? $item['nome_produto']
                        ?? $item['nome']
                        ?? 'Produto';

                    $nomeVariacao = $item['variacao_nome'] ?? '';

                    $sku = $item['sku']
                        ?? $item['variacao_id']
                        ?? '-';
                    ?>

                    <tr>
                        <td class="nf-td-codigo">
                            <?= $esc($sku) ?>
                        </td>

                        <td class="nf-td-descricao">
                            <?= $esc($nomeProduto) ?>

                            <?php if ($nomeVariacao !== ''): ?>
                                <span class="nf-detalhe">
                                    (<?= $esc($nomeVariacao) ?>)
                                </span>
                            <?php endif; ?>
                        </td>

                        <td class="nf-td-qtd">
                            <?= $quantidade ?>
                        </td>

                        <td class="nf-td-valor-uni">
                            R$ <?= number_format(
                                $precoUnitario, 2, ',', '.'
                            ) ?>
                        </td>

                        <td class="nf-td-valor-tot">
                            R$ <?= number_format(
                                $subtotal, 2, ',', '.'
                            ) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

            <?php else: ?>
                <tr>
                    <td colspan="5" class="nf-sem-itens">
                        Nenhum item foi informado para esta venda.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </main>

    <!-- TOTAL -->
    <footer class="nf-rodape">
        <div class="nf-total-bloco">
            <span class="nf-total-label">VALOR TOTAL DA VENDA</span>

            <span class="nf-total-valor">
                R$ <?= number_format(
                    $valorTotalVenda, 2, ',', '.'
                ) ?>
            </span>
        </div>
    </footer>

    <p class="nf-aviso">
        Documento demonstrativo gerado pelo sistema Magic Donuts.
    </p>

    <!-- IMPRESSÃO -->
    <div class="nf-botoes no-print">
        <button
            type="button"
            class="nf-btn-imprimir"
            onclick="window.print()"
        >
            Imprimir documento
        </button>

        <button
    type="button"
    class="nf-btn-voltar"
    onclick="window.location.href='index.php?controller=venda&action=index'"
>
    Voltar
</button>
    </div>

</div>
</body>
</html>
