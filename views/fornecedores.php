<?php
// ============================================================================
// CONCEITO MVC: VIEW (CAMADA DE APRESENTAÇÃO / TELA)
// A View não acessa o banco de dados diretamente. Ela apenas recebe as variáveis
// preparadas pelo Controller ($fornecedores, $editar) e as exibe em HTML.
// ============================================================================


// Recupera o nome do usuário armazenado na Sessão durante o Login.
// O operador '??' (Null Coalescing) exibe 'Usuário' como padrão caso o nome não exista.
$nomeUser = $_SESSION['nome'] ?? 'Usuário';
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Fornecedores</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Importação do arquivo de estilos CSS da aplicação -->
  <link rel="stylesheet" href="/magicdonuts/public/assets/css/style.css">
  <link rel="icon" type="image/jpeg" href="/magicdonuts/imagem/logo.jpg">

</head>
<body>
  <div class="container">
   
    <!-- BARRA SUPERIOR (TOPBAR): Exibe o título da página e dados do usuário logado -->
    <div class="topbar">
      <div class="brand">
        <div class="badge"></div>
        <div>
          <h1>Fornecedores</h1>
          <small>CRUD (admin)</small>
        </div>
      </div>
     
      <div class="pill">
        <!-- SEGURANÇA (XSS): htmlspecialchars() impede a execução de scripts maliciosos
             convertendo caracteres especiais HTML em texto seguro (ex: '<' vira '&lt;') -->
        Olá, <strong><?= htmlspecialchars($nomeUser) ?></strong>
        <!-- Link de Logout aciona o Controller 'auth' e a Action 'logout' -->
        • <a href="/magicdonuts/index.php?controller=auth&action=logout">Sair</a>
      </div>
    </div>


    <!-- LAYOUT EM GRID: Divide a tela em duas colunas (1fr para o formulário, 2fr para a tabela) -->
    <div class="grid" style="grid-template-columns: 1fr 2fr;">
     
      <!-- =================================================================== -->
      <!-- COLUNA 1: FORMULÁRIO DUAL (CADASTRO E EDIÇÃO NO MESMO FORMULÁRIO)   -->
      <!-- =================================================================== -->
      <div class="card">
        <!-- Título Dinâmico: Se a variável $editar contiver dados, exibe "Editar", caso contrário "Novo" -->
        <h2><?= $editar ? "Editar Fornecedor #".(int)$editar['id'] : "Novo Fornecedor" ?></h2>


        <!-- O formulário envia os dados via POST para a Action 'salvar' do FornecedorController -->
        <form method="post" action="index.php?controller=fornecedor&action=salvar">
         
          <!-- CAMPO OCULTO (hidden): Crucial para o Controller diferenciar Inserção de Atualização.
               Se o ID for 0 -> Novo Registro (INSERT). Se ID > 0 -> Alteração (UPDATE). -->
          <input type="hidden" name="id" value="<?= $editar ? (int)$editar['id'] : 0 ?>">


          <!-- Campo Nome (Obrigatório) -->
          <label>Nome</label>
          <input class="input" type="text" name="nome" required
                 value="<?= $editar ? htmlspecialchars($editar['nome']) : '' ?>">


          <!-- Campo CNPJ (Preenche o valor existente se estiver em modo de edição) -->
          <label>CNPJ</label>
          <input class="input" type="text" name="cnpj"
                 value="<?= $editar ? htmlspecialchars($editar['cnpj'] ?? '') : '' ?>">


          <!-- Campo Telefone -->
          <label>Telefone</label>
          <input class="input" type="text" name="telefone"
                 value="<?= $editar ? htmlspecialchars($editar['telefone'] ?? '') : '' ?>">


          <!-- Campo E-mail -->
          <label>E-mail</label>
          <input class="input" type="email" name="email"
                 value="<?= $editar ? htmlspecialchars($editar['email'] ?? '') : '' ?>">


          <!-- Campo Endereço -->
          <label>Endereço</label>
          <textarea class="input" name="endereco" rows="2"><?= $editar ? htmlspecialchars($editar['endereco'] ?? '') : '' ?></textarea>


          <!-- Botões de Ação do Formulário -->
          <div class="actions" style="margin-top:14px; display:flex; gap:10px;">
            <button class="btn btn-primary" type="submit">Salvar</button>
            <!-- O botão Limpar recarrega a página sem o ID na URL, cancelando o modo de edição -->
            <a class="btn" href="index.php?controller=fornecedor&action=index">Limpar</a>
          </div>
        </form>
      </div>


      <!-- =================================================================== -->
      <!-- COLUNA 2: LISTAGEM E TABELA DE DADOS                               -->
      <!-- =================================================================== -->
      <div class="card">
        <h2>Lista de Fornecedores</h2>
        <table class="table">
          <thead>
            <tr>
              <th>ID</th><th>Nome</th><th>CNPJ</th><th>Telefone</th><th>Status</th>
              <th style="width:260px;">Ações</th>
            </tr>
          </thead>
          <tbody>
            <!-- LAÇO REPETITIVO (foreach): Percorre a lista de fornecedores enviada pelo Controller -->
            <?php foreach ($fornecedores as $f): ?>
            <tr>
              <!-- Exibição do ID com conversão de tipo segura -->
              <td>#<?= (int)$f['id'] ?></td>
             
              <!-- Exibição do Nome com tratamento de segurança XSS -->
              <td><?= htmlspecialchars($f['nome']) ?></td>
             
              <!-- Exibição de campos opcionais: exibe '-' caso o valor venha nulo do banco -->
              <td><?= htmlspecialchars($f['cnpj'] ?? '-') ?></td>
              <td><?= htmlspecialchars($f['telefone'] ?? '-') ?></td>
             
              <!-- RENDERIZAÇÃO CONDICIONAL DO STATUS:
                   Usa o operador ternário para formatar visualmente se está Ativo ou Inativo -->
              <td>
                <?= ((int)$f['ativo'] === 1)
                    ? '<span class="tag ok">Ativo</span>'
                    : '<span class="tag off">Inativo</span>' ?>
              </td>
             
              <!-- BOTÕES DE AÇÃO POR LINHA -->
              <td>
                <!-- Botão Editar: Recarrega a página passando o 'id' via GET na URL -->
                <a class="btn" href="index.php?controller=fornecedor&action=index&id=<?= (int)$f['id'] ?>">Editar</a>
               
                <!-- ALTERNÂNCIA DE BOTÃO DE STATUS (Ativar / Inativar) -->
                <?php if ((int)$f['ativo'] === 1): ?>
                  <!-- Se estiver ATIVO, exibe botão para INATIVAR (ativo=0).
                       O evento 'onclick' aciona uma confirmação via JavaScript no navegador -->
                  <a class="btn btn-danger"
                     href="index.php?controller=fornecedor&action=toggle&id=<?= (int)$f['id'] ?>&ativo=0"
                     onclick="return confirm('Inativar este fornecedor?')">Inativar</a>
                <?php else: ?>
                  <!-- Se estiver INATIVO, exibe botão para ATIVAR (ativo=1) -->
                  <a class="btn btn-success"
                     href="index.php?controller=fornecedor&action=toggle&id=<?= (int)$f['id'] ?>&ativo=1">Ativar</a>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?> <!-- Fim do laço foreach -->
          </tbody>
        </table>
       
        <!-- Navegação global para retornar ao menu principal do sistema -->
        <div style="margin-top:14px;">
          <a class="btn" href="/magicdonuts/index.php?controller=auth&action=dashboard">Voltar ao Dashboard</a>
        </div>
      </div>


    </div>
  </div>
</body>
</html>

