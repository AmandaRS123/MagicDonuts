
<?php

$topProdutos = $topProdutos ?? [];
$fornecedores = $fornecedores ?? [];
$entradas = $entradas ?? [];
$baixoEstoque = $baixoEstoque ?? [];

$paginaAtual = 'relatorio';
$tituloPagina = 'Relatórios';
$descricaoPagina = 'Acompanhe o desempenho e a movimentação da sua loja.';

require_once __DIR__ . '/layout/inicio.php';

?>

<main class="relatorios-conteudo">

    <!-- APRESENTAÇÃO -->
    <section class="relatorios-apresentacao">
        <div class="relatorios-apresentacao-texto">
            <span class="relatorios-etiqueta">MAGIC DONUTS · GESTÃO</span>
            <h1>Visão geral <span>✦</span></h1>
            <p>
                Todas as informações importantes da sua loja reunidas
                em um só lugar.
            </p>
        </div>

        <div class="relatorios-apresentacao-icone">
            
        </div>
    </section>


    <!-- 1. PRODUTOS MAIS VENDIDOS -->
    <section class="relatorio-card">

        <div class="relatorio-card-title">
            <div class="relatorio-icon relatorio-icon-produtos">🍩</div>

            <div class="relatorio-titulo-texto">
                <span class="relatorio-numero">01 · DESEMPENHO</span>
                <h2>Produtos mais vendidos</h2>
                <p>Produtos com maior quantidade de vendas registradas.</p>
            </div>
        </div>

        <div class="relatorio-table-wrapper">
            <table class="relatorio-table">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Quantidade vendida</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($topProdutos)): ?>

                        <tr>
                            <td colspan="2" class="relatorio-vazio">
                                <span class="relatorio-vazio-icone">🍩</span>
                                <strong>Nenhuma venda registrada ainda.</strong>
                                <small>Os produtos vendidos aparecerão aqui.</small>
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($topProdutos as $i => $p): ?>
                            <tr>
                                <td>
                                    <div class="relatorio-produto">
                                        <span class="relatorio-posicao">
                                            <?= (int)($i + 1) ?>
                                        </span>

                                        <span class="produto-nome">
                                            <?= htmlspecialchars(
                                                $p['nome'] ?? '',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>
                                    </div>
                                </td>

                                <td>
                                    <span class="quantidade-badge">
                                        <?= (int)($p['total_vendido'] ?? 0) ?>
                                        <small>un.</small>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="relatorio-card-rodape">
            <span>Resumo de desempenho dos produtos</span>
            <span class="relatorio-card-rodape-icone">✦</span>
        </div>

    </section>


    <!-- 2. FORNECEDORES -->
    <section class="relatorio-card">

        <div class="relatorio-card-title">
            <div class="relatorio-icon relatorio-icon-fornecedores">📦</div>

            <div class="relatorio-titulo-texto">
                <span class="relatorio-numero">02 · ABASTECIMENTO</span>
                <h2>Fornecedores</h2>
                <p>Fornecedores com maior número de entradas registradas.</p>
            </div>
        </div>

        <div class="relatorio-table-wrapper">
            <table class="relatorio-table">
                <thead>
                    <tr>
                        <th>Fornecedor</th>
                        <th>Entradas</th>
                        <th>Valor total</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($fornecedores)): ?>

                        <tr>
                            <td colspan="3" class="relatorio-vazio">
                                <span class="relatorio-vazio-icone">📦</span>
                                <strong>Nenhum fornecedor encontrado.</strong>
                                <small>Os dados serão exibidos quando houver registros.</small>
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($fornecedores as $f): ?>
                            <tr>
                                <td>
                                    <strong class="fornecedor-nome">
                                        <?= htmlspecialchars(
                                            $f['nome'] ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </strong>
                                </td>

                                <td>
                                    <span class="quantidade-badge">
                                        <?= (int)($f['total_entradas'] ?? 0) ?>
                                    </span>
                                </td>

                                <td>
                                    <strong class="valor-relatorio">
                                        R$ <?= number_format(
                                            (float)($f['valor_total'] ?? 0),
                                            2,
                                            ',',
                                            '.'
                                        ) ?>
                                    </strong>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="relatorio-card-rodape">
            <span>Acompanhamento dos parceiros de fornecimento</span>
            <span class="relatorio-card-rodape-icone">✦</span>
        </div>

    </section>


    <!-- 3. ENTRADAS RECENTES -->
    <section class="relatorio-card">

        <div class="relatorio-card-title">
            <div class="relatorio-icon relatorio-icon-entradas">🚚</div>

            <div class="relatorio-titulo-texto">
                <span class="relatorio-numero">03 · MOVIMENTAÇÃO</span>
                <h2>Entradas recentes</h2>
                <p>Últimas mercadorias recebidas pela loja.</p>
            </div>
        </div>

        <div class="relatorio-table-wrapper">
            <table class="relatorio-table">
                <thead>
                    <tr>
                        <th>Identificação</th>
                        <th>Fornecedor</th>
                        <th>Data</th>
                        <th>Valor</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($entradas)): ?>

                        <tr>
                            <td colspan="4" class="relatorio-vazio">
                                <span class="relatorio-vazio-icone">🚚</span>
                                <strong>Nenhuma entrada registrada ainda.</strong>
                                <small>As movimentações recentes aparecerão aqui.</small>
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($entradas as $e): ?>
                            <tr>
                                <td>
                                    <span class="id-badge">
                                        #<?= (int)($e['id'] ?? 0) ?>
                                    </span>
                                </td>

                                <td>
                                    <strong class="fornecedor-nome">
                                        <?= htmlspecialchars(
                                            $e['fornecedor_nome'] ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </strong>
                                </td>

                                <td>
                                    <span class="data-relatorio">
                                        <?= !empty($e['data'])
                                            ? date('d/m/Y', strtotime($e['data']))
                                            : '—' ?>
                                    </span>
                                </td>

                                <td>
                                    <strong class="valor-relatorio">
                                        R$ <?= number_format(
                                            (float)($e['valor_total'] ?? 0),
                                            2,
                                            ',',
                                            '.'
                                        ) ?>
                                    </strong>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="relatorio-card-rodape">
            <span>Histórico das últimas movimentações</span>
            <span class="relatorio-card-rodape-icone">✦</span>
        </div>

    </section>


    <!-- 4. ESTOQUE BAIXO -->
    <section class="relatorio-card relatorio-card-alerta">

        <div class="relatorio-card-title">
            <div class="relatorio-icon relatorio-icon-alerta">⚠️</div>

            <div class="relatorio-titulo-texto">
                <span class="relatorio-numero relatorio-numero-alerta">
                    04 · ATENÇÃO
                </span>
                <h2>Estoque baixo</h2>
                <p>Produtos que precisam de atenção e possível reposição.</p>
            </div>

            <span class="relatorio-status-alerta">
                Monitoramento
            </span>
        </div>

        <div class="relatorio-table-wrapper">
            <table class="relatorio-table">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>SKU</th>
                        <th>Quantidade atual</th>
                        <th>Estoque mínimo</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($baixoEstoque)): ?>

                        <tr>
                            <td colspan="4" class="relatorio-vazio relatorio-vazio-sucesso">
                                <span class="relatorio-vazio-icone">✓</span>
                                <strong>Estoque em dia!</strong>
                                <small>Nenhum item está abaixo do estoque mínimo.</small>
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($baixoEstoque as $b): ?>
                            <tr>
                                <td>
                                    <strong class="fornecedor-nome">
                                        <?= htmlspecialchars(
                                            $b['produto_nome'] ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </strong>
                                </td>

                                <td>
                                    <span class="sku-badge">
                                        <?= htmlspecialchars(
                                            $b['sku'] ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="estoque-baixo-badge">
                                        <?= (int)($b['quantidade'] ?? 0) ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="estoque-minimo">
                                        <?= (int)($b['minimo'] ?? 0) ?> un.
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="relatorio-card-rodape relatorio-card-rodape-alerta">
            <span>Verifique os itens sinalizados para evitar falta de produtos</span>
            <span>⚠</span>
        </div>

    </section>


    <!-- EXPORTAÇÃO -->
    <section class="relatorios-exportacao">

        <div class="relatorios-exportacao-texto">
            <div class="relatorios-exportacao-icone">📄</div>

            <div>
                <h2>Seu relatório, pronto para compartilhar.</h2>
                <p>Gere uma versão em PDF com os dados apresentados nesta página.</p>
            </div>
        </div>

        <a
            href="index.php?controller=relatorio&action=exportarPDF"
            class="relatorios-exportar-btn"
            target="_blank"
            rel="noopener"
        >
            <span>↓</span>
            Exportar PDF
        </a>

    </section>

</main>

<?php require_once __DIR__ . '/layout/fim.php'; ?>
