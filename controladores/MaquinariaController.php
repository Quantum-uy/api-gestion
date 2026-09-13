<?php

require_once __DIR__ . '/../modelos/MaquinariaModel.php';

class MaquinariaController
{
    private $modelo;

    public function __construct($conn)
    {
        $this->modelo = new MaquinariaModel($conn);
    }

    public function getAll()
    {
        echo json_encode($this->modelo->getAll());
    }

    public function getById($id)
    {
        $maq = $this->modelo->getById($id);
        if ($maq) {
            echo json_encode($maq);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Maquinaria no encontrada"]);
        }
    }

    public function create($data)
    {
        if (empty($data['tipo'])) {
            http_response_code(400);
            echo json_encode(["error" => "Falta campo obligatorio: tipo"]);
            return;
        }
        $result = $this->modelo->create($data);
        http_response_code(isset($result['error']) ? 400 : 201);
        echo json_encode($result);
    }

    public function update($id, $data)
    {
        if (empty($data['tipo'])) {
            http_response_code(400);
            echo json_encode(["error" => "Falta campo obligatorio: tipo"]);
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
