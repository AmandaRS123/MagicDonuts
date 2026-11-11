-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 11/11/2026 às 02:53
-- Versão do servidor: 10.11.16-MariaDB
-- Versão do PHP: 8.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `magicdonuts`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `categoria`
--

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categoria`
--

INSERT INTO `categoria` (`id`, `nome`, `ativo`) VALUES
(8, 'Com recheio', 1),
(9, 'Sem recheio', 1),
(10, 'Especial', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente`
--

CREATE TABLE `cliente` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cpf_cnpj` varchar(20) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cliente`
--

INSERT INTO `cliente` (`id`, `nome`, `cpf_cnpj`, `telefone`, `email`, `endereco`) VALUES
(1, 'Mariana Oliveira', '12345678901', '(21) 98765-4321', 'mariana.oliveira@example.com', 'Rua das Flores, 125, Centro, Nova Iguaçu - RJ'),
(2, 'Beatriz Santos', '23456789012', '(21) 97654-3210', 'beatriz.santos@example.com', 'Rua do Sol, 240, Jardim Tropical, Nova Iguaçu - RJ'),
(3, 'Gabriel Costa', '34567890123', '(21) 96543-2109', 'gabriel.costa@example.com', 'Avenida Brasil, 580, Posse, Nova Iguaçu - RJ');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente_site`
--

CREATE TABLE `cliente_site` (
  `cpf` char(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `endereco` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cliente_site`
--

INSERT INTO `cliente_site` (`cpf`, `nome`, `email`, `senha_hash`, `endereco`, `telefone`, `criado_em`) VALUES
('79663453466', 'Charlotte', 'charlotte2@gmail.com', '$2y$12$K7pxA01kvStgtJdKCh/TuuxnyamEg8BpuGq5TauKMYhShlzPPe0qy', 'Av. das Américas, 4666 – Barra da Tijuca, Rio de Janeiro – RJ', '21965808907', '2026-11-10 19:58:52');

-- --------------------------------------------------------

--
-- Estrutura para tabela `entrada_item`
--

