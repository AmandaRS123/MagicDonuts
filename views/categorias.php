
<?php

require_once __DIR__ . '/layout/inicio.php';

$esc = static function ($valor): string {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
};

$mensagens = [
    'cadastrada' => 'Categoria cadastrada com sucesso!',
    'editada' => 'Categoria atualizada com sucesso!',
    'ativada' => 'Categoria ativada com sucesso!',
    'inativada' => 'Categoria inativada com sucesso!',
    'excluida' => 'Categoria excluída com sucesso!',
];

$erros = [
    'id' => 'O código informado é inválido.',
    'nao_encontrada' => 'Categoria não encontrada.',
    'nome' => 'Informe um nome com até 100 caracteres.',
    'dados' => 'Os dados enviados são inválidos.',
    'salvar' => 'Não foi possível salvar a categoria.',
    'status' => 'Não foi possível alterar o status da categoria.',
    'vinculada' => 'Esta categoria possui produtos vinculados. Para preservá-los, inative a categoria.',
    'excluir' => 'Não foi possível excluir a categoria.',
];

$sucesso = $_GET['sucesso'] ?? '';
$erro = $_GET['erro'] ?? '';

?>

<style>

/* =========================================
   MAGIC DONUTS - GERENCIAMENTO DE CATEGORIAS
========================================= */

.categorias-page {
    width: 100%;
    color: #392477;
    animation: categoriaEntrada .45s ease;
}

@keyframes categoriaEntrada {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ÁREA PRINCIPAL */

.categorias-page .categoria-grid {
    display: grid;
    grid-template-columns: minmax(280px, 0.85fr) minmax(0, 2fr);
    gap: 24px;
    align-items: start;
}

/* CARDS */

.categorias-page .categoria-card {
    position: relative;
    min-width: 0;
    padding: 27px;
    background: #ffffff;
    border: 1px solid #eee7f8;
    border-radius: 22px;
    box-shadow: 0 8px 28px rgba(57, 36, 119, 0.06);
    transition: transform .25s ease, box-shadow .25s ease;
}

.categorias-page .categoria-card:hover {
    box-shadow: 0 12px 32px rgba(57, 36, 119, 0.10);
}

/* TÍTULOS */

.categorias-page .categoria-card h3 {
    display: flex;
    align-items: center;
    gap: 11px;
    margin: 0 0 24px;
    color: #392477;
    font-size: 19px;
    font-weight: 750;
    letter-spacing: -0.4px;
}

.categorias-page .categoria-card h3 i {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border-radius: 12px;
    background: #f0e7ff;
    color: #7951bf;
    font-size: 16px;
}

/* FORMULÁRIO */

.categorias-page .categoria-campo {
    display: flex;
    flex-direction: column;
    gap: 9px;
}

.categorias-page .categoria-campo label {
    color: #604c7c;
    font-size: 13px;
    font-weight: 700;
}

.categorias-page .categoria-campo input {
    width: 100%;
    box-sizing: border-box;
    padding: 14px 16px;
    border: 1.5px solid #e8def5;
    border-radius: 13px;
    outline: none;
    background: #fcfaff;
    color: #392477;
    font-family: inherit;
    font-size: 14px;
    transition: all .25s ease;
}

.categorias-page .categoria-campo input::placeholder {
    color: #b0a2c2;
}

.categorias-page .categoria-campo input:hover {
    border-color: #cbb8e8;
    background: #ffffff;
}

.categorias-page .categoria-campo input:focus {
    border-color: #7951bf;
    background: #ffffff;
    box-shadow: 0 0 0 4px rgba(121, 81, 191, 0.11);
}

/* BOTÕES */

.categorias-page .categoria-acoes {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 20px;
}

.categorias-page .categoria-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 11px 15px;
    border: none;
    border-radius: 11px;
    text-decoration: none;
    font-family: inherit;
    font-size: 12px;
    font-weight: 750;
    cursor: pointer;
    transition: all .22s ease;
    white-space: nowrap;
}

.categorias-page .categoria-btn:hover {
    transform: translateY(-2px);
}

.categorias-page .categoria-btn:active {
    transform: translateY(0);
}

