
<?php

$tituloPagina = 'Pagamento do pedido';

$pedido = $pedido ?? [];

$idPedido = (int) ($pedido['id'] ?? 0);
$formaPagamento = $pedido['forma_pagamento'] ?? 'pix';
$valorTotal = (float) ($pedido['valor_total'] ?? 0);

$formas = [
    'pix' => 'PIX',
    'cartao' => 'Cartão',
    'boleto' => 'Boleto'
];

$nomeForma = $formas[$formaPagamento] ?? 'Não identificada';

?>

<section class="pagina pagina-pagamento md-pagina-pagamento">

    <div class="container">

        <div class="pagina-titulo">
            <h1>Quase lá! 💜</h1>
            <p>Finalize sua compra e prepare-se para aproveitar seus donuts.</p>
        </div>

        <div class="md-pagamento-card">

            <div class="md-pagamento-topo">

                <div class="md-pagamento-icone">
                    <?php if ($formaPagamento === 'pix'): ?>
                        <i class="fa-brands fa-pix" aria-hidden="true"></i>
                    <?php elseif ($formaPagamento === 'cartao'): ?>
                        <i class="fa-solid fa-credit-card" aria-hidden="true"></i>
                    <?php else: ?>
                        <i class="fa-solid fa-barcode" aria-hidden="true"></i>
                    <?php endif; ?>
                </div>

                <span class="md-pagamento-etiqueta">
                    Finalização do pedido
                </span>

                <h2>Pedido #<?= $idPedido ?></h2>

                <p>
                    Você escolheu pagar com
                    <strong><?= e($nomeForma) ?></strong>.
                </p>

            </div>

            <div class="md-pagamento-valor">

                <span>Valor total do pedido</span>

                <strong><?= e(moeda($valorTotal)) ?></strong>

            </div>

            <?php if ($formaPagamento === 'pix'): ?>

                <div class="md-pagamento-conteudo md-pix-conteudo">

                    <div class="md-conteudo-titulo">
                        <span class="md-mini-icone">
                            <i class="fa-brands fa-pix" aria-hidden="true"></i>
                        </span>

                        <div>
                            <h3>Pague com PIX</h3>
                            <p>Rápido, prático e sem complicação!</p>
                        </div>
                    </div>

                    <div class="md-pix-area">

                        <div class="md-pix-qr">
                            <!--
                                Quando criar seu QR Code, substitua este conteúdo por:

                                <img
                                    src="<?= e(url_site('caminho/da/sua/imagem-qr.png')) ?>"
                                    alt="QR Code PIX"
                                >
                            -->

                            <i class="fa-solid fa-qrcode" aria-hidden="true"></i>
                            <strong>Seu QR Code ficará aqui</strong>
                            <small>Adicione a imagem do seu QR Code nesta área.</small>
                        </div>

                        <div class="md-pix-instrucoes">

                            <h4>Como pagar?</h4>

                            <ol>
                                <li>Abra o aplicativo do seu banco.</li>
                                <li>Escolha a opção de pagamento por PIX.</li>
                                <li>Escaneie o QR Code acima.</li>
                                <li>Confira as informações e conclua o pagamento.</li>
                            </ol>

                            <div class="md-pix-aviso">
                                <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                                <span>
                                    Área demonstrativa. Nenhum pagamento real será realizado.
                                </span>
                            </div>

                        </div>

                    </div>

                </div>

            <?php elseif ($formaPagamento === 'cartao'): ?>

                <div class="md-pagamento-conteudo">

                    <div class="md-conteudo-titulo">
                        <span class="md-mini-icone">
                            <i class="fa-solid fa-credit-card" aria-hidden="true"></i>
                        </span>

                        <div>
                            <h3>Dados do cartão</h3>
                            <p>Preencha os dados para finalizar seu pedido.</p>
                        </div>
                    </div>

                    <div class="md-cartao-visual">
                        <div class="md-cartao-chip">
                            <i class="fa-solid fa-sim-card" aria-hidden="true"></i>
                        </div>

                        <i class="fa-solid fa-credit-card md-cartao-simbolo" aria-hidden="true"></i>

                        <span class="md-cartao-numero-preview" id="preview-numero">
                            •••• •••• •••• ••••
                        </span>

                        <div class="md-cartao-rodape">
                            <div>
                                <small>Nome no cartão</small>
                                <strong id="preview-nome">SEU NOME</strong>
                            </div>

                            <div>
                                <small>Validade</small>
                                <strong id="preview-validade">MM/AA</strong>
                            </div>
                        </div>
                    </div>

                    <div class="md-cartao-formulario">

                        <div class="md-campo">
                            <label for="numero-cartao">Número do cartão</label>
                            <input
                                type="text"
                                id="numero-cartao"
                                name="numero_cartao"
                                placeholder="0000 0000 0000 0000"
                                inputmode="numeric"
                                autocomplete="cc-number"
                                maxlength="19"
                                pattern="[0-9 ]{13,19}"
                                required
                            >
                        </div>

                        <div class="md-campo">
                            <label for="nome-cartao">Nome impresso no cartão</label>
                            <input
                                type="text"
                                id="nome-cartao"
                                name="nome_cartao"
                                placeholder="Como está no cartão"
                                autocomplete="cc-name"
                                minlength="3"
                                maxlength="60"
                                required
                            >
                        </div>

                        <div class="md-campos-duplos">

                            <div class="md-campo">
                                <label for="validade-cartao">Validade</label>
                                <input
                                    type="text"
                                    id="validade-cartao"
                                    name="validade_cartao"
                                    placeholder="MM/AA"
                                    inputmode="numeric"
                                    autocomplete="cc-exp"
                                    maxlength="5"
                                    pattern="(0[1-9]|1[0-2])\/[0-9]{2}"
                                    required
                                >
                            </div>

                            <div class="md-campo">
                                <label for="cvv-cartao">CVV</label>
                                <input
                                    type="password"
                                    id="cvv-cartao"
                                    name="cvv_cartao"
                                    placeholder="000"
                                    inputmode="numeric"
                                    autocomplete="cc-csc"
                                    maxlength="4"
                                    pattern="[0-9]{3,4}"
                                    required
                                >
                            </div>

                        </div>

                        <div class="md-seguranca">
                            <i class="fa-solid fa-lock" aria-hidden="true"></i>
                            <span>
                                Ambiente demonstrativo. Não informe dados de um cartão real.
                            </span>
                        </div>

                    </div>

                </div>

            <?php elseif ($formaPagamento === 'boleto'): ?>

                <div class="md-pagamento-conteudo">

                    <div class="md-conteudo-titulo">
                        <span class="md-mini-icone">
                            <i class="fa-solid fa-barcode" aria-hidden="true"></i>
                        </span>

                        <div>
                            <h3>Seu boleto</h3>
                            <p>Confira as informações abaixo.</p>
                        </div>
                    </div>

                    <div class="md-boleto-area">

                        <div class="md-boleto-cabecalho">
                            <i class="fa-solid fa-file-invoice-dollar" aria-hidden="true"></i>

                            <div>
                                <strong>MAGIC DONUTS</strong>
                                <small>Boleto</small>
                            </div>

                            <span>BOLETO</span>
                        </div>

                        <div class="md-boleto-valor">
                            <span>Valor do documento</span>
                            <strong><?= e(moeda($valorTotal)) ?></strong>
                        </div>

                        <div class="md-boleto-linha">
                            <span>Linha digitável</span>

                            <strong>
                                00000.00000 00000.000000 00000.000000 0 00000000000000
                            </strong>
                        </div>

                        <div class="md-boleto-codigo" aria-label="Código de barras ilustrativo"></div>

                        <div class="md-boleto-vencimento">
                            <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                            <span>
                                Vencimento: <?= date('d/m/Y', strtotime('+3 days')) ?>
                            </span>
                        </div>

                        <div class="md-pix-aviso">
                            <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                            <span>
                                Este boleto é fictício e não pode ser pago em bancos ou aplicativos.
                            </span>
                        </div>

                    </div>

                </div>

            <?php else: ?>

                <div class="md-pagamento-conteudo">
                    <p>Não foi possível identificar a forma de pagamento escolhida.</p>
                </div>

            <?php endif; ?>

            <form
                method="post"
                action="<?= e(url_site('pedido', 'simularPagamento')) ?>"
                id="form-finalizar-compra"
            >

                <?= csrf_campo() ?>

                <input
                    type="hidden"
                    name="id"
                    value="<?= $idPedido ?>"
                >

                <button type="submit" class="md-btn-finalizar">
                    <i class="fa-solid fa-check" aria-hidden="true"></i>
                    Finalizar compra
                </button>

            </form>

            <p class="md-pagamento-rodape">
                <i class="fa-solid fa-heart" aria-hidden="true"></i>
                Obrigado por escolher a Magic Donuts!
            </p>

        </div>

    </div>

