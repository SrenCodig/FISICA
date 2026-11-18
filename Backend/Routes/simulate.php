<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['error'=>'Usa el método POST.']); return; }
require_once __DIR__ . '/../Physics/Simulator.php';
try {
    $body=json_decode(file_get_contents('php://input') ?: '', true, 512, JSON_THROW_ON_ERROR);
    if (!is_array($body) || !isset($body['topic'], $body['values']) || !is_array($body['values'])) throw new InvalidArgumentException('Completa los datos de la simulación.');
    $limits=['height'=>[0,10000],'speed'=>[0,10000],'time'=>[0.1,10000],'angle'=>[0,90],'mass'=>[0.01,10000]];
    foreach ($body['values'] as $key=>$value) {
        if ($key === 'forces' && is_array($value)) { foreach ($value as $force) { if (!is_array($force) || !is_numeric($force['magnitude'] ?? null) || !is_numeric($force['angle'] ?? null) || $force['magnitude']<0 || $force['magnitude']>10000 || $force['angle']<0 || $force['angle']>360) throw new InvalidArgumentException('Revisa la magnitud (0–10 000 N) y el ángulo (0–360°) de cada fuerza.'); } continue; }
        if (isset($limits[$key]) && (!is_numeric($value) || $value<$limits[$key][0] || $value>$limits[$key][1])) throw new InvalidArgumentException('Revisa el valor de ' . $key . '. Debe estar dentro del rango permitido.');
    }
    echo json_encode(simulate((string)$body['topic'], $body['values']), JSON_THROW_ON_ERROR);
} catch (JsonException|InvalidArgumentException $e) { http_response_code(422); echo json_encode(['error'=>$e->getMessage()]); }
