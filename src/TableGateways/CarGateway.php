<?php
namespace Src\TableGateways;

class CarGateway {

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAllCars()
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

    public function findCarByQuery(string $queryParams)
    {
        $query = "
            SELECT *
            FROM car
            where 
                veiculo = :veiculo or
                marca = :marca or
                ano = :ano or
                vendido = :vendido
            ;
        ";

        parse_str($queryParams, $output);

        $data = [
            'veiculo' => $output['veiculo'] ?? null,
            'marca' => $output['marca'] ?? null,
            'ano' => $output['ano'] ?? null,
            'vendido' => $output['vendido'] ?? null
        ];


        try {
            $statement = $this->db->prepare($query);
            $statement->execute($data);
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
            $statement->execute([intval($id)]);
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
            $statement->execute([
                'veiculo' => $input['veiculo'],
                'marca'  => $input['marca'],
                'ano' => $input['ano'],
                'descricao' => $input['descricao'],
                'vendido' => $input['vendido'] ?? false,
            ]);


            $statement = $this->db->query('SELECT * FROM car ORDER BY id DESC LIMIT 1');

            $lastRecord = $statement->fetch(\PDO::FETCH_ASSOC);
        
            return $lastRecord;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function update($id, array $input)
    {
        $statement = "
            UPDATE car
            SET 
                veiculo = :veiculo,
                marca  = :marca,
                ano = :ano,
                descricao = :descricao,
                vendido = :vendido
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute([
                'id' => $id,
                'veiculo' => $input['veiculo'],
                'marca'  => $input['marca'],
                'ano' => $input['ano'],
                'descricao' => $input['descricao'],
                'vendido' => $input['vendido'],
            ]);


            $statement = $this->db->query('SELECT * FROM car WHERE id = ' . $id);
            $updatedRecord = $statement->fetch(\PDO::FETCH_ASSOC);

            return $updatedRecord;
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
            $statement->execute(['id' => $id]);
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }
}