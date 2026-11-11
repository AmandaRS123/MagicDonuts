
<?php

$paginaAtual = 'fornecedor';
$tituloPagina = 'Fornecedores';
$descricaoPagina = 'Cadastre e gerencie os fornecedores da loja.';

$fornecedores = $fornecedores ?? [];
$editar = $editar ?? null;

require_once __DIR__ . '/layout/inicio.php';

?>

<div class="produtos-main">

    <!-- FORMULÁRIO DE CADASTRO E EDIÇÃO -->
    <section class="produto-card">

        <div class="section-title">
            <h1>
                <?= $editar ? 'Editar Fornecedor' : 'Cadastrar Fornecedor' ?>
                <span class="title-sparkles">✦</span>
            </h1>
        </div>

        <form
            method="post"
            action="index.php?controller=fornecedor&action=salvar"
            class="produto-form"
        >

            <input
                type="hidden"
                name="id"
                value="<?= $editar ? (int) $editar['id'] : 0 ?>"
            >

            <!-- Nome -->
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
                        maxlength="100"
                        placeholder="Digite o nome do fornecedor"
                        value="<?= htmlspecialchars($editar['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>

            </div>

            <!-- CNPJ -->
            <div class="produto-form-group">

                <label for="cnpj">CNPJ <span style="color: #d9534f;">*</span></label>

                <div class="produto-input-wrapper">
                    <div class="produto-input-icon">
                        <i class="fa-solid fa-id-card"></i>
                    </div>

                    <input
                        class="produto-input"
                        id="cnpj"
                        type="text"
                        name="cnpj"
                        required
                        maxlength="18"
                        inputmode="numeric"
                        placeholder="00.000.000/0000-00"
                        value="<?= htmlspecialchars($editar['cnpj'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>

                <small class="produto-help">
                    O CNPJ é obrigatório e deve conter 14 dígitos.
                </small>

            </div>

            <!-- Telefone -->
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
                        maxlength="20"
                        placeholder="Digite o telefone"
                        value="<?= htmlspecialchars($editar['telefone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>

            </div>

            <!-- E-mail -->
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
                        maxlength="100"
                        placeholder="fornecedor@exemplo.com"
                        value="<?= htmlspecialchars($editar['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>

            </div>

            <!-- CEP -->
            <div class="produto-form-group">

                <label for="cep">CEP</label>

                <div class="produto-input-wrapper">
                    <div class="produto-input-icon">
                        <i class="fa-solid fa-map-pin"></i>
                    </div>

                    <input
                        class="produto-input"
                        id="cep"
                        type="text"
                        name="cep"
                        maxlength="9"
                        inputmode="numeric"
                        autocomplete="postal-code"
                        placeholder="00000-000"
                        value="<?= htmlspecialchars($editar['cep'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>

                <small id="cep-status" class="produto-help">
                    Digite o CEP para preencher o endereço automaticamente.
                </small>

            </div>

            <!-- Endereço -->
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
                        placeholder="Rua, bairro, cidade e estado"
                    ><?= htmlspecialchars($editar['endereco'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <small class="produto-help">
                    Você pode complementar ou editar o endereço preenchido.
                </small>

            </div>

            <!-- Botões -->
            <div class="produto-actions">

                <button
                    class="produto-btn produto-btn-primary"
                    type="submit"
                >
                    <i class="fa-regular fa-floppy-disk"></i>
                    Salvar fornecedor
                </button>

                <a
                    class="produto-btn produto-btn-secondary"
                    href="index.php?controller=fornecedor&action=index"
                >
                    <i class="fa-solid fa-rotate"></i>
                    Limpar
                </a>

            </div>

        </form>

    </section>


    <!-- LISTA DE FORNECEDORES -->
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
                        <th><i class="fa-solid fa-location-dot"></i> CEP</th>
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
                                    <strong>
                                        <?= htmlspecialchars($f['nome'], ENT_QUOTES, 'UTF-8') ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= htmlspecialchars($f['cnpj'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($f['telefone'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($f['cep'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                </td>

                                <td>

                                    <?php if ((int) ($f['ativo'] ?? 0) === 1): ?>

                                        <span class="tag ok">Ativo</span>

                                    <?php else: ?>

                                        <span class="tag off">Inativo</span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <div class="produto-table-actions">

                                        <!-- Editar -->
                                        <a
                                            class="table-btn table-btn-edit"
                                            href="index.php?controller=fornecedor&action=index&id=<?= (int) $f['id'] ?>"
                                            title="Editar fornecedor"
                                        >
                                            <i class="fa-solid fa-pen"></i>
                                        </a>

                                        <!-- Ativar ou inativar -->
                                        <?php if ((int) ($f['ativo'] ?? 0) === 1): ?>

                                            <a
                                                class="table-btn table-btn-delete"
                                                href="index.php?controller=fornecedor&action=toggle&id=<?= (int) $f['id'] ?>&ativo=0"
                                                onclick="return confirm('Inativar este fornecedor?')"
                                                title="Inativar fornecedor"
                                            >
                                                <i class="fa-solid fa-ban"></i>
                                            </a>

                                        <?php else: ?>

                                            <a
                                                class="table-btn table-btn-edit"
                                                href="index.php?controller=fornecedor&action=toggle&id=<?= (int) $f['id'] ?>&ativo=1"
                                                onclick="return confirm('Ativar este fornecedor?')"
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
                            <td colspan="7" style="text-align: center; padding: 30px;">
                                <i class="fa-solid fa-circle-info"></i>
                                Nenhum fornecedor cadastrado ainda.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

</div>


<!-- CONSULTA AUTOMÁTICA DE CEP -->
<script>

document.addEventListener('DOMContentLoaded', function () {

    const campoCep = document.getElementById('cep');
    const campoEndereco = document.getElementById('endereco');
    const statusCep = document.getElementById('cep-status');

    if (!campoCep || !campoEndereco || !statusCep) {
        return;
    }

    // Formata o CEP enquanto o usuário digita.
    campoCep.addEventListener('input', function () {

        let valor = this.value.replace(/\D/g, '').substring(0, 8);

        if (valor.length > 5) {
            valor = valor.substring(0, 5) + '-' + valor.substring(5);
        }

        this.value = valor;

    });

    // Consulta o CEP quando o usuário sai do campo.
    campoCep.addEventListener('blur', async function () {

        const cep = this.value.replace(/\D/g, '');

        if (cep.length === 0) {
            statusCep.textContent = 'Digite o CEP para preencher o endereço automaticamente.';
            return;
        }

        if (cep.length !== 8) {
            statusCep.textContent = 'Informe um CEP com 8 dígitos.';
            return;
        }

        statusCep.textContent = 'Consultando CEP...';

        try {

            const resposta = await fetch(
                'index.php?controller=cep&action=buscar&cep=' + encodeURIComponent(cep)
            );

            const dados = await resposta.json();

            if (!resposta.ok) {
                throw new Error(dados.erro || 'Não foi possível consultar o CEP.');
            }

            const partes = [
                dados.logradouro,
                dados.bairro,
                dados.cidade && dados.estado
                    ? dados.cidade + '/' + dados.estado
                    : dados.cidade || dados.estado
            ].filter(Boolean);

            if (partes.length === 0) {
                statusCep.textContent = 'CEP encontrado, mas sem endereço detalhado. Preencha manualmente.';
                return;
            }

            campoEndereco.value = partes.join(', ');

            statusCep.textContent = 'Endereço preenchido com sucesso!';

        } catch (erro) {

            statusCep.textContent = erro.message;

        }

    });

});

</script>

<?php require_once __DIR__ . '/layout/fim.php'; ?>
