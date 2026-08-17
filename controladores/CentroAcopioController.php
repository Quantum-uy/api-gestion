<?php

require_once __DIR__ . '/../modelos/CentroAcopioModel.php';

class CentroAcopioController
{
    private $modelo;

    public function __construct($conn)
    {
        $this->modelo = new CentroAcopioModel($conn);
    }

    public function getAll()
    {
        echo json_encode($this->modelo->getAll());
    }

    public function getById($id)
    {
        $centro = $this->modelo->getById($id);
        if ($centro) {
            echo json_encode($centro);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Centro de acopio no encontrado"]);
        }
    }

    public function create($data)
    {
        if (empty($data['nombre'])) {
            http_response_code(400);
            echo json_encode(["error" => "El campo nombre es obligatorio"]);
            return;
        }
        $result = $this->modelo->create($data);
        if (isset($result['error'])) http_response_code(400);
        else http_response_code(201);
        echo json_encode($result);
    }

    public function update($id, $data)
    {
        if (empty($data['nombre'])) {
            http_response_code(400);
            echo json_encode(["error" => "El campo nombre es obligatorio"]);
            return;
        }
        $result = $this->modelo->update($id, $data);
        if (isset($result['error'])) http_response_code(400);
        echo json_encode($result);
    }

    public function delete($id)
    {
        $result = $this->modelo->delete($id);
        if (isset($result['error'])) http_response_code(400);
        echo json_encode($result);
    }
}
