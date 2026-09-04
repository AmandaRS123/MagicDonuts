<?php

class EntradaController
{
    private function check()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?controller=auth&action=form");
            exit;
        }
    }

    /**
     * Página Principal de Entradas (Listagem)
     */
    public function index()
    {
        // 1. Protege a rota
        $this->check();

        // 2. Aqui você buscaria os dados do model futuramente, ex:
        // $entradaModel = new Entrada();
        // $entradas = $entradaModel->listarTodas();
        $entradas = []; // Array vazio por enquanto para não dar erro de variável

        // 3. Carrega a visão (View)
        require_once __DIR__ . '/../views/entradas.php';
    }
}