<div class="titulo">Herança</div>

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

class Usuario extends Pessoa {
  public $login;

  function __construct($nome, $idade, $login)
  {
    parent::__construct($nome, $idade);
    $this->login = $login;
    echo 'Usuário criado! <br>';
  }

  public function __destruct()
  {
    echo "Usuário diz: Tchau!!<br>";
    parent::__destruct();
  }

  public function apresentar()
  {
    echo "@{$this->login}: ";
    parent::apresentar();
  }
}

$usuario = new Usuario('Gustavo Mendonça', 21, 'gust_mend');
$usuario->apresentar();

unset($usuario);
