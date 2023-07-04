<div class="titulo">Desafio Recursão</div>

<?php
/**
 * $array = [1, 2, [3, 4, 5], 6, [7, [8, 9]], 10];
 * > 1
 * > 2
 * >> 3
 * >> 4
 * >> 5
 * > 6
 * >> 7
 * >>> 8
 * >>> 9
 * > 10
 */

$array = [1, 2, [3, 4, 5], 6, [7, [8, 9]], 10];
function buscaRecursiva($array, $depth = '>') {
  foreach ($array as $item) {
    if (is_array($item)) {
      buscaRecursiva($item, $depth . $depth[0]);
    } else {
      echo "{$depth}{$item}<br>";
    }
  }
}

buscaRecursiva($array);
