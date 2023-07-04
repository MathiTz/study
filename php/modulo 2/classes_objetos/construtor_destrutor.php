<div class="titulo">Construtor e Destruturo</div>

<?php

class Pessoa {
  public $nome;
  public $idade;

  public function __construct($novoNome, $idade = 18)
  {
    echo 'Construtor invocado! <br>';
    $this->nome = $novoNome;
    $this->idade = $idade;
  }

  public function __destruct()
  {
    echo 'E morreu!';
  }

  public function apresentar() {
    return "{$this->nome}, {$this->idade} anos <br>";
  }
}

$pessoa = new Pessoa('John', 40);
echo $pessoa->apresentar();
unset($pessoa);

$pessoaB = new Pessoa('Kimberly', 40);
echo $pessoaB->apresentar();
$pessoaB = null;
