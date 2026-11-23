
<?php
$usuarios = $usuarios ?? [];

$sucesso = $_GET['sucesso'] ?? '';
$erro = $_GET['erro'] ?? '';

$mensagensSucesso = [
    'cadastro' => 'Funcionário cadastrado com sucesso!',
    'status' => 'Situação do funcionário atualizada com sucesso!'
];

$mensagensErro = [
    'dados' => 'Dados inválidos.',
    'propria_conta' => 'Você não pode desativar sua própria conta.',
    'ultimo_admin' => 'Não é possível desativar o último administrador ativo.',
    'nao_encontrado' => 'Funcionário não encontrado.',
    'operacao' => 'Não foi possível realizar a operação.'
];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gerenciar Usuários - Magic Donuts</title>

    <link rel="stylesheet" href="/magicdonuts/public/assets/css/style.css">
    <link rel="icon" type="image/jpeg" href="/magicdonuts/imagem/logo.jpg">

    <style>
       
/* ==================================================
   GERENCIAMENTO DE FUNCIONÁRIOS - MAGIC DONUTS
================================================== */

.usuarios-container {
    max-width: 1150px;
    margin: 30px auto;
    padding: 10px;
}

/* Cartão principal */

.usuarios-card {
    background: rgba(255, 255, 255, 0.96);
    padding: 32px;
    border-radius: 22px;
    border: 1px solid rgba(255, 255, 255, 0.9);
    box-shadow: 0 10px 28px rgba(60, 38, 110, 0.09);
}

/* Cabeçalho */

.usuarios-topo {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 18px;
    flex-wrap: wrap;
    margin-bottom: 28px;
}

.usuarios-topo h1 {
    margin: 0 0 9px;
    color: #392477;
    font-size: 25px;
    font-weight: 700;
}

.usuarios-topo p {
    margin: 0;
    color: #88779b;
    font-size: 14px;
    line-height: 1.5;
}

/* Botões do cabeçalho */

.usuarios-acoes {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.usuarios-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 17px;
    border-radius: 11px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    font-size: 13px;
    font-weight: 700;
    transition: 0.2s ease;
}

