
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastrar Funcionário - Magic Donuts</title>

    <link rel="stylesheet" href="/magicdonuts/public/assets/css/style.css">
    <link rel="icon" type="image/jpeg" href="/magicdonuts/imagem/logo.jpg">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>

<div class="cadastro-container">
    <div class="cadastro-card">

        <div class="brand">
            <img src="/magicdonuts/imagem/logo.jpg" class="logo" alt="Logo Magic Donuts">

            <div>
                <h1>Magic Donuts</h1>
                <small>Cadastro de funcionário</small>
            </div>
        </div>

        <form action="index.php?controller=usuario&action=store" method="POST">

            <label for="nome">Nome</label>
            <input
                type="text"
                id="nome"
                name="nome"
                placeholder="Nome completo"
                maxlength="100"
                required
            >

            <label for="email">E-mail</label>
            <input
                type="email"
                id="email"
                name="email"
                placeholder="E-mail"
                maxlength="100"
                required
            >

            <label for="senha">Senha</label>
            <input
                type="password"
                id="senha"
                name="senha"
                placeholder="Mínimo de 6 caracteres"
                minlength="6"
                required
            >

            <div class="buttons">
                <button type="submit" class="btn cadastrar-btn">
                    Cadastrar funcionário
                </button>

                <a
                    href="index.php?controller=usuario&action=index"
                    class="btn voltar-btn"
                >
                    Voltar
                </a>
            </div>

        </form>

    </div>
</div>

</body>
</html>
