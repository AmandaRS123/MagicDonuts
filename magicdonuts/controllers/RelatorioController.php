<?php

require_once __DIR__ . '/../models/Relatorio.php';
require_once __DIR__ . '/../models/Estoque.php';

class RelatorioController
{
public function index(): void
{
$this->check();

$relatorioModel = new Relatorio();
$estoqueModel = new Estoque();

$topProdutos = $relatorioModel->topProdutosMaisVendidos();
$fornecedores = $relatorioModel->fornecedoresComMaisEntregas();
$entradas = $relatorioModel->entradasRecentes();
$baixoEstoque = $estoqueModel->listarBaixoEstoque();

require_once __DIR__ . '/../views/relatorios.php';
}

private function check(): void
{
if (!isset($_SESSION['usuario_id'])) {
header("Location: index.php?controller=auth&action=form");
exit;
}
}
}