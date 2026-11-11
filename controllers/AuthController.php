<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Usuario.php';

class AuthController
{
    public function form()
    {
        require_once __DIR__ . '/../views/login.php';
    }

    public function login()
    {
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';

        $usuarioModel = new Usuario();
        $user = $usuarioModel->buscarPorEmail($email);

        if (!$user || (int) $user['ativo'] !== 1) {
            die("Usuário inválido ou inativo.");
        }

        if (!password_verify($senha, $user['senha'])) {
            die("Senha inválida.");
        }

        // Sessão do usuário
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['perfil'] = $user['perfil'];
        $_SESSION['nome'] = $user['nome'];

        header("Location: index.php?controller=auth&action=dashboard");
        exit;
    }

    public function dashboard()
    {
        $this->check();

        $pdo = Database::getConnection();

        // Indicadores iniciais
        $vendasMes = 0;
        $entradasRegistradas = 0;
        $estoqueTotal = 0;
        $produtosCadastrados = 0;

        try {

            // 1. Vendas finalizadas no mês atual
            $sql = "
                SELECT COUNT(*)
                FROM venda
                WHERE status = 'finalizada'
                  AND data >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                  AND data < DATE_ADD(
                      DATE_FORMAT(CURDATE(), '%Y-%m-01'),
                      INTERVAL 1 MONTH
                  )
            ";

            $vendasMes = (int) $pdo
                ->query($sql)
                ->fetchColumn();


            // 2. Entradas de mercadoria confirmadas
            $sql = "
                SELECT COUNT(*)
                FROM entrada_mercadoria
                WHERE status = 'confirmar'
            ";

            $entradasRegistradas = (int) $pdo
                ->query($sql)
                ->fetchColumn();


            // 3. Quantidade total de unidades disponíveis no estoque
            $sql = "
                SELECT COALESCE(SUM(e.quantidade), 0)
                FROM estoque e
                INNER JOIN variacao v
                    ON v.id = e.variacao_id
                INNER JOIN produto p
                    ON p.id = v.produto_id
                WHERE p.ativo = 1
            ";

            $estoqueTotal = (int) $pdo
                ->query($sql)
                ->fetchColumn();


            // 4. Produtos ativos cadastrados
            $sql = "
                SELECT COUNT(*)
                FROM produto
                WHERE ativo = 1
            ";

            $produtosCadastrados = (int) $pdo
                ->query($sql)
                ->fetchColumn();

        } catch (PDOException $e) {

            error_log(
                'Erro ao carregar indicadores do painel: '
                . $e->getMessage()
            );
        }

        // Informações da página
        $paginaAtual = 'dashboard';
        $tituloPagina = 'Dashboard';
        $descricaoPagina = 'Visão geral da sua loja.';

        require_once __DIR__ . '/../views/layout/inicio.php';
        require_once __DIR__ . '/../views/dashboard.php';
        require_once __DIR__ . '/../views/layout/fim.php';
    }

    public function logout()
    {
        session_destroy();

        header("Location: index.php?controller=auth&action=form");
        exit;
    }

    public function check()
    {
        if (!isset($_SESSION['usuario_id'])) {

            header("Location: index.php?controller=auth&action=form");
            exit;
        }
    }

    public function onlyAdmin()
    {
        $this->check();

        if (($_SESSION['perfil'] ?? '') !== 'admin') {
            die("Acesso negado.");
        }
    }
}