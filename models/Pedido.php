
<?php

require_once __DIR__ . '/../config/db.php';

class Pedido
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    /*
    |--------------------------------------------------------------------------
    | BUSCAR PEDIDO POR ID
    |--------------------------------------------------------------------------
    */

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT
                p.*,
                c.nome AS cliente_nome,
                c.email AS cliente_email,
                c.telefone AS cliente_telefone
            FROM pedido p
            INNER JOIN cliente_site c
                ON c.cpf = p.cliente_cpf
            WHERE p.id = :id
            LIMIT 1
        ");

        $stmt->execute([':id' => $id]);

        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

        return $pedido ?: null;
    }

    /*
    |--------------------------------------------------------------------------
    | BUSCAR PEDIDO DO CLIENTE
    |--------------------------------------------------------------------------
    */

    public function buscarDoCliente(int $id, string $cpf): ?array
    {
        $cpf = preg_replace('/\D/', '', $cpf);

        $stmt = $this->conn->prepare("
            SELECT *
            FROM pedido
            WHERE id = :id
              AND cliente_cpf = :cpf
            LIMIT 1
        ");

        $stmt->execute([
            ':id' => $id,
            ':cpf' => $cpf
        ]);

        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

        return $pedido ?: null;
    }

    /*
    |--------------------------------------------------------------------------
    | LISTAR PEDIDOS DO CLIENTE
    |--------------------------------------------------------------------------
    */

    public function listarPorCliente(string $cpf): array
    {
        $cpf = preg_replace('/\D/', '', $cpf);

        $stmt = $this->conn->prepare("
            SELECT
                id,
                data,
                status,
                endereco_entrega,
                cep_entrega,
                valor_frete,
                prazo_frete_dias,
                forma_pagamento,
                valor_total
            FROM pedido
            WHERE cliente_cpf = :cpf
            ORDER BY data DESC, id DESC
        ");

        $stmt->execute([':cpf' => $cpf]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | LISTAR ITENS DO PEDIDO
    |--------------------------------------------------------------------------
    */

    public function listarItens(int $pedidoId): array
    {
        $stmt = $this->conn->prepare("
            SELECT
                pi.id,
                pi.pedido_id,
                pi.variacao_id,
                pi.quantidade,
                pi.preco_unitario,

                v.tamanho AS variacao_nome,
                v.tamanho,
                v.sku,

                p.id AS produto_id,
                p.nome AS produto_nome,

                (pi.quantidade * pi.preco_unitario) AS subtotal

            FROM pedido_item pi

            INNER JOIN variacao v
                ON v.id = pi.variacao_id

            INNER JOIN produto p
                ON p.id = v.produto_id

            WHERE pi.pedido_id = :pedido_id

            ORDER BY pi.id ASC
        ");

        $stmt->execute([
            ':pedido_id' => $pedidoId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | CRIAR PEDIDO
    |--------------------------------------------------------------------------
    */

    public function criar(array $dados): int
    {
        $stmt = $this->conn->prepare("
            INSERT INTO pedido (
                cliente_cpf,
                endereco_entrega,
                cep_entrega,
                valor_frete,
                prazo_frete_dias,
                forma_pagamento,
                valor_total
            ) VALUES (
                :cpf,
                :endereco,
                :cep,
                :frete,
                :prazo,
                :pagamento,
                :total
            )
        ");

        $stmt->execute([
            ':cpf' => preg_replace(
                '/\D/',
                '',
                $dados['cliente_cpf'] ?? ''
            ),

            ':endereco' => trim(
                $dados['endereco_entrega'] ?? ''
            ),

            ':cep' => $dados['cep_entrega'] ?? null,

            ':frete' => (float) ($dados['valor_frete'] ?? 0),

            ':prazo' => $dados['prazo_frete_dias'] ?? null,

            ':pagamento' => $dados['forma_pagamento'] ?? 'pix',

            ':total' => (float) ($dados['valor_total'] ?? 0)
        ]);

        return (int) $this->conn->lastInsertId();
    }

    /*
    |--------------------------------------------------------------------------
    | ADICIONAR ITEM AO PEDIDO
    |--------------------------------------------------------------------------
    */

    public function adicionarItem(
        int $pedidoId,
        int $variacaoId,
        int $quantidade,
        float $precoUnitario
    ): bool {

        if (
            $pedidoId <= 0 ||
            $variacaoId <= 0 ||
            $quantidade <= 0 ||
            $precoUnitario < 0
        ) {
            return false;
        }

        $stmt = $this->conn->prepare("
            INSERT INTO pedido_item (
                pedido_id,
                variacao_id,
                quantidade,
                preco_unitario
            ) VALUES (
                :pedido_id,
                :variacao_id,
                :quantidade,
                :preco
            )
        ");

        return $stmt->execute([
            ':pedido_id' => $pedidoId,
            ':variacao_id' => $variacaoId,
            ':quantidade' => $quantidade,
            ':preco' => $precoUnitario
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ATUALIZAR STATUS DO PEDIDO
    |--------------------------------------------------------------------------
    */

    public function atualizarStatus(int $id, string $status): bool
    {
        $permitidos = [
            'pendente',
            'pago',
            'enviado',
            'entregue',
            'cancelado'
        ];

        if (
            $id <= 0 ||
            !in_array($status, $permitidos, true)
        ) {
            return false;
        }

        $stmt = $this->conn->prepare("
            UPDATE pedido
            SET status = :status
            WHERE id = :id
        ");

        return $stmt->execute([
            ':status' => $status,
            ':id' => $id
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CANCELAR PEDIDO DO CLIENTE
    |--------------------------------------------------------------------------
    */

    public function cancelar(int $id, string $cpf): bool
    {
        $cpf = preg_replace('/\D/', '', $cpf);

        $stmt = $this->conn->prepare("
            UPDATE pedido
            SET status = 'cancelado'
            WHERE id = :id
              AND cliente_cpf = :cpf
              AND status = 'pendente'
        ");

        return $stmt->execute([
            ':id' => $id,
            ':cpf' => $cpf
        ]);
    }
}
