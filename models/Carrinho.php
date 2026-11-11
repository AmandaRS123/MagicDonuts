<?php

require_once __DIR__ . '/../config/db.php';

class Carrinho
{
    public const MAX_POR_ITEM = 20;

    private PDO $conn;

    private string $sessao = 'carrinho';

    public function __construct()
    {
        $this->conn = Database::getConnection();

        if (!isset($_SESSION[$this->sessao])) {
            $_SESSION[$this->sessao] = [];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ADICIONAR PRODUTO
    |--------------------------------------------------------------------------
    */

    public function adicionar(
        int $variacaoId,
        int $quantidade = 1
    ): bool {

        if ($variacaoId <= 0 || $quantidade <= 0) {
            return false;
        }

        $variacao = $this->buscarVariacao($variacaoId);

        if (!$variacao) {
            return false;
        }

        $carrinho = $_SESSION[$this->sessao];

        $quantidadeAtual = (int) (
            $carrinho[$variacaoId] ?? 0
        );

        $novaQuantidade = $quantidadeAtual + $quantidade;

        $limite = min(
            (int) $variacao['estoque'],
            self::MAX_POR_ITEM
        );

        if (
            $limite <= 0 ||
            $novaQuantidade > $limite
        ) {
            return false;
        }

        $_SESSION[$this->sessao][$variacaoId] =
            $novaQuantidade;

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | ATUALIZAR QUANTIDADES
    |--------------------------------------------------------------------------
    */

    public function atualizar(array $quantidades): void
    {
        foreach ($quantidades as $id => $quantidade) {

            $id = (int) $id;
            $quantidade = (int) $quantidade;

            if ($id <= 0) {
                continue;
            }

            if ($quantidade <= 0) {
                unset($_SESSION[$this->sessao][$id]);
                continue;
            }

            $variacao = $this->buscarVariacao($id);

            if (!$variacao) {
                unset($_SESSION[$this->sessao][$id]);
                continue;
            }

            $limite = min(
                (int) $variacao['estoque'],
                self::MAX_POR_ITEM
            );

            if ($limite <= 0) {
                unset($_SESSION[$this->sessao][$id]);
                continue;
            }

            $_SESSION[$this->sessao][$id] = min(
                $quantidade,
                $limite
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVER PRODUTO
    |--------------------------------------------------------------------------
    */

    public function remover(int $variacaoId): void
    {
        unset(
            $_SESSION[$this->sessao][$variacaoId]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | LIMPAR CARRINHO
    |--------------------------------------------------------------------------
    */

    public function limpar(): void
    {
        $_SESSION[$this->sessao] = [];
    }

    /*
    |--------------------------------------------------------------------------
    | LISTAR PRODUTOS DO CARRINHO
    |--------------------------------------------------------------------------
    */

    public function listar(): array
    {
        $carrinho = $_SESSION[$this->sessao] ?? [];

        if (empty($carrinho)) {
            return [];
        }

        $itens = [];

        foreach ($carrinho as $variacaoId => $quantidade) {

            $variacaoId = (int) $variacaoId;
            $quantidade = (int) $quantidade;

            $sql = "
                SELECT
                    v.id AS variacao_id,
                    v.produto_id,
                    v.tamanho,
                    v.sku,
                    v.preco,
                    p.nome AS produto_nome,
                    p.descricao,
                    COALESCE(e.quantidade, 0) AS estoque

                FROM variacao v

                INNER JOIN produto p
                    ON p.id = v.produto_id

                LEFT JOIN estoque e
                    ON e.variacao_id = v.id

                WHERE v.id = :id
                  AND p.ativo = 1

                LIMIT 1
            ";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ':id' => $variacaoId
            ]);

            $item = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$item) {
                unset($_SESSION[$this->sessao][$variacaoId]);
                continue;
            }

            $estoque = (int) $item['estoque'];

            $limite = min(
                $estoque,
                self::MAX_POR_ITEM
            );

            if ($limite <= 0) {
                unset($_SESSION[$this->sessao][$variacaoId]);
                continue;
            }

            $quantidade = min($quantidade, $limite);

            $_SESSION[$this->sessao][$variacaoId] =
                $quantidade;

            $item['quantidade'] = $quantidade;
            $item['estoque'] = $estoque;
            $item['limite'] = $limite;

            $item['preco'] = (float) $item['preco'];

            $item['subtotal'] =
                $item['preco'] * $quantidade;

            $itens[] = $item;
        }

        return $itens;
    }

    /*
    |--------------------------------------------------------------------------
    | CALCULAR SUBTOTAL
    |--------------------------------------------------------------------------
    */

    public function subtotal(): float
    {
        $subtotal = 0;

        foreach ($this->listar() as $item) {
            $subtotal += (float) $item['subtotal'];
        }

        return $subtotal;
    }

    /*
    |--------------------------------------------------------------------------
    | QUANTIDADE TOTAL DE UNIDADES
    |--------------------------------------------------------------------------
    */

    public function quantidadeTotal(): int
    {
        $total = 0;

        foreach ($this->listar() as $item) {
            $total += (int) $item['quantidade'];
        }

        return $total;
    }

    /*
    |--------------------------------------------------------------------------
    | CONTAGEM DE TIPOS DE PRODUTOS
    |--------------------------------------------------------------------------
    */

    public function quantidadeProdutos(): int
    {
        return count($this->listar());
    }

    /*
    |--------------------------------------------------------------------------
    | BUSCAR VARIAÇÃO E ESTOQUE
    |--------------------------------------------------------------------------
    */

    public function buscarVariacao(int $id): ?array
    {
        $sql = "
            SELECT
                v.id,
                v.produto_id,
                v.tamanho,
                v.sku,
                v.preco,
                p.nome AS produto_nome,
                COALESCE(e.quantidade, 0) AS estoque

            FROM variacao v

            INNER JOIN produto p
                ON p.id = v.produto_id

            LEFT JOIN estoque e
                ON e.variacao_id = v.id

            WHERE v.id = :id
              AND p.ativo = 1

            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado ?: null;
    }
}