<?php

class Pessoa {
  public $nome;
  public $idade;

  public function __construct($nome, $idade)
  {
    $this->nome = $nome;
    $this->idade = $idade;
    echo 'Pessoa Criada! <br>';
  }

  public function __destruct()
  {
    // TODO: Implement __destruct() method.
    echo 'Pessoa diz: Tchau!!';
  }

  public function apresentar() {
    echo "{$this->nome}, {$this->idade} anos<br>";
  }
}
