<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
      <link rel="stylesheet" href="public/assets/css/style.css">
    <title>Cadastrar Vendedor</title>


</head>
<body>

 <div class="cop">

  <div class="card">

    <h2>Cadastro de Vendedor</h2>

    <form action="index.php?controller=usuario&action=store" method="POST">

      <div>
        <label style="color: black;" for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required>
      </div>

      <br>

      <div>
        <label style="color: black;" for="email">E-mail:</label>
        <input type="email" id="email" name="email" required>
      </div>

      <br>

      <div>
        <label style="color: black;" for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>
      </div>

      <br>
      <div style="color: black;" class="actions-login">
        <button style="color: black;" type="submit" class="btn">Cadastrar</button>
        <a style="color: black;" href="/loja_roupas/index.php?controller=auth&action=form" class="btn">Voltar</a>
      </div>

    </form>

  </div>

</div>

</body>
</html>
