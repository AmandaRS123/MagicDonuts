
<?php

$itens = $itens ?? [];
$subtotal = (float) ($subtotal ?? 0);
$quantidadeItens = (int) ($quantidadeItens ?? 0);

$faltaFreteGratis = max(0, MD_FRETE_GRATIS_A_PARTIR - $subtotal);

?>

<section class="pagina">
    <div class="container">

        <div class="pagina-titulo">
            <h1>Seu carrinho</h1>

            <?php if (!empty($itens)): ?>
                <p>
                    <?= $quantidadeItens ?>
                    <?= $quantidadeItens === 1 ? 'unidade' : 'unidades' ?>
                    no carrinho.
                </p>
            <?php endif; ?>
        </div>

        <?php if (empty($itens)): ?>

            <div class="vazio">
                <i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>

                <h2>Seu carrinho está vazio</h2>

                <p>Escolha seus donuts favoritos no cardápio e eles aparecem aqui.</p>

                <a class="btn" href="<?= e(url_site('loja', 'index')) ?>">
                    Ver o cardápio
                </a>
            </div>

        <?php else: ?>

            <div class="duas-colunas">

                <form
                    class="painel"
                    id="form-carrinho"
                    method="post"
                    action="<?= e(url_site('carrinho', 'atualizar')) ?>"
                >
                    <?= csrf_campo() ?>

                    <ul class="itens-lista">

                        <?php foreach ($itens as $item): ?>

                            <?php
                            $idVariacao = (int) $item['variacao_id'];

                            $linkProduto = url_site('loja', 'produto', [
                                'id' => (int) $item['produto_id']
                            ]);
                            ?>

                            <li class="item-carrinho">

                                <a
                                    class="item-foto"
                                    href="<?= e($linkProduto) ?>"
                                    tabindex="-1"
                                    aria-hidden="true"
                                >
                                    <img
                                        src="<?= e(imagem_produto((int) $item['produto_id'])) ?>"
                                        alt=""
                                        loading="lazy"
                                        decoding="async"
                                    >
                                </a>

                                <div class="item-info">

                                    <h3>
                                        <a href="<?= e($linkProduto) ?>">
                                            <?= e($item['produto_nome']) ?>
                                        </a>
                                    </h3>

                                    <p>
                                        Tamanho: <?= e($item['tamanho']) ?>
                                        &middot;
                                        <?= e(moeda($item['preco'])) ?> cada
                                    </p>

                                    <div class="item-controles">

                                        <div class="quantidade quantidade-pequena" data-stepper>

                                            <button
                                                type="button"
                                                data-step="-1"
                                                aria-label="Diminuir quantidade de <?= e($item['produto_nome']) ?>"
                                            >
                                                <i class="fa-solid fa-minus" aria-hidden="true"></i>
                                            </button>

                                            <input
                                                type="number"
                                                name="quantidade[<?= $idVariacao ?>]"
                                                value="<?= (int) $item['quantidade'] ?>"
                                                min="1"
                                                max="<?= (int) $item['limite'] ?>"
                                                inputmode="numeric"
                                                aria-label="Quantidade de <?= e($item['produto_nome']) ?>"
                                                data-auto-atualizar
                                            >

                                            <button
                                                type="button"
                                                data-step="1"
                                                aria-label="Aumentar quantidade de <?= e($item['produto_nome']) ?>"
                                            >
                                                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                                            </button>

                                        </div>

                                        <button
                                            class="btn-texto"
                                            type="submit"
                                            name="variacao_id"
                                            value="<?= $idVariacao ?>"
                                            formaction="<?= e(url_site('carrinho', 'remover')) ?>"
                                            formnovalidate
                                        >
                                            Remover
                                        </button>

                                    </div>
                                </div>

                                <div class="item-total">
                                    <strong><?= e(moeda($item['subtotal'])) ?></strong>
                                </div>

                            </li>

                        <?php endforeach; ?>

                    </ul>

                    <div class="carrinho-rodape">

                        <a href="<?= e(url_site('loja', 'index')) ?>">
                            &larr; Continuar comprando
                        </a>

                        <noscript>
                            <button class="btn btn-pequeno btn-contorno" type="submit">
                                Atualizar quantidades
                            </button>
                        </noscript>

                        <button
                            class="btn-texto"
                            type="submit"
                            formaction="<?= e(url_site('carrinho', 'limpar')) ?>"
                            formnovalidate
                            onclick="return confirm('Esvaziar o carrinho?');"
                        >
                            Esvaziar carrinho
                        </button>

                    </div>

                </form>

                <aside class="painel resumo lateral" aria-label="Resumo do pedido">

                    <h2>Resumo do pedido</h2>

                    <dl class="resumo-linhas">

                        <div class="resumo-linha">
                            <dt>Subtotal</dt>
                            <dd id="resumo-subtotal">
                                <?= e(moeda($subtotal)) ?>
                            </dd>
                        </div>

                        <div class="resumo-linha">
                            <dt>Frete</dt>
                            <dd id="resumo-frete">A calcular</dd>
                        </div>

                        <div class="resumo-linha resumo-total">
                            <dt>Total</dt>
                            <dd id="resumo-total">
                                <?= e(moeda($subtotal)) ?>
                            </dd>
                        </div>

                    </dl>

                    <?php if ($faltaFreteGratis > 0): ?>

                        <p class="dica" style="margin-top: 12px;" id="aviso-frete-gratis">
                            Faltam <?= e(moeda($faltaFreteGratis)) ?> para ganhar frete grátis!
                        </p>

                    <?php else: ?>

                        <p class="dica" style="margin-top: 12px;" id="aviso-frete-gratis">
                            Você ganhou frete grátis!
                        </p>

                    <?php endif; ?>

                    <div class="frete-caixa">

                        <h3>Como deseja receber?</h3>

                        <div class="entrega-opcoes">

                            <label class="entrega-opcao">

                                <input
                                    type="radio"
                                    name="tipo_entrega_carrinho"
                                    value="entrega"
                                    checked
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
                                    name="tipo_entrega_carrinho"
                                    value="retirada"
                                >

                                <span>
                                    <i class="fa-solid fa-store" aria-hidden="true"></i>

                                    <strong>Retirar na loja</strong>

                                    <small>
                                        Retirada gratuita em Nova Iguaçu/RJ.
                                    </small>
                                </span>

                            </label>

                        </div>

                    </div>

                    <div class="frete-caixa" id="caixa-calculo-frete">

                        <label for="cep-frete">Calcular frete pelo CEP</label>

                        <div class="frete-form">

                            <input
                                id="cep-frete"
                                type="text"
                                inputmode="numeric"
                                autocomplete="postal-code"
                                placeholder="00000-000"
                                maxlength="9"
                                data-mask="cep"
                            >

                            <button
                                class="btn btn-pequeno btn-contorno"
                                type="button"
                                id="btn-frete"
                            >
                                Calcular
                            </button>

                        </div>

                        <p
                            class="frete-resultado"
                            id="frete-resultado"
                            aria-live="polite"
                        >
                            Calcule o frete para continuar com seu pedido.
                        </p>

                    </div>

                    <div class="resumo-acoes">

                        <a
                            class="btn btn-bloco"
                            id="btn-finalizar-carrinho"
                            href="#"
                            aria-disabled="true"
                            tabindex="-1"
                            style="opacity: .55; cursor: not-allowed;"
                        >
                            Calcule o frete para continuar
                        </a>

                    </div>

                    <?php if (!cliente_logado()): ?>

                        <p class="dica" style="margin-top: 12px;">
                            Você entra na sua conta ou cria uma na próxima etapa.
                        </p>

                    <?php endif; ?>

                </aside>

            </div>

        <?php endif; ?>

    </div>
