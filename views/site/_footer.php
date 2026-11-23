</main>

<footer class="site-footer">
    <div class="container footer-grade">

        <div class="footer-marca">
            <a href="<?= e(url_site('loja', 'index')) ?>" class="site-logo">
                <img
                    src="/magicdonuts/imagem/logo.jpg"
                    alt="Magic Donuts"
                >

                <span>
                    <strong>Magic Donuts</strong>
                    <small>Um mundo de sabor!</small>
                </span>
            </a>

            <p>
                Donuts deliciosos, feitos com carinho para deixar
                seus momentos ainda mais especiais.
            </p>
        </div>

        <div class="footer-links">
            <h3>Navegação</h3>

            <a href="<?= e(url_site('loja', 'index')) ?>">Início</a>
            <a href="<?= e(url_site('loja', 'index')) ?>#cardapio">Cardápio</a>
            <a href="<?= e(url_site('carrinho', 'index')) ?>">Meu carrinho</a>
            <a href="<?= e(url_site('pedido', 'meus')) ?>">Meus pedidos</a>
        </div>

        <div class="footer-links">
            <h3>Minha conta</h3>

            <?php if (cliente_logado()): ?>

                <a
                    href="<?= e(url_site('cliente', 'conta')) ?>"
                    class="link-minha-conta"
                >
                    Olá, <?= e($_SESSION['cliente_nome'] ?? 'Cliente') ?>!
                </a>

                <a href="<?= e(url_site('cliente', 'conta')) ?>">
                    Minha conta
                </a>

                <a href="<?= e(url_site('pedido', 'meus')) ?>">
                    Acompanhar pedidos
                </a>

                <a href="<?= e(url_site('cliente', 'sair')) ?>">
                    Sair da conta
                </a>

            <?php else: ?>

                <a href="<?= e(url_site('cliente', 'login')) ?>">
                    Entrar
                </a>

                <a href="<?= e(url_site('cliente', 'cadastro')) ?>">
                    Criar cadastro
                </a>

            <?php endif; ?>
        </div>

        <div class="footer-contato">
            <h3>Magic Donuts</h3>

            <p>Uma experiência doce em cada mordida!</p>

            <span>
                <i class="fa-solid fa-heart" aria-hidden="true"></i>
                Feito com carinho.
            </span>
        </div>

    </div>

    <div class="footer-final">
        <div class="container">
            <p>
                &copy; <?= date('Y') ?> Magic Donuts.
                Todos os direitos reservados.
            </p>
        </div>
    </div>

</footer>

<script
    src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js"
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
></script>

<script src="/magicdonuts/public/assets/js/loja.js"></script>

</body>
</html>