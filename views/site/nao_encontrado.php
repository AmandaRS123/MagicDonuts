<?php

$tituloPagina = 'Página não encontrada';

?>

<section class="pagina pagina-erro">

    <div class="container">

        <div class="erro-card">

            <div class="erro-ilustracao">
                <i class="fa-solid fa-donut" aria-hidden="true"></i>
            </div>

            <span class="erro-numero">404</span>

            <h1>Ops! Parece que esse donut sumiu!</h1>

            <p>
                Não encontramos a página que você está procurando.
                Mas não se preocupe, ainda temos muitas delícias esperando por você!
            </p>

            <a
                href="<?= e(url_site('loja', 'index')) ?>"
                class="btn"
            >
                <i class="fa-solid fa-house" aria-hidden="true"></i>
                Voltar para a página inicial
            </a>

        </div>

    </div>

</section>