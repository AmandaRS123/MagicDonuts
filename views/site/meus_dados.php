
<?php
$tituloPagina = 'Minha conta | Magic Donuts';

$cpf = preg_replace('/\D/', '', $cliente['cpf'] ?? '');
$telefone = preg_replace('/\D/', '', $cliente['telefone'] ?? '');

$cpfFormatado = strlen($cpf) === 11
    ? substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2)
    : $cpf;

$telefoneFormatado = strlen($telefone) === 11
    ? '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 5) . '-' . substr($telefone, 7, 4)
    : $telefone;
?>

<style>
    /* PÁGINA MINHA CONTA */
    .dados-pagina {
        padding: 45px 0;
        background: #faf8ff;
    }

    .dados-card {
        max-width: 850px;
        margin: 0 auto;
        background: transparent;
        padding: 0;
        box-shadow: none;
    }

    .dados-card > h1 {
        color: #392477;
        font-size: 30px;
        margin-bottom: 8px;
    }

    .dados-subtitulo {
        color: #777;
        margin-bottom: 30px;
    }

    /* CARTÕES SEPARADOS */
    .dados-secao {
        background: #fff;
        margin-top: 22px;
        padding: 30px;
        border: 1px solid #eee6fa;
        border-radius: 18px;
        box-shadow: 0 5px 20px rgba(57, 36, 119, 0.07);
    }

    .dados-secao h2 {
        color: #392477;
        font-size: 21px;
        margin: 0 0 22px;
        padding-bottom: 14px;
        border-bottom: 1px solid #eee;
    }

    /* CAMPOS */
    .dados-campo {
        margin-bottom: 18px;
    }

    .dados-campo label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #444;
    }

    .dados-campo input {
        width: 100%;
        padding: 13px;
        border: 1px solid #ddd;
        border-radius: 9px;
        font-size: 15px;
        box-sizing: border-box;
        transition: border-color .2s;
    }

    .dados-campo input:focus {
        outline: none;
        border-color: #a855f7;
        box-shadow: 0 0 0 3px rgba(168, 85, 247, .10);
    }

    .dados-campo input[readonly] {
        background: #f5f5f5;
        color: #777;
        cursor: not-allowed;
    }

    .dados-aviso {
        color: #777;
        font-size: 13px;
        margin-top: 8px;
    }

    .cep-status {
        font-size: 13px;
        margin-top: 8px;
        color: #777;
    }

    .cep-status.erro {
        color: #dc2626;
    }

    .cep-status.sucesso {
        color: #15803d;
    }

    /* BOTÕES */
    .dados-botao {
        border: none;
        padding: 13px 22px;
        border-radius: 9px;
        cursor: pointer;
        font-weight: 600;
        margin-top: 5px;
        transition: .2s;
    }

    .dados-botao-principal {
        background: #a855f7;
        color: white;
    }

    .dados-botao-principal:hover {
        background: #8736d0;
    }

    .dados-botao-excluir {
        background: #dc2626;
        color: white;
    }

    .dados-botao-excluir:hover {
        background: #b91c1c;
    }

    /* ÁREA DE EXCLUSÃO */
    .dados-secao:last-child {
        border-color: #fecaca;
    }

    .dados-secao:last-child h2 {
        color: #dc2626;
    }

    /* MENSAGENS */
    .dados-alerta {
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .dados-alerta-erro {
        background: #fee2e2;
        color: #b91c1c;
    }

    .dados-alerta-sucesso {
        background: #dcfce7;
        color: #166534;
    }

    /* CELULAR */
    @media (max-width: 600px) {
        .dados-card {
            padding: 0 12px;
        }

        .dados-secao {
            padding: 20px;
        }

        .dados-card > h1 {
            font-size: 25px;
        }

        .dados-botao {
            width: 100%;
        }
    }
</style>

<section class="pagina dados-pagina">
    <div class="container">
        <div class="dados-card">

            <h1>Olá, <?= e($cliente['nome']) ?>! 💜</h1>

            <p class="dados-subtitulo">
                Aqui você pode consultar e atualizar as informações da sua conta.
            </p>

            <?php if (!empty($erro)): ?>
                <div class="dados-alerta dados-alerta-erro">
                    <?= e($erro) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($sucesso)): ?>
                <div class="dados-alerta dados-alerta-sucesso">
                    <?= e($sucesso) ?>
                </div>
            <?php endif; ?>

            <!-- DADOS PESSOAIS -->
            <div class="dados-secao">
                <h2>Meus dados</h2>

                <form method="post" action="<?= e(url_site('cliente', 'atualizarDados')) ?>">
                    <?= csrf_campo() ?>

                    <div class="dados-campo">
                        <label for="nome">Nome completo</label>
                        <input
                            type="text"
                            id="nome"
                            name="nome"
                            value="<?= e($cliente['nome'] ?? '') ?>"
                            required
                        >
                    </div>

                    <div class="dados-campo">
                        <label for="cpf">CPF</label>
                        <input
                            type="text"
                            id="cpf"
                            name="cpf"
                            value="<?= e($cpfFormatado) ?>"
                            readonly
                        >
                        <p class="dados-aviso">O CPF não pode ser alterado.</p>
                    </div>

                    <div class="dados-campo">
                        <label for="email">E-mail</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?= e($cliente['email'] ?? '') ?>"
                            required
                        >
                    </div>

                    <div class="dados-campo">
                        <label for="telefone">Telefone</label>
                        <input
                            type="text"
                            id="telefone"
                            name="telefone"
                            value="<?= e($telefoneFormatado) ?>"
                            placeholder="(00) 00000-0000"
                            maxlength="15"
                            inputmode="numeric"
                            required
                        >
                    </div>

                    <div class="dados-campo">
                        <label for="cep">CEP</label>
                        <input
                            type="text"
                            id="cep"
                            placeholder="00000-000"
                            maxlength="9"
                            inputmode="numeric"
                            autocomplete="postal-code"
                        >
                        <p class="cep-status" id="cep-status">
                            Digite o CEP para preencher seu endereço automaticamente.
                        </p>
                    </div>

                    <div class="dados-campo">
                        <label for="endereco">Endereço</label>
                        <input
                            type="text"
                            id="endereco"
                            name="endereco"
                            value="<?= e($cliente['endereco'] ?? '') ?>"
                            placeholder="Rua, número, bairro, cidade e estado"
                            required
                        >
                        <p class="dados-aviso">
                            Confira o endereço preenchido e acrescente o número e o complemento, se necessário.
                        </p>
                    </div>

                    <button type="submit" class="dados-botao dados-botao-principal">
                        Salvar alterações
                    </button>
                </form>
            </div>

            <!-- ALTERAR SENHA -->
            <div class="dados-secao">
                <h2>Alterar senha</h2>

                <form method="post" action="<?= e(url_site('cliente', 'alterarSenha')) ?>">
                    <?= csrf_campo() ?>

                    <div class="dados-campo">
                        <label for="senha_atual">Senha atual</label>
                        <input
                            type="password"
                            id="senha_atual"
                            name="senha_atual"
                            autocomplete="current-password"
                            required
                        >
                    </div>

                    <div class="dados-campo">
                        <label for="nova_senha">Nova senha</label>
                        <input
                            type="password"
                            id="nova_senha"
                            name="nova_senha"
                            autocomplete="new-password"
                            required
                        >
                    </div>

                    <div class="dados-campo">
                        <label for="confirmar_senha">Confirmar nova senha</label>
                        <input
                            type="password"
                            id="confirmar_senha"
                            name="confirmar_senha"
                            autocomplete="new-password"
                            required
                        >
                    </div>

                    <button type="submit" class="dados-botao dados-botao-principal">
                        Alterar senha
                    </button>
                </form>
            </div>

            <!-- EXCLUIR CONTA -->
            <div class="dados-secao">
                <h2>Excluir minha conta</h2>

                <p>
                    Se você deseja excluir sua conta, informe sua senha para confirmar.
                    Essa ação pode ser permanente.
                </p>

                <form
                    method="post"
                    action="<?= e(url_site('cliente', 'excluirConta')) ?>"
                    onsubmit="return confirm('Tem certeza de que deseja excluir sua conta? Essa ação pode ser permanente.');"
                >
                    <?= csrf_campo() ?>

                    <div class="dados-campo">
                        <label for="senha_exclusao">Digite sua senha</label>
                        <input
                            type="password"
                            id="senha_exclusao"
                            name="senha_exclusao"
                            autocomplete="current-password"
                            required
                        >
                    </div>

                    <button type="submit" class="dados-botao dados-botao-excluir">
                        Excluir minha conta
                    </button>
                </form>
            </div>

        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const telefone = document.getElementById('telefone');
    const cep = document.getElementById('cep');
    const endereco = document.getElementById('endereco');
    const cepStatus = document.getElementById('cep-status');

    function somenteNumeros(valor) {
        return valor.replace(/\D/g, '');
    }

    // FORMATAÇÃO DO TELEFONE
    function formatarTelefone(valor) {
        let numeros = somenteNumeros(valor).substring(0, 11);

        if (numeros.length === 0) return '';
        if (numeros.length <= 2) return '(' + numeros;

        if (numeros.length <= 7) {
            return '(' + numeros.substring(0, 2) + ') ' + numeros.substring(2);
        }

        return '(' + numeros.substring(0, 2) + ') '
            + numeros.substring(2, 7) + '-'
            + numeros.substring(7);
    }

    telefone.addEventListener('input', function () {
        this.value = formatarTelefone(this.value);
    });

    // FORMATAÇÃO DO CEP
    function formatarCep(valor) {
        let numeros = somenteNumeros(valor).substring(0, 8);

        if (numeros.length > 5) {
            return numeros.substring(0, 5) + '-' + numeros.substring(5);
        }

        return numeros;
    }

    // CONSULTA AO VIACEP
    let ultimaConsulta = '';

    async function buscarCep() {
        const numeros = somenteNumeros(cep.value);

        if (numeros.length !== 8) {
            cepStatus.textContent = 'Digite um CEP com 8 números.';
            cepStatus.className = 'cep-status erro';
            return;
        }

        if (numeros === ultimaConsulta) {
            return;
        }

        ultimaConsulta = numeros;

        cepStatus.textContent = 'Consultando CEP...';
        cepStatus.className = 'cep-status';

        try {
            const resposta = await fetch(
                'https://viacep.com.br/ws/' + numeros + '/json/'
            );

            if (!resposta.ok) {
                throw new Error('Não foi possível consultar o CEP.');
            }

            const dados = await resposta.json();

            if (dados.erro) {
                cepStatus.textContent = 'CEP não encontrado. Confira os números.';
                cepStatus.className = 'cep-status erro';
                ultimaConsulta = '';
                return;
            }

            const partesEndereco = [
                dados.logradouro,
                dados.bairro,
                dados.localidade,
                dados.uf
            ].filter(Boolean);

            endereco.value = partesEndereco.join(', ');

            cepStatus.textContent = 'Endereço preenchido! Confira e complete com o número e complemento.';
            cepStatus.className = 'cep-status sucesso';

        } catch (erro) {
            cepStatus.textContent = 'Não foi possível consultar o CEP. Verifique sua conexão e tente novamente.';
            cepStatus.className = 'cep-status erro';
            ultimaConsulta = '';
        }
    }

    cep.addEventListener('input', function () {
        this.value = formatarCep(this.value);

        const numeros = somenteNumeros(this.value);

        if (numeros.length < 8) {
            ultimaConsulta = '';
            cepStatus.textContent = 'Digite o CEP para preencher seu endereço automaticamente.';
            cepStatus.className = 'cep-status';
        }

        if (numeros.length === 8) {
            buscarCep();
        }
    });

    cep.addEventListener('blur', function () {
        if (somenteNumeros(this.value).length === 8) {
            buscarCep();
        }
    });
});
</script>
