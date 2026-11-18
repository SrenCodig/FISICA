<?php
declare(strict_types=1);
require_once __DIR__ . '/../Config/constants.php';

function row(string $name, float|int $value, string $unit): array
{
    return ['name' => $name, 'value' => $value, 'unit' => $unit];
}

function simulate(string $topic, array $v): array
{
    $g = (float)($v['gravity'] ?? GRAVITY);
    $inputs = [];
    $outputs = [];
    $series = ['curve' => $topic];
    switch ($topic) {
        case 'freefall':
            $h = (float)$v['height']; $u = (float)$v['initialSpeed'];
            $t = ($u + sqrt($u * $u + 2 * $g * $h)) / $g;
            $final = $u + $g * $t;
            $inputs = [row('Altura inicial', $h, 'm'), row('Velocidad inicial hacia abajo', $u, 'm/s'), row('Gravedad', $g, 'm/s²')];
            $outputs = [row('Tiempo hasta el suelo', $t, 's'), row('Velocidad al impactar', $final, 'm/s'), row('Aceleración', $g, 'm/s²'), row('Distancia recorrida', $h, 'm')];
            $series += ['time'=>$t,'height'=>$h,'initialSpeed'=>$u,'gravity'=>$g];
            break;
        case 'mru':
            $x0 = (float)$v['position']; $speed = (float)$v['speed']; $t = (float)$v['time']; $x = $x0 + $speed * $t;
            $inputs = [row('Posición inicial', $x0, 'm'), row('Velocidad', $speed, 'm/s'), row('Tiempo', $t, 's')];
            $outputs = [row('Posición final', $x, 'm'), row('Desplazamiento', $speed * $t, 'm'), row('Rapidez', abs($speed), 'm/s'), row('Aceleración', 0, 'm/s²')];
            $series += ['time'=>$t,'position'=>$x0,'speed'=>$speed,'distance'=>$speed*$t];
            break;
        case 'forces':
            $mass = (float)$v['mass']; $fx = 0.0; $fy = 0.0; $vectors = [];
            foreach ($v['forces'] as $i => $force) {
                $magnitude = (float)$force['magnitude']; $angle = (float)$force['angle'];
                $x = $magnitude * cos(deg2rad($angle)); $y = $magnitude * sin(deg2rad($angle)); $fx += $x; $fy += $y;
                $inputs[] = row('Fuerza ' . ($i + 1) . ' · magnitud', $magnitude, 'N');
                $inputs[] = row('Fuerza ' . ($i + 1) . ' · ángulo', $angle, '°');
                $vectors[] = ['magnitude'=>$magnitude,'angle'=>$angle,'x'=>$x,'y'=>$y];
            }
            $resultant = hypot($fx, $fy);
            $inputs[] = row('Masa del anillo', $mass, 'kg');
            $outputs = [row('Componente resultante en X', $fx, 'N'), row('Componente resultante en Y', $fy, 'N'), row('Fuerza resultante', $resultant, 'N'), row('Dirección resultante', rad2deg(atan2($fy, $fx)), '°'), row('Aceleración del anillo', $resultant / $mass, 'm/s²')];
            $series += ['vectors'=>$vectors,'mass'=>$mass,'resultantX'=>$fx,'resultantY'=>$fy];
            break;
        case 'projectile':
            $speed = (float)$v['speed']; $angle = (float)$v['angle']; $h = (float)$v['height'];
            $rad = deg2rad($angle); $vx = $speed * cos($rad); $vy = $speed * sin($rad);
            $t = max(0.0, ($vy + sqrt($vy * $vy + 2 * $g * $h)) / $g);
            $range = $vx * $t; $apex = $h + max(0.0, $vy) ** 2 / (2 * $g);
            $inputs = [row('Velocidad inicial', $speed, 'm/s'), row('Ángulo del cañón', $angle, '°'), row('Altura del cañón', $h, 'm'), row('Gravedad', $g, 'm/s²')];
            $outputs = [row('Componente horizontal vₓ', $vx, 'm/s'), row('Componente vertical vᵧ', $vy, 'm/s'), row('Alcance horizontal', $range, 'm'), row('Altura máxima', $apex, 'm'), row('Tiempo de vuelo', $t, 's')];
            $series += ['time'=>$t,'height'=>$apex,'initialHeight'=>$h,'speed'=>$speed,'angle'=>$angle,'vx'=>$vx,'vy'=>$vy,'gravity'=>$g,'range'=>$range];
            break;
        case 'vertical':
            $speed = (float)$v['speed']; $h = (float)$v['height'];
            $tApex = max(0.0, $speed / $g); $apex = $h + max(0.0, $speed) ** 2 / (2 * $g);
            $tFlight = max(0.0, ($speed + sqrt($speed * $speed + 2 * $g * $h)) / $g);
            $inputs = [row('Velocidad inicial', $speed, 'm/s'), row('Altura de lanzamiento', $h, 'm'), row('Gravedad', $g, 'm/s²')];
            $outputs = [row('Altura máxima', $apex, 'm'), row('Tiempo hasta altura máxima', $tApex, 's'), row('Tiempo hasta el suelo', $tFlight, 's'), row('Velocidad al impactar', $speed - $g * $tFlight, 'm/s')];
            $series += ['time'=>$tFlight,'apexTime'=>$tApex,'height'=>$apex,'initialHeight'=>$h,'initialSpeed'=>$speed,'gravity'=>$g];
            break;
        default:
            throw new InvalidArgumentException('Tema de simulación desconocido.');
    }
    return ['inputs'=>$inputs,'outputs'=>$outputs,'series'=>$series];
}
