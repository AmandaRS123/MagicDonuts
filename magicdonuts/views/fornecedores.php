
<?php
$nomeUser = $_SESSION['nome'] ?? 'Usuário';
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Fornecedores - Magic Donuts</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="/magicdonuts/public/assets/css/style.css">
    <link rel="icon" type="image/jpeg" href="/magicdonuts/imagem/logo.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="produtos-body">

<header class="produtos-header">
    <div class="produtos-header-inner">

        <a href="/magicdonuts/index.php?controller=auth&action=dashboard"
           class="produtos-brand">
            <img src="/magicdonuts/imagem/logo.jpg"
                 alt="Magic Donuts"
                 class="produtos-logo">
            <strong>Fornecedores</strong>
        </a>

        <div class="produtos-user">
            <span>
                Olá,
                <strong><?= htmlspecialchars($nomeUser) ?></strong>
            </span>

            <a href="/magicdonuts/index.php?controller=auth&action=logout">
                Sair
            </a>
        </div>

    </div>
</header>

<main class="produtos-main">

    <!-- Formulário de cadastro e edição -->
    <section class="produto-card">

        <div class="section-title">
            <h1>
                <?= $editar ? 'Editar Fornecedor' : 'Cadastrar Fornecedor' ?>
                <span class="title-sparkles">✦</span>
            </h1>
        </div>

        <form method="post"
              action="/magicdonuts/index.php?controller=fornecedor&action=salvar"
              class="produto-form">

            <input type="hidden"
                   name="id"
                   value="<?= $editar ? (int) $editar['id'] : 0 ?>">

            <div class="produto-form-group">
                <label for="nome">Nome do fornecedor</label>

                <div class="produto-input-wrapper">
                    <div class="produto-input-icon">
                        <i class="fa-solid fa-truck"></i>
                    </div>

                    <input
                        class="produto-input"
                        id="nome"
                        type="text"
                        name="nome"
                        required
                        placeholder="Digite o nome do fornecedor"
                        value="<?= $editar ? htmlspecialchars($editar['nome']) : '' ?>"
                    >
                </div>
            </div>

            <div class="produto-form-group">
                <label for="cnpj">CNPJ</label>

                <div class="produto-input-wrapper">
                    <div class="produto-input-icon">
                        <i class="fa-solid fa-id-card"></i>
                    </div>

                    <input
                        class="produto-input"
                        id="cnpj"
                        type="text"
                        name="cnpj"
                        placeholder="Digite o CNPJ"
                        value="<?= $editar ? htmlspecialchars($editar['cnpj'] ?? '') : '' ?>"
                    >
                </div>
            </div>

            <div class="produto-form-group">
                <label for="telefone">Telefone</label>

                <div class="produto-input-wrapper">
                    <div class="produto-input-icon">
                        <i class="fa-solid fa-phone"></i>
                    </div>

                    <input
                        class="produto-input"
                        id="telefone"
                        type="text"
                        name="telefone"
                        placeholder="Digite o telefone"
                        value="<?= $editar ? htmlspecialchars($editar['telefone'] ?? '') : '' ?>"
                    >
                </div>
            </div>

            <div class="produto-form-group">
                <label for="email">E-mail</label>

                <div class="produto-input-wrapper">
                    <div class="produto-input-icon">
                        <i class="fa-solid fa-envelope"></i>
                    </div>

                    <input
                        class="produto-input"
                        id="email"
                        type="email"
                        name="email"
                        placeholder="fornecedor@exemplo.com"
                        value="<?= $editar ? htmlspecialchars($editar['email'] ?? '') : '' ?>"
                    >
                </div>
            </div>

            <div class="produto-form-group">
                <label for="endereco">Endereço</label>

                <div class="produto-input-wrapper">
                    <div class="produto-input-icon">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>

                    <textarea
                        class="produto-input"
                        id="endereco"
                        name="endereco"
                        rows="3"
                        placeholder="Digite o endereço do fornecedor"
                    ><?= $editar ? htmlspecialchars($editar['endereco'] ?? '') : '' ?></textarea>
                </div>
            </div>

            <div class="produto-actions">
                <button class="produto-btn produto-btn-primary" type="submit">
                    <i class="fa-regular fa-floppy-disk"></i>
                    Salvar fornecedor
                </button>

                <a
                    class="produto-btn produto-btn-secondary"
                    href="/magicdonuts/index.php?controller=fornecedor&action=index"
                >
                    <i class="fa-solid fa-rotate"></i>
                    Limpar
                </a>
            </div>

        </form>
    </section>

    <!-- Lista de fornecedores cadastrados -->
    <section class="produto-card lista-produtos-card">

        <div class="section-title">
            <h2>
                Fornecedores cadastrados
                <span class="title-sparkles">✦</span>
            </h2>
        </div>

        <div class="table-wrapper">
            <table class="produtos-table">

                <thead>
                    <tr>
                        <th><i class="fa-solid fa-hashtag"></i> ID</th>
                        <th><i class="fa-solid fa-truck"></i> Nome</th>
                        <th><i class="fa-solid fa-id-card"></i> CNPJ</th>
                        <th><i class="fa-solid fa-phone"></i> Telefone</th>
                        <th><i class="fa-solid fa-circle-check"></i> Status</th>
                        <th><i class="fa-solid fa-gear"></i> Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($fornecedores)): ?>

                        <?php foreach ($fornecedores as $f): ?>
                            <tr>
                                <td>#<?= (int) $f['id'] ?></td>

                                <td>
                                    <strong><?= htmlspecialchars($f['nome']) ?></strong>
                                </td>

                                <td>
                                    <?= htmlspecialchars($f['cnpj'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($f['telefone'] ?? '-') ?>
                                </td>

                                <td>
                                    <?php if ((int) $f['ativo'] === 1): ?>
                                        <span class="tag ok">Ativo</span>
                                    <?php else: ?>
                                        <span class="tag off">Inativo</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <div class="produto-table-actions">

                                        <!-- Editar fornecedor -->
                                        <a
                                            class="table-btn table-btn-edit"
                                            href="/magicdonuts/index.php?controller=fornecedor&action=index&id=<?= (int) $f['id'] ?>"
                                            title="Editar fornecedor"
                                        >
                                            <i class="fa-solid fa-pen"></i>
                                        </a>

                                        <!-- Ativar ou inativar fornecedor -->
                                        <?php if ((int) $f['ativo'] === 1): ?>

                                            <a
                                                class="table-btn table-btn-delete"
                                                href="/magicdonuts/index.php?controller=fornecedor&action=toggle&id=<?= (int) $f['id'] ?>&ativo=0"
                                                onclick="return confirm('Inativar este fornecedor?')"
                                                title="Inativar fornecedor"
                                            >
                                                <i class="fa-solid fa-ban"></i>
                                            </a>

                                        <?php else: ?>

                                            <a
                                                class="table-btn table-btn-edit"
                                                href="/magicdonuts/index.php?controller=fornecedor&action=toggle&id=<?= (int) $f['id'] ?>&ativo=1"
                                                title="Ativar fornecedor"
                                            >
                                                <i class="fa-solid fa-check"></i>
                                            </a>

                                        <?php endif; ?>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px;">
                                <i class="fa-solid fa-circle-info"></i>
                                Nenhum fornecedor cadastrado ainda.
                            </td>
                        </tr>

                    <?php endif; ?>
                </tbody>

            </table>
        </div>
    </section>

    <!-- Botão de voltar ao dashboard no final da página -->
    <div style="display: flex; justify-content: flex-end; margin-top: 20px; margin-bottom: 20px;">
        <a
            href="/magicdonuts/index.php?controller=auth&action=dashboard"
            class="produto-btn produto-btn-secondary"
        >
            <i class="fa-solid fa-arrow-left"></i>
            Voltar ao Dashboard
        </a>
    </div>

</main>

</body>
</html>