<?php

class IncidenciaModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAll()
    {
        $sql = "SELECT i.*, c.ubicacion AS contenedor_ubicacion, c.zona AS contenedor_zona,
                       u.nombre AS conductor_nombre, u.apellido AS conductor_apellido
                FROM incidencia i
                LEFT JOIN contenedor c ON i.id_contenedor = c.id_contenedor
                LEFT JOIN usuario u ON i.id_conductor_asignado = u.id_usuario
                ORDER BY i.id_incidencia DESC";
        $result = mysqli_query($this->conn, $sql);
        $incidencias = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $incidencias[] = $row;
        }

        return $incidencias;
    }

    public function getById($id)
    {
        $stmt = mysqli_prepare($this->conn, "SELECT * FROM incidencia WHERE id_incidencia = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_assoc($result);
    }

    public function create($data)
    {
        $stmt = mysqli_prepare($this->conn,
            "INSERT INTO incidencia (tipo, descripcion, ubicacion, zona, imagen, fecha_reporte, estado, id_contenedor, id_conductor_asignado)
             VALUES (?, ?, ?, ?, ?, CURDATE(), 'abierta', ?, ?)"
        );

        $imagen = $data['imagen'] ?? null;
        $id_contenedor = !empty($data['id_contenedor']) ? $data['id_contenedor'] : null;
        $id_conductor = !empty($data['id_conductor_asignado']) ? $data['id_conductor_asignado'] : null;

        mysqli_stmt_bind_param($stmt, "sssssii",
            $data['tipo'],
            $data['descripcion'],
            $data['ubicacion'],
            $data['zona'],
            $imagen,
            $id_contenedor,
            $id_conductor
        );

        if (mysqli_stmt_execute($stmt)) {
            return [
                "success" => "Incidencia registrada",
                "id" => mysqli_insert_id($this->conn)
            ];
        }

        return ["error" => "No se pudo registrar la incidencia"];
    }

    public function updateEstado($id, $estado)
    {
        $fechaRes = ($estado === 'resuelta') ? date('Y-m-d') : null;

        $stmt = mysqli_prepare($this->conn,
            "UPDATE incidencia SET estado = ?, fecha_resolucion = ? WHERE id_incidencia = ?"
        );

        mysqli_stmt_bind_param($stmt, "ssi", $estado, $fechaRes, $id);

        if (mysqli_stmt_execute($stmt)) {
            return ["success" => "Estado actualizado"];
        }

        return ["error" => "No se pudo actualizar"];
    }
}
