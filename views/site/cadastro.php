
<?php

$tituloPagina = 'Criar minha conta';

$nome = $_POST['nome'] ?? '';
$cpf = $_POST['cpf'] ?? '';
$email = $_POST['email'] ?? '';
$telefone = $_POST['telefone'] ?? '';
$endereco = $_POST['endereco'] ?? '';

?>

<style>
    .campo input.campo-invalido {
        border: 2px solid #dc2626 !important;
        outline: none;
    }

    .campo input.campo-valido {
        border: 2px solid #16a34a;
    }

    .mensagem-campo {
        display: block;
        color: #dc2626;
        font-size: 13px;
        margin-top: 5px;
        font-weight: 500;
    }
</style>

<section class="pagina pagina-autenticacao">
    <div class="container">

        <?php if (!empty($erro)): ?>
            <div class="mensagem-erro" role="alert">
                <?= e($erro) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($sucesso)): ?>
            <div class="mensagem-sucesso" role="status">
                <?= e($sucesso) ?>
            </div>
        <?php endif; ?>

        <div class="auth-card auth-card-largo">

            <div class="auth-intro">
                <span class="auth-icone">
                    <i class="fa-solid fa-user-plus" aria-hidden="true"></i>
                </span>

                <h1>Crie sua conta!</h1>

                <p>
                    Faça seu cadastro e aproveite todas as delícias
                    da Magic Donuts.
                </p>
            </div>

            <form
                method="post"
                action="<?= e(url_site('cliente', 'cadastrar')) ?>"
                class="form-auth"
                id="formCadastro"
                novalidate
            >

                <?= csrf_campo() ?>

                <div class="campo">
                    <label for="nome">Nome completo</label>
                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        placeholder="Digite seu nome completo"
                        value="<?= e($nome) ?>"
                        maxlength="100"
                        autocomplete="name"
                        required
                    >
                </div>

                <div class="campo">
                    <label for="cpf">CPF</label>
                    <input
                        type="text"
                        id="cpf"
                        name="cpf"
                        placeholder="000.000.000-00"
                        value="<?= e($cpf) ?>"
                        maxlength="14"
                        inputmode="numeric"
                        autocomplete="off"
                        required
                    >
                </div>

                <div class="campo">
                    <label for="email">E-mail</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="seuemail@exemplo.com"
                        value="<?= e($email) ?>"
                        maxlength="100"
                        autocomplete="email"
                        required
                    >
                </div>

                <div class="campo">
                    <label for="telefone">Telefone</label>
                    <input
                        type="tel"
                        id="telefone"
                        name="telefone"
                        placeholder="(00) 00000-0000"
                        value="<?= e($telefone) ?>"
                        maxlength="15"
                        inputmode="numeric"
                        autocomplete="tel"
                    >
                </div>

                <div class="campo">
                    <label for="endereco">Endereço completo</label>
                    <input
                        type="text"
                        id="endereco"
                        name="endereco"
                        placeholder="Rua, número, bairro, cidade e CEP"
                        value="<?= e($endereco) ?>"
                        maxlength="255"
                        autocomplete="street-address"
                        required
                    >
                </div>

                <div class="campo">
                    <label for="senha">Senha</label>
                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        placeholder="Crie uma senha com pelo menos 8 caracteres"
                        minlength="8"
                        autocomplete="new-password"
                        required
                    >

                    <small>
                        Sua senha deve possuir pelo menos 8 caracteres.
                    </small>
                </div>

                <div class="campo">
                    <label for="confirmar_senha">Confirmar senha</label>
                    <input
                        type="password"
                        id="confirmar_senha"
                        name="confirmar_senha"
                        placeholder="Digite sua senha novamente"
                        minlength="8"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-auth">
                    Criar minha conta
                </button>

            </form>

            <p class="auth-alternativa">
                Já possui cadastro?

                <a href="<?= e(url_site('cliente', 'login')) ?>">
                    Fazer login
                </a>
            </p>

            <a href="<?= e(url_site('loja', 'index')) ?>" class="auth-voltar">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                Voltar para a loja
            </a>

        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const formulario = document.getElementById('formCadastro');

    const campoCpf = document.getElementById('cpf');
    const campoTelefone = document.getElementById('telefone');

    const campos = {
        nome: document.getElementById('nome'),
        cpf: campoCpf,
        email: document.getElementById('email'),
        endereco: document.getElementById('endereco'),
        senha: document.getElementById('senha'),
        confirmar_senha: document.getElementById('confirmar_senha')
    };

    // MÁSCARA DO CPF
    function formatarCPF(valor) {
        let numeros = valor.replace(/\D/g, '').slice(0, 11);

        numeros = numeros.replace(/(\d{3})(\d)/, '$1.$2');
        numeros = numeros.replace(/(\d{3})(\d)/, '$1.$2');
        numeros = numeros.replace(/(\d{3})(\d{1,2})$/, '$1-$2');

        return numeros;
    }

    campoCpf.addEventListener('input', function () {
        this.value = formatarCPF(this.value);
    });

    campoCpf.value = formatarCPF(campoCpf.value);

    // MÁSCARA DO TELEFONE
    function formatarTelefone(valor) {
        let numeros = valor.replace(/\D/g, '').slice(0, 11);

        if (numeros.length === 0) return '';

        if (numeros.length < 3) return '(' + numeros;

        if (numeros.length <= 6) {
            return '(' + numeros.slice(0, 2) + ') ' + numeros.slice(2);
        }

        if (numeros.length <= 10) {
            return '(' + numeros.slice(0, 2) + ') ' +
                numeros.slice(2, 6) + '-' + numeros.slice(6);
        }

        return '(' + numeros.slice(0, 2) + ') ' +
            numeros.slice(2, 7) + '-' + numeros.slice(7);
    }

    campoTelefone.addEventListener('input', function () {
        this.value = formatarTelefone(this.value);
    });

    campoTelefone.value = formatarTelefone(campoTelefone.value);

    // VALIDAÇÃO DOS CAMPOS
    function verificarCampo(campo) {

        const valor = campo.value.trim();
        let mensagem = '';

        switch (campo.id) {

            case 'nome':
                if (valor === '') {
                    mensagem = 'Este campo é obrigatório.';
                } else if (valor.length < 3) {
                    mensagem = 'Informe seu nome completo.';
                }
                break;

            case 'cpf':
                if (valor === '') {
                    mensagem = 'Informe seu CPF.';
                } else if (valor.replace(/\D/g, '').length !== 11) {
                    mensagem = 'O CPF deve conter 11 números.';
                }
                break;

            case 'email':
                if (valor === '') {
                    mensagem = 'Informe seu e-mail.';
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valor)) {
                    mensagem = 'Digite um e-mail válido.';
                }
                break;

            case 'endereco':
                if (valor === '') {
                    mensagem = 'Informe seu endereço de entrega.';
                }
                break;

            case 'senha':
                if (valor === '') {
                    mensagem = 'Informe uma senha.';
                } else if (valor.length < 8) {
                    mensagem = 'A senha precisa ter pelo menos 8 caracteres.';
                }
                break;

            case 'confirmar_senha':
                if (valor === '') {
                    mensagem = 'Confirme sua senha.';
                } else if (valor !== campos.senha.value) {
                    mensagem = 'As senhas não coincidem.';
                }
                break;
        }

        mostrarErro(campo, mensagem);

        return mensagem === '';
    }

    // MOSTRAR OU REMOVER ERRO
    function mostrarErro(campo, mensagem) {

        const container = campo.closest('.campo');

        let mensagemElemento = container.querySelector('.mensagem-campo');

        if (!mensagemElemento) {
            mensagemElemento = document.createElement('small');
            mensagemElemento.className = 'mensagem-campo';
            mensagemElemento.setAttribute('role', 'alert');
            campo.insertAdjacentElement('afterend', mensagemElemento);
        }

        if (mensagem) {
            campo.classList.add('campo-invalido');
            campo.classList.remove('campo-valido');

            mensagemElemento.textContent = mensagem;
            mensagemElemento.style.display = 'block';

            campo.setAttribute('aria-invalid', 'true');

        } else {
            campo.classList.remove('campo-invalido');
            campo.classList.add('campo-valido');

            mensagemElemento.textContent = '';
            mensagemElemento.style.display = 'none';

            campo.removeAttribute('aria-invalid');
        }
    }

    // VALIDAR AO SAIR DO CAMPO
    Object.values(campos).forEach(function (campo) {

        campo.addEventListener('blur', function () {
            verificarCampo(campo);
        });

        campo.addEventListener('input', function () {

            if (campo.classList.contains('campo-invalido') ||
                campo.classList.contains('campo-valido')) {
                verificarCampo(campo);
            }

            if (campo.id === 'senha' &&
                campos.confirmar_senha.value !== '') {
                verificarCampo(campos.confirmar_senha);
            }
        });

    });

    // VALIDAR AO ENVIAR
    formulario.addEventListener('submit', function (evento) {

        let formularioValido = true;
        let primeiroCampoComErro = null;

        Object.values(campos).forEach(function (campo) {

            if (!verificarCampo(campo)) {
                formularioValido = false;

                if (!primeiroCampoComErro) {
                    primeiroCampoComErro = campo;
                }
            }

        });

        if (!formularioValido) {
            evento.preventDefault();

            primeiroCampoComErro.focus();
        }

    });

});
</script>
