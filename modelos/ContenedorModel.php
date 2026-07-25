<?php

class ContenedorModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM contenedor ORDER BY id_contenedor DESC";
        $result = mysqli_query($this->conn, $sql);
        $contenedores = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $contenedores[] = $row;
        }

        return $contenedores;
    }

    public function getById($id)
    {
        $stmt = mysqli_prepare($this->conn, "SELECT * FROM contenedor WHERE id_contenedor = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_assoc($result);
    }

    public function create($data)
    {
        $stmt = mysqli_prepare($this->conn,
            "INSERT INTO contenedor (ubicacion, zona, estado, tipo_residuo)
             VALUES (?, ?, ?, ?)"
        );

        $estado = $data['estado'] ?? 'funcional';

        mysqli_stmt_bind_param($stmt, "ssss",
            $data['ubicacion'],
            $data['zona'],
            $estado,
            $data['tipo_residuo']
        );

        if (mysqli_stmt_execute($stmt)) {
            return ["success" => "Contenedor creado", "id" => mysqli_insert_id($this->conn)];
        }

        return ["error" => "No se pudo crear el contenedor"];
    }

    public function update($id, $data)
    {
        $stmt = mysqli_prepare($this->conn,
            "UPDATE contenedor SET ubicacion = ?, zona = ?, estado = ?, tipo_residuo = ?
             WHERE id_contenedor = ?"
        );

        mysqli_stmt_bind_param($stmt, "ssssi",
            $data['ubicacion'],
            $data['zona'],
            $data['estado'],
            $data['tipo_residuo'],
            $id
        );

        if (mysqli_stmt_execute($stmt)) {
            return ["success" => "Contenedor actualizado"];
        }

        return ["error" => "No se pudo actualizar el contenedor"];
    }

    public function delete($id)
    {
        $stmt = mysqli_prepare($this->conn, "DELETE FROM contenedor WHERE id_contenedor = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);

        if (mysqli_stmt_execute($stmt)) {
            return ["success" => "Contenedor eliminado"];
        }

        return ["error" => "No se pudo eliminar el contenedor"];
    }
}
