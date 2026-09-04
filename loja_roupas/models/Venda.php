<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/Estoque.php';

class Venda
{
    private PDO $conn;
    private Estoque $estoqueModel;

    public function __construct()
    {
        $this->conn = Database::getConnection();
        $this->estoqueModel = new Estoque();
    }

    /**
     * Lista as vendas mais recentes.
     */
    public function listarRecentes(int $limite = 20): array
    {
        $sql = "
            SELECT
                v.id,
                v.data,
                v.status,
                v.valor_total,
                c.nome AS cliente_nome,
                u.nome AS vendedor_nome

            FROM venda v

            INNER JOIN cliente c
                ON c.id = v.cliente_id

            INNER JOIN usuario u
                ON u.id_usuario = v.usuario_id

            ORDER BY v.id DESC

            LIMIT :limite
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(
            ':limite',
            $limite,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Registra uma venda completa.
     *
     * Deduz o estoque de cada variação vendida.
     *
     * @param int $usuarioId
     * @param int $clienteId
     * @param array $itens
     *
     * Cada item deve conter:
     * [
     *     'variacao_id' => int,
     *     'quantidade' => int,
     *     'preco_unitario' => float
     * ]
     */
    public function registrar(
        int $usuarioId,
        int $clienteId,
        array $itens
    ): int {

        if (empty($itens)) {
            throw new InvalidArgumentException(
                "A venda precisa ter ao menos um item."
            );
        }


        // Calcula o valor total da venda
        $valorTotal = 0.0;

        foreach ($itens as $item) {

            $valorTotal +=
                $item['quantidade']
                * $item['preco_unitario'];
        }


        // Inicia a transação
        $this->conn->beginTransaction();


        try {

            // Cria o cabeçalho da venda
            $stmt = $this->conn->prepare("
                INSERT INTO venda (
                    usuario_id,
                    cliente_id,
                    data,
                    status,
                    valor_total
                )
                VALUES (
                    :usuario_id,
                    :cliente_id,
                    CURDATE(),
                    'finalizada',
                    :valor_total
                )
            ");

            $stmt->execute([
                ':usuario_id' => $usuarioId,
                ':cliente_id' => $clienteId,
                ':valor_total' => $valorTotal
            ]);


            // Recupera o ID da venda criada
            $vendaId = (int) $this->conn->lastInsertId();


            // Prepara o cadastro dos itens da venda
            $stmtItem = $this->conn->prepare("
                INSERT INTO venda_item (
                    venda_id,
                    variacao_id,
                    quantidade,
                    preco_unitario
                )
                VALUES (
                    :venda_id,
                    :variacao_id,
                    :quantidade,
                    :preco_unitario
                )
            ");


            // Prepara o registro do movimento de estoque
            $stmtMov = $this->conn->prepare("
                INSERT INTO movimento_estoque (
                    variacao_id,
                    tipo,
                    quantidade,
                    origem,
                    origem_id,
                    data
                )
                VALUES (
                    :variacao_id,
                    'saida',
                    :quantidade,
                    'venda',
                    :origem_id,
                    NOW()
                )
            ");


            // Processa cada item
            foreach ($itens as $item) {

                $variacaoId = (int) $item['variacao_id'];

                $quantidade = (int) $item['quantidade'];

                $precoUnitario = (float) $item['preco_unitario'];


                // Diminui o estoque
                $this->estoqueModel->ajustar(
                    $variacaoId,
                    -$quantidade
                );


                // Registra o item da venda
                $stmtItem->execute([
                    ':venda_id' => $vendaId,
                    ':variacao_id' => $variacaoId,
                    ':quantidade' => $quantidade,
                    ':preco_unitario' => $precoUnitario
                ]);


                // Registra o movimento de saída
                $stmtMov->execute([
                    ':variacao_id' => $variacaoId,
                    ':quantidade' => $quantidade,
                    ':origem_id' => $vendaId
                ]);
            }


            // Confirma a transação
            $this->conn->commit();


            return $vendaId;


        } catch (Throwable $e) {

            // Cancela tudo caso aconteça algum erro
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            throw $e;
        }
    }
}