</section>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const numero = document.getElementById('numero-cartao');
    const nome = document.getElementById('nome-cartao');
    const validade = document.getElementById('validade-cartao');

    const previewNumero = document.getElementById('preview-numero');
    const previewNome = document.getElementById('preview-nome');
    const previewValidade = document.getElementById('preview-validade');

    const formulario = document.getElementById('form-finalizar-compra');

    // Formatação demonstrativa do número do cartão.
    if (numero) {
        numero.addEventListener('input', function () {
            let valor = this.value.replace(/\D/g, '').slice(0, 16);
            valor = valor.replace(/(\d{4})(?=\d)/g, '$1 ');

            this.value = valor;

            previewNumero.textContent = valor || '•••• •••• •••• ••••';
        });
    }

    // Atualiza o nome no cartão ilustrativo.
    if (nome) {
        nome.addEventListener('input', function () {
            previewNome.textContent =
                this.value.trim().toUpperCase() || 'SEU NOME';
        });
    }

    // Formata a validade como MM/AA.
    if (validade) {
        validade.addEventListener('input', function () {
            let valor = this.value.replace(/\D/g, '').slice(0, 4);

            if (valor.length > 2) {
                valor = valor.slice(0, 2) + '/' + valor.slice(2);
            }

            this.value = valor;

            previewValidade.textContent = valor || 'MM/AA';
        });
    }

    // Validação adicional dos campos fictícios do cartão.
    if (formulario && numero && nome && validade) {
        formulario.addEventListener('submit', function (event) {

            const numeroLimpo = numero.value.replace(/\D/g, '');
            const nomePreenchido = nome.value.trim();
            const validadeValor = validade.value.trim();
            const cvv = document.getElementById('cvv-cartao');

            const validadeValida =
                /^(0[1-9]|1[0-2])\/\d{2}$/.test(validadeValor);

            const numeroValido =
                numeroLimpo.length >= 13 &&
                numeroLimpo.length <= 16;

            const cvvValido =
                cvv && /^[0-9]{3,4}$/.test(cvv.value);

            if (
                !numeroValido ||
                nomePreenchido.length < 3 ||
                !validadeValida ||
                !cvvValido
            ) {
                event.preventDefault();

                alert('Confira os dados do cartão antes de finalizar a compra.');

                if (!numeroValido) {
                    numero.focus();
                } else if (nomePreenchido.length < 3) {
                    nome.focus();
                } else if (!validadeValida) {
                    validade.focus();
                } else if (cvv) {
                    cvv.focus();
                }
            }
        });
    }

});
</script>