.usuarios-btn-cadastrar {
    background: linear-gradient(135deg, #7951bf, #956cc6);
    color: #fff;
    box-shadow: 0 4px 12px rgba(112, 69, 199, 0.18);
}

.usuarios-btn-cadastrar:hover {
    background: linear-gradient(135deg, #6040a5, #7951bf);
    transform: translateY(-2px);
    box-shadow: 0 7px 16px rgba(112, 69, 199, 0.22);
}

.usuarios-btn-voltar {
    background: #f0e7ff;
    color: #60429e;
}

.usuarios-btn-voltar:hover {
    background: #e5d8fa;
    transform: translateY(-2px);
}

/* Tabela */

.usuarios-tabela-wrapper {
    overflow-x: auto;
    border-radius: 14px;
    border: 1px solid #eee7f8;
}

.usuarios-tabela {
    width: 100%;
    border-collapse: collapse;
    min-width: 650px;
}

.usuarios-tabela th,
.usuarios-tabela td {
    padding: 16px 15px;
    text-align: left;
    border-bottom: 1px solid #f0ebf6;
}

.usuarios-tabela th {
    background: #f5efff;
    color: #60429e;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.usuarios-tabela td {
    color: #665580;
    font-size: 13px;
}

.usuarios-tabela tbody tr {
    transition: background 0.2s ease;
}

.usuarios-tabela tbody tr:hover {
    background: #faf7ff;
}

.usuarios-tabela tbody tr:last-child td {
    border-bottom: none;
}

/* Situação do usuário */

.usuario-status {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 7px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
}

.usuario-ativo {
    background: #e8f5ed;
    color: #368052;
}

.usuario-inativo {
    background: #f1eafa;
    color: #7951bf;
}

/* Perfil */

.usuario-perfil {
    text-transform: capitalize;
    font-weight: 600;
    color: #7951bf !important;
}

/* Botões de ação */

.usuario-acao {
    border: none;
    padding: 9px 14px;
    border-radius: 9px;
    cursor: pointer;
    font-size: 12px;
    font-weight: 700;
    transition: 0.2s ease;
}

.usuario-desativar {
    background: #f1eafa;
    color: #7951bf;
}

.usuario-desativar:hover {
    background: #e5d8fa;
    transform: translateY(-1px);
}

.usuario-reativar {
    background: #e8f5ed;
    color: #368052;
}

.usuario-reativar:hover {
    background: #d5eddd;
    transform: translateY(-1px);
}

.usuario-acao:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Mensagens */

.usuarios-alerta {
    padding: 14px 17px;
    border-radius: 11px;
    margin-bottom: 20px;
    font-size: 13px;
    font-weight: 600;
}

.usuarios-alerta.sucesso {
    background: #e8f5ed;
    color: #368052;
    border: 1px solid #d5eddd;
}

.usuarios-alerta.erro {
    background: #f1eafa;
    color: #7951bf;
    border: 1px solid #e5d8fa;
}

/* Lista vazia */

.usuarios-vazio {
    text-align: center !important;
    padding: 35px !important;
    color: #9385a3 !important;
    font-size: 14px !important;
}

/* Responsividade */

@media (max-width: 768px) {

    .usuarios-container {
        margin: 15px auto;
        padding: 8px;
    }

    .usuarios-card {
        padding: 20px 15px;
        border-radius: 16px;
    }

    .usuarios-topo {
        align-items: flex-start;
    }

    .usuarios-topo h1 {
        font-size: 21px;
    }

    .usuarios-acoes {
        width: 100%;
    }

    .usuarios-btn {
        flex: 1;
    }

    .usuarios-tabela th,
    .usuarios-tabela td {
        padding: 13px 11px;
    }
}

@media (max-width: 480px) {

    .usuarios-acoes {
        flex-direction: column;
    }

    .usuarios-btn {
        width: 100%;
    }

}

    </style>
</head>

<body>

<div class="usuarios-container">

    <div class="usuarios-card">

        <div class="usuarios-topo">
            <div>
                <h1>Gerenciar funcionários</h1>
                <p>Cadastre, consulte, desative e reative os usuários do sistema.</p>
            </div>

            <div class="usuarios-acoes">
                <a
                    href="index.php?controller=usuario&action=create"
                    class="usuarios-btn usuarios-btn-cadastrar"
                >
                    + Cadastrar funcionário
                </a>

                <a
                    href="index.php?controller=auth&action=dashboard"
                    class="usuarios-btn usuarios-btn-voltar"
                >
                    Voltar ao painel
                </a>
            </div>
        </div>

        <?php if (isset($mensagensSucesso[$sucesso])): ?>
            <div class="usuarios-alerta sucesso">
                <?= htmlspecialchars($mensagensSucesso[$sucesso], ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if (isset($mensagensErro[$erro])): ?>
            <div class="usuarios-alerta erro">
                <?= htmlspecialchars($mensagensErro[$erro], ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <div class="usuarios-tabela-wrapper">

            <table class="usuarios-tabela">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Perfil</th>
                        <th>Situação</th>
                        <th>Ação</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (empty($usuarios)): ?>

                    <tr>
                        <td colspan="6" class="usuarios-vazio">
                            Nenhum usuário cadastrado.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($usuarios as $usuario): ?>

                        <?php
                        $id = (int) $usuario['id'];
                        $ativo = (int) $usuario['ativo'] === 1;
                        $ehAdmin = $usuario['perfil'] === 'admin';
                        $ehUsuarioLogado = $id === (int) $_SESSION['usuario_id'];
                        ?>

                        <tr>

                            <td>#<?= $id ?></td>

                            <td>
                                <?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?>
                            </td>

                            <td class="usuario-perfil">
                                <?= htmlspecialchars($usuario['perfil'], ENT_QUOTES, 'UTF-8') ?>
                            </td>

                            <td>
                                <span class="usuario-status <?= $ativo ? 'usuario-ativo' : 'usuario-inativo' ?>">
                                    <?= $ativo ? 'Ativo' : 'Inativo' ?>
                                </span>
                            </td>

                            <td>

                               <?php if ($ehUsuarioLogado): ?>

                                   <span class="usuario-status usuario-ativo">
                                         Sua conta
                                   </span>

                                <?php else: ?>

                                    <form
                                        method="POST"
                                        action="index.php?controller=usuario&action=alterarStatus"
                                        onsubmit="return confirm('Tem certeza que deseja <?= $ativo ? 'desativar' : 'reativar' ?> este funcionário?');"
                                    >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= $id ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="ativo"
                                            value="<?= $ativo ? 0 : 1 ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="usuario-acao <?= $ativo ? 'usuario-desativar' : 'usuario-reativar' ?>"
                                        >
                                            <?= $ativo ? 'Desativar' : 'Reativar' ?>
                                        </button>

                                    </form>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>
</html>
