<div class="titulo">Array</div>

<?php
$lista = array(1, 2, 3.4, "texto");
echo $lista . "<br>";
var_dump($lista);
echo "<br>";
print_r($lista);

$lista[0] = 1234;

echo '<br>' . $lista[0];
echo '<br>' . $lista[1];
echo '<br>' . $lista[2];
echo '<br>' . $lista[3];
//echo '<br>' . $lista[3000]; -> retorna valor null

$texto = 'Esse é um texto de teste';
echo '<br>' . $texto[0];
echo '<br>' . $texto[3];
echo '<br>' . $texto[11]; // cai no espaço pois acento conta como dois espaços
echo '<br>' . mb_substr($texto, 10, 1); // considera o encoding usado
