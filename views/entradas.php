<?php

$nomeUser = $_SESSION['nome'] ?? 'Usuário';

?>

<!doctype html>

<html lang="pt-br">

<head>

<meta charset="utf-8">

<title>Entradas de Mercadoria</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link
rel="stylesheet"
href="/magicdonuts/public/assets/css/style.css"
>

<link rel="icon" type="image/jpeg" href="/magicdonuts/imagem/logo.jpg">

</head>

<body class="entradas-body">

<div class="entradas-container">

<!-- TOPO -->

<div class="entradas-topbar">

<div class="entradas-brand">

<div class="entradas-logo">

<img
src="/magicdonuts/imagem/logo.jpg"
alt="Logo Magic Donuts"
>

</div>

<div>

<h1>Entradas de Mercadoria</h1>

<span>Compra e reposição de estoque</span>

</div>

</div>


<div class="entradas-user">

<div class="entradas-user-info">

<span class="entradas-user-label">
Olá,
</span>

<strong>
<?= htmlspecialchars($nomeUser) ?>
</strong>

</div>


<a
class="entradas-logout"
href="/magicdonuts/index.php?controller=auth&action=logout"
>

Sair

</a>

</div>

</div>


<!-- FORMULÁRIO DE NOVA ENTRADA -->

<div class="entrada-card">

<div class="entrada-card-title">

<div class="entrada-icon">

<svg
viewBox="0 0 24 24"
fill="none"
xmlns="http://www.w3.org/2000/svg"
>
<path d="M4 7L12 3L20 7L12 11L4 7Z"/>
<path d="M4 7V17L12 21L20 17V7"/>
<path d="M12 11V21"/>
</svg>

</div>

<div>

<h2>Registrar Nova Entrada</h2>

<p>
Informe o fornecedor e os produtos recebidos.
</p>

</div>

</div>


<form
method="post"
action="index.php?controller=entrada&action=salvar"
>

<!-- FORNECEDOR -->

<div class="entrada-campo">

<label for="fornecedor_id">
Fornecedor
</label>

<div class="entrada-select">

<select
class="entrada-input"
name="fornecedor_id"
id="fornecedor_id"
required
>

<option value="">
-- Selecione...
</option>

<?php foreach ($fornecedores as $f): ?>

<option
value="<?= (int)$f['id'] ?>"
>

<?= htmlspecialchars($f['nome']) ?>

</option>

<?php endforeach; ?>

</select>

<span class="entrada-seta">⌄</span>

</div>

</div>


<!-- ITENS DA ENTRADA -->

<div class="entrada-itens-titulo">

<div class="entrada-mini-icon">

<svg
viewBox="0 0 24 24"
fill="none"
xmlns="http://www.w3.org/2000/svg"
>
<path d="M20 13L13 20L4 11V4H11L20 13Z"/>
<circle cx="8" cy="8" r="1"/>
</svg>

</div>

<div>

<h3>Itens da entrada</h3>

<p>
Preencha uma linha para cada variação (SKU)
recebida. Deixe quantidade "0" para ignorar
uma linha.
</p>

</div>

</div>


<!-- TABELA DE ITENS -->

<div class="entrada-table-wrapper">

<table
class="entrada-table"
id="tabela-itens"
>

<thead>

<tr>

<th>
Variação (SKU)
</th>

<th class="entrada-col-quantidade">
Quantidade
</th>

<th class="entrada-col-custo">
Custo Unitário (R$)
</th>

</tr>

</thead>


<tbody>

<?php for ($linha = 0; $linha < 5; $linha++): ?>

<tr>

<td>

<div class="entrada-select">

<select
class="entrada-input"
name="variacao_id[]"
>

<option value="">
-- não usar esta linha --
</option>

<?php foreach ($variacoes as $v): ?>

<option
value="<?= (int)$v['id'] ?>"
>

<?= htmlspecialchars($v['produto_nome']) ?>

-

<?= htmlspecialchars($v['sku']) ?>

(<?= htmlspecialchars($v['tamanho']) ?>)

/

<?= htmlspecialchars($v['cor']) ?>

</option>

<?php endforeach; ?>

</select>

<span class="entrada-seta">⌄</span>

</div>

</td>


<td>

<input
class="entrada-input entrada-numero"
type="number"
min="0"
name="quantidade[]"
value="0"
>

</td>


<td>

<div class="entrada-preco">

<span>R$</span>

<input
class="entrada-input"
type="text"
name="custo_unitario[]"
value="0,00"
>

</div>

</td>

</tr>

<?php endfor; ?>

</tbody>

</table>

</div>


<!-- BOTÃO -->

<div class="entrada-actions">

<button
class="entrada-btn-primary"
type="submit"
>

<svg
viewBox="0 0 24 24"
fill="none"
xmlns="http://www.w3.org/2000/svg"
>
<path d="M5 12L10 17L19 7"/>
</svg>

Confirmar Entrada

</button>

</div>

</form>

</div>


<!-- ÚLTIMAS ENTRADAS -->

<div class="entrada-card">

<div class="entrada-card-title entrada-historico-title">

<div class="entrada-icon">

<svg
viewBox="0 0 24 24"
fill="none"
xmlns="http://www.w3.org/2000/svg"
>
<rect x="5" y="3" width="14" height="18" rx="2"/>
<path d="M9 3H15V6H9V3Z"/>
<path d="M8 10H16"/>
<path d="M8 14H16"/>
<path d="M8 18H13"/>
</svg>

</div>

<div>

<h2>Últimas Entradas</h2>

<p>
Confira as últimas entradas de mercadoria realizadas.
</p>

</div>

</div>


<div class="entrada-table-wrapper">

<table class="entrada-table entrada-historico-table">

<thead>

<tr>

<th>ID</th>

<th>Fornecedor</th>

<th>Data</th>

<th>Status</th>

<th>Valor Total</th>

</tr>

</thead>


<tbody>

<?php foreach ($entradas as $e): ?>

<tr>

<td>

<span class="entrada-id">
#<?= (int)$e['id'] ?>
</span>

</td>


<td>

<?= htmlspecialchars(
$e['fornecedor_nome']
) ?>

</td>


<td>

<?= htmlspecialchars(
$e['data']
) ?>

</td>


<td>

<span class="entrada-status">
<?= htmlspecialchars(
$e['status']
) ?>
</span>

</td>


<td>

<span class="entrada-valor">

R$

<?= number_format(
(float)$e['valor_total'],
2,
',',
'.'
) ?>

</span>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>


<!-- VOLTAR -->

<div class="entrada-footer">

<a
class="entrada-voltar"
href="/magicdonuts/index.php?controller=auth&action=dashboard"
>

<svg
viewBox="0 0 24 24"
fill="none"
xmlns="http://www.w3.org/2000/svg"
>
<path d="M19 12H5"/>
<path d="M11 18L5 12L11 6"/>
</svg>

Voltar ao Dashboard

</a>

</div>

</div>

</div>

</body>

</html>