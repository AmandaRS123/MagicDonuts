<?php

require_once __DIR__ . '/../config/db.php';

class NotaFiscal
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    // NOTAS FISCAIS DE VENDA

    public function buscarPorVenda(int $vendaId): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT *
            FROM nota_fiscal_venda
            WHERE venda_id = :venda_id
            LIMIT 1
        ");

        $stmt->execute([
            ':venda_id' => $vendaId
        ]);

        $nota = $stmt->fetch(PDO::FETCH_ASSOC);

        return $nota ?: null;
    }

    public function cadastrarVenda(array $dados): bool
    {
        $stmt = $this->conn->prepare("
            INSERT INTO nota_fiscal_venda (
                venda_id,
                modelo,
                serie,
                numero,
                data_emissao,
                valor_total
            ) VALUES (
                :venda_id,
                :modelo,
                :serie,
                :numero,
                :data_emissao,
                :valor_total
            )
        ");

        return $stmt->execute([
            ':venda_id' => (int) $dados['venda_id'],
            ':modelo' => trim($dados['modelo']),
            ':serie' => trim($dados['serie']),
            ':numero' => trim($dados['numero']),
            ':data_emissao' => $dados['data_emissao'],
            ':valor_total' => (float) $dados['valor_total']
        ]);
    }

    public function listarVendas(): array
    {
        $stmt = $this->conn->query("
            SELECT
                nf.*,
                v.data AS data_venda,
                v.status AS status_venda
            FROM nota_fiscal_venda nf
            INNER JOIN venda v
                ON v.id = nf.venda_id
            ORDER BY nf.data_emissao DESC, nf.id DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // NOTAS FISCAIS DE ENTRADA

    public function buscarPorEntrada(int $entradaId): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT *
            FROM nota_fiscal_entrada
            WHERE entrada_id = :entrada_id
            LIMIT 1
        ");

        $stmt->execute([
            ':entrada_id' => $entradaId
        ]);

        $nota = $stmt->fetch(PDO::FETCH_ASSOC);

        return $nota ?: null;
    }

    public function cadastrarEntrada(array $dados): bool
    {
        $stmt = $this->conn->prepare("
            INSERT INTO nota_fiscal_entrada (
                entrada_id,
                modelo,
                serie,
                numero,
                chave_acesso,
                data_emissao,
                valor_total
            ) VALUES (
                :entrada_id,
                :modelo,
                :serie,
                :numero,
                :chave_acesso,
                :data_emissao,
                :valor_total
            )
        ");

        return $stmt->execute([
            ':entrada_id' => (int) $dados['entrada_id'],
            ':modelo' => trim($dados['modelo']),
            ':serie' => trim($dados['serie']),
            ':numero' => trim($dados['numero']),
            ':chave_acesso' => trim($dados['chave_acesso']),
            ':data_emissao' => $dados['data_emissao'],
            ':valor_total' => (float) $dados['valor_total']
        ]);
    }

    public function listarEntradas(): array
    {
        $stmt = $this->conn->query("
            SELECT
                nf.*,
                e.data AS data_entrada,
                e.status AS status_entrada
            FROM nota_fiscal_entrada nf
            INNER JOIN entrada_mercadoria e
                ON e.id = nf.entrada_id
            ORDER BY nf.data_emissao DESC, nf.id DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}