
<?php

$clientes = $clientes ?? [];
$variacoes = $variacoes ?? [];
$vendas = $vendas ?? [];

$paginaAtual = 'venda';
$tituloPagina = 'Vendas';
$descricaoPagina = 'Registre e acompanhe as vendas da loja.';

require_once __DIR__ . '/layout/inicio.php';

$mensagemSucesso = $_SESSION['venda_sucesso'] ?? null;
$mensagemErro = $_SESSION['venda_erro'] ?? null;

unset($_SESSION['venda_sucesso'], $_SESSION['venda_erro']);

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

?>

<div class="produtos-main vendas-page">

    <?php if ($mensagemSucesso): ?>
        <div class="venda-alerta sucesso">
            <span>✓</span>
            <?= htmlspecialchars($mensagemSucesso, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if ($mensagemErro): ?>
        <div class="venda-alerta erro">
            <span>!</span>
            <?= htmlspecialchars($mensagemErro, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <!-- NOVA VENDA -->
    <section class="venda-card">

        <div class="venda-card-header">
            <div>
                <span class="venda-etiqueta">MAGIC DONUTS</span>
                <h2>Nova venda</h2>
                <p>Preencha os dados para registrar uma nova venda.</p>
            </div>

            <div class="venda-header-icon">🛒</div>
        </div>

        <form
            method="POST"
            action="index.php?controller=venda&action=salvar"
            id="form-venda"
        >

            <!-- CLIENTE -->
            <div class="venda-secao">

                <div class="venda-secao-titulo">
                    <span class="venda-numero">01</span>
                    <div>
                        <h3>Informações do cliente</h3>
                        <p>Selecione quem está realizando a compra.</p>
                    </div>
                </div>

                <div class="cliente-box">

                    <label for="cliente_id">Cliente cadastrado</label>

                    <select
                        id="cliente_id"
                        name="cliente_id"
                        class="venda-select"
                        required
                    >
                        <option value="">Selecione um cliente</option>

                        <?php foreach ($clientes as $c): ?>
                            <option value="<?= (int) $c['id'] ?>">
                                <?= htmlspecialchars($c['nome'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>

                    </select>

                    <div class="cliente-cpf-area">

                        <div class="cliente-cpf-texto">
                            <strong>Cliente da loja virtual?</strong>
                            <span>Busque o cadastro utilizando o CPF.</span>
                        </div>

                        <div class="cliente-cpf-form">

                            <input
                                type="text"
                                id="cpf_cliente"
                                class="venda-input"
                                placeholder="Digite o CPF"
                                maxlength="14"
                                inputmode="numeric"
                            >

                            <button
                                type="button"
                                id="buscar-cpf"
                                class="btn-buscar-cpf"
                            >
                                Confirmar
                            </button>

                        </div>

                        <div
                            id="cpf-feedback"
                            class="cpf-feedback"
                            aria-live="polite"
                        ></div>

                    </div>

                </div>

            </div>

            <!-- PRODUTOS -->
            <div class="venda-secao">

                <div class="venda-secao-titulo">
                    <span class="venda-numero">02</span>

                    <div>
                        <h3>Produtos da venda</h3>
                        <p>Adicione os produtos e informe as quantidades.</p>
                    </div>
                </div>

                <div class="venda-itens-container">

                    <div class="venda-itens-header">
                        <div>
                            <h3>Itens selecionados</h3>
                            <span>Você pode adicionar quantos produtos precisar.</span>
                        </div>

                        <span class="itens-contador">
                            <strong id="quantidade-itens">0</strong> itens
                        </span>
                    </div>

                    <div id="lista-produtos">

                        <!-- PRIMEIRA LINHA -->
                        <div class="venda-produto-linha">

                            <div class="produto-linha-topo">
                                <span class="produto-linha-numero">
                                    Produto 01
                                </span>

                                <button
                                    type="button"
                                    class="remover-produto"
                                    disabled
                                    title="É necessário manter pelo menos uma linha"
                                >
                                    ×
                                </button>
                            </div>

                            <div class="produto-linha-conteudo">

                                <div class="produto-imagem-box">
                                    <img
                                        src="/magicdonuts/imagem/produto_sem_foto.png"
                                        alt="Imagem do produto"
                                        class="produto-imagem"
                                    >
                                </div>

                                <div class="produto-linha-dados">

                                    <div class="produto-selecao">
                                        <label>Produto / Variação</label>

                                        <select
                                            name="variacao_id[]"
                                            class="venda-select variacao-select"
                                            required
                                        >
                                            <option value="">
                                                Selecione um produto
                                            </option>

                                            <?php foreach ($variacoes as $v): ?>

                                                <option
                                                    value="<?= (int) $v['id'] ?>"
                                                    data-preco="<?= htmlspecialchars((string) $v['preco'], ENT_QUOTES, 'UTF-8') ?>"
                                                    data-produto="<?= htmlspecialchars($v['produto_nome'] ?? 'Produto', ENT_QUOTES, 'UTF-8') ?>"
                                                    data-estoque="<?= (int) ($v['estoque_quantidade'] ?? 0) ?>"
                                                    data-imagem="<?= htmlspecialchars(imagemProdutoUrl((int) $v['produto_id']), ENT_QUOTES, 'UTF-8') ?>"
                                                >
                                                    <?= htmlspecialchars($v['produto_nome'] ?? 'Produto', ENT_QUOTES, 'UTF-8') ?>
                                                    —
                                                    <?= htmlspecialchars($v['variacao_nome'] ?? 'Variação', ENT_QUOTES, 'UTF-8') ?>
                                                    —
                                                    <?= htmlspecialchars($v['sku'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                                </option>

                                            <?php endforeach; ?>

                                        </select>
                                    </div>

                                    <div class="produto-linha-campos">

                                        <div class="venda-field">
                                            <label>Estoque disponível</label>

                                            <div class="estoque-indicador">
                                                <span class="estoque-valor">—</span>
                                                <small>unidades</small>
                                            </div>
                                        </div>

                                        <div class="venda-field">
                                            <label>Quantidade</label>

                                            <input
                                                type="number"
                                                name="quantidade[]"
                                                class="venda-input quantidade-input"
                                                min="0"
                                                step="1"
                                                value="0"
                                            >
                                        </div>

                                        <div class="venda-field">
                                            <label>Preço unitário</label>

                                            <div class="preco-wrapper">
                                                <span>R$</span>

                                                <input
                                                    type="text"
                                                    name="preco_unitario[]"
                                                    class="venda-input preco-input"
                                                    value="0,00"
                                                    inputmode="decimal"
                                                >
                                            </div>
                                        </div>

                                        <div class="venda-field">
                                            <label>Subtotal</label>

                                            <div class="venda-subtotal">
                                                R$ <span class="subtotal-valor">0,00</span>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="produto-feedback"></div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <button
                        type="button"
                        id="adicionar-produto"
                        class="adicionar-produto-btn"
                    >
                        <span>+</span>
                        Selecionar outro produto
                    </button>

                </div>

            </div>

            <!-- OBSERVAÇÃO -->
            <div class="venda-dica">
                <span>💡</span>
                <p>
                    Confira os produtos, as quantidades e os valores antes
                    de finalizar. O estoque será atualizado automaticamente
                    após a confirmação da venda.
                </p>
            </div>

            <!-- RESUMO -->
            <div class="venda-resumo">

                <div class="resumo-info">
                    <span>Quantidade de itens</span>
                    <strong id="resumo-quantidade">0</strong>
                </div>

                <div class="resumo-total">
                    <span>Valor total da venda</span>
                    <strong id="valor-total">R$ 0,00</strong>
                </div>

            </div>

            <button
                type="submit"
                class="finalizar-venda-btn"
                id="finalizar-venda"
            >
                🛒 Finalizar venda
                <span>→</span>
            </button>

        </form>

    </section>

    <!-- VENDAS RECENTES -->
    <section class="vendas-recentes-card">

        <div class="vendas-recentes-header">

            <div>
                <span class="venda-etiqueta">ACOMPANHAMENTO</span>
                <h2>Vendas recentes</h2>
                <p>Últimas vendas realizadas no balcão.</p>
            </div>

            <div class="vendas-recentes-icon">📋</div>

        </div>

        <div class="vendas-tabela-wrapper">

            <table class="vendas-tabela">

                <thead>
                    <tr>
                        <th>Venda</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>Produtos</th>
                        <th>Quantidade</th>
                        <th>Data</th>
                        <th>Valor total</th>
                        <th>Nota fiscal</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($vendas)): ?>

                        <tr>
                            <td colspan="8" class="vendas-vazio">
                                <span>🍩</span>
                                <strong>Nenhuma venda registrada ainda.</strong>
                                <small>As vendas realizadas aparecerão aqui.</small>
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($vendas as $v): ?>

                            <?php $notaEmitida = (int) ($v['nf_emitida'] ?? 0) === 1; ?>

                            <tr>

                                <td>
                                    <span class="venda-id">
                                        #<?= (int) $v['id'] ?>
                                    </span>
                                </td>

                                <td>
                                    <strong class="cliente-nome-tabela">
                                        <?= htmlspecialchars($v['cliente_nome'] ?? 'Não informado', ENT_QUOTES, 'UTF-8') ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= htmlspecialchars($v['vendedor_nome'] ?? 'Não informado', ENT_QUOTES, 'UTF-8') ?>
                                </td>

                                <td>
                                    <div class="venda-produtos-tabela">
                                        <?= htmlspecialchars($v['produtos_nome'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                </td>

                                <td>
                                    <?= (int) ($v['quantidade_itens'] ?? 0) ?>
                                </td>

                                <td>
                                    <?= !empty($v['data'])
                                        ? date('d/m/Y', strtotime($v['data']))
                                        : '—' ?>
                                </td>

                                <td>
                                    <strong class="valor-tabela">
                                        R$
                                        <?= number_format(
                                            (float) $v['valor_total'],
                                            2,
                                            ',',
                                            '.'
                                        ) ?>
                                    </strong>
                                </td>

                                <td>
                                    <div class="nota-acoes">

                                        <?php if (!$notaEmitida): ?>

                                            <form
                                                method="POST"
                                                action="index.php?controller=venda&action=emitirNota"
                                            >
                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?= (int) $v['id'] ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    class="btn-emitir-nota"
                                                >
                                                    Emitir nota
                                                </button>
                                            </form>

                                            <button
                                                type="button"
                                                class="btn-ver-nota"
                                                disabled
                                                title="Emita a nota antes de visualizá-la."
                                            >
                                                Ver NF
                                            </button>

                                        <?php else: ?>

                                            <button
                                                type="button"
                                                class="btn-emitir-nota"
                                                disabled
                                            >
                                                Nota emitida
                                            </button>

                                            <a
                                                href="index.php?controller=venda&action=notaFiscal&id=<?= (int) $v['id'] ?>"
                                                class="btn-ver-nota"
                                            >
                                                Ver NF
                                            </a>

                                        <?php endif; ?>

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


<script>
document.addEventListener('DOMContentLoaded', function () {

    const listaProdutos = document.getElementById('lista-produtos');
    const adicionarBotao = document.getElementById('adicionar-produto');
    const formulario = document.getElementById('form-venda');

    const quantidadeItens = document.getElementById('quantidade-itens');
    const resumoQuantidade = document.getElementById('resumo-quantidade');
    const valorTotal = document.getElementById('valor-total');

    const imagemPadrao = '/magicdonuts/imagem/produto_sem_foto.png';

    function formatarMoeda(valor) {
        return Number(valor).toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function converterPreco(valor) {
        let texto = String(valor).trim();

        if (texto.includes(',')) {
            texto = texto.replace(/\./g, '').replace(',', '.');
        }

        const numero = Number(texto);

        return Number.isFinite(numero) ? numero : 0;
    }

    function atualizarLinha(linha) {

        const select = linha.querySelector('.variacao-select');
        const quantidade = linha.querySelector('.quantidade-input');
        const preco = linha.querySelector('.preco-input');

        const imagem = linha.querySelector('.produto-imagem');
        const estoque = linha.querySelector('.estoque-valor');
        const subtotal = linha.querySelector('.subtotal-valor');
        const feedback = linha.querySelector('.produto-feedback');

        const opcao = select.options[select.selectedIndex];

        if (select.value && opcao) {

            const precoSugerido = Number(opcao.dataset.preco || 0);
            const estoqueDisponivel = Number(opcao.dataset.estoque || 0);

            if (!preco.dataset.editado || preco.dataset.editado !== 'true') {
                preco.value = formatarMoeda(precoSugerido);
            }

            estoque.textContent = estoqueDisponivel;

            imagem.src = opcao.dataset.imagem || imagemPadrao;

            imagem.onerror = function () {
                this.onerror = null;
                this.src = imagemPadrao;
            };

            const qtd = Number(quantidade.value || 0);

            if (qtd > estoqueDisponivel) {
                feedback.textContent = 'Quantidade maior que o estoque disponível.';
                feedback.classList.add('erro');
            } else if (estoqueDisponivel <= 0) {
                feedback.textContent = 'Este produto está sem estoque disponível.';
                feedback.classList.add('erro');
            } else {
                feedback.textContent = '';
                feedback.classList.remove('erro');
            }

        } else {

            estoque.textContent = '—';
            imagem.src = imagemPadrao;
            feedback.textContent = '';
            feedback.classList.remove('erro');

        }

        const qtd = Number(quantidade.value || 0);
        const precoUnitario = converterPreco(preco.value);

        subtotal.textContent = formatarMoeda(qtd * precoUnitario);

        atualizarResumo();
    }

    function atualizarResumo() {

        let total = 0;
        let quantidade = 0;

        document.querySelectorAll('.venda-produto-linha').forEach(linha => {

            const select = linha.querySelector('.variacao-select');
            const qtd = Number(linha.querySelector('.quantidade-input').value || 0);
            const preco = converterPreco(linha.querySelector('.preco-input').value);

            if (select.value && qtd > 0) {
                quantidade += qtd;
                total += qtd * preco;
            }

        });

        quantidadeItens.textContent = quantidade;
        resumoQuantidade.textContent = quantidade;
        valorTotal.textContent = 'R$ ' + formatarMoeda(total);
    }

    function atualizarNumeracao() {

        const linhas = document.querySelectorAll('.venda-produto-linha');

        linhas.forEach((linha, indice) => {

            const numero = String(indice + 1).padStart(2, '0');

            linha.querySelector('.produto-linha-numero').textContent =
                'Produto ' + numero;

            const botao = linha.querySelector('.remover-produto');

            botao.disabled = linhas.length === 1;

        });

    }

    function adicionarLinha() {

        const primeiraLinha = listaProdutos.querySelector('.venda-produto-linha');
        const novaLinha = primeiraLinha.cloneNode(true);

        novaLinha.querySelector('.variacao-select').value = '';
        novaLinha.querySelector('.quantidade-input').value = 0;

        const preco = novaLinha.querySelector('.preco-input');
        preco.value = '0,00';
        delete preco.dataset.editado;

        novaLinha.querySelector('.estoque-valor').textContent = '—';
        novaLinha.querySelector('.subtotal-valor').textContent = '0,00';

        novaLinha.querySelector('.produto-feedback').textContent = '';
        novaLinha.querySelector('.produto-feedback').classList.remove('erro');

        const imagem = novaLinha.querySelector('.produto-imagem');
        imagem.src = imagemPadrao;

        imagem.onerror = function () {
            this.onerror = null;
            this.src = imagemPadrao;
        };

        listaProdutos.appendChild(novaLinha);

        atualizarNumeracao();
        atualizarResumo();
    }

    adicionarBotao.addEventListener('click', adicionarLinha);

    listaProdutos.addEventListener('change', function (evento) {

        const linha = evento.target.closest('.venda-produto-linha');

        if (!linha) return;

        if (evento.target.classList.contains('variacao-select')) {
            const preco = linha.querySelector('.preco-input');
            delete preco.dataset.editado;
        }

        atualizarLinha(linha);
    });

    listaProdutos.addEventListener('input', function (evento) {

        const linha = evento.target.closest('.venda-produto-linha');

        if (!linha) return;

        if (evento.target.classList.contains('preco-input')) {
            evento.target.dataset.editado = 'true';
        }

        if (
            evento.target.classList.contains('quantidade-input') ||
            evento.target.classList.contains('preco-input')
        ) {
            atualizarLinha(linha);
        }

    });

    listaProdutos.addEventListener('click', function (evento) {

        const botao = evento.target.closest('.remover-produto');

        if (!botao || botao.disabled) return;

        botao.closest('.venda-produto-linha').remove();

        atualizarNumeracao();
        atualizarResumo();

    });

    formulario.addEventListener('submit', function (evento) {

        const cliente = document.getElementById('cliente_id');

        if (!cliente.value) {
            evento.preventDefault();
            alert('Selecione um cliente antes de finalizar a venda.');
            cliente.focus();
            return;
        }

        const linhas = document.querySelectorAll('.venda-produto-linha');

        let encontrouItem = false;
        let erro = '';

        linhas.forEach(linha => {

            const select = linha.querySelector('.variacao-select');

            const quantidade = Number(
                linha.querySelector('.quantidade-input').value || 0
            );

            const preco = converterPreco(
                linha.querySelector('.preco-input').value
            );

            const estoque = Number(
                select.options[select.selectedIndex]?.dataset.estoque || 0
            );

            if (select.value && quantidade > 0) {

                encontrouItem = true;

                if (quantidade > estoque) {
                    erro = 'A quantidade de um dos produtos ultrapassa o estoque disponível.';
                }

                if (preco < 0) {
                    erro = 'Existe um preço inválido.';
                }

            }

        });

        if (!encontrouItem) {
            evento.preventDefault();
            alert('Adicione pelo menos um produto à venda.');
            return;
        }

        if (erro) {
            evento.preventDefault();
            alert(erro);
        }

    });

    atualizarNumeracao();
    atualizarResumo();


    // BUSCA DE CLIENTE PELO CPF
    const cpfInput = document.getElementById('cpf_cliente');
    const buscarCpf = document.getElementById('buscar-cpf');
    const cpfFeedback = document.getElementById('cpf-feedback');

    cpfInput.addEventListener('input', function () {

        let cpf = this.value.replace(/\D/g, '').slice(0, 11);

        cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
        cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
        cpf = cpf.replace(/(\d{3})(\d{1,2})$/, '$1-$2');

        this.value = cpf;

    });

    buscarCpf.addEventListener('click', async function () {

        const cpf = cpfInput.value.replace(/\D/g, '');

        if (cpf.length !== 11) {
            cpfFeedback.textContent = 'Informe um CPF válido com 11 dígitos.';
            cpfFeedback.className = 'cpf-feedback erro';
            return;
        }

        cpfFeedback.textContent = 'Consultando cadastro...';
        cpfFeedback.className = 'cpf-feedback carregando';

        buscarCpf.disabled = true;

        try {

            const resposta = await fetch(
                'index.php?controller=venda&action=buscarClienteCPF',
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({ cpf: cpf })
                }
            );

            const dados = await resposta.json();

            if (!resposta.ok || !dados.sucesso) {
                throw new Error(dados.mensagem || 'Cliente não encontrado.');
            }

            const clienteSelect = document.getElementById('cliente_id');

            let opcao = Array.from(clienteSelect.options).find(
                option => option.value === String(dados.cliente.id)
            );

            if (!opcao) {
                opcao = new Option(
                    dados.cliente.nome,
                    dados.cliente.id,
                    true,
                    true
                );

                clienteSelect.add(opcao);
            }

            clienteSelect.value = dados.cliente.id;

            cpfFeedback.textContent =
                'Cliente encontrado: ' + dados.cliente.nome;

            cpfFeedback.className = 'cpf-feedback sucesso';

        } catch (erro) {

            cpfFeedback.textContent = erro.message;
            cpfFeedback.className = 'cpf-feedback erro';

        } finally {

            buscarCpf.disabled = false;

        }

    });

});
</script>
