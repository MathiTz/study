<div class="titulo">Métodos Mágicos</div>

<?php

class Pessoa {
  public $nome;
  public $idade;

  public function __construct($nome, $idade)
  {
    echo 'Construtor invocado!<br>';
    $this->nome = $nome;
    $this->idade = $idade;
  }

  public function __destruct()
  {
    echo '<br>E morreu!';
  }

  public function __toString()
  {
    return "{$this->nome} tem {$this->idade} anos.";
  }

  public function apresentar() {
    echo $this . "<br>";
  }

  public function __get($atrib)
  {
    echo "Lendo atributo não declarado: {$atrib}<br>";
  }

  public function __set($atrib, $value)
  {
    echo "Alterando atributo não declarado: {$atrib}/{$value}<br>";
  }

  public function __call($name, $arguments)
  {
    echo "Tentando executar método ${name}.";
    echo ", com os parametros: ";
    print_r($arguments);
  }
}

$pessoa = new Pessoa('Ricardo', 40);
//$pessoa->apresentar();
//echo $pessoa, '<br>';
//$pessoa->nome = 'Reinaldo';
//$pessoa->apresentar();

//$pessoa->nomeCompleto;
//
//$pessoa->nomeCompleto = 'Muito Legal!!!!';

$pessoa->exec(1, 'test', true, [1,2,3]);
$pessoa = null;
