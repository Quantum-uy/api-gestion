<?php
class RutaModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function create($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO ruta (nombre, zona, numero, descripcion, color, estado, id_conductor, id_camion) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $color  = $data['color']  ?? '#1a5c52';
        $estado = $data['estado'] ?? 'activa';
        $numero = $data['numero'] ?? 1;
        $id_conductor = !empty($data['id_conductor']) ? $data['id_conductor'] : null;
        $id_camion = !empty($data['id_camion']) ? $data['id_camion'] : null;
        $stmt->bind_param('ssisssii',
            $data['nombre'], $data['zona'], $numero, $data['descripcion'], $color, $estado, $id_conductor, $id_camion
        );
        $stmt->execute();
        return $this->conn->insert_id;
    }

    public function update($id, $data) {
        $stmt = $this->conn->prepare(
            "UPDATE ruta SET nombre=?, zona=?, numero=?, descripcion=?, color=?, estado=?, id_conductor=?, id_camion=? WHERE id_ruta=?"
        );
        $color  = $data['color']  ?? '#1a5c52';
        $estado = $data['estado'] ?? 'activa';
        $numero = $data['numero'] ?? 1;
        $id_conductor = !empty($data['id_conductor']) ? $data['id_conductor'] : null;
        $id_camion = !empty($data['id_camion']) ? $data['id_camion'] : null;
        $stmt->bind_param('ssisssiii',
            $data['nombre'], $data['zona'], $numero, $data['descripcion'], $color, $estado, $id_conductor, $id_camion, $id
        );
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM ruta WHERE id_ruta=?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public function getAll() {
        $sql = "SELECT r.id_ruta, r.nombre, r.zona, r.numero, r.descripcion, r.color, r.estado,
                       r.id_conductor, r.id_camion,
                       cam.matricula AS camion_matricula, cam.modelo AS camion_modelo, cam.estado AS camion_estado,
                       u_cond.nombre AS conductor_nombre, u_cond.apellido AS conductor_apellido,
                       u_peon.nombre AS peon_nombre, u_peon.apellido AS peon_apellido,
                       cond.id_peon,
                       cont.id_contenedor, cont.ubicacion, cont.zona AS zona_contenedor,
                       cont.estado AS estado_contenedor, tr.nombre AS tipo_residuo, cont.orden_en_ruta
                FROM ruta r
                LEFT JOIN camion cam ON r.id_camion = cam.id_camion
                LEFT JOIN conductor cond ON r.id_conductor = cond.id_usuario
                LEFT JOIN usuario u_cond ON r.id_conductor = u_cond.id_usuario
                LEFT JOIN usuario u_peon ON cond.id_peon = u_peon.id_usuario
                LEFT JOIN contenedor cont ON cont.id_ruta = r.id_ruta
                LEFT JOIN tipo_residuo tr ON cont.id_tipo_residuo = tr.id_tipo_residuo
                ORDER BY r.zona, r.numero, cont.orden_en_ruta";

        $result = $this->conn->query($sql);
        $rutas = [];

        while ($row = $result->fetch_assoc()) {
            $id = $row['id_ruta'];
            if (!isset($rutas[$id])) {
                $rutas[$id] = [
                    'id_ruta'     => $id,
                    'nombre'      => $row['nombre'],
                    'zona'        => $row['zona'],
                    'numero'      => (int)$row['numero'],
                    'descripcion' => $row['descripcion'],
                    'color'       => $row['color'],
                    'estado'      => $row['estado'],
                    'id_conductor' => $row['id_conductor'],
                    'conductor'   => $row['conductor_nombre'] ? $row['conductor_nombre'] . ' ' . $row['conductor_apellido'] : null,
                    'id_camion'   => $row['id_camion'],
                    'camion'      => $row['camion_matricula'] ? $row['camion_matricula'] . ' - ' . $row['camion_modelo'] : null,
                    'camion_estado' => $row['camion_estado'],
                    'peon'        => $row['peon_nombre'] ? $row['peon_nombre'] . ' ' . $row['peon_apellido'] : null,
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
