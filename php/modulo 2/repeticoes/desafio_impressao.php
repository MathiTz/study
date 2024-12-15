<div class="titulo">Desafio Impressão</div>

<!--
  Enunciado:
  - Imprima apenas os valores do array que contém indice par
-->

<?php

$array = [
  "AAA",
  "BBB",
  "CCC",
  "DDD",
  "EEE",
  "FFF"
];

for ($i = 0; $i < count($array); $i++) {
  if ($i % 2 === 1)
  {
    continue;
  }
  echo "$array[$i] <br>";
}

echo "<hr>";

foreach ($array as $index => $value) {
  if ($index % 2 === 1) continue;

  echo "{$value} <br>";
}