</section>

<?php if (!empty($itens)): ?>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ELEMENTOS */

    const form = document.getElementById('form-carrinho');

    const campoCep = document.getElementById('cep-frete');
    const botaoFrete = document.getElementById('btn-frete');
    const resultado = document.getElementById('frete-resultado');

    const linhaFrete = document.getElementById('resumo-frete');
    const linhaTotal = document.getElementById('resumo-total');

    const avisoFreteGratis = document.getElementById('aviso-frete-gratis');

    const caixaCalculoFrete = document.getElementById('caixa-calculo-frete');

    const botaoFinalizar = document.getElementById('btn-finalizar-carrinho');

    const subtotalInicial = <?= json_encode($subtotal) ?>;

    const linkEntrega = <?= json_encode(url_site('pedido', 'checkout', [
        'tipo_entrega' => 'entrega'
    ])) ?>;

    const linkRetirada = <?= json_encode(url_site('pedido', 'checkout', [
        'tipo_entrega' => 'retirada'
    ])) ?>;

    let temporizador = null;

    let freteCalculado = false;
    let valorFrete = 0;
    let cepConsultado = '';

    /* FORMATAÇÃO */

    function moeda(valor) {

        return Number(valor).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });

    }

    function tipoSelecionado() {

        return document.querySelector(
            'input[name="tipo_entrega_carrinho"]:checked'
        )?.value || 'entrega';

    }

    /* CONTROLE DO BOTÃO FINALIZAR */

    function atualizarBotaoFinalizar() {

        const retirada = tipoSelecionado() === 'retirada';

        const liberado = retirada || (
            freteCalculado &&
            cepConsultado.length === 8
        );

        if (liberado) {

            botaoFinalizar.href = retirada
                ? linkRetirada
                : linkEntrega;

            botaoFinalizar.setAttribute('aria-disabled', 'false');
            botaoFinalizar.removeAttribute('tabindex');

            botaoFinalizar.style.opacity = '1';
            botaoFinalizar.style.cursor = 'pointer';

            botaoFinalizar.textContent = retirada
                ? 'Finalizar pedido - Retirada grátis'
                : 'Finalizar pedido';

        } else {

            botaoFinalizar.href = '#';

            botaoFinalizar.setAttribute('aria-disabled', 'true');
            botaoFinalizar.setAttribute('tabindex', '-1');

            botaoFinalizar.style.opacity = '.55';
            botaoFinalizar.style.cursor = 'not-allowed';

            botaoFinalizar.textContent = 'Calcule o frete para continuar';

        }

    }

    /* RESUMO */

    function atualizarResumo() {

        const tipo = tipoSelecionado();

        if (tipo === 'retirada') {

            linhaFrete.textContent = 'Grátis';
            linhaTotal.textContent = moeda(subtotalInicial);

            atualizarBotaoFinalizar();

            return;
        }

        if (!freteCalculado) {

            linhaFrete.textContent = 'A calcular';
            linhaTotal.textContent = moeda(subtotalInicial);

            atualizarBotaoFinalizar();

            return;
        }

        linhaFrete.textContent = valorFrete === 0
            ? 'Grátis'
            : moeda(valorFrete);

        linhaTotal.textContent = moeda(
            subtotalInicial + valorFrete
        );

        atualizarBotaoFinalizar();

    }

    /* OPÇÕES DE ENTREGA */

    function atualizarTipoEntrega() {

        const retirada = tipoSelecionado() === 'retirada';

        caixaCalculoFrete.style.display = retirada ? 'none' : '';

        if (retirada) {

            resultado.textContent =
                'Retirada na loja selecionada. Você não pagará frete.';

            resultado.classList.remove('erro');

        } else {

            resultado.textContent = freteCalculado
                ? resultado.textContent
                : 'Calcule o frete para continuar com seu pedido.';

        }

        atualizarResumo();

    }

    document.querySelectorAll(
        'input[name="tipo_entrega_carrinho"]'
    ).forEach(function (radio) {

        radio.addEventListener('change', atualizarTipoEntrega);

    });

    /* BLOQUEIA A FINALIZAÇÃO SE NÃO HOUVER CÁLCULO */

    botaoFinalizar.addEventListener('click', function (evento) {

        if (botaoFinalizar.getAttribute('aria-disabled') === 'true') {

            evento.preventDefault();

            if (tipoSelecionado() === 'entrega') {

                resultado.textContent =
                    'Antes de finalizar, informe seu CEP e calcule o frete.';

                resultado.classList.add('erro');

                campoCep.focus();

            }

            return;
        }

    });

    /* BOTÕES DE AUMENTAR E DIMINUIR */

    document.querySelectorAll('[data-stepper]').forEach(function (stepper) {

        const input = stepper.querySelector('input[type="number"]');

        const botaoMenos = stepper.querySelector(
            'button[data-step="-1"]'
        );

        const botaoMais = stepper.querySelector(
            'button[data-step="1"]'
        );

        if (!input) return;

        function alterarQuantidade(passo) {

            const minimo = Number(input.min) || 1;
            const maximo = Number(input.max) || 999;
            const atual = Number(input.value) || minimo;

            const novaQuantidade = Math.max(
                minimo,
                Math.min(atual + passo, maximo)
            );

            if (novaQuantidade === atual) return;

            input.value = novaQuantidade;

            input.dispatchEvent(new Event('change', {
                bubbles: true
            }));

        }

        if (botaoMenos) {

            botaoMenos.addEventListener('click', function (evento) {

                evento.preventDefault();

                alterarQuantidade(-1);

            });

        }

        if (botaoMais) {

            botaoMais.addEventListener('click', function (evento) {

                evento.preventDefault();

                alterarQuantidade(1);

            });

        }

        input.addEventListener('change', function () {

            const minimo = Number(input.min) || 1;
            const maximo = Number(input.max) || 999;
            const valor = Number(input.value) || minimo;

            input.value = Math.max(
                minimo,
                Math.min(valor, maximo)
            );

        });

    });

    /* ATUALIZAÇÃO AUTOMÁTICA DO CARRINHO */

    form.addEventListener('change', function (evento) {

        if (!evento.target.hasAttribute('data-auto-atualizar')) {
            return;
        }

        // O subtotal mudou. O frete anterior não pode ser aproveitado.
        freteCalculado = false;
        valorFrete = 0;
        cepConsultado = '';

        atualizarResumo();

        window.clearTimeout(temporizador);

        temporizador = window.setTimeout(function () {

            form.submit();

        }, 700);

    });

    /* CÁLCULO DE FRETE */

    async function calcularFrete() {

        const cep = campoCep.value.replace(/\D/g, '');

        if (cep.length !== 8) {

            resultado.textContent = 'Informe um CEP com 8 dígitos.';
            resultado.classList.add('erro');

            freteCalculado = false;
            valorFrete = 0;
            cepConsultado = '';

            atualizarResumo();

            return;
        }

        botaoFrete.disabled = true;

        resultado.textContent = 'Calculando frete...';
        resultado.classList.remove('erro');

        try {

            const urlFrete = new URL(
                <?= json_encode(url_site('pedido', 'frete')) ?>,
                window.location.href
            );

            urlFrete.searchParams.set('cep', cep);

            const resposta = await fetch(urlFrete.toString(), {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json'
                }
            });

            const textoResposta = await resposta.text();

            let dados;

            try {

                dados = JSON.parse(textoResposta);

            } catch (erroJson) {

                console.error('Resposta recebida do PHP:', textoResposta);

                throw new Error('O servidor não retornou um JSON válido.');

            }

            if (!resposta.ok) {
                throw new Error('Erro HTTP: ' + resposta.status);
            }

            if (!dados.valido) {

                freteCalculado = false;
                valorFrete = 0;
                cepConsultado = '';

                resultado.textContent =
                    dados.mensagem || 'Não foi possível calcular o frete.';

                resultado.classList.add('erro');

                atualizarResumo();

                return;
            }

            freteCalculado = true;
            valorFrete = Number(dados.valor);
            cepConsultado = cep;

            resultado.textContent =
                dados.mensagem + ' ' + (dados.prazo_texto || '');

            resultado.classList.remove('erro');

            if (valorFrete === 0) {

                avisoFreteGratis.textContent =
                    'Seu pedido tem frete grátis!';

            } else {

                const falta = Math.max(
                    0,
                    60 - subtotalInicial
                );

                avisoFreteGratis.textContent = falta > 0
                    ? 'Faltam ' + moeda(falta) + ' para ganhar frete grátis!'
                    : 'Você ganhou frete grátis!';

            }

            atualizarResumo();

        } catch (erro) {

            console.error('Erro ao calcular frete:', erro);

            freteCalculado = false;
            valorFrete = 0;
            cepConsultado = '';

            resultado.textContent =
                'Não foi possível calcular o frete agora. Tente novamente.';

            resultado.classList.add('erro');

            atualizarResumo();

        } finally {

            botaoFrete.disabled = false;

        }

    }

    botaoFrete.addEventListener('click', calcularFrete);

    campoCep.addEventListener('keydown', function (evento) {

        if (evento.key === 'Enter') {

            evento.preventDefault();

            calcularFrete();

        }

    });

    /* SE O CLIENTE ALTERAR O CEP, EXIGE NOVO CÁLCULO */

    campoCep.addEventListener('input', function () {

        const cepAtual = this.value.replace(/\D/g, '');

        if (cepAtual !== cepConsultado) {

            freteCalculado = false;
            valorFrete = 0;
            cepConsultado = '';

            resultado.textContent =
                'CEP alterado. Calcule novamente o frete para continuar.';

            resultado.classList.remove('erro');

            atualizarResumo();

        }

    });

    /* INICIALIZAÇÃO */

    atualizarTipoEntrega();

});
</script>

<?php endif; ?>
