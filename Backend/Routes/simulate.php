<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['error'=>'Usa el método POST.']); return; }
require_once __DIR__ . '/../Physics/Simulator.php';
try {
    $body = json_decode(file_get_contents('php://input') ?: '', true, 512, JSON_THROW_ON_ERROR);
    if (!is_array($body) || !isset($body['topic'], $body['values']) || !is_array($body['values'])) throw new InvalidArgumentException('Completa los datos de la simulación.');
    $v = $body['values'];
    $required = match ($body['topic']) {
        'freefall' => ['height','initialSpeed','gravity'],
        'mru' => ['position','speed','time'],
        'forces' => ['mass','gravity','forces'],
        'projectile' => ['speed','angle','height','gravity'],
        'vertical' => ['speed','height','gravity'],
        default => throw new InvalidArgumentException('Tema de simulación desconocido.')
    };
    foreach ($required as $key) if (!array_key_exists($key, $v)) throw new InvalidArgumentException('Falta un valor necesario para este ensayo.');
    foreach ($v as $key => $value) {
        if ($key === 'forces') {
            if (!is_array($value)) throw new InvalidArgumentException('Revisa la lista de fuerzas.');
            foreach ($value as $force) {
                if (!is_array($force) || !is_numeric($force['magnitude'] ?? null) || !is_numeric($force['angle'] ?? null) || !is_finite((float)$force['magnitude']) || !is_finite((float)$force['angle']) || $force['magnitude'] < 0) throw new InvalidArgumentException('Cada fuerza necesita una magnitud no negativa y un ángulo numérico.');
            }
            continue;
        }
        if (!is_numeric($value) || !is_finite((float)$value)) throw new InvalidArgumentException('Usa valores numéricos finitos para el ensayo.');
    }
    foreach (['height'] as $key) if (isset($v[$key]) && $v[$key] < 0) throw new InvalidArgumentException('La altura no puede ser menor que cero.');
    foreach (['gravity'] as $key) if (isset($v[$key]) && $v[$key] <= 0) throw new InvalidArgumentException('La gravedad debe ser mayor que cero.');
    if (isset($v['time']) && $v['time'] < 0) throw new InvalidArgumentException('El tiempo no puede ser menor que cero.');
    if (isset($v['speed']) && in_array($body['topic'], ['projectile','vertical'], true) && $v['speed'] < 0) throw new InvalidArgumentException('La rapidez inicial no puede ser menor que cero.');
    if (isset($v['initialSpeed']) && $body['topic'] === 'freefall' && $v['initialSpeed'] < 0) throw new InvalidArgumentException('La velocidad inicial hacia abajo no puede ser menor que cero.');
    if (isset($v['mass']) && $v['mass'] <= 0) throw new InvalidArgumentException('La masa debe ser mayor que cero.');
    echo json_encode(simulate((string)$body['topic'], $v), JSON_THROW_ON_ERROR);
} catch (JsonException|InvalidArgumentException $e) {
    http_response_code(422);
    echo json_encode(['error'=>$e->getMessage()]);
}
