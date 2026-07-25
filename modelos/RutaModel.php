<?php
class RutaModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function create($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO ruta (nombre, zona, descripcion, color, estado) VALUES (?, ?, ?, ?, ?)"
        );
        $color  = $data['color']  ?? '#1a5c52';
        $estado = $data['estado'] ?? 'activa';
        $stmt->bind_param('sssss',
            $data['nombre'], $data['zona'], $data['descripcion'], $color, $estado
        );
        $stmt->execute();
        return $this->conn->insert_id;
    }

    public function update($id, $data) {
        $stmt = $this->conn->prepare(
            "UPDATE ruta SET nombre=?, zona=?, descripcion=?, color=?, estado=? WHERE id_ruta=?"
        );
        $color  = $data['color']  ?? '#1a5c52';
        $estado = $data['estado'] ?? 'activa';
        $stmt->bind_param('sssssi',
            $data['nombre'], $data['zona'], $data['descripcion'], $color, $estado, $id
        );
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM ruta WHERE id_ruta=?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public function getAll() {
        $sql = "SELECT r.id_ruta, r.nombre, r.zona, r.descripcion, r.color, r.estado,
                       c.id_contenedor, c.ubicacion, c.zona AS zona_contenedor,
                       c.estado AS estado_contenedor, c.tipo_residuo, c.orden_en_ruta
                FROM ruta r
                LEFT JOIN contenedor c ON c.id_ruta = r.id_ruta
                WHERE r.estado = 'activa'
                ORDER BY r.id_ruta, c.orden_en_ruta";

        $result = $this->conn->query($sql);
        $rutas = [];

        while ($row = $result->fetch_assoc()) {
            $id = $row['id_ruta'];
            if (!isset($rutas[$id])) {
                $rutas[$id] = [
                    'id_ruta'     => $id,
                    'nombre'      => $row['nombre'],
                    'zona'        => $row['zona'],
                    'descripcion' => $row['descripcion'],
                    'color'       => $row['color'],
                    'estado'      => $row['estado'],
                    'contenedores' => [],
                ];
            }
            if ($row['id_contenedor']) {
                $rutas[$id]['contenedores'][] = [
                    'id_contenedor'    => $row['id_contenedor'],
                    'ubicacion'        => $row['ubicacion'],
                    'zona'             => $row['zona_contenedor'],
                    'estado'           => $row['estado_contenedor'],
                    'tipo_residuo'     => $row['tipo_residuo'],
                    'orden_en_ruta'    => $row['orden_en_ruta'],
                ];
            }
        }

        return array_values($rutas);
    }
}
