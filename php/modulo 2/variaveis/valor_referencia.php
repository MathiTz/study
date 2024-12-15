<div class="titulo">Valor vs Referência</div>

<?php
$variavel = 'valor inicial';
echo $variavel;
echo '<br>';

// Atribuição por Valor
$variavelValor = $variavel;
echo $variavelValor;
echo '<br>';
$variavelValor = 'novo valor';
echo "$variavelValor, $variavel";
echo '<br>';

// Atribuição por Referência, marcada pelo &
$variavelReferencia = &$variavel;
echo "$variavel, $variavelReferencia<br>";
$variavelReferencia = 'mesma referencia';
echo "$variavel, $variavelReferencia<br>";