CREATE TABLE `entrada_item` (
  `id` int(11) NOT NULL,
  `entrada_id` int(11) NOT NULL,
  `variacao_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `custo_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `entrada_item`
--

INSERT INTO `entrada_item` (`id`, `entrada_id`, `variacao_id`, `quantidade`, `custo_unitario`) VALUES
(7, 6, 2, 30, 11.00),
(8, 6, 1, 60, 4.00),
(9, 7, 3, 60, 9.00),
(10, 7, 4, 100, 3.00),
(11, 8, 5, 20, 5.00),
(12, 8, 6, 30, 4.00),
(13, 9, 1, 30, 2.00),
(14, 9, 2, 20, 6.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `entrada_mercadoria`
--

CREATE TABLE `entrada_mercadoria` (
  `id` int(11) NOT NULL,
  `fornecedor_id` int(11) NOT NULL,
  `data` date NOT NULL,
  `status` enum('rascunho','confirmar') NOT NULL DEFAULT 'rascunho',
  `valor_total` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `entrada_mercadoria`
--

INSERT INTO `entrada_mercadoria` (`id`, `fornecedor_id`, `data`, `status`, `valor_total`) VALUES
(6, 2, '2026-10-08', 'confirmar', 570.00),
(7, 1, '2026-10-08', 'confirmar', 840.00),
(8, 2, '2026-10-08', 'confirmar', 220.00),
(9, 1, '2026-11-09', 'confirmar', 180.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `estoque`
--

CREATE TABLE `estoque` (
  `id` int(11) NOT NULL,
  `variacao_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 0,
  `minimo` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `estoque`
--

INSERT INTO `estoque` (`id`, `variacao_id`, `quantidade`, `minimo`) VALUES
(1, 2, 80, 0),
(2, 1, 150, 0),
(5, 3, 53, 0),
(6, 4, 86, 0),
(7, 5, 8, 0),
(8, 6, 14, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `fornecedor`
--

CREATE TABLE `fornecedor` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cnpj` varchar(20) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `cep` varchar(9) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `fornecedor`
--

INSERT INTO `fornecedor` (`id`, `nome`, `cnpj`, `telefone`, `email`, `endereco`, `cep`, `ativo`) VALUES
(1, 'Jorge', '12.345.678/1234-12', '21 94321-6578', 'jorge1@gmail.com', 'Rua das Cerejeiras, nº 125, Nova Iguaçu', NULL, 1),
(2, 'Carlos Junior', '98.431.948/1142-99', '21 99987-3345', 'junin123@gmail.com', 'Av. Abílio Augusto Távora, 1.111 - Bairro da Luz, Nova Iguaçu', NULL, 1),
(3, 'Joana Rubens', '3434576523576', '4345433333', '091@gmail.com', 'Rua Beatriz, Valverde, Nova Iguaçu/RJ', '26290-672', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `movimento_estoque`
--

CREATE TABLE `movimento_estoque` (
  `id` int(11) NOT NULL,
  `variacao_id` int(11) NOT NULL,
  `tipo` enum('entrada','saida') NOT NULL,
  `quantidade` int(11) NOT NULL,
  `origem` enum('entrada','venda') NOT NULL,
  `observacao` varchar(255) DEFAULT NULL,
  `data` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `movimento_estoque`
--

INSERT INTO `movimento_estoque` (`id`, `variacao_id`, `tipo`, `quantidade`, `origem`, `observacao`, `data`) VALUES
(1, 2, 'entrada', 30, 'entrada', 'Entrada de mercadoria nº 5', '2026-10-08 21:18:59'),
(2, 1, 'entrada', 60, 'entrada', 'Entrada de mercadoria nº 5', '2026-10-08 21:18:59'),
(3, 2, 'entrada', 30, 'entrada', 'Entrada de mercadoria nº 6', '2026-10-08 21:20:39'),
(4, 1, 'entrada', 60, 'entrada', 'Entrada de mercadoria nº 6', '2026-10-08 21:20:39'),
(5, 3, 'entrada', 60, 'entrada', 'Entrada de mercadoria nº 7', '2026-10-08 21:36:39'),
(6, 4, 'entrada', 100, 'entrada', 'Entrada de mercadoria nº 7', '2026-10-08 21:36:39'),
(7, 3, 'saida', 3, 'venda', 'Venda nº 4', '2026-10-08 21:46:15'),
(8, 4, 'saida', 5, 'venda', 'Venda nº 4', '2026-10-08 21:46:15'),
(9, 5, 'entrada', 20, 'entrada', 'Entrada de mercadoria nº 8', '2026-10-08 21:49:08'),
(10, 6, 'entrada', 30, 'entrada', 'Entrada de mercadoria nº 8', '2026-10-08 21:49:08'),
(11, 6, 'saida', 4, 'venda', 'Venda nº 5', '2026-10-08 21:49:33'),
(12, 5, 'saida', 3, 'venda', 'Saída referente à venda nº 6', '2026-10-29 20:06:25'),
(13, 6, 'saida', 2, 'venda', 'Saída referente à venda nº 11', '2026-10-29 21:38:59'),
(14, 1, 'entrada', 30, 'entrada', 'Entrada de mercadoria nº 9', '2026-11-09 20:52:29'),
(15, 2, 'entrada', 20, 'entrada', 'Entrada de mercadoria nº 9', '2026-11-09 20:52:29');

-- --------------------------------------------------------

--
-- Estrutura para tabela `nota_fiscal_entrada`
--

CREATE TABLE `nota_fiscal_entrada` (
  `id` int(11) NOT NULL,
  `entrada_id` int(11) NOT NULL,
  `modelo` varchar(5) NOT NULL,
  `serie` varchar(5) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `chave_acesso` varchar(44) NOT NULL,
  `data_emissao` date NOT NULL,
  `valor_total` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `nota_fiscal_venda`
--

CREATE TABLE `nota_fiscal_venda` (
  `id` int(11) NOT NULL,
  `venda_id` int(11) NOT NULL,
  `modelo` varchar(5) NOT NULL,
  `serie` varchar(5) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `data_emissao` date NOT NULL,
  `valor_total` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido`
--

CREATE TABLE `pedido` (
  `id` int(11) NOT NULL,
  `cliente_cpf` varchar(14) DEFAULT NULL,
  `data` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('pendente','pago','enviado','entregue','cancelado') NOT NULL DEFAULT 'pendente',
  `tipo_entrega` enum('entrega','retirada') NOT NULL DEFAULT 'entrega',
  `endereco_entrega` varchar(255) NOT NULL,
  `cep_entrega` varchar(9) DEFAULT NULL,
  `valor_frete` decimal(10,2) NOT NULL DEFAULT 0.00,
  `prazo_frete_dias` int(11) DEFAULT NULL,
  `forma_pagamento` enum('pix','cartao','boleto') NOT NULL DEFAULT 'pix',
  `valor_total` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedido`
--

INSERT INTO `pedido` (`id`, `cliente_cpf`, `data`, `status`, `tipo_entrega`, `endereco_entrega`, `cep_entrega`, `valor_frete`, `prazo_frete_dias`, `forma_pagamento`, `valor_total`) VALUES
(1, NULL, '2026-11-09 20:15:25', 'pendente', 'entrega', 'Avenida Governador Roberto Silveira, nº 540, shopping, Nova Iguaçu, Nova Iguaçu - RJ', '26210210', 0.00, NULL, 'cartao', 10.00),
(2, NULL, '2026-11-09 23:32:46', 'pago', 'retirada', 'Retirada na loja - Magic Donuts, Nova Iguaçu/RJ', NULL, 0.00, NULL, 'pix', 13.00),
(3, NULL, '2026-11-09 23:51:41', 'pago', 'entrega', 'Parque Quinta da Boa Vista, nº s/nº, zoológico, Mangueira, Rio de Janeiro - RJ', '20940040', 0.00, 5, 'cartao', 68.00),
(4, '79663453466', '2026-11-10 20:20:23', 'pago', 'entrega', 'Avenida Governador Roberto Silveira, nº 4666, shopping, Nova Iguaçu, Rio de Janeiro - RJ, CEP: 22640102', '22640102', 12.00, 5, 'pix', 25.00),
(5, '79663453466', '2026-11-10 21:28:01', 'pago', 'entrega', 'Av. das Américas, 4666 – Barra da Tijuca, Rio de Janeiro – RJ, CEP: 22640102', '22640102', 12.00, 5, 'pix', 25.00),
(6, '79663453466', '2026-11-10 21:49:08', 'pago', 'entrega', 'Av. das Américas, 4666 – Barra da Tijuca, Rio de Janeiro – RJ, CEP: 22640102', '22640102', 12.00, 5, 'pix', 22.00),
(7, '79663453466', '2026-11-10 22:00:59', 'pago', 'entrega', 'Av. das Américas, 4666 – Barra da Tijuca, Rio de Janeiro – RJ, CEP: 22640102', '22640102', 12.00, 5, 'cartao', 32.00),
(8, '79663453466', '2026-11-10 22:03:20', 'pago', 'entrega', 'Av. das Américas, 4666 – Barra da Tijuca, Rio de Janeiro – RJ, CEP: 22640102', '22640102', 0.00, 5, 'boleto', 60.00),
(9, '79663453466', '2026-11-10 22:07:38', 'pago', 'entrega', 'Av. das Américas, 4666 – Barra da Tijuca, Rio de Janeiro – RJ, CEP: 26260045', '26260045', 5.00, 2, 'cartao', 35.00),
(10, '79663453466', '2026-11-10 22:08:39', 'pago', 'entrega', 'Av. das Américas, 4666 – Barra da Tijuca, Rio de Janeiro – RJ, CEP: 26260045', '26260045', 5.00, 2, 'pix', 10.00),
(11, '79663453466', '2026-11-10 22:09:15', 'pago', 'entrega', 'Av. das Américas, 4666 – Barra da Tijuca, Rio de Janeiro – RJ, CEP: 26260045', '26260045', 5.00, 2, 'boleto', 10.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido_item`
--

CREATE TABLE `pedido_item` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `variacao_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedido_item`
--

INSERT INTO `pedido_item` (`id`, `pedido_id`, `variacao_id`, `quantidade`, `preco_unitario`) VALUES
(1, 1, 4, 2, 5.00),
(2, 2, 3, 1, 13.00),
(3, 3, 5, 4, 10.00),
(4, 3, 4, 3, 5.00),
(5, 3, 3, 1, 13.00),
(6, 4, 3, 1, 13.00),
(7, 5, 3, 1, 13.00),
(8, 6, 4, 2, 5.00),
(9, 7, 5, 2, 10.00),
(10, 8, 6, 10, 6.00),
(11, 9, 5, 3, 10.00),
(12, 10, 4, 1, 5.00),
(13, 11, 4, 1, 5.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produto`
--

CREATE TABLE `produto` (
  `id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produto`
--

INSERT INTO `produto` (`id`, `categoria_id`, `nome`, `descricao`, `ativo`) VALUES
(1, 8, 'ChocoDream', NULL, 1),
(2, 10, 'Ninho com Morango', NULL, 1),
(3, 9, 'Donut tradicional', NULL, 1),
(4, 8, 'Donut arco-íris', NULL, 1),
(5, 8, 'Choco meio-amargo', NULL, 1),
(6, 9, 'Donut Fit', NULL, 1),
(7, 10, 'Oreo Crocante', NULL, 1),
(8, 10, 'Edição limitada', NULL, 1),
(30, 8, 'Donut fofo', NULL, 0),
(31, 8, 'Donut Chiclete', 'Donut sabor chiclette recheado com creme', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil` enum('admin','vendedor') NOT NULL,
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id`, `nome`, `email`, `senha`, `perfil`, `ativo`) VALUES
(2, 'Amanda', 'amandaramossoares1@gmail.com', '$2y$10$lEGGxijfdNrt8NqafmgOYONZqhHHSvDUM2WNhCC/asTQ9zZ4svy2a', 'admin', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `variacao`
--

CREATE TABLE `variacao` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `tamanho` varchar(30) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `preco` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `variacao`
--

INSERT INTO `variacao` (`id`, `produto_id`, `tamanho`, `sku`, `preco`) VALUES
(1, 30, 'Pequeno', 'DON-001', 6.00),
(2, 30, 'Grande', 'DON-002', 15.00),
(3, 6, 'Grande', 'DON-003', 13.00),
(4, 6, 'Pequeno', 'DON-004', 5.00),
(5, 4, 'Grande', 'DON-005', 10.00),
(6, 4, 'Médio', 'DON-006', 6.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `venda`
--

CREATE TABLE `venda` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `data` date NOT NULL,
  `status` enum('aberta','finalizada') NOT NULL DEFAULT 'aberta',
  `valor_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `nf_emitida` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `venda`
--

INSERT INTO `venda` (`id`, `usuario_id`, `cliente_id`, `data`, `status`, `valor_total`, `nf_emitida`) VALUES
(4, 2, 2, '2026-10-08', 'finalizada', 64.00, 0),
(5, 2, 3, '2026-10-08', 'finalizada', 24.00, 0),
(6, 2, 2, '2026-10-29', 'finalizada', 30.00, 0),
(11, 2, 1, '2026-10-29', 'finalizada', 12.00, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `venda_item`
--

CREATE TABLE `venda_item` (
  `id` int(11) NOT NULL,
  `venda_id` int(11) NOT NULL,
  `variacao_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `venda_item`
--

INSERT INTO `venda_item` (`id`, `venda_id`, `variacao_id`, `quantidade`, `preco_unitario`) VALUES
(2, 4, 3, 3, 13.00),
(3, 4, 4, 5, 5.00),
(4, 5, 6, 4, 6.00),
(5, 6, 5, 3, 10.00),
(6, 11, 6, 2, 6.00);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `cliente_site`
--
ALTER TABLE `cliente_site`
  ADD PRIMARY KEY (`cpf`),
  ADD UNIQUE KEY `uk_cliente_site_email` (`email`);

--
-- Índices de tabela `entrada_item`
--
ALTER TABLE `entrada_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_entrada_item_entrada` (`entrada_id`),
  ADD KEY `idx_entrada_item_variacao` (`variacao_id`);

--
-- Índices de tabela `entrada_mercadoria`
--
ALTER TABLE `entrada_mercadoria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_entrada_fornecedor` (`fornecedor_id`),
  ADD KEY `idx_entrada_data` (`data`),
  ADD KEY `idx_entrada_status` (`status`);

--
-- Índices de tabela `estoque`
--
ALTER TABLE `estoque`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `variacao_id` (`variacao_id`);

--
-- Índices de tabela `fornecedor`
--
ALTER TABLE `fornecedor`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `movimento_estoque`
--
ALTER TABLE `movimento_estoque`
  ADD PRIMARY KEY (`id`),
  ADD KEY `variacao_id` (`variacao_id`);

--
-- Índices de tabela `nota_fiscal_entrada`
--
ALTER TABLE `nota_fiscal_entrada`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `entrada_id` (`entrada_id`),
  ADD UNIQUE KEY `chave_acesso` (`chave_acesso`),
  ADD KEY `idx_nf_entrada_data` (`data_emissao`);

--
-- Índices de tabela `nota_fiscal_venda`
--
ALTER TABLE `nota_fiscal_venda`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `venda_id` (`venda_id`),
  ADD UNIQUE KEY `numero` (`numero`),
  ADD KEY `idx_nf_venda_data` (`data_emissao`);

--
-- Índices de tabela `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pedido_cliente_cpf` (`cliente_cpf`);

--
-- Índices de tabela `pedido_item`
--
ALTER TABLE `pedido_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pedido_item_pedido` (`pedido_id`),
  ADD KEY `idx_pedido_item_variacao` (`variacao_id`);

--
-- Índices de tabela `produto`
--
ALTER TABLE `produto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_produto_categoria` (`categoria_id`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `variacao`
--
ALTER TABLE `variacao`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `fk_variacao_produto` (`produto_id`);

--
-- Índices de tabela `venda`
--
ALTER TABLE `venda`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_venda_usuario` (`usuario_id`),
  ADD KEY `idx_venda_cliente` (`cliente_id`),
  ADD KEY `idx_venda_data` (`data`),
  ADD KEY `idx_venda_status` (`status`);

--
-- Índices de tabela `venda_item`
--
ALTER TABLE `venda_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_venda_item_venda` (`venda_id`),
  ADD KEY `idx_venda_item_variacao` (`variacao_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `entrada_item`
--
ALTER TABLE `entrada_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `entrada_mercadoria`
--
ALTER TABLE `entrada_mercadoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `estoque`
--
ALTER TABLE `estoque`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `fornecedor`
--
ALTER TABLE `fornecedor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `movimento_estoque`
--
ALTER TABLE `movimento_estoque`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `nota_fiscal_entrada`
--
ALTER TABLE `nota_fiscal_entrada`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `nota_fiscal_venda`
--
ALTER TABLE `nota_fiscal_venda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `pedido_item`
--
ALTER TABLE `pedido_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `produto`
--
ALTER TABLE `produto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `variacao`
--
ALTER TABLE `variacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `venda`
--
ALTER TABLE `venda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `venda_item`
--
ALTER TABLE `venda_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `entrada_item`
--
ALTER TABLE `entrada_item`
  ADD CONSTRAINT `fk_entrada_item_entrada` FOREIGN KEY (`entrada_id`) REFERENCES `entrada_mercadoria` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_entrada_item_variacao` FOREIGN KEY (`variacao_id`) REFERENCES `variacao` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `entrada_mercadoria`
--
ALTER TABLE `entrada_mercadoria`
  ADD CONSTRAINT `fk_entrada_fornecedor` FOREIGN KEY (`fornecedor_id`) REFERENCES `fornecedor` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `estoque`
--
ALTER TABLE `estoque`
  ADD CONSTRAINT `fk_estoque_variacao` FOREIGN KEY (`variacao_id`) REFERENCES `variacao` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `movimento_estoque`
--
ALTER TABLE `movimento_estoque`
  ADD CONSTRAINT `movimento_estoque_ibfk_1` FOREIGN KEY (`variacao_id`) REFERENCES `variacao` (`id`);

--
-- Restrições para tabelas `nota_fiscal_entrada`
--
ALTER TABLE `nota_fiscal_entrada`
  ADD CONSTRAINT `fk_nf_entrada_entrada` FOREIGN KEY (`entrada_id`) REFERENCES `entrada_mercadoria` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `nota_fiscal_venda`
--
ALTER TABLE `nota_fiscal_venda`
  ADD CONSTRAINT `fk_nf_venda_venda` FOREIGN KEY (`venda_id`) REFERENCES `venda` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `fk_pedido_cliente_site` FOREIGN KEY (`cliente_cpf`) REFERENCES `cliente_site` (`cpf`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Restrições para tabelas `pedido_item`
--
ALTER TABLE `pedido_item`
  ADD CONSTRAINT `fk_pedido_item_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pedido_item_variacao` FOREIGN KEY (`variacao_id`) REFERENCES `variacao` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `produto`
--
ALTER TABLE `produto`
  ADD CONSTRAINT `fk_produto_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `variacao`
--
ALTER TABLE `variacao`
  ADD CONSTRAINT `fk_variacao_produto` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `venda`
--
ALTER TABLE `venda`
  ADD CONSTRAINT `fk_venda_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_venda_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `venda_item`
--
ALTER TABLE `venda_item`
  ADD CONSTRAINT `fk_venda_item_variacao` FOREIGN KEY (`variacao_id`) REFERENCES `variacao` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_venda_item_venda` FOREIGN KEY (`venda_id`) REFERENCES `venda` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
