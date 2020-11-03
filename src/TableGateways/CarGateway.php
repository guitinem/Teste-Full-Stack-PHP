<?php
namespace Src\TableGateways;

class CarGateway {

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $statement = "
            SELECT 
                *
            FROM
                car;
        ";

        try {
            $statement = $this->db->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function find($id)
    {
        $statement = "
            SELECT 
                *
            FROM
                car
            WHERE id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function insert(Array $input)
    {
        $statement = "
            INSERT INTO car 
                (veiculo, marca, ano, descricao, vendido)
            VALUES
                (:veiculo, :marca, :ano, :descricao, :vendido);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'veiculo' => $input['veiculo'],
                'marca'  => $input['marca'],
                'ano' => $input['ano'],
                'descricao' => $input['descricao'],
                'vendido' => $input['vendido'] ?? false,
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function update($id, Array $input)
    {
        $statement = "
            UPDATE car
            SET 
                veiculo = :veiculo,
                marca  = :marca,
                ano = :ano,
                descricao = :descricao
                vendido = :vendido
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'id' => (int) $id,
                'veiculo' => $input['veiculo'],
                'marca'  => $input['marca'],
                'ano' => $input['ano'],
                'descricao' => $input['descricao'] ?? null,
                'vendido' => $input['vendido'],
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function delete($id)
    {
        $statement = "
            DELETE FROM car
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => $id));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }
}