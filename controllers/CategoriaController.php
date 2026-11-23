
<?php

require_once __DIR__ . '/../models/Categoria.php';

class CategoriaController
{
    private function check(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?controller=auth&action=form');
            exit;
        }
    }

    private function onlyAdmin(): void
    {
        if (($_SESSION['perfil'] ?? '') !== 'admin') {
            http_response_code(403);
            exit('Acesso permitido somente para administradores.');
        }
    }

    private function proteger(): void
    {
        $this->check();
        $this->onlyAdmin();
    }

    public function index(): void
    {
        $this->proteger();

        $categoriaModel = new Categoria();
        $categorias = $categoriaModel->listarTodas();

        $editar = null;

        if (isset($_GET['id'])) {
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

            if (!$id || $id <= 0) {
                header('Location: index.php?controller=categoria&action=index&erro=id');
                exit;
            }

            $editar = $categoriaModel->buscarPorId($id);

            if (!$editar) {
                header('Location: index.php?controller=categoria&action=index&erro=nao_encontrada');
                exit;
            }
        }

        $paginaAtual = 'categoria';
        $tituloPagina = 'Categorias';
        $descricaoPagina = 'Cadastre e organize as categorias dos produtos.';

        require __DIR__ . '/../views/categorias.php';
    }

    public function salvar(): void
    {
        $this->proteger();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=categoria&action=index');
            exit;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $id = $id ?: 0;

        $nome = trim($_POST['nome'] ?? '');

        if ($nome === '' || mb_strlen($nome) > 100) {
            $destino = $id > 0
                ? '&id=' . $id
                : '';

            header('Location: index.php?controller=categoria&action=index&erro=nome' . $destino);
            exit;
        }

        $categoriaModel = new Categoria();

        try {
            if ($id > 0) {
                if (!$categoriaModel->buscarPorId($id)) {
                    header('Location: index.php?controller=categoria&action=index&erro=nao_encontrada');
                    exit;
                }

                $categoriaModel->atualizar($id, $nome);

                header('Location: index.php?controller=categoria&action=index&sucesso=editada');
            } else {
                $categoriaModel->inserir($nome);

                header('Location: index.php?controller=categoria&action=index&sucesso=cadastrada');
            }

            exit;
        } catch (Throwable $e) {
            error_log('Erro ao salvar categoria: ' . $e->getMessage());

            header('Location: index.php?controller=categoria&action=index&erro=salvar');
            exit;
        }
    }

    public function toggle(): void
    {
        $this->proteger();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=categoria&action=index');
            exit;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $ativo = filter_input(INPUT_POST, 'ativo', FILTER_VALIDATE_INT);

        if (!$id || $id <= 0 || !in_array($ativo, [0, 1], true)) {
            header('Location: index.php?controller=categoria&action=index&erro=dados');
            exit;
        }

        $categoriaModel = new Categoria();

        try {
            if (!$categoriaModel->buscarPorId($id)) {
                header('Location: index.php?controller=categoria&action=index&erro=nao_encontrada');
                exit;
            }

            $categoriaModel->setAtivo($id, $ativo === 1);

            $mensagem = $ativo === 1 ? 'ativada' : 'inativada';

            header('Location: index.php?controller=categoria&action=index&sucesso=' . $mensagem);
            exit;
        } catch (Throwable $e) {
            error_log('Erro ao alterar status da categoria: ' . $e->getMessage());

            header('Location: index.php?controller=categoria&action=index&erro=status');
            exit;
        }
    }



public function excluir(): void
{
    $this->proteger();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?controller=categoria&action=index');
        exit;
    }

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id <= 0) {
        header('Location: index.php?controller=categoria&action=index&erro=dados');
        exit;
    }

    try {
        $categoriaModel = new Categoria();
        $resultado = $categoriaModel->excluir($id);

        if ($resultado === 'vinculada') {
            header('Location: index.php?controller=categoria&action=index&erro=vinculada');
            exit;
        }

        if ($resultado === 'nao_encontrada') {
            header('Location: index.php?controller=categoria&action=index&erro=nao_encontrada');
            exit;
        }

        header('Location: index.php?controller=categoria&action=index&sucesso=excluida');
        exit;

    } catch (Throwable $e) {
        error_log('Erro ao excluir categoria: ' . $e->getMessage());

        header('Location: index.php?controller=categoria&action=index&erro=excluir');
        exit;
    }
}

}
