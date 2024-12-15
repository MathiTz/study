<div class="titulo">Classe Data</div>

<?php

class Data {
  public $dia = 1;
  public $mes = 1;
  public $ano = 1970;

  public function apresentar() {
    return "{$this->dia}/{$this->mes}/{$this->ano}<br>";
  }
}

$aniversario = new Data;
$casamento = new Data();

$aniversario->dia = 15;
$aniversario->mes = 8;
$aniversario->ano = 2014;
echo $aniversario->apresentar();

$casamento->dia = 12;
$casamento->mes = 06;
$casamento->ano = 2023;
echo $casamento->apresentar();
