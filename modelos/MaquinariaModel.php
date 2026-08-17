<?php

class MaquinariaModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAll()
    {
        $sql = "SELECT m.*, ca.nombre AS centro_nombre
                FROM maquinaria m
                LEFT JOIN centro_acopio ca ON m.id_centro = ca.id_centro
                ORDER BY m.id_maquinaria DESC";
        $result = mysqli_query($this->conn, $sql);
        $maquinaria = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $maquinaria[] = $row;
        }
        return $maquinaria;
    }

    public function getById($id)
    {
        $stmt = mysqli_prepare($this->conn, "SELECT * FROM maquinaria WHERE id_maquinaria = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    }

    public function create($data)
    {
        $stmt = mysqli_prepare($this->conn,
            "INSERT INTO maquinaria (tipo, estado, id_centro) VALUES (?, ?, ?)"
        );
        $estado = $data['estado'] ?? 'operativa';
        $id_centro = $data['id_centro'] ?? null;
        mysqli_stmt_bind_param($stmt, "ssi", $data['tipo'], $estado, $id_centro);

        if (mysqli_stmt_execute($stmt)) {
            return ["success" => "Maquinaria creada", "id" => mysqli_insert_id($this->conn)];
        }
        return ["error" => "No se pudo crear la maquinaria"];
    }

    public function update($id, $data)
    {
        $stmt = mysqli_prepare($this->conn,
            "UPDATE maquinaria SET tipo = ?, estado = ?, id_centro = ? WHERE id_maquinaria = ?"
        );
        $id_centro = $data['id_centro'] ?? null;
        mysqli_stmt_bind_param($stmt, "ssii", $data['tipo'], $data['estado'], $id_centro, $id);

        if (mysqli_stmt_execute($stmt)) {
            return ["success" => "Maquinaria actualizada"];
        }
        return ["error" => "No se pudo actualizar la maquinaria"];
    }

    public function delete($id)
    {
        $stmt = mysqli_prepare($this->conn, "DELETE FROM maquinaria WHERE id_maquinaria = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);

        if (mysqli_stmt_execute($stmt)) {
            return ["success" => "Maquinaria eliminada"];
        }
        return ["error" => "No se pudo eliminar la maquinaria"];
    }
}
