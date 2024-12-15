<div class="titulo">Desafio PI</div>

<?php
echo pi();

$pi = 3.14;

var_dump(pi());
var_dump($pi);

if ($pi === pi()) {
  echo "<br>Iguais!";
} else {
  echo "<br>Diferentes!";
}

// Operador relacional

$piErrado = 2.8;

// Resposta
echo "<br>" . ($pi - pi());
echo "<br>" . ($pi - $piErrado) . "<br>";

if ($pi - pi() <= 0.01) {
  echo "Praticamente iguais!<br>";
} else {
  echo "Não tolerável!<br>";
}

if ($pi - $piErrado <= 0.01) {
  echo "Praticamente iguais!<br>";
} else {
  echo "Não tolerável!<br>";
}
