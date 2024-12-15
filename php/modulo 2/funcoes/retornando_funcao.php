<div class="titulo">Retornando Função</div>

<?php

function soma($a) {
  return function($b) use ($a) {
    return $a + $b;
  };
}


echo soma(3)(3) . "<br>";

$doisMais = soma(2);
echo '<br>', $doisMais(10);
echo '<br>', $doisMais(18);
