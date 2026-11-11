<?php

$tituloPagina = 'Entrar na minha conta';

$email = $_POST['email'] ?? '';

?>

<section class="pagina pagina-autenticacao">

    <div class="container">

        <div class="auth-card">

            <div class="auth-intro">
                <span class="auth-icone">
                    <i class="fa-solid fa-user" aria-hidden="true"></i>
                </span>

                <h1>Que bom ter você de volta!</h1>

                <p>
                    Entre na sua conta para acompanhar seus pedidos
                    e continuar escolhendo seus donuts favoritos.
                </p>
            </div>

            <form
                method="post"
                action="<?= e(url_site('cliente', 'autenticar')) ?>"
                class="form-auth"
            >

                <?= csrf_campo() ?>

                <div class="campo">
                    <label for="email">E-mail</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Digite seu e-mail"
                        value="<?= e($email) ?>"
                        maxlength="100"
                        autocomplete="email"
                        required
                    >
                </div>

                <div class="campo">
                    <label for="senha">Senha</label>

                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        placeholder="Digite sua senha"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-auth">
                    Entrar na minha conta
                </button>

            </form>

            <div class="auth-separador">
                <span>ou</span>
            </div>

            <p class="auth-alternativa">
                Ainda não tem cadastro?

                <a href="<?= e(url_site('cliente', 'cadastro')) ?>">
                    Criar minha conta
                </a>
            </p>

            <a href="<?= e(url_site('loja', 'index')) ?>" class="auth-voltar">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                Voltar para a loja
            </a>

        </div>

    </div>

</section>