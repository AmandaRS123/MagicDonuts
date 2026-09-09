<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">

    <title>Login - Magic Donuts</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="/magicdonuts/public/assets/css/style.css">

    <link rel="icon" type="image/jpeg" href="/magicdonuts/imagem/logo.jpg">


    <!-- Font Awesome - ícones -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
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

            <span></span>

            <p>ou</p>

            <span></span>

        </div>


        <!-- CRIAR CONTA -->
        <a
            href="/magicdonuts/index.php?controller=usuario&action=create"
            class="btn-cadastrar"
        >

            <i class="fa-regular fa-user"></i>

            Criar uma conta

        </a>

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