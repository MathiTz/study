<div class="titulo">Função & Escopo</div>

<?php

function imprimirMensagens() {
  echo "Olá! ";
  echo "Até a próxima!<br>";
}

imprimirMensagens();

$variable = 1;
function trocaValor() {
  $variable = 2;
  echo "Durante a função: $variable <br>";
}

echo "Antes: $variable <br>";

trocaValor();

echo "Depois: $variable <br>";

function trocaValorDeVerdade() {
  global $variable; // palavra reservada para mudança de variável em escopo acima de função

  $variable = 3;

  echo "Durante a função: $variable <br>";
}

echo "Antes: $variable <br>";

trocaValorDeVerdade();

echo "Depois: $variable <br>";

var_dump(trocaValorDeVerdade()); // retorna null ~ void
