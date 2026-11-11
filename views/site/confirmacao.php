
<?php

$tituloPagina = 'Pedido confirmado';

$pedido = $pedido ?? [];
$itens = $itens ?? [];

$idPedido = (int) ($pedido['id'] ?? 0);
$status = $pedido['status'] ?? 'pendente';

$statusTexto = [
    'pendente' => 'Aguardando pagamento',
    'pago' => 'Pagamento aprovado',
    'enviado' => 'Pedido enviado',
    'entregue' => 'Pedido entregue',
    'cancelado' => 'Pedido cancelado'
];

$statusClasse = [
    'pendente' => 'md-status-pendente',
    'pago' => 'md-status-pago',
    'enviado' => 'md-status-enviado',
    'entregue' => 'md-status-entregue',
    'cancelado' => 'md-status-cancelado'
];

$pagamentos = [
    'pix' => 'PIX',
    'cartao' => 'Cartão',
    'boleto' => 'Boleto'
];

$formaPagamento = strtolower($pedido['forma_pagamento'] ?? '');
$pagamentoTexto = $pagamentos[$formaPagamento] ?? ucfirst($formaPagamento);

$dataPedido = !empty($pedido['data'])
    ? date('d/m/Y \à\s H:i', strtotime($pedido['data']))
    : 'Não informada';

$enderecoEntrega = trim($pedido['endereco_entrega'] ?? '');
$cepEntrega = trim($pedido['cep_entrega'] ?? '');

?>

<section class="pagina pagina-confirmacao md-confirmacao">

    <div class="container">

        <!-- CONFIRMAÇÃO PRINCIPAL -->
        <div class="md-confirmacao-card">

            <div class="md-confirmacao-decoracao md-decoracao-1"></div>
            <div class="md-confirmacao-decoracao md-decoracao-2"></div>

            <div class="md-confirmacao-icone">
                <?php if ($status === 'cancelado'): ?>
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                <?php else: ?>
                    <i class="fa-solid fa-check" aria-hidden="true"></i>
                <?php endif; ?>
            </div>

            <span class="md-confirmacao-etiqueta">
                <?= $status === 'cancelado' ? 'Pedido cancelado' : 'Tudo certinho!' ?>
            </span>

            <h1>
                <?= $status === 'cancelado'
                    ? 'Pedido cancelado'
                    : 'Pedido registrado!' ?>
            </h1>

            <p class="md-confirmacao-mensagem">
                <?php if ($status === 'cancelado'): ?>
                    Este pedido foi cancelado. Se precisar de ajuda, entre em contato com a Magic Donuts.
                <?php else: ?>
                    Obrigado por escolher a Magic Donuts!
                    Recebemos seu pedido e estamos preparando tudo com carinho. 💜
                <?php endif; ?>
            </p>

            <div class="md-confirmacao-numero">

                <div>
                    <span>Número do pedido</span>
                    <strong>#<?= $idPedido ?></strong>
                </div>

                <i class="fa-solid fa-receipt" aria-hidden="true"></i>

            </div>

            <div class="md-confirmacao-status">

                <span>Status do pedido</span>

                <strong class="<?= e($statusClasse[$status] ?? 'md-status-pendente') ?>">
                    <i class="fa-solid fa-circle" aria-hidden="true"></i>
                    <?= e($statusTexto[$status] ?? ucfirst($status)) ?>
                </strong>

            </div>

        </div>


<?php if ($status === 'pendente' && $idPedido > 0): ?>

    <div class="md-pagamento-pendente">

        <div class="md-pagamento-pendente-icone">
            <i class="fa-solid fa-wallet" aria-hidden="true"></i>
        </div>

        <div class="md-pagamento-pendente-texto">
            <strong>Falta só mais um passo! 💜</strong>
            <p>Seu pedido está aguardando pagamento. Finalize agora para continuar.</p>
        </div>

        <a
            href="<?= e(url_site('pedido', 'pagamento', [
                'id' => $idPedido
            ])) ?>"
            class="md-btn-pagar-agora"
        >
            <i class="fa-solid fa-lock" aria-hidden="true"></i>
            Finalizar pagamento
            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
        </a>

    </div>

