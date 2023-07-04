<div class="titulo">Arrays Constantes</div>

<?php
const FRUTAS = array('laranja', 'abacaxi');

//FRUTAS[0] = 'banana'; -> gives error
//FRUTAS[] = 'banana'; -> giver error
print_r(FRUTAS);

const CARROS = ["fiat" => "uno", "ford" => "fiesta"];
echo '<br>' . CARROS["Fiat"];

define('CIDADES', array('Belo Horizonte', 'Recife'));
echo '<br>' . CIDADES[1];
