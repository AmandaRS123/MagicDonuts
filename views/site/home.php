<?php

$categorias = $categorias ?? [];
$produtos = $produtos ?? [];
$destaques = $destaques ?? [];
$categoriaId = $categoriaId ?? null;
$categoriaAtual = $categoriaAtual ?? null;
$busca = $busca ?? '';
$ordem = $ordem ?? 'relevancia';
$filtrando = $filtrando ?? false;

// Monta links do filtro mantendo busca e ordenação.
$urlFiltro = static function (?int $categoria) use ($busca, $ordem): string {
    $params = [];

    if ($categoria !== null) {
        $params['categoria'] = $categoria;
    }

    if ($busca !== '') {
        $params['q'] = $busca;
    }

    if ($ordem !== 'relevancia') {
        $params['ordem'] = $ordem;
    }

    return url_site('loja', 'index', $params) . '#cardapio';
};

$fotosHero = [];

foreach ($destaques as $d) {
    $foto = imagem_produto((int) $d['id'], false);

    if ($foto !== null) {
        $fotosHero[] = ['src' => $foto, 'nome' => $d['nome']];
    }
}

?>

<?php if (!$filtrando): ?>

    <section class="hero">
        <div class="container hero-grade">

            <div>
                <h1>Donuts artesanais, entregues fresquinhos no Rio.</h1>

                <p class="hero-texto">
                    Escolha o sabor e a opção, informe seu CEP e receba em casa.
                    Pague com Pix, cartão de crédito ou boleto.
                </p>

                <p id="clima-home" class="hero-texto" style="display:none; font-weight:600;"></p>

                <div class="hero-acoes">
                    <a class="btn" href="#cardapio">Ver o cardápio</a>

                    <?php if (!cliente_logado()): ?>
                        <a class="btn btn-contorno" href="<?= e(url_site('cliente', 'cadastro')) ?>">
                            Criar minha conta
                        </a>
                    <?php else: ?>
                        <a class="btn btn-contorno" href="<?= e(url_site('pedido', 'meus')) ?>">
                            Meus pedidos
                        </a>
                    <?php endif; ?>
                </div>

                <ul class="hero-fatos">
                    <li><i class="fa-solid fa-truck-fast" aria-hidden="true"></i> Entrega em todo o RJ</li>
                    <li><i class="fa-solid fa-gift" aria-hidden="true"></i> Frete grátis a partir de <?= e(moeda(MD_FRETE_GRATIS_A_PARTIR)) ?></li>
                    <li><i class="fa-solid fa-credit-card" aria-hidden="true"></i> Pix, cartão ou boleto</li>
                </ul>
            </div>

            <div class="hero-visual" aria-hidden="true">

                <div class="anel">
                    <img
                        src="<?= e($fotosHero[0]['src'] ?? 'imagem/logo.jpg') ?>"
                        alt=""
                        decoding="async"
                    >
                </div>

                <?php if (isset($fotosHero[1])): ?>
                    <div class="satelite satelite-a">
                        <img src="<?= e($fotosHero[1]['src']) ?>" alt="" loading="lazy" decoding="async">
                    </div>
                <?php endif; ?>

                <?php if (isset($fotosHero[2])): ?>
                    <div class="satelite satelite-b">
                        <img src="<?= e($fotosHero[2]['src']) ?>" alt="" loading="lazy" decoding="async">
                    </div>
                <?php endif; ?>

                <span class="confete confete-1"></span>
                <span class="confete confete-2"></span>
                <span class="confete confete-3"></span>
                <span class="confete confete-4"></span>

            </div>

        </div>
    </section>

    <div class="gotejo" aria-hidden="true"></div>

<?php endif; ?>

