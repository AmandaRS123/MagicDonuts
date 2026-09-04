<?php
// helper simples
function imagemProdutoUrl(int $produtoId): string
{
    $baseFs = __DIR__ . "/../public/uploads/produtos/";
    $baseUrl = "public/uploads/produtos/";
    foreach (['jpg','png','webp'] as $ext) {
        if (file_exists($baseFs . $produtoId . '.' . $ext)) {
            return $baseUrl . $produtoId . '.' . $ext;
        }
    }
    return "public/assets/img/produto_sem_foto.png";
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Produtos</title>
    <link rel="stylesheet" href="public/assets/css/style.css">
</head>
<body class="pagina-produtos">
<div class="header">
    <div class="container header-inner">
        <div class="cabecaproduto">
            <strong>Loja Nerdis</strong>
            <span class="badge">Produtos</span>
        </div>
        <div class="user">
   <span style="color: black;">Olá,</span> <strong><?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário') ?></strong>
    <span style="margin: 8px;">|</span>
    <a href="index.php?controller=auth&action=dashboard" style="margin-right: 8px; color: black;">Inicio</a>
    <span style="margin: 4px;">•</span>
    <a href="index.php?controller=auth&action=logout" style="color: black;">Sair</a>
</div>
</div>
    </div>
</div>

<div class="container grid">
    <div class="card">
        <h2><?= $editar ? "Editar Produto #".(int)$editar['id_produto'] : "Cadastrar Produto" ?></h2>
        <form method="post" action="index.php?controller=produto&action=salvar" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $editar ? (int)$editar['id_produto'] : 0 ?>">
            
            <div class="form-group">
                <label style="color: black;">Categoria</label>
                <select class="input" name="categoria_id" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($categorias as $c): ?>
                        <option value="<?= (int)$c['id_categoria'] ?>"
                            <?= $editar && (int)$editar['categoria_id'] === (int)$c['id_categoria'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label style="color: black;">Nome</label>
                <input class="input" type="text" name="nome" required
                       value="<?= $editar ? htmlspecialchars($editar['nome']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label style="color: black;">Descrição (opcional)</label>
                <textarea class="input" name="descricao" rows="3"><?= $editar ? htmlspecialchars($editar['descricao'] ?? '') : '' ?></textarea>
            </div>

            <div class="form-group">
                <label style="color: black;">Preço (R$)</label>
                <input class="input" type="number" step="0.01" name="preco" required
                       value="<?= $editar ? (float)$editar['preco'] : '0.00' ?>">
            </div>

            <div class="form-group">
                <label style="color: black;">Quantidade em Estoque</label>
                <input class="input" type="number" name="estoque" required
                       value="<?= $editar ? (int)$editar['estoque'] : '0' ?>">
            </div>

            <div class="form-group">
                <label style="color: black;">Imagem do produto (opcional)</label>
                <input class="input" type="file" name="imagem" accept="image/png, image/jpeg, image/webp">
                <small class="muted" style="display: block; margin-bottom: 5px;">Formatos: JPG, PNG, WEBP (até 2MB). Salva como ID do produto.</small>
                
                <?php if (isset($editar) && $editar): ?>
                    <?php
                    // Verifica se existe alguma imagem física salva para este ID
                    $caminhoUploads = __DIR__ . '/../public/uploads/produtos/';
                    $temImagem = false;
                    foreach (['jpg', 'png', 'webp'] as $ext) {
                        if (file_exists($caminhoUploads . $editar['id_produto'] . '.' . $ext)) {
                            $temImagem = true;
                            break;
                        }
                    }
                    ?>
                    
                    <?php if ($temImagem): ?>
                        <div style="margin-top: 12px; margin-bottom: 5px;">
                            <a class="btn3" 
                               style="color: #ff4d4d; width: 100%; display: inline-flex; justify-content: center; align-items: center; box-sizing: border-box;" 
                               href="index.php?controller=produto&action=removerImagem&id=<?= $editar['id_produto'] ?>"
                               onclick="return confirm('Tem certeza que deseja apagar a imagem deste produto?');">
                               Deletar Imagem Atual
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            
            <div class="actions">
                <button style="color: black;" class="btn btn-primary" type="submit">Salvar</button>
                <a style="color: black;" class="btn" href="index.php?controller=produto&action=index">Limpar</a>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>Lista de Produtos</h2>
        <table class="table">
            <thead>
                <tr>
                    <th style="color: black;">Imagem</th>
                    <th style="color: black;">ID</th>
                    <th style="color: black;">Nome</th>
                    <th style="color: black;">Categoria</th>
                    <th style="color: black;">Preço</th>
                    <th style="color: black;">Estoque</th>
                    <th style="color: black;">Status</th>
                    <th style="width:220px; color: black;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $posicao = 1; 
                foreach ($produtos as $p): 
                ?>
                <tr>
                    <td>
                        <img class="thumb" src="<?= imagemProdutoUrl((int)$p['id_produto']) ?>" alt="produto">
                    </td>
                    
                    <td style="color: black;">#<?= $posicao ?></td>
                    
                    <td style="color: black;"><?= htmlspecialchars($p['nome']) ?></td>
                    
                    <td style="color: black;"><?= htmlspecialchars($p['categoria_nome']) ?></td>
                    
                    <td style="color: black;">R$ <?= number_format((float)($p['preco'] ?? 0), 2, ',', '.') ?></td>
                    
                    <td style="color: black;"><?= (int)($p['estoque'] ?? 0) ?> un</td>
                    
                    <td>
                        <?php if (isset($p['ativo']) && (int)$p['ativo'] === 1): ?>
                            <span style="color: black;" class="tag ok">Ativo</span>
                        <?php else: ?>
                            <span style="color: black;" class="tag off">Inativo</span>
                        <?php endif; ?>
                    </td>
                    
                    <td>
                        <div style="display: flex; flex-direction: column; gap: 4px;">
                            <a style="color: black;" class="btn" href="index.php?controller=produto&action=index&id=<?= (int)$p['id_produto'] ?>">Editar</a>
                            
                            <?php if (isset($p['ativo']) && (int)$p['ativo'] === 1): ?>
                                <a style="color: black;" class="btn btn-danger"
                                   href="index.php?controller=produto&action=toggle&id=<?= (int)$p['id_produto'] ?>&ativo=0"
                                   onclick="return confirm('Inativar este produto?')">Inativar</a>
                            <?php else: ?>
                                <a style="color: black;" class="btn btn-success"
                                   href="index.php?controller=produto&action=toggle&id=<?= (int)$p['id_produto'] ?>&ativo=1">Ativar</a>
                            <?php endif; ?>
                            
                            <a class="btn btn-danger" style="color: red;"
                               href="index.php?controller=produto&action=deletar&id=<?= (int)$p['id_produto'] ?>"
                               onclick="return confirm('⚠️ DELETAR permanentemente? Não há volta!')">Excluir</a>
                        </div>
                    </td>
                </tr>
                <?php 
                $posicao++; 
                endforeach; 
                ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>