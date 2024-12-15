<div class="titulo">Desafio Equação</div>

<?php
$numeradorA = (6 * (3 + 2)) ** 2;
$numeradorB = (1 - 5) * (2 - 7);
$denominadorA = 3 * 2;
$denominadorB = 2;

$denominadorC = 10 ** 3;

$resultadoA = $numeradorA / $denominadorA;
$resultadoB = ($numeradorB / $denominadorB) ** 2;

$equação = (($resultadoA - $resultadoB) ** 3 / $denominadorC);

echo $equação;
