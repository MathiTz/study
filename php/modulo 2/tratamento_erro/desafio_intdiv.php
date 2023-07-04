<?php

namespace Aritmetica;

class NaoInteiroException extends \Exception {
  public function __construct($message = "", $code = 0, Throwable $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }
}

function intdiv($a, $b) {
  if ($b == 0) {
    throw new \DivisionByZeroError();
  }

  if ($a % $b > 0) {
    throw new NaoInteiroException();
  }

  return $a / $b;
}
