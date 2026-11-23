<?php

class CepController
{
    public function buscar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $cep = preg_replace('/[^0-9]/', '', $_GET['cep'] ?? '');

        if (strlen($cep) !== 8) {
            http_response_code(400);
            echo json_encode([
                'erro' => 'CEP inválido. Informe 8 dígitos.'
            ]);
            exit;
        }

        if (!function_exists('curl_init')) {
            http_response_code(500);
            echo json_encode([
                'erro' => 'A extensão cURL do PHP não está habilitada.'
            ]);
            exit;
        }

        $url = "https://viacep.com.br/ws/{$cep}/json/";

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_FOLLOWLOCATION => true
        ]);

        $resposta = curl_exec($ch);
        $erroCurl = curl_error($ch);
        $statusHTTP = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($erroCurl || $resposta === false || $statusHTTP !== 200) {
            http_response_code(502);
            echo json_encode([
                'erro' => 'Não foi possível consultar o serviço de CEP.'
            ]);
            exit;
        }

        $dados = json_decode($resposta, true);

        if (!is_array($dados) || isset($dados['erro'])) {
            http_response_code(404);
            echo json_encode([
                'erro' => 'CEP não encontrado.'
            ]);
            exit;
        }

        echo json_encode([
            'cep'        => $dados['cep'] ?? '',
            'logradouro' => $dados['logradouro'] ?? '',
            'bairro'     => $dados['bairro'] ?? '',
            'cidade'     => $dados['localidade'] ?? '',
            'estado'     => $dados['uf'] ?? ''
        ]);

        exit;
    }
}