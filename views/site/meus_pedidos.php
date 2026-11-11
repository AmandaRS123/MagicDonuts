
<?php

$tituloPagina = 'Meus pedidos';

$pedidos = $pedidos ?? [];
$clienteChave = $clienteChave ?? '';

$statusTexto = [
    'pendente' => 'Pendente',
    'pago' => 'Pago',
    'enviado' => 'A caminho',
    'entregue' => 'Entregue',
    'cancelado' => 'Cancelado'
];

?>

<style>
.pagina-pedidos .vazio {
    min-height: 320px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    gap: 12px;
    padding: 40px 20px;
    margin: 24px auto;
    width: 100%;
    box-sizing: border-box;
}

.pagina-pedidos .vazio[hidden] {
    display: none !important;
}

.pagina-pedidos .vazio > i {
    font-size: 48px;
    margin-bottom: 8px;
}

.pagina-pedidos .vazio h2,
.pagina-pedidos .vazio p {
    margin: 0;
}

.pagina-pedidos .vazio .btn {
    margin-top: 12px;
}
</style>

<section class="pagina pagina-pedidos">

    <div class="container">

        <div class="pagina-titulo">
            <h1>Meus pedidos</h1>

            <p>
                Acompanhe suas compras e confira o histórico dos seus pedidos.
            </p>
        </div>

        <?php if (!empty($pedidos)): ?>

            <div class="pedidos-lista">

                <?php foreach ($pedidos as $pedido): ?>

                    <?php
                    $id = (int) $pedido['id'];

                    $status = $pedido['status'] ?? 'pendente';

                    $classeStatus = 'status-' . $status;

                    $data = !empty($pedido['data'])
                        ? date('d/m/Y H:i', strtotime($pedido['data']))
                        : 'Data não informada';

                    $tipoEntrega = $pedido['tipo_entrega'] ?? 'entrega';

                    $horas = (int) ($pedido['prazo_horas'] ?? 0);

                    if ($tipoEntrega === 'retirada') {
                        $prazoTexto = 'Retirada na loja';
                    } elseif ($horas === 2) {
                        $prazoTexto = 'Previsão: 2 a 4 horas';
                    } elseif ($horas === 3) {
                        $prazoTexto = 'Previsão: 3 a 5 horas';
                    } elseif ($horas === 5) {
                        $prazoTexto = 'Previsão: 5 a 8 horas';
                    } else {
                        $prazoTexto = 'Prazo não informado';
                    }

                    $podeConfirmar = !in_array(
                        $status,
                        ['pendente', 'cancelado'],
                        true
                    );
                    ?>

                    <article
                        class="painel pedido-card"
                        data-pedido-id="<?= $id ?>"
                    >

                        <div class="pedido-card-topo">

                            <div>
                                <span class="pedido-legenda">
                                    Pedido
                                </span>

                                <h2>
                                    #<?= $id ?>
                                </h2>
                            </div>

                            <span class="pedido-status <?= e($classeStatus) ?>">
                                <?= e($statusTexto[$status] ?? ucfirst($status)) ?>
                            </span>

                        </div>

                        <div class="pedido-card-informacoes">

                            <div>
                                <span>Data da compra</span>
                                <strong><?= e($data) ?></strong>
                            </div>

                            <div>
                                <span>Pagamento</span>

                                <strong>
                                    <?= e(ucfirst($pedido['forma_pagamento'] ?? '')) ?>
                                </strong>
                            </div>

                            <div>
                                <span>Total</span>

                                <strong>
                                    <?= e(moeda((float) $pedido['valor_total'])) ?>
                                </strong>
                            </div>

                        </div>

                        <div class="pedido-prazo">
                            <i class="fa-regular fa-clock" aria-hidden="true"></i>

                            <span>
                                <?= e($prazoTexto) ?>
                            </span>

                            <?php if ($tipoEntrega === 'entrega'): ?>
                                <small>
                                    Prazo estimado pela Magic Donuts.
                                </small>
                            <?php endif; ?>
                        </div>

                        <div class="pedido-card-rodape">

                            <a
                                href="<?= e(url_site('pedido', 'confirmacao', [
                                    'id' => $id
                                ])) ?>"
                                class="btn btn-pequeno"
                            >
                                Ver detalhes
                            </a>

                        </div>

                        <?php if ($podeConfirmar): ?>

                            <div class="pedido-recebimento">

                                <div class="recebimento-pendente">
                                    <p>
                                        Seu pedido já chegou?
                                    </p>

                                    <button
                                        type="button"
                                        class="btn-recebido"
                                        data-acao="recebido"
                                    >
                                        <i class="fa-solid fa-check"></i>
                                        Já recebi meu pedido
                                    </button>
                                </div>

                                <div class="recebimento-confirmado" hidden>

                                    <p class="mensagem-recebido">
                                        <i class="fa-solid fa-circle-check"></i>
                                        Recebimento confirmado! 💜
                                    </p>

                                    <button
                                        type="button"
                                        class="btn-excluir-historico"
                                        data-acao="excluir"
                                    >
                                        <i class="fa-regular fa-trash-can"></i>
                                        Excluir do meu histórico
                                    </button>

                                </div>

                            </div>

                        <?php endif; ?>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

        <div
            class="vazio"
            id="pedidos-vazio"
            <?= !empty($pedidos) ? 'hidden' : '' ?>
        >
            <i class="fa-solid fa-box-open" aria-hidden="true"></i>

            <h2>Você ainda não realizou pedidos</h2>

            <p>
                Que tal escolher alguns donuts deliciosos para começar?
            </p>

            <a
                href="<?= e(url_site('loja', 'index')) ?>"
                class="btn"
            >
                Conhecer o cardápio
            </a>
        </div>

    </div>

