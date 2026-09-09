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

    public function index()
    {
        $this->check();

        $entradas = [];

        require_once __DIR__ . '/../views/entradas.php';
    }
}