.categorias-page .categoria-btn-primary {
    background: linear-gradient(135deg, #7951bf, #956cc6);
    color: #ffffff;
    box-shadow: 0 5px 13px rgba(121, 81, 191, 0.20);
}

.categorias-page .categoria-btn-primary:hover {
    background: linear-gradient(135deg, #6842aa, #8259b8);
    box-shadow: 0 7px 17px rgba(121, 81, 191, 0.28);
}

.categorias-page .categoria-btn-light {
    background: #f3edfb;
    color: #60429e;
}

.categorias-page .categoria-btn-light:hover {
    background: #e8dcf8;
}

.categorias-page .categoria-btn-edit {
    background: #f0e7ff;
    color: #7045b1;
}

.categorias-page .categoria-btn-edit:hover {
    background: #e5d7fa;
}

.categorias-page .categoria-btn-danger {
    background: #fff0f1;
    color: #bd5363;
}

.categorias-page .categoria-btn-danger:hover {
    background: #ffe0e3;
}

.categorias-page .categoria-btn-success {
    background: #e8f6ed;
    color: #328358;
}

.categorias-page .categoria-btn-success:hover {
    background: #d8efdf;
}

/* BOTÃO EXCLUIR */

.categorias-page .categoria-btn-excluir {
    background: #fcebed;
    color: #b52f46;
}

.categorias-page .categoria-btn-excluir:hover {
    background: #f8d9de;
    box-shadow: 0 4px 10px rgba(181, 47, 70, 0.10);
}

/* TEXTO DE INFORMAÇÃO */

.categorias-page .categoria-aviso {
    display: block;
    margin: 22px 0 0;
    padding: 13px 14px;
    border-radius: 12px;
    background: #f8f4ff;
    color: #88779b;
    font-size: 12px;
    line-height: 1.7;
}

.categorias-page .categoria-aviso i {
    color: #7951bf;
    margin-right: 5px;
}

/* ALERTAS */

.categorias-page .categoria-alerta {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px 18px;
    border-radius: 13px;
    margin-bottom: 20px;
    font-size: 13px;
    font-weight: 650;
    animation: categoriaEntrada .3s ease;
}

.categorias-page .categoria-alerta.sucesso {
    background: #edf8f0;
    color: #28794b;
    border: 1px solid #d1ead8;
}

.categorias-page .categoria-alerta.erro {
    background: #fff1f2;
    color: #b84455;
    border: 1px solid #f3d1d5;
}

/* TABELA */

.categorias-page .categoria-tabela-wrap {
    width: 100%;
    overflow-x: auto;
    border-radius: 13px;
}

.categorias-page .categoria-tabela {
    width: 100%;
    min-width: 560px;
    border-collapse: separate;
    border-spacing: 0;
}

.categorias-page .categoria-tabela th {
    padding: 15px 13px;
    background: #f5efff;
    color: #60429e;
    text-align: left;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .5px;
}

.categorias-page .categoria-tabela th:first-child {
    border-radius: 11px 0 0 11px;
}

.categorias-page .categoria-tabela th:last-child {
    border-radius: 0 11px 11px 0;
}

.categorias-page .categoria-tabela td {
    padding: 16px 13px;
    border-bottom: 1px solid #f0ebf6;
    color: #665580;
    font-size: 13px;
    vertical-align: middle;
    transition: background .2s ease;
}

.categorias-page .categoria-tabela tbody tr:hover td {
    background: #fcfaff;
}

.categorias-page .categoria-tabela tbody tr:last-child td {
    border-bottom: none;
}

.categorias-page .categoria-tabela td strong {
    color: #4a3477;
    font-weight: 700;
}

/* STATUS */

.categorias-page .categoria-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 11px;
    border-radius: 30px;
    font-size: 11px;
    font-weight: 800;
}

.categorias-page .categoria-status.ativa {
    color: #28794b;
    background: #e8f5ed;
}

.categorias-page .categoria-status.ativa::before {
    content: "";
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #43a76d;
}

.categorias-page .categoria-status.inativa {
    color: #765b91;
    background: #f1eafa;
}

.categorias-page .categoria-status.inativa::before {
    content: "";
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #a48ac3;
}

/* AÇÕES DA TABELA */

.categorias-page .categoria-acoes-tabela {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 7px;
}

.categorias-page .categoria-form-status {
    display: inline;
    margin: 0;
}

.categorias-page .categoria-vazio {
    padding: 35px 15px !important;
    color: #9385a3 !important;
    text-align: center;
    font-size: 13px;
}

/* RESPONSIVIDADE */

@media (max-width: 1100px) {
    .categorias-page .categoria-grid {
        grid-template-columns: minmax(250px, .9fr) minmax(0, 1.5fr);
        gap: 17px;
    }

    .categorias-page .categoria-card {
        padding: 20px;
    }
}

@media (max-width: 850px) {
    .categorias-page .categoria-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 520px) {
    .categorias-page .categoria-card {
        padding: 18px;
        border-radius: 17px;
    }

    .categorias-page .categoria-acoes {
        flex-direction: column;
    }

    .categorias-page .categoria-acoes .categoria-btn {
        width: 100%;
        box-sizing: border-box;
    }

    .categorias-page .categoria-acoes-tabela {
        align-items: flex-start;
        flex-direction: column;
    }
}

</style>

<div class="categorias-page">

    <?php if (isset($mensagens[$sucesso])): ?>
        <div class="categoria-alerta sucesso">
            <i class="fa-solid fa-circle-check"></i>
            <?= $esc($mensagens[$sucesso]) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($erros[$erro])): ?>
        <div class="categoria-alerta erro">
            <i class="fa-solid fa-circle-exclamation"></i>
            <?= $esc($erros[$erro]) ?>
        </div>
    <?php endif; ?>

    <div class="categoria-grid">

        <!-- FORMULÁRIO -->
        <section class="categoria-card">

            <h3>
                <i class="fa-solid <?= $editar ? 'fa-pen-to-square' : 'fa-plus-circle' ?>"></i>
                <?= $editar ? 'Editar categoria' : 'Nova categoria' ?>
            </h3>

            <?php if ($editar): ?>
                <p class="categoria-aviso" style="margin-top:-10px; margin-bottom:15px;">
                    Editando a categoria #<?= (int) $editar['id'] ?>
                </p>
            <?php endif; ?>

            <form method="post" action="index.php?controller=categoria&action=salvar">

                <input
                    type="hidden"
                    name="id"
                    value="<?= $editar ? (int) $editar['id'] : 0 ?>"
                >

                <div class="categoria-campo">
                    <label for="nome">Nome da categoria</label>

                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        maxlength="100"
                        placeholder="Ex.: Com recheio"
                        value="<?= $editar ? $esc($editar['nome']) : '' ?>"
                        required
                    >
                </div>

                <div class="categoria-acoes">

                    <button type="submit" class="categoria-btn categoria-btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <?= $editar ? 'Salvar alterações' : 'Cadastrar categoria' ?>
                    </button>

                    <a
                        href="index.php?controller=categoria&action=index"
                        class="categoria-btn categoria-btn-light"
                    >
                        <i class="fa-solid fa-eraser"></i>
                        Limpar
                    </a>

                </div>

            </form>

            <p class="categoria-aviso">
                <i class="fa-solid fa-circle-info"></i>
                Se uma categoria não estiver mais em uso, você pode inativá-la.
                Assim, ela deixa de aparecer nas opções de cadastro sem perder o histórico.
            </p>

        </section>

        <!-- LISTA -->
        <section class="categoria-card">

            <h3>
                <i class="fa-solid fa-list"></i>
                Categorias cadastradas
            </h3>

            <div class="categoria-tabela-wrap">

                <table class="categoria-tabela">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if (empty($categorias)): ?>

                            <tr>
                                <td colspan="4" class="categoria-vazio">
                                    Nenhuma categoria cadastrada.
                                </td>
                            </tr>

                        <?php else: ?>

                            <?php foreach ($categorias as $c): ?>

                                <tr>

                                    <td>#<?= (int) $c['id'] ?></td>

                                    <td>
                                        <strong><?= $esc($c['nome']) ?></strong>
                                    </td>

                                    <td>
                                        <?php if ((int) $c['ativo'] === 1): ?>
                                            <span class="categoria-status ativa">Ativa</span>
                                        <?php else: ?>
                                            <span class="categoria-status inativa">Inativa</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <div class="categoria-acoes-tabela">

                                            <!-- EDITAR -->
                                            <a
                                                href="index.php?controller=categoria&action=index&id=<?= (int) $c['id'] ?>"
                                                class="categoria-btn categoria-btn-edit"
                                            >
                                                <i class="fa-solid fa-pen"></i>
                                                Editar
                                            </a>

                                            <!-- EXCLUIR -->
                                            <form
                                                method="post"
                                                action="index.php?controller=categoria&action=excluir"
                                                class="categoria-form-status"
                                                onsubmit="return confirm('Tem certeza de que deseja excluir definitivamente esta categoria? Esta ação não poderá ser desfeita.');"
                                            >
                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?= (int) $c['id'] ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    class="categoria-btn categoria-btn-excluir"
                                                >
                                                    <i class="fa-solid fa-trash-can"></i>
                                                    Excluir
                                                </button>
                                            </form>

                                            <!-- ATIVAR / INATIVAR -->
                                            <form
                                                method="post"
                                                action="index.php?controller=categoria&action=toggle"
                                                class="categoria-form-status"
                                                onsubmit="return confirm('<?= (int) $c['ativo'] === 1 ? 'Deseja inativar' : 'Deseja ativar' ?> esta categoria?');"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?= (int) $c['id'] ?>"
                                                >

                                                <?php if ((int) $c['ativo'] === 1): ?>

                                                    <input type="hidden" name="ativo" value="0">

                                                    <button
                                                        type="submit"
                                                        class="categoria-btn categoria-btn-danger"
                                                    >
                                                        <i class="fa-solid fa-ban"></i>
                                                        Inativar
                                                    </button>

                                                <?php else: ?>

                                                    <input type="hidden" name="ativo" value="1">

                                                    <button
                                                        type="submit"
                                                        class="categoria-btn categoria-btn-success"
                                                    >
                                                        <i class="fa-solid fa-check"></i>
                                                        Ativar
                                                    </button>

                                                <?php endif; ?>

                                            </form>

                                        </div>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>

    </div>

</div>

</main>
</div>
</div>
</body>
</html>