</section>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const chave = 'magicdonuts_pedidos_<?= e($clienteChave) ?>';

    let dados = {
        recebidos: [],
        ocultos: []
    };

    try {
        const salvo = localStorage.getItem(chave);

        if (salvo) {
            const convertido = JSON.parse(salvo);

            dados.recebidos = Array.isArray(convertido.recebidos)
                ? convertido.recebidos
                : [];

            dados.ocultos = Array.isArray(convertido.ocultos)
                ? convertido.ocultos
                : [];
        }
    } catch (erro) {
        console.warn('Não foi possível carregar o histórico local.');
    }

    function salvar() {
        try {
            localStorage.setItem(chave, JSON.stringify(dados));
        } catch (erro) {
            alert('Não foi possível salvar essa alteração no navegador.');
        }
    }

    const estadoVazio = document.querySelector('#pedidos-vazio');

    function atualizarEstadoVazio() {
        const pedidosVisiveis = document.querySelectorAll('.pedido-card');

        if (estadoVazio) {
            estadoVazio.hidden = pedidosVisiveis.length > 0;
        }
    }

    document.querySelectorAll('.pedido-card').forEach(function (card) {

        const id = card.dataset.pedidoId;

        const botaoRecebido = card.querySelector(
            '[data-acao="recebido"]'
        );

        const botaoExcluir = card.querySelector(
            '[data-acao="excluir"]'
        );

        const pendente = card.querySelector('.recebimento-pendente');

        const confirmado = card.querySelector('.recebimento-confirmado');

        // Restaura o estado salvo no navegador.
        if (dados.ocultos.includes(id)) {
            card.remove();
            return;
        }

        if (dados.recebidos.includes(id) && confirmado) {
            pendente.hidden = true;
            confirmado.hidden = false;
        }

        // Confirma o recebimento.
        if (botaoRecebido) {

            botaoRecebido.addEventListener('click', function () {

                const confirmou = confirm(
                    'Você confirma que já recebeu este pedido?'
                );

                if (!confirmou) {
                    return;
                }

                if (!dados.recebidos.includes(id)) {
                    dados.recebidos.push(id);
                }

                salvar();

                pendente.hidden = true;
                confirmado.hidden = false;

            });

        }

        // Esconde o pedido somente do histórico deste navegador.
        if (botaoExcluir) {

            botaoExcluir.addEventListener('click', function () {

                const confirmou = confirm(
                    'Deseja retirar este pedido do seu histórico? ' +
                    'O registro continuará salvo na loja.'
                );

                if (!confirmou) {
                    return;
                }

                if (!dados.ocultos.includes(id)) {
                    dados.ocultos.push(id);
                }

                salvar();

                card.remove();

                atualizarEstadoVazio();

            });

        }

    });

    // Atualiza a mensagem também quando todos os pedidos
    // foram ocultados anteriormente no navegador.
    atualizarEstadoVazio();

});
</script>
