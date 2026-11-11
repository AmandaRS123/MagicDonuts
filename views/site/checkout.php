
<?php

$tituloPagina = 'Finalizar pedido';

$itens = $itens ?? [];
$subtotal = (float) ($subtotal ?? 0);
$cliente = $cliente ?? [];

$tipoEntrega = $tipoEntrega ?? 'entrega';
$enderecoCadastrado = trim($cliente['endereco'] ?? '');

$temEnderecoCadastrado = $enderecoCadastrado !== '';

$valorFrete = isset($freteAtual) && $freteAtual !== null
    ? (float) $freteAtual
    : null;

$cidadeFrete = $cidadeFrete ?? '';
$estadoFrete = $estadoFrete ?? '';

$total = $subtotal + (
    $tipoEntrega === 'retirada' ? 0 : ($valorFrete ?? 0)
);

?>

<section class="pagina pagina-checkout">
    <div class="container">

        <div class="pagina-titulo">
            <h1>Finalizar pedido</h1>
            <p>Estamos quase lá! Escolha como deseja receber seus donuts.</p>
        </div>

        <?php if (empty($itens)): ?>

            <div class="vazio">
                <h2>Seu carrinho está vazio</h2>
                <a class="btn" href="<?= e(url_site('loja', 'index')) ?>">
                    Voltar para a loja
                </a>
            </div>

        <?php else: ?>

            <form
                method="post"
                action="<?= e(url_site('pedido', 'finalizar')) ?>"
                class="checkout-layout"
                id="form-checkout"
            >

                <?= csrf_campo() ?>

                <div class="checkout-formularios">

                    <!-- TIPO DE ENTREGA -->

                    <section class="painel">
                        <h2>
                            <i class="fa-solid fa-truck" aria-hidden="true"></i>
                            Como deseja receber?
                        </h2>

                        <div class="entrega-opcoes">

                            <label class="entrega-opcao">
                                <input
                                    type="radio"
                                    name="tipo_entrega"
                                    value="entrega"
                                    <?= $tipoEntrega === 'entrega' ? 'checked' : '' ?>
                                >

                                <span>
                                    <i class="fa-solid fa-house" aria-hidden="true"></i>
                                    <strong>Receber em casa</strong>
                                    <small>
                                        Entregamos em Nova Iguaçu e demais cidades do RJ.
                                    </small>
                                </span>
                            </label>

                            <label class="entrega-opcao">
                                <input
                                    type="radio"
                                    name="tipo_entrega"
                                    value="retirada"
                                    <?= $tipoEntrega === 'retirada' ? 'checked' : '' ?>
                                >

                                <span>
                                    <i class="fa-solid fa-store" aria-hidden="true"></i>
                                    <strong>Retirar na loja</strong>
                                    <small>
                                        Retire seu pedido gratuitamente em nossa loja.
                                    </small>
                                </span>
                            </label>

                        </div>
                    </section>

                    <!-- ENDEREÇO DE ENTREGA -->

                    <section class="painel" id="painel-endereco">

                        <h2>
                            <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                            Endereço de entrega
                        </h2>

                        <p class="checkout-ajuda">
                            Escolha qual endereço deseja utilizar neste pedido.
                        </p>

                        <div class="endereco-opcoes">

                            <?php if ($temEnderecoCadastrado): ?>

                                <label class="entrega-opcao">
                                    <input
                                        type="radio"
                                        name="endereco_tipo"
                                        value="cadastrado"
                                        id="endereco-cadastrado"
                                        checked
                                    >

                                    <span>
                                        <i class="fa-solid fa-user" aria-hidden="true"></i>
                                        <strong>Usar endereço cadastrado</strong>
                                        <small>
                                            Utilizar o endereço salvo na minha conta.
                                        </small>
                                    </span>
                                </label>

                            <?php endif; ?>

                            <label class="entrega-opcao">
                                <input
                                    type="radio"
                                    name="endereco_tipo"
                                    value="outro"
                                    id="endereco-outro"
                                    <?= !$temEnderecoCadastrado ? 'checked' : '' ?>
                                >

                                <span>
                                    <i class="fa-solid fa-map-location-dot" aria-hidden="true"></i>
                                    <strong>Entregar em outro endereço</strong>
                                    <small>
                                        Informar um endereço diferente para este pedido.
                                    </small>
                                </span>
                            </label>

                        </div>

                        <!-- ENDEREÇO CADASTRADO -->

                        <?php if ($temEnderecoCadastrado): ?>

                            <div id="bloco-endereco-cadastrado" class="campo">
                                <label>Meu endereço cadastrado</label>

                                <div class="endereco-cadastrado-visual">
                                    <i class="fa-solid fa-house"></i>
                                    <span><?= e($enderecoCadastrado) ?></span>
                                </div>

                                <small class="checkout-ajuda">
                                    Esse endereço será utilizado somente neste pedido.
                                </small>
                            </div>

                        <?php endif; ?>

                        <!-- OUTRO ENDEREÇO -->

                        <div
                            id="bloco-outro-endereco"
                            style="<?= $temEnderecoCadastrado ? 'display:none;' : '' ?>"
                        >

                            <div class="campo">
                                <label for="endereco">Rua / Avenida</label>

                                <input
                                    type="text"
                                    id="endereco"
                                    name="endereco"
                                    maxlength="150"
                                    autocomplete="address-line1"
                                    placeholder="Digite sua rua ou avenida"
                                >
                            </div>

                            <div class="campo">
                                <label for="numero">Número</label>

                                <input
                                    type="text"
                                    id="numero"
                                    name="numero"
                                    maxlength="15"
                                    placeholder="Número do imóvel"
                                >
                            </div>

                            <div class="campo">
                                <label for="complemento">Complemento</label>

                                <input
                                    type="text"
                                    id="complemento"
                                    name="complemento"
                                    maxlength="80"
                                    placeholder="Apartamento, bloco, referência..."
                                >
                            </div>

                            <div class="campo">
                                <label for="bairro">Bairro</label>

                                <input
                                    type="text"
                                    id="bairro"
                                    name="bairro"
                                    maxlength="80"
                                    placeholder="Digite seu bairro"
                                >
                            </div>

                        </div>

                        <!-- CEP -->

                        <div class="campo">
                            <label for="cep">CEP</label>

                            <input
                                type="text"
                                id="cep"
                                name="cep"
                                placeholder="00000-000"
                                maxlength="9"
                                inputmode="numeric"
                                autocomplete="postal-code"
                                value="<?= e($_SESSION['frete_cep'] ?? '') ?>"
                                required
                            >

                            <small class="checkout-ajuda">
                                Ao completar o CEP, preencheremos automaticamente rua, bairro,
                                cidade e estado. Depois, confirme para calcular o frete.
                            </small>
                        </div>

                        <!-- CIDADE E ESTADO -->

                        <div class="checkout-duas-colunas">

                            <div class="campo">
                                <label for="cidade">Cidade</label>

                                <input
                                    type="text"
                                    id="cidade"
                                    name="cidade"
                                    maxlength="80"
                                    value="<?= e($cidadeFrete) ?>"
                                    readonly
                                >
                            </div>

                            <div class="campo">
                                <label for="estado">Estado</label>

                                <input
                                    type="text"
                                    id="estado"
                                    name="estado"
                                    maxlength="2"
                                    placeholder="UF"
                                    value="<?= e($estadoFrete) ?>"
                                    readonly
                                >
                            </div>

                        </div>

                    </section>

                    <!-- RETIRADA -->

                    <section
                        class="painel"
                        id="painel-retirada"
                        style="display:none;"
                    >

                        <h2>
                            <i class="fa-solid fa-store" aria-hidden="true"></i>
                            Retirada na loja
                        </h2>

                        <p>
                            Seu pedido ficará disponível para retirada na Magic Donuts,
                            em Nova Iguaçu/RJ.
                        </p>

                        <p>
                            <strong>Você não pagará frete!</strong>
                        </p>

                    </section>

                    <!-- CONFIRMAÇÃO -->

                    <section class="painel">

                        <h2>
                            <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                            Confirme como deseja receber
                        </h2>

                        <p class="checkout-ajuda" id="ajuda-confirmacao">
                            Informe seus dados e confirme o endereço e o frete.
                        </p>

                        <button
                            type="button"
                            class="btn btn-contorno btn-bloco"
                            id="confirmar-entrega"
                        >
                            <i class="fa-solid fa-check" aria-hidden="true"></i>
                            <span id="texto-confirmacao">
                                Confirmar endereço e frete
                            </span>
                        </button>

                        <p
                            id="resultado-cep"
                            class="frete-resultado"
                            aria-live="polite"
                        ></p>

                    </section>

                    <!-- PAGAMENTO -->

                    <section class="painel" id="painel-pagamento">

                        <h2>
                            <i class="fa-solid fa-credit-card" aria-hidden="true"></i>
                            Forma de pagamento
                        </h2>

                        <p class="checkout-ajuda" id="ajuda-pagamento">
                            Confirme primeiro como deseja receber seu pedido.
                        </p>

                        <div class="pagamento-opcoes">

                            <label class="pagamento-opcao">
                                <input
                                    type="radio"
                                    name="forma_pagamento"
                                    value="pix"
                                    checked
                                    disabled
                                >

                                <span>
                                    <i class="fa-brands fa-pix" aria-hidden="true"></i>
                                    <strong>PIX</strong>
                                    <small>Pagamento via PIX</small>
                                </span>
                            </label>

                            <label class="pagamento-opcao">
                                <input
                                    type="radio"
                                    name="forma_pagamento"
                                    value="cartao"
                                    disabled
                                >

                                <span>
                                    <i class="fa-solid fa-credit-card" aria-hidden="true"></i>
                                    <strong>Cartão</strong>
                                    <small>Pagamento com cartão</small>
                                </span>
                            </label>

                            <label class="pagamento-opcao">
                                <input
                                    type="radio"
                                    name="forma_pagamento"
                                    value="boleto"
                                    disabled
                                >

                                <span>
                                    <i class="fa-solid fa-barcode" aria-hidden="true"></i>
                                    <strong>Boleto</strong>
                                    <small>Pagamento por boleto</small>
                                </span>
                            </label>

                        </div>

                    </section>

                </div>

                <!-- RESUMO -->

                <aside class="painel resumo checkout-resumo">

                    <h2>Resumo do pedido</h2>

                    <div class="checkout-produtos">

                        <?php foreach ($itens as $item): ?>

                            <div class="checkout-produto">

                                <div>
                                    <strong>
                                        <?= e($item['produto_nome']) ?>
                                    </strong>

                                    <small>
                                        <?= (int) $item['quantidade'] ?> unidade(s)
                                        · <?= e($item['tamanho']) ?>
                                    </small>
                                </div>

                                <strong>
                                    <?= e(moeda($item['subtotal'])) ?>
                                </strong>

                            </div>

                        <?php endforeach; ?>

                    </div>

                    <dl class="resumo-linhas">

                        <div class="resumo-linha">
                            <dt>Subtotal</dt>
                            <dd id="resumo-subtotal">
                                <?= e(moeda($subtotal)) ?>
                            </dd>
                        </div>

                        <div class="resumo-linha">
                            <dt>Frete</dt>
                            <dd id="resumo-frete">
                                <?= $tipoEntrega === 'retirada' ? 'Grátis' : 'A calcular' ?>
                            </dd>
                        </div>

                        <div class="resumo-linha resumo-total">
                            <dt>Total</dt>
                            <dd id="resumo-total">
                                <?= e(moeda($subtotal)) ?>
                            </dd>
                        </div>

                    </dl>

                    <button
                        type="submit"
                        class="btn btn-bloco"
                        id="btn-confirmar"
                        disabled
                    >
                        Confirmar pedido
                    </button>

                    <a
                        href="<?= e(url_site('carrinho', 'index')) ?>"
                        class="auth-voltar"
                    >
                        Voltar ao carrinho
                    </a>

                </aside>

            </form>

        <?php endif; ?>

    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('form-checkout');

    if (!form) return;

    const radiosEntrega = document.querySelectorAll(
        'input[name="tipo_entrega"]'
    );

    const radiosEndereco = document.querySelectorAll(
        'input[name="endereco_tipo"]'
    );

    const painelEndereco = document.getElementById('painel-endereco');
    const painelRetirada = document.getElementById('painel-retirada');

    const blocoCadastrado = document.getElementById(
        'bloco-endereco-cadastrado'
    );

    const blocoOutro = document.getElementById('bloco-outro-endereco');

    const botaoConfirmarEntrega = document.getElementById('confirmar-entrega');
    const ajudaConfirmacao = document.getElementById('ajuda-confirmacao');
    const ajudaPagamento = document.getElementById('ajuda-pagamento');

    const resultado = document.getElementById('resultado-cep');

    const resumoFrete = document.getElementById('resumo-frete');
    const resumoTotal = document.getElementById('resumo-total');

    const botaoFinalizar = document.getElementById('btn-confirmar');

    const campoCep = document.getElementById('cep');
    const campoEndereco = document.getElementById('endereco');
    const campoNumero = document.getElementById('numero');
    const campoComplemento = document.getElementById('complemento');
    const campoBairro = document.getElementById('bairro');
    const campoCidade = document.getElementById('cidade');
    const campoEstado = document.getElementById('estado');

    const subtotal = <?= json_encode($subtotal) ?>;
    const temEnderecoCadastrado = <?= $temEnderecoCadastrado ? 'true' : 'false' ?>;

    let freteCalculado = false;
    let valorFrete = 0;
    let cepConsultado = '';
    let confirmado = false;
    let consultando = false;

    function moeda(valor) {
        return Number(valor).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });
    }

    function tipoSelecionado() {
        return document.querySelector(
            'input[name="tipo_entrega"]:checked'
        )?.value || 'entrega';
    }

    function enderecoSelecionado() {
        return document.querySelector(
            'input[name="endereco_tipo"]:checked'
        )?.value || 'outro';
    }

    function atualizarResumo() {

        if (tipoSelecionado() === 'retirada') {
            resumoFrete.textContent = 'Grátis';
            resumoTotal.textContent = moeda(subtotal);
            return;
        }

        if (!freteCalculado) {
            resumoFrete.textContent = 'A calcular';
            resumoTotal.textContent = moeda(subtotal);
            return;
        }

        resumoFrete.textContent = valorFrete === 0
            ? 'Grátis'
            : moeda(valorFrete);

        resumoTotal.textContent = moeda(subtotal + valorFrete);
    }

    function atualizarBotaoConfirmacao() {

        const retirada = tipoSelecionado() === 'retirada';

        if (confirmado) {

            botaoConfirmarEntrega.style.backgroundColor = '#238636';
            botaoConfirmarEntrega.style.borderColor = '#238636';
            botaoConfirmarEntrega.style.color = '#ffffff';

            botaoConfirmarEntrega.innerHTML =
                '<i class="fa-solid fa-circle-check"></i> ' +
                (retirada
                    ? 'Retirada confirmada'
                    : 'Endereço e frete confirmados');

            ajudaPagamento.textContent =
                'Tudo certo! Agora escolha a forma de pagamento.';

            ajudaConfirmacao.textContent = retirada
                ? 'Sua retirada gratuita foi confirmada.'
                : 'Seu endereço e o valor do frete foram confirmados.';

        } else {

            botaoConfirmarEntrega.style.backgroundColor = '';
            botaoConfirmarEntrega.style.borderColor = '';
            botaoConfirmarEntrega.style.color = '';

            botaoConfirmarEntrega.innerHTML =
                '<i class="fa-solid fa-check"></i> ' +
                (retirada
                    ? 'Confirmar retirada gratuita'
                    : 'Confirmar endereço e frete');

            ajudaPagamento.textContent =
                'Confirme primeiro como deseja receber seu pedido.';

            ajudaConfirmacao.textContent = retirada
                ? 'Clique abaixo para confirmar a retirada gratuita.'
                : 'Informe seus dados e confirme o endereço e o frete.';
        }

        document.querySelectorAll(
            'input[name="forma_pagamento"]'
        ).forEach(function (radio) {
            radio.disabled = !confirmado;
        });

        botaoFinalizar.disabled = !confirmado;
    }

    function limparConfirmacao() {
        confirmado = false;
        atualizarBotaoConfirmacao();
        atualizarResumo();
    }

    function atualizarTipoEndereco() {

        const usarCadastrado =
            temEnderecoCadastrado &&
            enderecoSelecionado() === 'cadastrado';

        if (blocoCadastrado) {
            blocoCadastrado.style.display = usarCadastrado ? '' : 'none';
        }

        if (blocoOutro) {
            blocoOutro.style.display = usarCadastrado ? 'none' : '';
        }

        if (campoEndereco) campoEndereco.required = !usarCadastrado;
        if (campoNumero) campoNumero.required = !usarCadastrado;
        if (campoBairro) campoBairro.required = !usarCadastrado;

        limparConfirmacao();

        resultado.textContent = usarCadastrado
            ? 'O endereço cadastrado será utilizado neste pedido. Informe o CEP para calcular o frete.'
            : 'Informe o CEP para preencher rua, bairro, cidade e estado automaticamente.';

        if (!usarCadastrado) {
            cepPreenchidoAutomaticamente = '';
            if (campoCep.value.replace(/\D/g, '').length === 8) {
                preencherEnderecoPeloCep();
            }
        }
    }

    function atualizarEntrega() {

        const retirada = tipoSelecionado() === 'retirada';

        painelEndereco.style.display = retirada ? 'none' : '';
        painelRetirada.style.display = retirada ? '' : 'none';

        campoCep.required = !retirada;

        if (retirada) {

            freteCalculado = false;
            valorFrete = 0;
            cepConsultado = '';

            resultado.textContent =
                'A retirada na loja é gratuita. Confirme para continuar.';

        } else {

            atualizarTipoEndereco();
        }

        limparConfirmacao();
        atualizarResumo();
    }

    radiosEntrega.forEach(function (radio) {
        radio.addEventListener('change', atualizarEntrega);
    });

    radiosEndereco.forEach(function (radio) {
        radio.addEventListener('change', atualizarTipoEndereco);
    });

    botaoConfirmarEntrega.addEventListener('click', async function () {

        if (consultando) return;

        const retirada = tipoSelecionado() === 'retirada';

        if (retirada) {

            freteCalculado = false;
            valorFrete = 0;
            cepConsultado = '';
            confirmado = true;

            resultado.textContent =
                'Sua retirada na Magic Donuts foi confirmada. Não há cobrança de frete!';

            atualizarResumo();
            atualizarBotaoConfirmacao();

            return;
        }

        const usarCadastrado = enderecoSelecionado() === 'cadastrado';
        const cep = campoCep.value.replace(/\D/g, '');

        if (cep.length !== 8) {
            resultado.textContent = 'Digite um CEP válido com 8 números.';
            campoCep.focus();
            limparConfirmacao();
            return;
        }

        if (!usarCadastrado) {

            if (!campoEndereco.value.trim()) {
                resultado.textContent = 'Informe sua rua ou avenida.';
                campoEndereco.focus();
                limparConfirmacao();
                return;
            }

            if (!campoNumero.value.trim()) {
                resultado.textContent = 'Informe o número do endereço.';
                campoNumero.focus();
                limparConfirmacao();
                return;
            }

            if (!campoBairro.value.trim()) {
                resultado.textContent = 'Informe seu bairro.';
                campoBairro.focus();
                limparConfirmacao();
                return;
            }
        }

        consultando = true;
        botaoConfirmarEntrega.disabled = true;

        botaoConfirmarEntrega.innerHTML =
            '<i class="fa-solid fa-spinner fa-spin"></i> Consultando endereço e frete...';

        resultado.textContent =
            'Estamos verificando seu CEP e calculando o frete...';

        limparConfirmacao();

        try {

            const urlCep = new URL(
                <?= json_encode(url_site('cep', 'buscar')) ?>,
                window.location.href
            );

            urlCep.searchParams.set('cep', cep);

            const respostaCep = await fetch(urlCep.toString(), {
                method: 'GET',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            });

            if (!respostaCep.ok) {
                throw new Error('Erro ao consultar o CEP.');
            }

            const dadosCep = await respostaCep.json();

            if (dadosCep.erro || dadosCep.valido === false) {
                throw new Error(dadosCep.mensagem || 'CEP não encontrado.');
            }

            const cidade =
                dadosCep.cidade || dadosCep.localidade || '';

            const estado =
                dadosCep.estado || dadosCep.uf || '';

            if (!cidade || !estado) {
                throw new Error(
                    'A consulta não retornou a cidade e o estado.'
                );
            }

            campoCidade.value = cidade;
            campoEstado.value = estado;

            // Só preenche rua e bairro automaticamente
            // quando o cliente escolheu informar outro endereço.

            if (!usarCadastrado) {

                const logradouro =
                    dadosCep.logradouro || dadosCep.endereco || '';

                const bairro = dadosCep.bairro || '';

                if (logradouro && !campoEndereco.value.trim()) {
                    campoEndereco.value = logradouro;
                }

                if (bairro && !campoBairro.value.trim()) {
                    campoBairro.value = bairro;
                }
            }

            const urlFrete = new URL(
                <?= json_encode(url_site('pedido', 'frete')) ?>,
                window.location.href
            );

            urlFrete.searchParams.set('cep', cep);

            const respostaFrete = await fetch(urlFrete.toString(), {
                method: 'GET',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            });

            if (!respostaFrete.ok) {
                throw new Error('Erro ao calcular o frete.');
            }

            const dadosFrete = await respostaFrete.json();

            if (!dadosFrete.valido) {
                throw new Error(
                    dadosFrete.mensagem || 'Não foi possível atender esse endereço.'
                );
            }

            freteCalculado = true;
            valorFrete = Number(dadosFrete.valor);
            cepConsultado = cep;
            confirmado = true;

            resultado.textContent =
                (dadosFrete.mensagem || 'Endereço confirmado.') +
                ' ' +
                (dadosFrete.valor_fmt || moeda(valorFrete)) +
                (dadosFrete.prazo_texto
                    ? ' ' + dadosFrete.prazo_texto
                    : '');

            atualizarResumo();
            atualizarBotaoConfirmacao();

        } catch (erro) {

            console.error('Erro no checkout:', erro);

            freteCalculado = false;
            valorFrete = 0;
            cepConsultado = '';
            confirmado = false;

            resultado.textContent =
                erro.message ||
                'Não foi possível consultar o CEP ou calcular o frete.';

            atualizarResumo();
            atualizarBotaoConfirmacao();

        } finally {

            consultando = false;
            botaoConfirmarEntrega.disabled = false;

            if (!confirmado) {
                atualizarBotaoConfirmacao();
            }
        }
    });

    function enderecoFoiAlterado() {

        if (tipoSelecionado() !== 'entrega') return;

        limparConfirmacao();

        resultado.textContent =
            'Endereço alterado. Confirme novamente para continuar.';
    }

    // Consulta o CEP assim que o cliente digitar os 8 números.
    // O preenchimento automático é aplicado somente quando ele escolhe
    // informar um endereço diferente do endereço cadastrado.
    let cepPreenchidoAutomaticamente = '';
    let consultandoCepAutomatico = false;

    async function preencherEnderecoPeloCep() {

        const cep = campoCep.value.replace(/\D/g, '');

        if (
            cep.length !== 8 ||
            cep === cepPreenchidoAutomaticamente ||
            consultandoCepAutomatico ||
            tipoSelecionado() !== 'entrega' ||
            enderecoSelecionado() === 'cadastrado'
        ) {
            return;
        }

        consultandoCepAutomatico = true;
        resultado.textContent = 'Buscando o endereço correspondente ao CEP...';

        try {

            const url = new URL(
                <?= json_encode(url_site('cep', 'buscar')) ?>,
                window.location.href
            );

            url.searchParams.set('cep', cep);

            const resposta = await fetch(url.toString(), {
                method: 'GET',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            });

            if (!resposta.ok) {
                throw new Error('Não foi possível consultar esse CEP.');
            }

            const dados = await resposta.json();

            if (dados.erro || dados.valido === false) {
                throw new Error(dados.mensagem || 'CEP não encontrado. Confira os números.');
            }

            const logradouro = dados.logradouro || dados.endereco || '';
            const bairro = dados.bairro || '';
            const cidade = dados.cidade || dados.localidade || '';
            const estado = dados.estado || dados.uf || '';

            if (!cidade || !estado) {
                throw new Error('A consulta não retornou a cidade e o estado.');
            }

            // Atualiza os campos automaticamente. Número e complemento
            // continuam sendo preenchidos pelo cliente.
            campoEndereco.value = logradouro;
            campoBairro.value = bairro;
            campoCidade.value = cidade;
            campoEstado.value = estado;

            cepPreenchidoAutomaticamente = cep;

            freteCalculado = false;
            valorFrete = 0;
            cepConsultado = '';
            limparConfirmacao();
            atualizarResumo();

            resultado.textContent =
                'Endereço localizado! Confira a rua e o bairro, informe o número e o complemento e confirme para calcular o frete.';

        } catch (erro) {

            cepPreenchidoAutomaticamente = '';

            resultado.textContent =
                erro.message || 'Não foi possível preencher o endereço pelo CEP.';

            campoCidade.value = '';
            campoEstado.value = '';

        } finally {
            consultandoCepAutomatico = false;
        }
    }

    campoCep.addEventListener('input', function () {

        const cepAtual = this.value.replace(/\D/g, '');

        if (cepAtual !== cepConsultado) {

            freteCalculado = false;
            valorFrete = 0;
            cepConsultado = '';

            enderecoFoiAlterado();
        }

        if (cepAtual.length < 8) {
            cepPreenchidoAutomaticamente = '';
            campoCidade.value = '';
            campoEstado.value = '';
        }

        if (cepAtual.length === 8) {
            preencherEnderecoPeloCep();
        }
    });

    // Também consulta ao sair do campo, caso o CEP tenha sido colado.
    campoCep.addEventListener('blur', preencherEnderecoPeloCep);

    [
        campoEndereco,
        campoNumero,
        campoComplemento,
        campoBairro
    ].forEach(function (campo) {

        if (!campo) return;

        campo.addEventListener('input', enderecoFoiAlterado);
    });

    form.addEventListener('submit', function (evento) {

        if (!confirmado) {
            evento.preventDefault();

            resultado.textContent =
                'Confirme primeiro seu endereço e frete ou sua retirada na loja.';

            return;
        }

        if (
            tipoSelecionado() === 'entrega' &&
            (
                !freteCalculado ||
                campoCep.value.replace(/\D/g, '') !== cepConsultado
            )
        ) {
            evento.preventDefault();

            confirmado = false;
            atualizarBotaoConfirmacao();

            resultado.textContent =
                'O CEP precisa ser consultado novamente antes de finalizar.';
        }
    });

    atualizarEntrega();

});
</script>
