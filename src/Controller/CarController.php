<?php
namespace Src\Controller;

use Src\TableGateways\CarGateway;

class CarController {

    private $db;
    private $requestMethod;
    private $pathParams;
    private $queryParams;
    private $carId;

    private $carGateway;

    public function __construct(array $request_var)
    {
        $this->db = $request_var['db'];
        $this->requestMethod = $request_var['method'];
        $this->pathParams = $request_var['path_params'];
        $this->queryParams = $request_var['query_params'];
        $this->carId = null;

        $this->carGateway = new CarGateway($this->db);
    }

    public function processRequest()
    {

        $this->validateUriParams();

        switch ($this->requestMethod) {
            case 'GET':
                if ($this->carId) {
                    $response = $this->getCar($this->carId);
                } else {
                    $response = $this->getAllCars();
                };
                break;
            case 'POST':
                $response = $this->createCarFromRequest();
                break;
            case 'PUT':
                $response = $this->updateCarFromRequest($this->carId);
                break;
            case 'DELETE':
                $response = $this->deleteCar($this->carId);
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }

        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body']; 
        }
    }

    private function getAllCars()
    {
        $result = [];
        if ($this->queryParams) {
            $result = $this->carGateway->findCarByQuery($this->queryParams);

            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($result);
    
            return $response;
        }

        $result = $this->carGateway->findAllCars();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);

        return $response;
    }

    private function getCar($id)
    {
        $result = $this->carGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createCarFromRequest()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateCarValues($input)) {
            return $this->unprocessableEntityResponse();
        }
        $result = $this->carGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = json_encode($result);

        return $response;
    }

    private function updateCarFromRequest($id)
    {
        $result = $this->carGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }

        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateCarValues($input)) {
            return $this->unprocessableEntityResponse();
        }

        $result = $this->carGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function deleteCar($id)
    {
        $result = $this->carGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->carGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validateCarValues($input)
    {

        $inputKeyDifference = array_diff_key($input,[ 
            'veiculo'   => '', 
            'marca'     => '', 
            'ano'       => '', 
            'descricao' => '', 
            'vendido'   => '' ]
        );
    
        if (count($inputKeyDifference) != 0) {
            return false;
        }

        if (!\is_numeric($input['ano'])) {
            return false;
        }

        return true;
    }

    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid input'
        ]);
        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404';
        $response['body'] = null;
        return $response;
    }

    private function validateUriParams()
    {        
        // Validate path
        if ($this->pathParams[1] !== 'car') {
            header("HTTP/1.1 404");
            exit();
        }

        // Validate car id
        if (isset($this->pathParams[2]) && !is_numeric($this->pathParams[2])) {
            header("HTTP/1.1 400");
            exit();
        } else {
            $this->carId = $this->pathParams[2]; 
        }        
    }
}