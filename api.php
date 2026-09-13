<?php

require_once 'config.php';
require_once __DIR__ . '/../auth.php';
require_once 'controladores/ContenedorController.php';
require_once 'controladores/IncidenciaController.php';
require_once 'controladores/RutaController.php';
require_once 'controladores/CentroAcopioController.php';
require_once 'controladores/MaquinariaController.php';

$contenedorCtrl = new ContenedorController($conn);
$incidenciaCtrl = new IncidenciaController($conn);
$rutaCtrl = new RutaController($conn);
$centroCtrl = new CentroAcopioController($conn);
$maquinariaCtrl = new MaquinariaController($conn);

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$basePath = '/sigeru/api-gestion';
$endpoint = str_replace($basePath, '', $uri);

switch ($method) {
    case 'GET':
        if ($endpoint === '/tipos-residuo') {
            $result = mysqli_query($conn, "SELECT * FROM tipo_residuo ORDER BY nombre");
            $tipos = [];
            while ($row = mysqli_fetch_assoc($result)) { $tipos[] = $row; }
            echo json_encode($tipos);
        } elseif ($endpoint === '/contenedores') {
            $contenedorCtrl->getAll();
        } elseif (preg_match('/^\/contenedores\/(\d+)$/', $endpoint, $matches)) {
            $contenedorCtrl->getById($matches[1]);
        } elseif ($endpoint === '/incidencias') {
            $incidenciaCtrl->getAll();
        } elseif (preg_match('/^\/incidencias\/(\d+)$/', $endpoint, $matches)) {
            $incidenciaCtrl->getById($matches[1]);
        } elseif ($endpoint === '/rutas') {
            $rutaCtrl->getAll();
        } elseif ($endpoint === '/centros-acopio') {
            $centroCtrl->getAll();
        } elseif (preg_match('/^\/centros-acopio\/(\d+)$/', $endpoint, $matches)) {
            $centroCtrl->getById($matches[1]);
        } elseif ($endpoint === '/maquinaria') {
            $maquinariaCtrl->getAll();
        } elseif (preg_match('/^\/maquinaria\/(\d+)$/', $endpoint, $matches)) {
            $maquinariaCtrl->getById($matches[1]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Endpoint no encontrado"]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);

        if ($endpoint === '/contenedores') {
            requiereRol(['administrador']);
            $contenedorCtrl->create($data);
        } elseif ($endpoint === '/incidencias') {
            requiereRol(['administrador', 'operario', 'conductor', 'peon']);
            $incidenciaCtrl->create($data);
        } elseif ($endpoint === '/rutas') {
            requiereRol(['administrador']);
            $rutaCtrl->create($data);
        } elseif ($endpoint === '/centros-acopio') {
            requiereRol(['administrador']);
            $centroCtrl->create($data);
        } elseif ($endpoint === '/maquinaria') {
            requiereRol(['administrador']);
            $maquinariaCtrl->create($data);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Endpoint no encontrado"]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);

        if (preg_match('/^\/contenedores\/(\d+)$/', $endpoint, $matches)) {
            requiereRol(['administrador']);
            $contenedorCtrl->update($matches[1], $data);
        } elseif (preg_match('/^\/incidencias\/(\d+)$/', $endpoint, $matches)) {
            requiereRol(['administrador', 'operario']);
            $incidenciaCtrl->updateEstado($matches[1], $data);
        } elseif (preg_match('/^\/rutas\/(\d+)$/', $endpoint, $matches)) {
            requiereRol(['administrador']);
            $rutaCtrl->update($matches[1], $data);
        } elseif (preg_match('/^\/centros-acopio\/(\d+)$/', $endpoint, $matches)) {
            requiereRol(['administrador']);
            $centroCtrl->update($matches[1], $data);
        } elseif (preg_match('/^\/maquinaria\/(\d+)$/', $endpoint, $matches)) {
            requiereRol(['administrador']);
            $maquinariaCtrl->update($matches[1], $data);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Endpoint no encontrado"]);
        }
        break;

    case 'DELETE':
        if (preg_match('/^\/contenedores\/(\d+)$/', $endpoint, $matches)) {
            requiereRol(['administrador']);
            $contenedorCtrl->delete($matches[1]);
        } elseif (preg_match('/^\/rutas\/(\d+)$/', $endpoint, $matches)) {
            requiereRol(['administrador']);
            $rutaCtrl->delete($matches[1]);
        } elseif (preg_match('/^\/centros-acopio\/(\d+)$/', $endpoint, $matches)) {
            requiereRol(['administrador']);
            $centroCtrl->delete($matches[1]);
        } elseif (preg_match('/^\/maquinaria\/(\d+)$/', $endpoint, $matches)) {
            requiereRol(['administrador']);
            $maquinariaCtrl->delete($matches[1]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Endpoint no encontrado"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
