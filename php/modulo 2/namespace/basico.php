<?php namespace context; ?>

<div class="titulo">Exemplo Básico</div>

<?php
echo __NAMESPACE__ . '<br>';
const constante1 = 123;
define('constante2', 234);
define('context\constante2', 1234);
define(__NAMESPACE__ . '\constante3', 345);
define('outro_context\constante4', 456);

echo constante1 . '<br>';
echo \constante2 . '<br>'; // 234
echo constante2 . '<br>'; // 1234
// echo context\constante3 . '<br>'; // não imprime porque ele vai utilizar context como subpasta -> context\context\constante3
echo constant(__NAMESPACE__ . '\constante3') . '<br>';
//echo constante4 . '<br>'; não imprime porque não é o mesmo contexto
echo \outro_context\constante4 . '<br>';

function soma($a, $b) {
  return $a + $b;
}

echo \context\soma(1,2) . '<br>';
echo soma(1,2) . '<br>';
//echo \soma(1,2) . '<br>'; não acessa porque não está definida

function strpos($str, $text) {
  echo "Buscando o texto '{$text}' em '{$str}'<br>";
  return 1;
}

echo strpos('Texto genérico para busca', 'busca') . '<br>';
echo \strpos('Texto genérico para busca', 'busca') . '<br>';
