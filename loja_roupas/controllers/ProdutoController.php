<?php
require_once __DIR__ . '/../models/Produto.php';
require_once __DIR__ . '/../models/Categoria.php';
class ProdutoController
{
public function index(): void
{
$this->check();
$produtoModel = new Produto();
$categoriaModel = new Categoria();
$produtos = $produtoModel->listarComCategoria(false);
$categorias = $categoriaModel->listarAtivas();
$editar = null;
if (isset($_GET['id'])) {
$editar = $produtoModel->buscarPorId((int)$_GET['id']);
}
require_once __DIR__ . '/../views/produtos.php';
}
public function salvar(): void
{
    $this->check();
    // $this->onlyAdmin();
    $id = (int)($_POST['id'] ?? 0);
    $categoriaId = (int)($_POST['categoria_id'] ?? 0);
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $descricao = $descricao === '' ? null : $descricao;
    $preco = (float)($_POST['preco'] ?? 0.00);
    $estoque = (int)($_POST['estoque'] ?? 0);

    if ($categoriaId <= 0 || $nome === '') {
        die("Dados inválidos.");
    }

    $produtoModel = new Produto();
    
    if ($id > 0) {
        $produtoModel->atualizar($id, $categoriaId, $nome, $descricao, $preco, $estoque);
        $this->salvarImagemDoProduto($id); 
    } else {
        $novoId = $produtoModel->inserir($categoriaId, $nome, $descricao, $preco, $estoque);
        $this->salvarImagemDoProduto($novoId); 
    }

    header("Location: index.php?controller=produto&action=index");
    exit;
}

//  NOVO MÉTODO DELETAR - ADICIONE AQUI
public function deletar(): void
{
    $this->check();
    if (strtolower($_SESSION['perfil'] ?? '') !== 'admin') {
        die("Acesso negado. Apenas administradores podem excluir produtos.");
    }
    
    $id = (int)($_GET['id'] ?? 0);

    if ($id <= 0) die("ID inválido.");

    $produtoModel = new Produto();
    $produto = $produtoModel->buscarPorId($id);

    if (!$produto) die("Produto não encontrado.");

    $this->deletarImagemDoProduto($id);

    $produtoModel->deletar($id);

    header("Location: index.php?controller=produto&action=index");
    exit;
}
public function toggle(): void
{
    $this->check(); 
    
    $id = (int)($_GET['id'] ?? 0);
    $ativo = (int)($_GET['ativo'] ?? 1);

    if ($id > 0) {
        $produtoModel = new Produto();
        $produtoModel->setAtivo($id, $ativo === 1);
    }

    header("Location: index.php?controller=produto&action=index");
    exit;
}

public function removerImagem(): void
{
    $this->check();
    
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) {
        $this->deletarImagemDoProduto($id);
    }
    
    header("Location: index.php?controller=produto&action=index&id=" . $id);
    exit;
}
// -------------------------
// Upload (POO + seguro)
// -------------------------
private function salvarImagemDoProduto(int $produtoId): void
{
if (!isset($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
return; // sem imagem
}

if (($_FILES['imagem']['size'] ?? 0) > 2 * 1024 * 1024) {
return;
}
$tmp = $_FILES['imagem']['tmp_name'];
$mime = mime_content_type($tmp);
$ext = match ($mime) {
'image/jpeg' => 'jpg',
'image/png' => 'png',
'image/webp' => 'webp',
default => null
};
if ($ext === null) return;
$destDir = __DIR__ . '/../public/uploads/produtos/';
if (!is_dir($destDir)) {
mkdir($destDir, 0777, true);
}
foreach (['jpg','png','webp'] as $e) {
$old = $destDir . $produtoId . '.' . $e;
if (file_exists($old)) unlink($old);
}
$dest = $destDir . $produtoId . '.' . $ext;
move_uploaded_file($tmp, $dest);
}

private function deletarImagemDoProduto(int $produtoId): void
{
$destDir = __DIR__ . '/../public/uploads/produtos/';
foreach (['jpg','png','webp'] as $ext) {
$arquivo = $destDir . $produtoId . '.' . $ext;
if (file_exists($arquivo)) {
unlink($arquivo);
}
}
}


private function check(): void
{
if (!isset($_SESSION['usuario_id'])) {
header("Location: index.php?controller=auth&action=form");
exit;
}
}
private function onlyAdmin(): void
{
    if (($_SESSION['perfil'] ?? '') !== 'admin') {
        die("Acesso negado.");
    }
}
}