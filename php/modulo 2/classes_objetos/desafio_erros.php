<div class="titulo">Desafio Erros</div>

<?php

interface Template {
  function metodo1();
  public function metodo2($parametro);
}

abstract class ClassAbstrata implements Template
{
  public function metodo3() {
    echo "Estou funcionando";
  }
}

class Classe extends ClassAbstrata
{
  function __construct($parametro) {}

  function metodo1()
  {
    // TODO: Implement metodo1() method.
  }

  public function metodo2($parametro)
  {
    // TODO: Implement metodo2() method.
  }
}

$exemplo = new Classe("algo");
$exemplo->metodo3();
