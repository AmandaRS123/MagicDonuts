
<?php

require_once __DIR__ . '/../models/Relatorio.php';
require_once __DIR__ . '/../models/Estoque.php';

class RelatorioController
{
    /**
     * Exibe a página de relatórios.
     */
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


    /**
     * Gera e disponibiliza o relatório em PDF.
     */
    public function exportarPDF(): void
    {
        $this->check();

        // Carrega a biblioteca Dompdf.
        $autoload = __DIR__ . '/../vendor/autoload.php';

        if (!file_exists($autoload)) {
            error_log('Dompdf não encontrado. Verifique a instalação do Composer.');

            http_response_code(500);
            exit('Não foi possível gerar o PDF. Verifique a instalação do Dompdf.');
        }

        require_once $autoload;

        // Busca os dados atualizados.
        $relatorioModel = new Relatorio();
        $estoqueModel = new Estoque();

        $topProdutos = $relatorioModel->topProdutosMaisVendidos();
        $fornecedores = $relatorioModel->fornecedoresComMaisEntregas();
        $entradas = $relatorioModel->entradasRecentes();
        $baixoEstoque = $estoqueModel->listarBaixoEstoque();

        // Monta o HTML que será convertido em PDF.
        ob_start();

        require __DIR__ . '/../views/relatorio_pdf.php';

        $html = ob_get_clean();

        // Configura o Dompdf.
        $options = new \Dompdf\Options();

        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new \Dompdf\Dompdf($options);

        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Disponibiliza o arquivo PDF para download.
        $dompdf->stream('relatorio-magic-donuts.pdf', [
            'Attachment' => true
        ]);

        exit;
    }


    /**
     * Verifica se o usuário está autenticado.
     */
    private function check(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?controller=auth&action=form");
            exit;
        }
    }
}
