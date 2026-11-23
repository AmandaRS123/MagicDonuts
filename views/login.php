
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">

    <title>Login - Magic Donuts</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="/magicdonuts/public/assets/css/style.css">

    <link rel="icon" type="image/jpeg" href="/magicdonuts/imagem/logo.jpg">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
       .login-header {
    position: relative;
    z-index: 0;
}

.login-alerta {
    position: relative;
    z-index: 2;

    width: calc(100% - 40px);
    box-sizing: border-box;

    padding: 14px 16px;
    margin: 24px auto 18px;

    border-radius: 10px;

    font-size: 13px;
    line-height: 1.5;

    display: flex;
    align-items: center;
    gap: 10px;
}

.login-alerta.inativo {
    background: #f1eafa;
    color: #60429e;
    border: 1px solid #e5d8fa;
}

.login-alerta.erro {
    background: #fff0f0;
    color: #a33d50;
    border: 1px solid #f5d5dc;
}

.login-alerta i {
    flex-shrink: 0;
    font-size: 16px;
}

.login-alerta strong {
    display: inline-block;
    margin-bottom: 2px;
}
    </style>

</head>

<body>

<div class="login-container">

    <div class="login-card">

        <!-- CABEÇALHO -->
        <div class="login-header">

            <img
                src="/magicdonuts/imagem/logo.jpg"
                alt="Magic Donuts"
                class="login-logo"
            >

            <div class="login-title">
                <h1>Magic Donuts</h1>
                <p>Faça login para continuar</p>
            </div>

        </div>

        <!-- MENSAGENS DE ERRO -->
        <?php if (($_GET['erro'] ?? '') === 'inativo'): ?>

            <div class="login-alerta inativo">
                <i class="fa-solid fa-circle-exclamation"></i>

                <div>
                    <strong>Conta inativa!</strong><br>
                    Sua conta está desativada. Entre em contato com o administrador para solicitar a reativação.
                </div>
            </div>

        <?php elseif (($_GET['erro'] ?? '') === 'credenciais'): ?>

            <div class="login-alerta erro">
                <i class="fa-solid fa-triangle-exclamation"></i>

                <div>
                    <strong>Não foi possível entrar.</strong><br>
                    E-mail ou senha incorretos. Verifique seus dados e tente novamente.
                </div>
            </div>

        <?php endif; ?>

        <!-- FORMULÁRIO -->
        <form
            method="post"
            action="/magicdonuts/index.php?controller=auth&action=login"
        >

            <!-- EMAIL -->
            <div class="input-group">

                <div class="input-icon">
                    <i class="fa-regular fa-envelope"></i>
                </div>

                <input
                    type="email"
                    name="email"
                    placeholder="Email"
                    required
                >

            </div>

            <!-- SENHA -->
            <div class="input-group">

                <div class="input-icon">
                    <i class="fa-solid fa-lock"></i>
                </div>

                <input
                    type="password"
                    name="senha"
                    id="senha"
                    placeholder="Senha"
                    required
                >

                <button
                    type="button"
                    class="show-password"
                    onclick="mostrarSenha()"
                >
                    <i class="fa-regular fa-eye" id="eye"></i>
                </button>

            </div>

            <!-- ENTRAR -->
            <button
                class="btn-entrar"
                type="submit"
            >

                <i class="fa-solid fa-arrow-right-to-bracket"></i>

                Entrar

            </button>

        </form>

        <!-- OU -->
        <div class="divider">
        </div>

    </div>

</div>

<script>

function mostrarSenha() {

    const senha = document.getElementById("senha");
    const eye = document.getElementById("eye");

    if (senha.type === "password") {

        senha.type = "text";

        eye.classList.remove("fa-eye");
        eye.classList.add("fa-eye-slash");

    } else {

        senha.type = "password";

        eye.classList.remove("fa-eye-slash");
        eye.classList.add("fa-eye");

    }

}

</script>

</body>
</html>
