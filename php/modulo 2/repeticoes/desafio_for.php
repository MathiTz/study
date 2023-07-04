<div class="titulo">Desafio For</div>

<!--
Usar o for para gerar o retorno
#
##
###
####
#####
1) Pode usar incremento $i++
2) Não pode usar incremento $i++
-->

<?php

$array = ['#', '##', '###', '####', '#####'];
$impressao = '';
for ($i = 0; $i < count($array); $i++) {
  echo "{$array[$i]} <br>";
}

echo "<hr>";

for ($i = 1; $i <= 5; $i++) {
  $impressao .= '#';
  echo "{$impressao} <br>";
}

echo "<hr>";

for ($impressao2 = '#'; $impressao2 !== '######'; $impressao2 .= '#') {
  echo "{$impressao2} <br>";
}


