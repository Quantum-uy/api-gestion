<?php

require_once 'config.php';
require_once 'controladores/ContenedorController.php';
require_once 'controladores/IncidenciaController.php';
require_once 'controladores/RutaController.php';

$contenedorCtrl = new ContenedorController($conn);
$incidenciaCtrl = new IncidenciaController($conn);
$rutaCtrl = new RutaController($conn);

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$basePath = '/sigeru/api-gestion';
$endpoint = str_replace($basePath, '', $uri);

switch ($method) {
    case 'GET':
        if ($endpoint === '/contenedores') {
            $contenedorCtrl->getAll();
        } elseif (preg_match('/^\/contenedores\/(\d+)$/', $endpoint, $matches)) {
            $contenedorCtrl->getById($matches[1]);
        } elseif ($endpoint === '/incidencias') {
            $incidenciaCtrl->getAll();
        } elseif (preg_match('/^\/incidencias\/(\d+)$/', $endpoint, $matches)) {
            $incidenciaCtrl->getById($matches[1]);
        } elseif ($endpoint === '/rutas') {
            $rutaCtrl->getAll();
        } elseif (preg_match('/^\/rutas\/(\d+)$/', $endpoint, $matches)) {
            // future: getById
            http_response_code(404);
            echo json_encode(['error' => 'No implementado']);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Endpoint no encontrado"]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);

        if ($endpoint === '/contenedores') {
            $contenedorCtrl->create($data);
        } elseif ($endpoint === '/incidencias') {
            $incidenciaCtrl->create($data);
        } elseif ($endpoint === '/rutas') {
            $rutaCtrl->create($data);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Endpoint no encontrado"]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);

        if (preg_match('/^\/contenedores\/(\d+)$/', $endpoint, $matches)) {
            $contenedorCtrl->update($matches[1], $data);
        } elseif (preg_match('/^\/incidencias\/(\d+)$/', $endpoint, $matches)) {
            $incidenciaCtrl->updateEstado($matches[1], $data);
        } elseif (preg_match('/^\/rutas\/(\d+)$/', $endpoint, $matches)) {
            $rutaCtrl->update($matches[1], $data);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Endpoint no encontrado"]);
        }
        break;

    case 'DELETE':
        if (preg_match('/^\/contenedores\/(\d+)$/', $endpoint, $matches)) {
            $contenedorCtrl->delete($matches[1]);
        } elseif (preg_match('/^\/rutas\/(\d+)$/', $endpoint, $matches)) {
            $rutaCtrl->delete($matches[1]);
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