<section class="cardapio" id="cardapio">
    <div class="container">

        <div class="cardapio-topo">
            <div>
                <h2>
                    <?php if ($categoriaAtual): ?>
                        <?= e($categoriaAtual['nome']) ?>
                    <?php elseif ($busca !== ''): ?>
                        Resultados da busca
                    <?php else: ?>
                        Cardápio
                    <?php endif; ?>
                </h2>

                <p>
                    <?php if ($busca !== ''): ?>
                        <?= count($produtos) ?> resultado(s) para &ldquo;<?= e($busca) ?>&rdquo;.
                    <?php else: ?>
                        Escolha seu item e adicione ao carrinho.
                    <?php endif; ?>
                </p>
            </div>

            <form class="ordenar" method="get" action="index.php">
                <input type="hidden" name="controller" value="loja">
                <input type="hidden" name="action" value="index">

                <?php if ($categoriaId !== null): ?>
                    <input type="hidden" name="categoria" value="<?= (int) $categoriaId ?>">
                <?php endif; ?>

                <?php if ($busca !== ''): ?>
                    <input type="hidden" name="q" value="<?= e($busca) ?>">
                <?php endif; ?>

                <label for="ordem">Ordenar por</label>

                <select id="ordem" name="ordem" onchange="this.form.submit()">
                    <option value="relevancia" <?= $ordem === 'relevancia' ? 'selected' : '' ?>>Novidades</option>
                    <option value="menor" <?= $ordem === 'menor' ? 'selected' : '' ?>>Menor preço</option>
                    <option value="maior" <?= $ordem === 'maior' ? 'selected' : '' ?>>Maior preço</option>
                    <option value="nome" <?= $ordem === 'nome' ? 'selected' : '' ?>>Nome (A–Z)</option>
                </select>

                <noscript><button class="btn btn-pequeno" type="submit">Aplicar</button></noscript>
            </form>
        </div>

        <?php if (!empty($categorias)): ?>
            <nav class="filtros" aria-label="Categorias">
                <a
                    class="filtro <?= $categoriaId === null ? 'ativo' : '' ?>"
                    href="<?= e($urlFiltro(null)) ?>"
                    <?= $categoriaId === null ? 'aria-current="true"' : '' ?>
                >
                    Todos
                </a>

                <?php foreach ($categorias as $categoria): ?>
                    <?php $ativa = ((int) $categoria['id'] === $categoriaId); ?>

                    <a
                        class="filtro <?= $ativa ? 'ativo' : '' ?>"
                        href="<?= e($urlFiltro((int) $categoria['id'])) ?>"
                        <?= $ativa ? 'aria-current="true"' : '' ?>
                    >
                        <?= e($categoria['nome']) ?>
                        <small><?= (int) $categoria['total'] ?></small>
                    </a>
                <?php endforeach; ?>
            </nav>
        <?php endif; ?>

        <?php if (empty($produtos)): ?>

            <div class="vazio">
                <i class="fa-regular fa-face-frown" aria-hidden="true"></i>

                <h2>Nenhum donut encontrado</h2>

                <p>
                    Tente outra palavra ou veja o cardápio completo.
                </p>

                <a class="btn" href="<?= e(url_site('loja', 'index')) ?>">Ver todos os donuts</a>
            </div>

        <?php else: ?>

            <div class="grade-produtos">
                <?php foreach ($produtos as $produto): ?>
                    <?php
                    $idProduto = (int) $produto['id'];
                    $disponivel = (int) $produto['disponivel'] === 1;
                    $temVariacao = (int) $produto['qtd_variacoes'] > 0;
                    $link = url_site('loja', 'produto', ['id' => $idProduto]);

                    $precoMin = (float) $produto['preco_min'];
                    $precoMax = (float) $produto['preco_max'];
                    ?>

                    <article class="cartao-produto <?= $disponivel ? '' : 'indisponivel' ?>">

                        <a class="cartao-foto" href="<?= e($link) ?>" tabindex="-1" aria-hidden="true">
                            <img
                                src="<?= e(imagem_produto($idProduto)) ?>"
                                alt=""
                                loading="lazy"
                                decoding="async"
                            >

                            <?php if (!$temVariacao): ?>
                                <span class="selo selo-estado">Em breve</span>
                            <?php elseif (!$disponivel): ?>
                                <span class="selo selo-estado">Esgotado</span>
                            <?php endif; ?>

                            <span class="selo selo-categoria"><?= e($produto['categoria_nome']) ?></span>
                        </a>

                        <div class="cartao-corpo">
                            <h3><a href="<?= e($link) ?>"><?= e($produto['nome']) ?></a></h3>

                            <?php if (!empty($produto['descricao'])): ?>
                                <p class="cartao-desc"><?= e($produto['descricao']) ?></p>
                            <?php endif; ?>

                            <div class="cartao-rodape">
                                <?php if ($temVariacao): ?>
                                    <p class="preco-linha">
                                        <?php if ($precoMin !== $precoMax): ?>
                                            <small>a partir de</small>
                                        <?php endif; ?>
                                        <strong><?= e(moeda($precoMin)) ?></strong>
                                    </p>

                                    <a class="btn btn-pequeno <?= $disponivel ? '' : 'btn-contorno' ?>" href="<?= e($link) ?>">
                                        <?= $disponivel ? 'Escolher' : 'Ver detalhes' ?>
                                    </a>
                                <?php else: ?>
                                    <p class="preco-nd">Em breve no cardápio</p>
                                <?php endif; ?>
                            </div>
                        </div>

                    </article>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>

    </div>
</section>

<script>
// CLIMA DO DIA (API Open-Meteo: gratuita e sem chave)
document.addEventListener('DOMContentLoaded', async function () {

    const caixa = document.getElementById('clima-home');

    if (!caixa) {
        return;
    }

    // Coordenadas do centro do Rio de Janeiro.
    const url = 'https://api.open-meteo.com/v1/forecast'
        + '?latitude=-22.9068&longitude=-43.1729'
        + '&current=temperature_2m,weather_code'
        + '&timezone=America%2FSao_Paulo';

    try {
        const resposta = await fetch(url);

        if (!resposta.ok) {
            throw new Error('Falha ao consultar o clima.');
        }

        const dados = await resposta.json();
        const temperatura = Math.round(dados.current.temperature_2m);
        const codigo = dados.current.weather_code;

        const chuva = [51, 53, 55, 56, 57, 61, 63, 65, 66, 67,
                       80, 81, 82, 95, 96, 99].includes(codigo);

        let mensagem;

        if (chuva) {
            mensagem = '🌧️ ' + temperatura + '°C e chovendo no Rio: dia perfeito para donut com um suco natural! '
                     + 'A entrega pode demorar um pouco mais.';
        } else if (temperatura >= 28) {
            mensagem = '☀️ ' + temperatura + '°C no Rio hoje: que tal um donut com cola-cola geladinha?';
        } else if (temperatura <= 20) {
            mensagem = '🧥 ' + temperatura + '°C no Rio hoje: donut quentinho combina com o friozinho!';
        } else {
            mensagem = '🌤️ ' + temperatura + '°C no Rio hoje: ótimo clima para um donut!';
        }

        caixa.textContent = mensagem;
        caixa.style.display = 'block';

    } catch (erro) {
        // Se a API falhar, a página funciona normalmente, só sem o aviso.
        caixa.style.display = 'none';
    }
});
</script>