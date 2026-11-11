<?php

require_once __DIR__ . '/../config/db.php';

class Vitrine
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    /*
    |--------------------------------------------------------------------------
    | LISTAR PRODUTOS DA LOJA
    |--------------------------------------------------------------------------
    */

    public function listarProdutos(
        ?int $categoriaId = null,
        string $busca = '',
        string $ordem = 'relevancia'
    ): array {

        $sql = "
            SELECT
                p.id,
                p.nome,
                p.descricao,
                p.categoria_id,
                c.nome AS categoria_nome,

                COUNT(DISTINCT v.id) AS qtd_variacoes,

                COALESCE(
                    MIN(v.preco),
                    0
                ) AS preco_min,

                COALESCE(
                    MAX(v.preco),
                    0
                ) AS preco_max,

                MAX(
                    CASE
                        WHEN COALESCE(e.quantidade, 0) > 0
                        THEN 1
                        ELSE 0
                    END
                ) AS disponivel

            FROM produto p

            INNER JOIN categoria c
                ON c.id = p.categoria_id

            LEFT JOIN variacao v
                ON v.produto_id = p.id

            LEFT JOIN estoque e
                ON e.variacao_id = v.id

            WHERE p.ativo = 1
              AND c.ativo = 1
        ";

        $params = [];

        if ($categoriaId !== null && $categoriaId > 0) {

            $sql .= " AND p.categoria_id = :categoria ";

            $params[':categoria'] = $categoriaId;
        }

        if ($busca !== '') {

            $sql .= "
                AND (
                    p.nome LIKE :busca
                    OR p.descricao LIKE :busca
                )
            ";

            $params[':busca'] = '%' . $busca . '%';
        }

        $sql .= "
            GROUP BY
                p.id,
                p.nome,
                p.descricao,
                p.categoria_id,
                c.nome
        ";

        switch ($ordem) {

            case 'menor':
                $sql .= " ORDER BY preco_min ASC, p.nome ASC ";
                break;

            case 'maior':
                $sql .= " ORDER BY preco_max DESC, p.nome ASC ";
                break;

            case 'nome':
                $sql .= " ORDER BY p.nome ASC ";
                break;

            default:
                $sql .= " ORDER BY p.id DESC ";
                break;
        }

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | LISTAR CATEGORIAS COM QUANTIDADE DE PRODUTOS
    |--------------------------------------------------------------------------
    */

    public function listarCategorias(): array
    {
        $sql = "
            SELECT
                c.id,
                c.nome,
                COUNT(DISTINCT p.id) AS total

            FROM categoria c

            LEFT JOIN produto p
                ON p.categoria_id = c.id
                AND p.ativo = 1

            WHERE c.ativo = 1

            GROUP BY c.id, c.nome

            ORDER BY c.nome ASC
        ";

        return $this->conn
            ->query($sql)
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | BUSCAR PRODUTO PELO ID
    |--------------------------------------------------------------------------
    */

    public function buscarProduto(int $id): ?array
    {
        $sql = "
            SELECT
                p.id,
                p.nome,
                p.descricao,
                p.categoria_id,
                c.nome AS categoria_nome

            FROM produto p

            INNER JOIN categoria c
                ON c.id = p.categoria_id

            WHERE p.id = :id
              AND p.ativo = 1
              AND c.ativo = 1

            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$produto) {
            return null;
        }

        $produto['variacoes'] = $this->listarVariacoes($id);

        return $produto;
    }

    /*
    |--------------------------------------------------------------------------
    | LISTAR VARIAÇÕES E ESTOQUE DO PRODUTO
    |--------------------------------------------------------------------------
    */

    public function listarVariacoes(int $produtoId): array
{
    $sql = "
        SELECT
            v.id,
            v.produto_id,
            v.tamanho,
            v.sku,
            v.preco,
            COALESCE(e.quantidade, 0) AS estoque
        FROM variacao v
        LEFT JOIN estoque e ON e.variacao_id = v.id
        WHERE v.produto_id = :produto_id
        ORDER BY v.tamanho ASC, v.id ASC
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
        ':produto_id' => $produtoId
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    /*
    |--------------------------------------------------------------------------
    | PRODUTOS EM DESTAQUE
    |--------------------------------------------------------------------------
    */

    public function listarDestaques(int $limite = 3): array
    {
        $limite = max(1, min($limite, 8));

        $sql = "
            SELECT
                p.id,
                p.nome,
                p.descricao,
                c.nome AS categoria_nome

            FROM produto p

            INNER JOIN categoria c
                ON c.id = p.categoria_id

            WHERE p.ativo = 1
              AND c.ativo = 1

            ORDER BY p.id DESC

            LIMIT {$limite}
        ";

        return $this->conn
            ->query($sql)
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | PRODUTOS RELACIONADOS
    |--------------------------------------------------------------------------
    */

    public function listarRelacionados(
        int $produtoId,
        int $categoriaId,
        int $limite = 4
    ): array {

        $limite = max(1, min($limite, 8));

        $sql = "
            SELECT
                p.id,
                p.nome,
                p.descricao,

                COUNT(DISTINCT v.id) AS qtd_variacoes,

                COALESCE(
                    MIN(v.preco),
                    0
                ) AS preco_min,

                COALESCE(
                    MAX(v.preco),
                    0
                ) AS preco_max,

                MAX(
                    CASE
                        WHEN COALESCE(e.quantidade, 0) > 0
                        THEN 1
                        ELSE 0
                    END
                ) AS disponivel

            FROM produto p

            LEFT JOIN variacao v
                ON v.produto_id = p.id

            LEFT JOIN estoque e
                ON e.variacao_id = v.id

            WHERE p.ativo = 1
              AND p.categoria_id = :categoria
              AND p.id <> :produto

            GROUP BY
                p.id,
                p.nome,
                p.descricao

            ORDER BY p.id DESC

            LIMIT {$limite}
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':categoria' => $categoriaId,
            ':produto' => $produtoId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}