<?php
declare(strict_types=1);
require_once __DIR__ . '/../Config/constants.php';

function row(string $name, float|int $value, string $unit, string $formula = '', string $dimension = 'other'): array
{
    return ['name' => $name, 'value' => $value, 'unit' => $unit, 'formula' => $formula, 'dimension' => $dimension];
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
            $t = (sqrt($u * $u + 2 * $g * $h) - $u) / $g;
            $final = $u + $g * $t;
            $inputs = [row('Altura inicial', $h, 'm', '', 'length'), row('Velocidad inicial hacia abajo', $u, 'm/s', '', 'speed'), row('Gravedad', $g, 'm/s²', '', 'acceleration')];
            $outputs = [row('Tiempo hasta el suelo', $t, 's', 't = (√(v₀² + 2gh) − v₀) / g', 'time'), row('Velocidad al impactar', $final, 'm/s', 'v = v₀ + gt', 'speed'), row('Aceleración', $g, 'm/s²', 'a = g', 'acceleration'), row('Distancia recorrida', $h, 'm', 'Δy = h₀', 'length')];
            $series += ['time'=>$t,'height'=>$h,'initialSpeed'=>$u,'gravity'=>$g];
            break;
        case 'mru':
            $x0 = (float)$v['position']; $speed = (float)$v['speed']; $t = (float)$v['time']; $x = $x0 + $speed * $t;
            $inputs = [row('Posición inicial', $x0, 'm', '', 'length'), row('Velocidad', $speed, 'm/s', '', 'speed'), row('Tiempo', $t, 's', '', 'time')];
            $outputs = [row('Posición final', $x, 'm', 'x = x₀ + vt', 'length'), row('Desplazamiento', $speed * $t, 'm', 'Δx = vt', 'length'), row('Rapidez', abs($speed), 'm/s', '|v|', 'speed'), row('Aceleración', 0, 'm/s²', 'a = 0 (velocidad constante)', 'acceleration')];
            $series += ['time'=>$t,'position'=>$x0,'speed'=>$speed,'distance'=>$speed*$t];
            break;
        case 'forces':
            $mass = (float)$v['mass']; $weight = $mass * $g; $fx = 0.0; $fy = 0.0; $vectors = [];
            foreach ($v['forces'] as $i => $force) {
                $magnitude = (float)$force['magnitude']; $angle = (float)$force['angle'];
                $x = $magnitude * cos(deg2rad($angle)); $y = $magnitude * sin(deg2rad($angle)); $fx += $x; $fy += $y;
                $inputs[] = row('Cuerda ' . ($i + 1) . ' · tensión', $magnitude, 'N');
                $inputs[] = row('Cuerda ' . ($i + 1) . ' · ángulo', $angle, '°');
                $vectors[] = ['magnitude'=>$magnitude,'angle'=>$angle,'x'=>$x,'y'=>$y];
            }
            if (abs($fx) < 1e-12) $fx = 0.0;
            if (abs($fy) < 1e-12) $fy = 0.0;
            $resultant = hypot($fx, $fy);
            $inputs[] = row('Masa del anillo', $mass, 'kg');
            $inputs[] = row('Gravedad', $g, 'm/s²', '', 'acceleration');
            $outputs = [row('Peso del anillo', $weight, 'N', 'W = m · g'), row('Fuerza normal de la mesa', $weight, 'N', 'N = W = m · g'), row('Componente resultante en X', $fx, 'N', 'ΣFₓ = Σ(Fᵢ cos θᵢ)'), row('Componente resultante en Y', $fy, 'N', 'ΣFᵧ = Σ(Fᵢ sin θᵢ)'), row('Fuerza resultante de las cuerdas', $resultant, 'N', '|ΣF| = √(ΣFₓ² + ΣFᵧ²)'), row('Dirección resultante', rad2deg(atan2($fy, $fx)), '°', 'θ = atan2(ΣFᵧ, ΣFₓ)'), row('Aceleración del anillo', $resultant / $mass, 'm/s²', 'a = |ΣF| / m', 'acceleration')];
            $series += ['vectors'=>$vectors,'mass'=>$mass,'weight'=>$weight,'gravity'=>$g,'resultantX'=>$fx,'resultantY'=>$fy];
            break;
        case 'projectile':
            $speed = (float)$v['speed']; $angle = (float)$v['angle']; $h = (float)$v['height'];
            $rad = deg2rad($angle); $vx = $speed * cos($rad); $vy = $speed * sin($rad);
            $t = max(0.0, ($vy + sqrt($vy * $vy + 2 * $g * $h)) / $g);
            $range = $vx * $t; $apex = $h + max(0.0, $vy) ** 2 / (2 * $g);
            $inputs = [row('Velocidad inicial', $speed, 'm/s', '', 'speed'), row('Ángulo del cañón', $angle, '°'), row('Altura del cañón', $h, 'm', '', 'length'), row('Gravedad', $g, 'm/s²', '', 'acceleration')];
            $outputs = [row('Componente horizontal vₓ', $vx, 'm/s', 'vₓ = v₀ cos θ', 'speed'), row('Componente vertical vᵧ', $vy, 'm/s', 'vᵧ = v₀ sin θ', 'speed'), row('Alcance horizontal', $range, 'm', 'x = vₓ t', 'length'), row('Altura máxima', $apex, 'm', 'hₘₐₓ = h₀ + vᵧ² / (2g)', 'length'), row('Tiempo de vuelo', $t, 's', 't = (vᵧ + √(vᵧ² + 2gh₀)) / g', 'time')];
            $series += ['time'=>$t,'height'=>$apex,'initialHeight'=>$h,'speed'=>$speed,'angle'=>$angle,'vx'=>$vx,'vy'=>$vy,'gravity'=>$g,'range'=>$range];
            break;
        case 'vertical':
            $speed = (float)$v['speed']; $h = (float)$v['height'];
            $tApex = max(0.0, $speed / $g); $apex = $h + max(0.0, $speed) ** 2 / (2 * $g);
            $tFlight = max(0.0, ($speed + sqrt($speed * $speed + 2 * $g * $h)) / $g);
            $inputs = [row('Velocidad inicial', $speed, 'm/s', '', 'speed'), row('Altura de lanzamiento', $h, 'm', '', 'length'), row('Gravedad', $g, 'm/s²', '', 'acceleration')];
            $outputs = [row('Altura máxima', $apex, 'm', 'hₘₐₓ = h₀ + v₀² / (2g)', 'length'), row('Tiempo hasta altura máxima', $tApex, 's', 'tₘₐₓ = v₀ / g', 'time'), row('Tiempo hasta el suelo', $tFlight, 's', 't = (v₀ + √(v₀² + 2gh₀)) / g', 'time'), row('Velocidad al impactar', $speed - $g * $tFlight, 'm/s', 'v = v₀ − gt', 'speed')];
            $series += ['time'=>$tFlight,'apexTime'=>$tApex,'height'=>$apex,'initialHeight'=>$h,'initialSpeed'=>$speed,'gravity'=>$g];
            break;
        default:
            throw new InvalidArgumentException('Tema de simulación desconocido.');
    }
    return ['inputs'=>$inputs,'outputs'=>$outputs,'series'=>$series];
}
