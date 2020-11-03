<?php
namespace Src\Controller;

use Src\TableGateways\CarGateway;

class CarController {

    private $db;
    private $requestMethod;
    private $userId;

    private $carGateway;

    public function __construct($db, $requestMethod, $userId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->userId = $userId;

        $this->carGateway = new CarGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->userId) {
                    $response = $this->getCar($this->userId);
                } else {
                    $response = $this->getAllCars();
                };
                break;
            case 'POST':
                $response = $this->createCarFromRequest();
                break;
            case 'PUT':
                $response = $this->updateCarFromRequest($this->userId);
                break;
            case 'DELETE':
                $response = $this->deleteCar($this->userId);
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
        $result = $this->carGateway->findAll();
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
        if (! $this->validateCar($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->carGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;

        return $response;
    }

    private function updateCarFromRequest($id)
    {
        $result = $this->carGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateCar($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->carGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
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

    private function validateCar($input)
    {
        if (! isset($input['veiculo'])) {
            return false;
        }
        if (! isset($input['ano'])) {
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
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}