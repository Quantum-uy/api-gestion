<?php
require_once 'modelos/RutaModel.php';

class RutaController {
    private $model;

    public function __construct($conn) {
        $this->model = new RutaModel($conn);
    }

    public function getAll() {
        echo json_encode($this->model->getAll());
    }

    public function create($data) {
        if (empty($data['nombre']) || empty($data['zona'])) {
            http_response_code(400);
            echo json_encode(['error' => 'nombre y zona son obligatorios']);
            return;
        }
        $id = $this->model->create($data);
        http_response_code(201);
        echo json_encode(['id_ruta' => $id]);
    }

    public function update($id, $data) {
        if (empty($data['nombre']) || empty($data['zona'])) {
            http_response_code(400);
            echo json_encode(['error' => 'nombre y zona son obligatorios']);
            return;
        }
        $this->model->update($id, $data);
        echo json_encode(['ok' => true]);
    }

    public function delete($id) {
        $this->model->delete($id);
        echo json_encode(['ok' => true]);
    }
}
