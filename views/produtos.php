
<?php

// Resolve a imagem do produto pelo ID.
if (!function_exists('imagemProdutoUrl')) {
    function imagemProdutoUrl(int $produtoId): string
    {
        $baseFs = __DIR__ . '/../public/uploads/produtos/';
        $baseUrl = '/magicdonuts/public/uploads/produtos/';

        foreach (['jpg', 'png', 'webp'] as $ext) {
            if (file_exists($baseFs . $produtoId . '.' . $ext)) {
                return $baseUrl . $produtoId . '.' . $ext;
            }
        }

        return '/magicdonuts/imagem/produto_sem_foto.png';
    }
}

// Evita avisos caso alguma variável não tenha sido enviada.
$editar = $editar ?? null;
$produtos = $produtos ?? [];
$categorias = $categorias ?? [];

// Valores utilizados pelo layout compartilhado.
$paginaAtual = 'produto';
$tituloPagina = 'Produtos';
$descricaoPagina = 'Cadastre, organize e acompanhe os produtos da loja.';

// Abre o layout compartilhado.
require_once __DIR__ . '/layout/inicio.php';
?>

<main class="produtos-main">

    <!-- APRESENTAÇÃO -->
    <section class="produtos-intro">
        <div class="produtos-intro-texto">
            <span class="produtos-eyebrow">
                <i class="fa-solid fa-store"></i>
                GESTÃO DA LOJA
            </span>

            <h1>Gerenciamento de produtos</h1>

            <p>
                Organize seu catálogo, cadastre novidades e mantenha
                os produtos da Magic Donuts sempre atualizados.
            </p>
        </div>

        <div class="produtos-intro-icon">
            <i class="fa-solid fa-box-open"></i>
        </div>
    </section>


    <!-- CADASTRO OU EDIÇÃO -->
    <section class="produto-card produto-cadastro-card">

        <div class="produto-card-heading">
            <div class="produto-heading-icon">
                <i class="fa-solid fa-<?= $editar ? 'pen-to-square' : 'plus' ?>"></i>
            </div>

            <div>
                <span class="produto-section-label">
                    <?= $editar ? 'ATUALIZAÇÃO DO CATÁLOGO' : 'NOVO CADASTRO' ?>
                </span>

                <h2>
                    <?= $editar
                        ? 'Editar produto #' . (int) $editar['id']
                        : 'Cadastrar produto'
                    ?>
                </h2>

                <p>Preencha as informações para manter seu catálogo organizado.</p>
            </div>
        </div>

        <form
            method="post"
            action="index.php?controller=produto&action=salvar"
            enctype="multipart/form-data"
            class="produto-form"
        >

            <input
                type="hidden"
                name="id"
                value="<?= $editar ? (int) $editar['id'] : 0 ?>"
            >

            <div class="produto-form-grid">

                <!-- CATEGORIA -->
                <div class="produto-form-group">
                    <label for="categoria_id">
                        Categoria <span class="campo-obrigatorio">*</span>
                    </label>

                    <div class="produto-input-wrapper">
                        <i class="fa-solid fa-layer-group produto-field-icon"></i>

                        <select
                            class="produto-input produto-select"
                            id="categoria_id"
                            name="categoria_id"
                            required
                        >
                            <option value="">Selecione uma categoria</option>

                            <?php foreach ($categorias as $c): ?>
                                <option
                                    value="<?= (int) $c['id'] ?>"
                                    <?= (
                                        $editar &&
                                        (int) $editar['categoria_id'] === (int) $c['id']
                                    ) ? 'selected' : '' ?>
                                >
                                    <?= htmlspecialchars($c['nome'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>


                <!-- NOME -->
                <div class="produto-form-group">
                    <label for="nome">
                        Nome do produto <span class="campo-obrigatorio">*</span>
                    </label>

                    <div class="produto-input-wrapper">
                        <i class="fa-solid fa-tag produto-field-icon"></i>

                        <input
                            class="produto-input"
                            id="nome"
                            type="text"
                            name="nome"
                            placeholder="Ex.: Donut de chocolate"
                            required
                            value="<?= $editar
                                ? htmlspecialchars($editar['nome'], ENT_QUOTES, 'UTF-8')
                                : '' ?>"
                        >
                    </div>
                </div>


                <!-- DESCRIÇÃO -->
                <div class="produto-form-group produto-form-full">
                    <label for="descricao">Descrição <span class="campo-opcional">Opcional</span></label>

                    <div class="produto-input-wrapper produto-textarea-wrapper">
                        <i class="fa-regular fa-file-lines produto-field-icon"></i>

                        <textarea
                            class="produto-input produto-textarea"
                            id="descricao"
                            name="descricao"
                            rows="4"
                            placeholder="Descreva os detalhes do produto..."
                        ><?= $editar
                            ? htmlspecialchars($editar['descricao'] ?? '', ENT_QUOTES, 'UTF-8')
                            : '' ?></textarea>
                    </div>

                    <small class="produto-help">
                        Uma boa descrição ajuda a identificar melhor cada produto.
                    </small>
                </div>


                <!-- IMAGEM -->
                <div class="produto-form-group produto-form-full">
                    <label for="imagem">Imagem do produto <span class="campo-opcional">Opcional</span></label>

                    <div class="produto-upload-area">
                        <div class="produto-upload-icon">
                            <i class="fa-regular fa-image"></i>
                        </div>

                        <div class="produto-upload-info">
                            <strong>Escolha uma imagem do produto</strong>
                            <span>JPG, PNG ou WEBP · Até 4 MB</span>
                        </div>

                        <input
                            class="produto-input-file"
                            id="imagem"
                            type="file"
                            name="imagem"
                            accept="image/png, image/jpeg, image/webp"
                        >
                    </div>

                    <small class="produto-help">
                        <?= $editar
                            ? 'Selecione um novo arquivo somente se desejar substituir a imagem atual.'
                            : 'A imagem será associada automaticamente ao ID do produto.'
                        ?>
                    </small>
                </div>

            </div>


            <!-- BOTÕES -->
            <div class="produto-actions">
                <button
                    class="produto-btn produto-btn-primary"
                    type="submit"
                >
                    <i class="fa-regular fa-floppy-disk"></i>
                    <?= $editar ? 'Salvar alterações' : 'Cadastrar produto' ?>
                </button>

                <a
                    class="produto-btn produto-btn-secondary"
                    href="index.php?controller=produto&action=index"
                >
                    <i class="fa-solid fa-rotate-left"></i>
                    <?= $editar ? 'Cancelar edição' : 'Limpar formulário' ?>
                </a>
            </div>

        </form>
    </section>


    <!-- LISTAGEM -->
    <section class="produto-card lista-produtos-card">

        <div class="produto-lista-heading">
            <div class="produto-card-heading">
                <div class="produto-heading-icon produto-heading-icon-lista">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>

                <div>
                    <span class="produto-section-label">CATÁLOGO</span>
                    <h2>Produtos cadastrados</h2>
                    <p>Consulte e gerencie os itens disponíveis na loja.</p>
                </div>
            </div>

            <span class="produto-total">
                <i class="fa-solid fa-cube"></i>
                <?= count($produtos) ?> <?= count($produtos) === 1 ? 'produto' : 'produtos' ?>
            </span>
        </div>


        <?php if (!empty($produtos)): ?>

            <div class="table-wrapper">
                <table class="produtos-table">

                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Identificação</th>
                            <th>Categoria</th>
                            <th>Status</th>
                            <th class="coluna-acoes">Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($produtos as $p): ?>
                            <tr>

                                <!-- PRODUTO E IMAGEM -->
                                <td>
                                    <div class="produto-info-cell">
                                        <img
                                            class="produto-thumb"
                                            src="<?= htmlspecialchars(
                                                imagemProdutoUrl((int) $p['id']),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                            alt="<?= htmlspecialchars($p['nome'], ENT_QUOTES, 'UTF-8') ?>"
                                            loading="lazy"
                                        >

                                        <div class="produto-info-texto">
                                            <strong>
                                                <?= htmlspecialchars($p['nome'], ENT_QUOTES, 'UTF-8') ?>
                                            </strong>
                                            <span>Produto do catálogo</span>
                                        </div>
                                    </div>
                                </td>


                                <!-- ID -->
                                <td>
                                    <span class="produto-id">
                                        #<?= (int) $p['id'] ?>
                                    </span>
                                </td>


                                <!-- CATEGORIA -->
                                <td>
                                    <span class="categoria-badge">
                                        <?= htmlspecialchars($p['categoria_nome'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>


                                <!-- STATUS -->
                                <td>
                                    <?php if ((int) $p['ativo'] === 1): ?>
                                        <span class="produto-status status-ativo">
                                            <span></span>
                                            Ativo
                                        </span>
                                    <?php else: ?>
                                        <span class="produto-status status-inativo">
                                            <span></span>
                                            Inativo
                                        </span>
                                    <?php endif; ?>
                                </td>


                                <!-- AÇÕES -->
                                <td>
                                    <div class="produto-table-actions">

                                        <a
                                            class="table-btn table-btn-variacoes"
                                            href="index.php?controller=variacao&action=index&produto_id=<?= (int) $p['id'] ?>"
                                            title="Gerenciar variações"
                                            aria-label="Gerenciar variações"
                                        >
                                            <i class="fa-solid fa-layer-group"></i>
                                        </a>

                                        <a
                                            class="table-btn table-btn-edit"
                                            href="index.php?controller=produto&action=index&id=<?= (int) $p['id'] ?>"
                                            title="Editar produto"
                                            aria-label="Editar produto"
                                        >
                                            <i class="fa-solid fa-pen"></i>
                                        </a>


                                        <?php if ((int) $p['ativo'] === 1): ?>

                                            <a
                                                class="table-btn table-btn-danger"
                                                href="index.php?controller=produto&action=toggle&id=<?= (int) $p['id'] ?>&ativo=0"
                                                onclick="return confirm('Inativar este produto?')"
                                                title="Inativar produto"
                                                aria-label="Inativar produto"
                                            >
                                                <i class="fa-solid fa-ban"></i>
                                            </a>

                                        <?php else: ?>

                                            <a
                                                class="table-btn table-btn-success"
                                                href="index.php?controller=produto&action=toggle&id=<?= (int) $p['id'] ?>&ativo=1"
                                                title="Ativar produto"
                                                aria-label="Ativar produto"
                                            >
                                                <i class="fa-solid fa-check"></i>
                                            </a>

                                        <?php endif; ?>


                                        <a
                                            class="table-btn table-btn-delete"
                                            href="index.php?controller=produto&action=deletar&id=<?= (int) $p['id'] ?>"
                                            onclick="return confirm('ATENÇÃO: deseja excluir este produto permanentemente?')"
                                            title="Excluir produto"
                                            aria-label="Excluir produto"
                                        >
                                            <i class="fa-solid fa-trash"></i>
                                        </a>

                                    </div>
                                </td>

                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>

        <?php else: ?>

            <div class="produto-empty-state">
                <div class="produto-empty-icon">
                    <i class="fa-solid fa-box-open"></i>
                </div>

                <h3>Nenhum produto cadastrado</h3>

                <p>
                    Seu catálogo ainda está vazio. Utilize o formulário acima
                    para cadastrar o primeiro produto.
                </p>
            </div>

        <?php endif; ?>

    </section>

</main>

<?php require_once __DIR__ . '/layout/fim.php'; ?>
