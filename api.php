<?php

require_once 'config.php';
require_once 'controladores/ContenedorController.php';
require_once 'controladores/IncidenciaController.php';
require_once 'controladores/RutaController.php';
require_once 'controladores/CentroAcopioController.php';
require_once 'controladores/MaquinariaController.php';

$contenedorCtrl   = new ContenedorController($conn);
$incidenciaCtrl   = new IncidenciaController($conn);
$rutaCtrl         = new RutaController($conn);
$centroCtrl       = new CentroAcopioController($conn);
$maquinariaCtrl   = new MaquinariaController($conn);

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$basePath = '/sigeru/api-gestion';
$endpoint = str_replace($basePath, '', $uri);

switch ($method) {
    case 'GET':
        if ($endpoint === '/contenedores') {
            $contenedorCtrl->getAll();
        } elseif (preg_match('/^\/contenedores\/(\d+)$/', $endpoint, $m)) {
            $contenedorCtrl->getById($m[1]);
        } elseif ($endpoint === '/incidencias') {
            $incidenciaCtrl->getAll();
        } elseif (preg_match('/^\/incidencias\/(\d+)$/', $endpoint, $m)) {
            $incidenciaCtrl->getById($m[1]);
        } elseif ($endpoint === '/rutas') {
            $rutaCtrl->getAll();
        } elseif ($endpoint === '/centros-acopio') {
            $centroCtrl->getAll();
        } elseif (preg_match('/^\/centros-acopio\/(\d+)$/', $endpoint, $m)) {
            $centroCtrl->getById($m[1]);
        } elseif ($endpoint === '/maquinaria') {
            $maquinariaCtrl->getAll();
        } elseif (preg_match('/^\/maquinaria\/(\d+)$/', $endpoint, $m)) {
            $maquinariaCtrl->getById($m[1]);
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
        } elseif ($endpoint === '/centros-acopio') {
            $centroCtrl->create($data);
        } elseif ($endpoint === '/maquinaria') {
            $maquinariaCtrl->create($data);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Endpoint no encontrado"]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        if (preg_match('/^\/contenedores\/(\d+)$/', $endpoint, $m)) {
            $contenedorCtrl->update($m[1], $data);
        } elseif (preg_match('/^\/incidencias\/(\d+)$/', $endpoint, $m)) {
            $incidenciaCtrl->updateEstado($m[1], $data);
        } elseif (preg_match('/^\/rutas\/(\d+)$/', $endpoint, $m)) {
            $rutaCtrl->update($m[1], $data);
        } elseif (preg_match('/^\/centros-acopio\/(\d+)$/', $endpoint, $m)) {
            $centroCtrl->update($m[1], $data);
        } elseif (preg_match('/^\/maquinaria\/(\d+)$/', $endpoint, $m)) {
            $maquinariaCtrl->update($m[1], $data);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Endpoint no encontrado"]);
        }
        break;

    case 'DELETE':
        if (preg_match('/^\/contenedores\/(\d+)$/', $endpoint, $m)) {
            $contenedorCtrl->delete($m[1]);
        } elseif (preg_match('/^\/rutas\/(\d+)$/', $endpoint, $m)) {
            $rutaCtrl->delete($m[1]);
        } elseif (preg_match('/^\/centros-acopio\/(\d+)$/', $endpoint, $m)) {
            $centroCtrl->delete($m[1]);
        } elseif (preg_match('/^\/maquinaria\/(\d+)$/', $endpoint, $m)) {
            $maquinariaCtrl->delete($m[1]);
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
