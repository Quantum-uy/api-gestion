<?php

require_once __DIR__ . '/../modelos/ContenedorModel.php';

class ContenedorController
{
    private $modelo;

    public function __construct($conn)
    {
        $this->modelo = new ContenedorModel($conn);
    }

    public function getAll()
    {
        echo json_encode($this->modelo->getAll());
    }

    public function getById($id)
    {
        $contenedor = $this->modelo->getById($id);

        if ($contenedor) {
            echo json_encode($contenedor);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Contenedor no encontrado"]);
        }
    }

    public function create($data)
    {
        if (empty($data['ubicacion']) || empty($data['zona'])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos obligatorios: ubicacion, zona"]);
            return;
        }

        $result = $this->modelo->create($data);

        if (isset($result['error'])) {
            http_response_code(400);
        } else {
            http_response_code(201);
        }

        echo json_encode($result);
    }

    public function update($id, $data)
    {
        if (empty($data['ubicacion']) || empty($data['zona']) || empty($data['estado'])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos obligatorios: ubicacion, zona, estado"]);
            return;
        }

        $result = $this->modelo->update($id, $data);

        if (isset($result['error'])) {
            http_response_code(400);
        }

        echo json_encode($result);
    }

    public function delete($id)
    {
        $result = $this->modelo->delete($id);

        if (isset($result['error'])) {
            http_response_code(400);
        }

        echo json_encode($result);
    }
}
