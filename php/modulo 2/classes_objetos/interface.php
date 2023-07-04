<div class="titulo">Interface</div>

<?php

interface Animal
{
  function respirar();
}

interface Mamifero
{
  function mamar();
}

interface Canino extends Animal
{
  function latir(): string;
  function correr(): string;
}

interface Felino
{
  function correr();
}

class Cachorro implements Canino, Mamifero
{

  function respirar()
  {
    return 'Irei usar oxigênio!<br>';
  }

  function latir(): string
  {
    return 'au au<br>';
  }

  function mamar()
  {
    return 'Irei usar leite!<br>';
  }

  function correr(): string
  {
    return 'Estou correndo<br>';
  }
}

$animal1 = new Cachorro();
echo $animal1->respirar();
echo $animal1->latir();
echo $animal1->mamar();
echo $animal1->correr();

echo 'Fim!';

echo '<br>';
var_dump($animal1);

echo '<br>';
var_dump($animal1 instanceof Cachorro);
var_dump($animal1 instanceof Canino);
var_dump($animal1 instanceof Mamifero);
var_dump($animal1 instanceof Animal);
