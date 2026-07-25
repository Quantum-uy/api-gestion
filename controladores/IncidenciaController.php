<?php

require_once __DIR__ . '/../modelos/IncidenciaModel.php';

class IncidenciaController
{
    private $modelo;

    public function __construct($conn)
    {
        $this->modelo = new IncidenciaModel($conn);
    }

    public function getAll()
    {
        echo json_encode($this->modelo->getAll());
    }

    public function getById($id)
    {
        $incidencia = $this->modelo->getById($id);

        if ($incidencia) {
            echo json_encode($incidencia);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Incidencia no encontrada"]);
        }
    }

    public function create($data)
    {
        if (empty($data['tipo']) || empty($data['ubicacion']) || empty($data['zona'])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos obligatorios: tipo, ubicacion, zona"]);
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

    public function updateEstado($id, $data)
    {
        if (empty($data['estado'])) {
            http_response_code(400);
            echo json_encode(["error" => "Falta el campo: estado"]);
            return;
        }

        $result = $this->modelo->updateEstado($id, $data['estado']);

        if (isset($result['error'])) {
            http_response_code(400);
        }

        echo json_encode($result);
    }
}
