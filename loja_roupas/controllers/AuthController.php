<?php
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

    if (!$user) {
        die("Erro: O banco de dados não encontrou NENHUM usuário com esse e-mail.");
    }

    if ((int)$user['ativo'] !== 1) {
        die("Erro: O usuário existe, mas o campo 'ativo' não é 1.");
    }

    if (!password_verify($senha, $user['senha'])) {
        die("Erro: A senha digitada não bate com o Hash do banco.");
    }

    // ==========================================
    // LOGIN COM SUCESSO!
    // ==========================================

    // sessao iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION['usuario_id'] = $user['id_usuario']; 
    $_SESSION['usuario_nome'] = $user['nome'];
    $_SESSION['perfil'] = $user['perfil']; // admin ou outro

    header("Location: index.php?controller=auth&action=dashboard");
    exit;
}
public function dashboard()
{




$this->check();
require_once __DIR__ . '/../views/dashboard.php';
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
} }
public function onlyAdmin()
{
$this->check();
if (($_SESSION['perfil'] ?? '') !== 'admin') {
die("Acesso negado.");
} } }
