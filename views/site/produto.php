<?php

$produto = $produto ?? [];
$relacionados = $relacionados ?? [];

$idProduto = (int) ($produto['id'] ?? 0);
$variacoes = $produto['variacoes'] ?? [];

$disponiveis = array_filter($variacoes, static function ($v) {
    return (int) $v['estoque'] > 0;
});

$primeira = reset($disponiveis);
$primeiraId = $primeira ? (int) $primeira['id'] : 0;

$precoInicial = $primeira
    ? (float) $primeira['preco']
    : (!empty($variacoes) ? (float) $variacoes[0]['preco'] : 0.0);

$estoqueInicial = $primeira ? (int) $primeira['estoque'] : 0;
$limiteInicial = max(1, min($estoqueInicial, Carrinho::MAX_POR_ITEM));

$urlProduto = url_site('loja', 'produto', ['id' => $idProduto]);

?>

<section class="pagina">
    <div class="container">

        <nav class="migalhas" aria-label="Você está em">
            <a href="<?= e(url_site('loja', 'index')) ?>">Cardápio</a>
            <span aria-hidden="true">/</span>
            <a href="<?= e(url_site('loja', 'index', ['categoria' => (int) $produto['categoria_id']]) . '#cardapio') ?>">
                <?= e($produto['categoria_nome']) ?>
            </a>
            <span aria-hidden="true">/</span>
            <span aria-current="page"><?= e($produto['nome']) ?></span>
        </nav>

        <div class="produto-grade">

            <div class="produto-foto">
                <img
                    src="<?= e(imagem_produto($idProduto)) ?>"
                    alt="<?= e($produto['nome']) ?>"
                    decoding="async"
                >
            </div>

            <div class="produto-info">

                <a
                    class="produto-categoria"
                    href="<?= e(url_site('loja', 'index', ['categoria' => (int) $produto['categoria_id']]) . '#cardapio') ?>"
                >
                    <?= e($produto['categoria_nome']) ?>
                </a>

                <h1><?= e($produto['nome']) ?></h1>

                <?php if (!empty($produto['descricao'])): ?>
                    <p class="produto-desc"><?= nl2br(e($produto['descricao'])) ?></p>
                <?php endif; ?>

                <?php if (empty($variacoes)): ?>

                    <div class="aviso aviso-info" style="margin-top: 24px;">
                        Este donut ainda não está disponível para pedido. Volte em breve!
                    </div>

                    <p style="margin-top: 20px;">
                        <a class="btn btn-contorno" href="<?= e(url_site('loja', 'index')) ?>">
                            Ver outros donuts
                        </a>
                    </p>

                <?php else: ?>

                    <p class="produto-preco">
                        <strong id="preco-atual"><?= e(moeda($precoInicial)) ?></strong>
                        <small>por unidade</small>
                    </p>

                    <?php if (empty($disponiveis)): ?>
                        <div class="aviso aviso-aviso" style="margin-top: 12px;">
                            Todos os tamanhos estão esgotados no momento.
                        </div>
                    <?php endif; ?>

                    <form
                        method="post"
                        action="<?= e(url_site('carrinho', 'adicionar')) ?>"
                        data-ajax-carrinho
                    >
                        <?= csrf_campo() ?>
                        <input type="hidden" name="retorno" value="<?= e($urlProduto) ?>">

                        <fieldset class="opcoes">
                            <legend>Tamanho</legend>

                            <div class="opcoes-lista">
                                <?php foreach ($variacoes as $v): ?>
                                    <?php
                                    $idVariacao = (int) $v['id'];
                                    $estoque = (int) $v['estoque'];
                                    ?>

                                    <label class="opcao">
                                        <input
                                            type="radio"
                                            name="variacao_id"
                                            value="<?= $idVariacao ?>"
                                            data-preco="<?= e(moeda($v['preco'])) ?>"
                                            data-estoque="<?= $estoque ?>"
                                            <?= $idVariacao === $primeiraId ? 'checked' : '' ?>
                                            <?= $estoque <= 0 ? 'disabled' : '' ?>
                                            required
                                        >

                                        <span class="opcao-corpo">
                                            <b><?= e($v['tamanho']) ?></b>
                                            <em><?= e(moeda($v['preco'])) ?></em>

                                            <?php if ($estoque <= 0): ?>
                                                <small>Esgotado</small>
                                            <?php elseif ($estoque <= 5): ?>
                                                <small class="pouco">Restam <?= $estoque ?></small>
                                            <?php else: ?>
                                                <small>Disponível</small>
                                            <?php endif; ?>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </fieldset>

                        <div class="linha-compra">

                            <div class="quantidade" data-stepper>
                                <button type="button" data-step="-1" aria-label="Diminuir quantidade">
                                    <i class="fa-solid fa-minus" aria-hidden="true"></i>
                                </button>

                                <input
                                    id="quantidade"
                                    type="number"
                                    name="quantidade"
                                    value="1"
                                    min="1"
                                    max="<?= $limiteInicial ?>"
                                    inputmode="numeric"
                                    aria-label="Quantidade"
                                >

                                <button type="button" data-step="1" aria-label="Aumentar quantidade">
                                    <i class="fa-solid fa-plus" aria-hidden="true"></i>
                                </button>
                            </div>

                            <button
                                class="btn"
                                type="submit"
                                <?= empty($disponiveis) ? 'disabled' : '' ?>
                            >
                                <i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
                                Adicionar ao carrinho
                            </button>

                            <button
                                class="btn btn-contorno"
                                type="submit"
                                name="comprar"
                                value="1"
                                <?= empty($disponiveis) ? 'disabled' : '' ?>
                            >
                                Comprar agora
                            </button>

                        </div>
                    </form>

                    <ul class="entrega-nota">
                        <li>
                            <i class="fa-solid fa-truck-fast" aria-hidden="true"></i>
                            <span>Entrega em todo o estado do RJ. Calcule o frete pelo CEP no carrinho.</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-gift" aria-hidden="true"></i>
                            <span>Frete grátis em pedidos a partir de <?= e(moeda(MD_FRETE_GRATIS_A_PARTIR)) ?>.</span>
                        </li>
                    </ul>

                <?php endif; ?>

            </div>
        </div>

        <?php if (!empty($relacionados)): ?>
            <section class="relacionados" aria-labelledby="titulo-relacionados">
                <h2 id="titulo-relacionados">Mais de <?= e($produto['categoria_nome']) ?></h2>

                <div class="grade-produtos">
                    <?php foreach ($relacionados as $rel): ?>
                        <?php
                        $idRel = (int) $rel['id'];
                        $linkRel = url_site('loja', 'produto', ['id' => $idRel]);
                        $relDisp = (int) $rel['disponivel'] === 1;
                        $relTem = (int) $rel['qtd_variacoes'] > 0;
                        ?>

                        <article class="cartao-produto <?= $relDisp ? '' : 'indisponivel' ?>">
                            <a class="cartao-foto" href="<?= e($linkRel) ?>" tabindex="-1" aria-hidden="true">
                                <img src="<?= e(imagem_produto($idRel)) ?>" alt="" loading="lazy" decoding="async">

                                <?php if (!$relTem): ?>
                                    <span class="selo selo-estado">Em breve</span>
                                <?php elseif (!$relDisp): ?>
                                    <span class="selo selo-estado">Esgotado</span>
                                <?php endif; ?>
                            </a>

                            <div class="cartao-corpo">
                                <h3><a href="<?= e($linkRel) ?>"><?= e($rel['nome']) ?></a></h3>

                                <div class="cartao-rodape">
                                    <?php if ($relTem): ?>
                                        <p class="preco-linha">
                                            <?php if ((float) $rel['preco_min'] !== (float) $rel['preco_max']): ?>
                                                <small>a partir de</small>
                                            <?php endif; ?>
                                            <strong><?= e(moeda($rel['preco_min'])) ?></strong>
                                        </p>

                                        <a class="btn btn-pequeno btn-contorno" href="<?= e($linkRel) ?>">Ver</a>
                                    <?php else: ?>
                                        <p class="preco-nd">Em breve no cardápio</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('.produto-info form');

    if (!form) { return; }

    var precoAtual = document.getElementById('preco-atual');
    var quantidade = document.getElementById('quantidade');
    var limiteMaximo = <?= (int) Carrinho::MAX_POR_ITEM ?>;

    function atualizar() {
        var escolhida = form.querySelector('input[name="variacao_id"]:checked');

        if (!escolhida) { return; }

        if (precoAtual) { precoAtual.textContent = escolhida.getAttribute('data-preco'); }

        var estoque = Number(escolhida.getAttribute('data-estoque')) || 1;

        quantidade.max = Math.max(1, Math.min(estoque, limiteMaximo));

        if (Number(quantidade.value) > Number(quantidade.max)) {
            quantidade.value = quantidade.max;
        }
    }

    form.addEventListener('change', function (evento) {
        if (evento.target.name === 'variacao_id') { atualizar(); }
    });

    atualizar();
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('[data-stepper]').forEach(function (stepper) {

        const input = stepper.querySelector('input[name="quantidade"]');
        const botoes = stepper.querySelectorAll('button[data-step]');

        if (!input) return;

        botoes.forEach(function (botao) {

            botao.addEventListener('click', function () {

                const passo = Number(botao.dataset.step);
                const minimo = Number(input.min) || 1;
                const maximo = Number(input.max) || 1;
                let quantidade = Number(input.value) || minimo;

                quantidade += passo;

                if (quantidade < minimo) {
                    quantidade = minimo;
                }

                if (quantidade > maximo) {
                    quantidade = maximo;
                }

                input.value = quantidade;

                input.dispatchEvent(new Event('change', {
                    bubbles: true
                }));

            });

        });

        input.addEventListener('change', function () {
            const minimo = Number(input.min) || 1;
            const maximo = Number(input.max) || 1;

            let valor = Number(input.value) || minimo;

            valor = Math.max(minimo, Math.min(valor, maximo));

            input.value = valor;
        });

    });

});
</script>