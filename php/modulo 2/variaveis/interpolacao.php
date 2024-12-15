<div class="titulo">Interpolação</div>

<?php
$numero = 10;
echo $numero;
echo '<br> $numero';
echo "<br> $numero + 1";

$text = "A sua nota é : $numero";
echo "<br>$text";

$objeto = 'caneta';
echo "<br>Eu tenho 5 $objetos";
echo "<br>Eu tenho 5 {$objeto}s";
echo "<br>Eu tinha 5 { $objeto}s mas perdi 3 {$objeto }s";
echo "<br>";
//echo "{$numero + 1}"; // Não consegue ser compilado
