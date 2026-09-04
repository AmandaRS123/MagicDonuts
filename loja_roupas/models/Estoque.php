<?php

require_once __DIR__ . '/../config/db.php';

class Estoque
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    /**
     * Garante que a variação tenha uma linha de estoque.
     */
    public function garantirRegistro(
        int $variacaoId,
        int $minimo = 0
    ): void {
        $stmt = $this->conn->prepare("
            SELECT id
            FROM estoque
            WHERE variacao_id = :vid
        ");

        $stmt->execute([
            ':vid' => $variacaoId
        ]);

        // Se não encontrar o registro,
        // cria um novo com quantidade inicial 0.
        if (!$stmt->fetch()) {

            $ins = $this->conn->prepare("
                INSERT INTO estoque (
                    variacao_id,
                    quantidade,
                    minimo
                )
                VALUES (
                    :vid,
                    0,
                    :minimo
                )
            ");

            $ins->execute([
                ':vid' => $variacaoId,
                ':minimo' => $minimo
            ]);
        }
    }

    /**
     * Busca as informações de estoque
     * para uma variação específica.
     *
     * Retorna um array com os dados
     * ou null caso não encontre.
     */
    public function buscarPorVariacao(
        int $variacaoId
    ): ?array {
        $stmt = $this->conn->prepare("
            SELECT *
            FROM estoque
            WHERE variacao_id = :vid
        ");

        $stmt->execute([
            ':vid' => $variacaoId
        ]);

        $resultado = $stmt->fetch();

        return $resultado ?: null;
    }

    /**
     * Ajusta a quantidade em estoque.
     *
     * Positivo = entrada
     * Negativo = saída
     *
     * Lança exceção se o estoque ficar negativo.
     */
    public function ajustar(
        int $variacaoId,
        int $delta
    ): void {
        // Garante que existe um registro
        // de estoque para esta variação.
        $this->garantirRegistro($variacaoId);

        // Busca a quantidade atual.
        $stmt = $this->conn->prepare("
            SELECT quantidade
            FROM estoque
            WHERE variacao_id = :vid
            FOR UPDATE
        ");

        $stmt->execute([
            ':vid' => $variacaoId
        ]);

        $atual = (int) $stmt->fetchColumn();

        // Calcula a nova quantidade.
        $novaQuantidade = $atual + $delta;

        // Impede que o estoque fique negativo.
        if ($novaQuantidade < 0) {
            throw new RuntimeException(
                "Estoque insuficiente para esta operação."
            );
        }

        // Atualiza o estoque.
        $up = $this->conn->prepare("
            UPDATE estoque
            SET quantidade = :q
            WHERE variacao_id = :vid
        ");

        $up->execute([
            ':q' => $novaQuantidade,
            ':vid' => $variacaoId
        ]);
    }

    /**
     * Lista todos os produtos que estão
     * com estoque abaixo ou igual ao mínimo.
     *
     * Retorna:
     * - quantidade
     * - mínimo
     * - SKU
     * - tamanho
     * - cor
     * - nome do produto
     */
    public function listarBaixoEstoque(): array
    {
        $sql = "
            SELECT
                e.quantidade,
                e.minimo,
                v.sku,
                v.tamanho,
                v.cor,
                p.nome AS produto_nome

            FROM estoque e

            INNER JOIN variacao v
                ON v.id = e.variacao_id

            INNER JOIN produto p
                ON p.id_produto = v.produto_id

            WHERE e.quantidade <= e.minimo

            ORDER BY p.nome
        ";

        return $this->conn
            ->query($sql)
            ->fetchAll();
    }

    /**
     * Conta quantos registros estão
     * com estoque abaixo ou igual ao mínimo.
     */
    public function contarBaixoEstoque(): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM estoque
            WHERE quantidade <= minimo
        ";

        return (int) $this->conn
            ->query($sql)
            ->fetchColumn();
    }
}