<?php endif; ?>

        <!-- DETALHES DO PEDIDO -->
        <div class="md-detalhes-card">

            <div class="md-secao-titulo">

                <div class="md-secao-icone">
                    <i class="fa-solid fa-clipboard-list" aria-hidden="true"></i>
                </div>

                <div>
                    <h2>Detalhes da compra</h2>
                    <p>Confira as informações do seu pedido.</p>
                </div>

            </div>

            <div class="md-detalhes-grade">

                <div class="md-detalhe-item">
                    <span>
                        <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                        Data do pedido
                    </span>

                    <strong><?= e($dataPedido) ?></strong>
                </div>

                <div class="md-detalhe-item">
                    <span>
                        <i class="fa-solid fa-credit-card" aria-hidden="true"></i>
                        Forma de pagamento
                    </span>

                    <strong><?= e($pagamentoTexto ?: 'Não informada') ?></strong>
                </div>

                <div class="md-detalhe-item md-detalhe-endereco">
                    <span>
                        <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                        Endereço de entrega
                    </span>

                    <strong>
                        <?= e($enderecoEntrega !== '' ? $enderecoEntrega : 'Não informado') ?>
                    </strong>
                </div>

                <div class="md-detalhe-item">
                    <span>
                        <i class="fa-solid fa-map-pin" aria-hidden="true"></i>
                        CEP
                    </span>

                    <strong>
                        <?= e($cepEntrega !== '' ? $cepEntrega : 'Não informado') ?>
                    </strong>
                </div>

            </div>

            <div class="md-produtos-secao">

                <div class="md-secao-titulo md-produtos-titulo">

                    <div class="md-secao-icone">
                        <i class="fa-solid fa-box-open" aria-hidden="true"></i>
                    </div>

                    <div>
                        <h2>Seus produtos</h2>
                        <p>Uma seleção deliciosa esperando por você!</p>
                    </div>

                </div>

                <?php if (!empty($itens)): ?>

                    <div class="md-lista-produtos">

                        <?php foreach ($itens as $item): ?>

                            <?php
                            $quantidade = (int) ($item['quantidade'] ?? 0);
                            $precoUnitario = (float) ($item['preco_unitario'] ?? 0);
                            $subtotal = $precoUnitario * $quantidade;
                            ?>

                            <div class="md-produto-item">

                                <div class="md-produto-icone">
                                    <i class="fa-solid fa-cookie-bite" aria-hidden="true"></i>
                                </div>

                                <div class="md-produto-info">

                                    <strong>
                                        <?= e($item['produto_nome'] ?? 'Produto') ?>
                                    </strong>

                                    <span>
                                        <?= $quantidade ?> unidade(s)
                                        <?php if (!empty($item['tamanho'])): ?>
                                            · <?= e($item['tamanho']) ?>
                                        <?php endif; ?>
                                    </span>

                                </div>

                                <strong class="md-produto-preco">
                                    <?= e(moeda($subtotal)) ?>
                                </strong>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php else: ?>

                    <p class="md-sem-produtos">
                        Não há produtos disponíveis para exibir.
                    </p>

                <?php endif; ?>

            </div>

            <!-- RESUMO FINANCEIRO -->
            <div class="md-resumo-final">

                <div class="md-resumo-linha">
                    <span>Subtotal dos produtos</span>

                    <strong>
                        <?php
                        $subtotalProdutos = 0;

                        foreach ($itens as $item) {
                            $subtotalProdutos +=
                                (float) ($item['preco_unitario'] ?? 0) *
                                (int) ($item['quantidade'] ?? 0);
                        }

                        echo e(moeda($subtotalProdutos));
                        ?>
                    </strong>
                </div>

                <div class="md-resumo-linha">
                    <span>Frete</span>

                    <strong>
                        <?= e(moeda((float) ($pedido['valor_frete'] ?? 0))) ?>
                    </strong>
                </div>

                <div class="md-resumo-total">
                    <span>Total do pedido</span>

                    <strong>
                        <?= e(moeda((float) ($pedido['valor_total'] ?? 0))) ?>
                    </strong>
                </div>

            </div>

        </div>

        <!-- AÇÕES -->
        <div class="md-confirmacao-acoes">


            <a
                href="<?= e(url_site('pedido', 'meus')) ?>"
                class="md-btn md-btn-principal"
            >
                <i class="fa-solid fa-box" aria-hidden="true"></i>
                Acompanhar meus pedidos
            </a>

           
<a
    href="<?= e(url_site('pedido', 'notaFiscal', [
        'id' => $idPedido
    ])) ?>"
    class="md-btn md-btn-secundario"
>
    <i class="fa-solid fa-file-invoice" aria-hidden="true"></i>
    Ver nota fiscal
</a>


            <a
                href="<?= e(url_site('loja', 'index')) ?>"
                class="md-btn md-btn-contorno"
            >
                <i class="fa-solid fa-store" aria-hidden="true"></i>
                Continuar comprando
            </a>

        </div>

        <p class="md-confirmacao-agradecimento">
            <i class="fa-solid fa-heart" aria-hidden="true"></i>
            Feito com carinho, especialmente para você!
        </p>

    </div>

</section>
