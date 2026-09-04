Loja Nerdis - Sistema de Gerenciamento de Produtos

Um sistema web para o gerenciamento de inventário e controle de acesso de uma loja, desenvolvido em PHP.
---

## Funcionalidades:

### Autenticação & Níveis de Acesso
- **Administrador (admin):** Possui controle total do sistema. Pode cadastrar, editar, ativar/inativar e **excluir** produtos, além de gerenciar categorias e mídias.

- **Vendedor (vendedor):** Tem limite limitado. Pode visualizar a lista de itens, cadastrar novos produtos e editar informações caso errar algo, mas possui restrição de segurança para **não excluir** dados do sistema.

### Gestão de Produtos
- Operações de CRUD.
- Controle dinâmico de Status (**Ativo/Inativo**).
- Preco e quantidade de itens em estoque.
- Upload de fotos dos produtos em: jpg, png, webp.
  
---

## Programas e tecnologias utilizados

* **Backend:** PHP
* **Banco de Dados:** MySQL, HeidiSQL
* **Frontend:** HTML, CSS