<?php

class CentroAcopioModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAll()
    {
        $result = mysqli_query($this->conn, "SELECT * FROM centro_acopio ORDER BY id_centro DESC");
        $centros = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $centros[] = $row;
        }
        return $centros;
    }

    public function getById($id)
    {
        $stmt = mysqli_prepare($this->conn, "SELECT * FROM centro_acopio WHERE id_centro = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    }

    public function create($data)
    {
        $stmt = mysqli_prepare($this->conn,
            "INSERT INTO centro_acopio (nombre, ubicacion, capacidad) VALUES (?, ?, ?)"
        );
        $capacidad = $data['capacidad'] ?? null;
        mysqli_stmt_bind_param($stmt, "ssi", $data['nombre'], $data['ubicacion'], $capacidad);

        if (mysqli_stmt_execute($stmt)) {
            return ["success" => "Centro de acopio creado", "id" => mysqli_insert_id($this->conn)];
        }
        return ["error" => "No se pudo crear el centro de acopio"];
    }

    public function update($id, $data)
    {
        $stmt = mysqli_prepare($this->conn,
            "UPDATE centro_acopio SET nombre = ?, ubicacion = ?, capacidad = ? WHERE id_centro = ?"
        );
        $capacidad = $data['capacidad'] ?? null;
        mysqli_stmt_bind_param($stmt, "ssii", $data['nombre'], $data['ubicacion'], $capacidad, $id);

        if (mysqli_stmt_execute($stmt)) {
            return ["success" => "Centro de acopio actualizado"];
        }
        return ["error" => "No se pudo actualizar el centro de acopio"];
    }

    public function delete($id)
    {
        $stmt = mysqli_prepare($this->conn, "DELETE FROM centro_acopio WHERE id_centro = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);

        if (mysqli_stmt_execute($stmt)) {
            return ["success" => "Centro de acopio eliminado"];
        }
        return ["error" => "No se pudo eliminar el centro de acopio"];
    }
}
