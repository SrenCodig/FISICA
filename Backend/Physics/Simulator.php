<?php
declare(strict_types=1);
require_once __DIR__ . '/../Config/constants.php';

function simulate(string $topic, array $v): array
{
    $g = GRAVITY;
    switch ($topic) {
        case 'freefall':
            $h = $v['height']; $t = sqrt(2 * $h / $g); $speed = $g * $t;
            return ['metrics' => [['label'=>'Tiempo de caída','value'=>round($t,2),'unit'=>'s'],['label'=>'Velocidad final','value'=>round($speed,2),'unit'=>'m/s'],['label'=>'Altura inicial','value'=>round($h,2),'unit'=>'m']], 'series'=>['time'=>$t,'height'=>$h,'vx'=>0,'vy'=>$speed,'range'=>0,'curve'=>'fall']];
        case 'mru':
            $speed = $v['speed']; $t = $v['time']; $distance = $speed * $t;
            return ['metrics'=>[['label'=>'Distancia','value'=>round($distance,2),'unit'=>'m'],['label'=>'Velocidad constante','value'=>round($speed,2),'unit'=>'m/s'],['label'=>'Tiempo','value'=>round($t,2),'unit'=>'s']], 'series'=>['time'=>$t,'height'=>0,'vx'=>$speed,'vy'=>0,'range'=>$distance,'curve'=>'line']];
        case 'forces':
            $mass=$v['mass']; $a=0.0; $x=0.0; $y=0.0; $vectors=[];
            foreach ($v['forces'] as $f) { $rad=deg2rad($f['angle']); $fx=$f['magnitude']*cos($rad); $fy=$f['magnitude']*sin($rad); $x+=$fx; $y+=$fy; $vectors[]=['magnitude'=>$f['magnitude'],'angle'=>$f['angle'],'x'=>round($fx,2),'y'=>round($fy,2)]; }
            $resultant=hypot($x,$y); $angle=rad2deg(atan2($y,$x)); $a=$resultant/$mass;
            return ['metrics'=>[['label'=>'Fuerza resultante','value'=>round($resultant,2),'unit'=>'N'],['label'=>'Dirección','value'=>round($angle,1),'unit'=>'°'],['label'=>'Aceleración','value'=>round($a,2),'unit'=>'m/s²']], 'series'=>['time'=>2,'height'=>0,'vx'=>$resultant,'vy'=>0,'range'=>$resultant,'curve'=>'vectors','vectors'=>$vectors,'mass'=>$mass]];
        case 'projectile':
            $speed=$v['speed']; $rad=deg2rad($v['angle']); $vx=$speed*cos($rad); $vy=$speed*sin($rad); $h=$v['height']; $t=($vy+sqrt($vy*$vy+2*$g*$h))/$g; $range=$vx*$t; $maxH=$h+($vy*$vy)/(2*$g);
            return ['metrics'=>[['label'=>'Alcance','value'=>round($range,2),'unit'=>'m'],['label'=>'Altura máxima','value'=>round($maxH,2),'unit'=>'m'],['label'=>'Tiempo de vuelo','value'=>round($t,2),'unit'=>'s']], 'series'=>['time'=>$t,'height'=>$maxH,'vx'=>$vx,'vy'=>$vy,'range'=>$range,'curve'=>'projectile']];
        case 'vertical':
            $u=$v['speed']; $t=$u/$g; $h=($u*$u)/(2*$g);
            return ['metrics'=>[['label'=>'Altura máxima','value'=>round($h,2),'unit'=>'m'],['label'=>'Tiempo de subida','value'=>round($t,2),'unit'=>'s'],['label'=>'Velocidad inicial','value'=>round($u,2),'unit'=>'m/s']], 'series'=>['time'=>$t,'height'=>$h,'vx'=>0,'vy'=>$u,'range'=>0,'curve'=>'vertical']];
        default: throw new InvalidArgumentException('Tema de simulación desconocido.');
    }
}
