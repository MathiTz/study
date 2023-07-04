<div class="titulo">Desafio Switch</div>

<form action="#" method="post">
  <input type="text" name="param">
  <select name="conversao" id="conversao">
    <option value="km-milha">Km > Milha</option>
    <option value="milha-km">Milha > Km</option>
    <option value="metro-km">Metros > Km</option>
    <option value="km-metro">Km > Metros</option>
    <option value="celsius-fahrenheit">Celsius > Fahrenheit</option>
    <option value="fahrenheit-celsius">Fahrenheit > Celsius</option>
  </select>

  <button>Calcular</button>
</form>

<style>
  form > * {
    font-size: 1.8rem;
  }
</style>

<?php

const FATOR_KM_MILHA = 0.621371;
const FATOR_METRO_KM = 1000;

$converter = $_POST['conversao'];
$amount = $_POST['param'];
$finalAmount;

switch ($converter) {
  case 'km-milha':
    $finalAmount = number_format($amount * FATOR_KM_MILHA, 2, ',', '.');
    break;
  case 'milha-km':
    $finalAmount = number_format($amount / FATOR_KM_MILHA, 2, ',', '.');
    break;
  case 'metro-km':
    $finalAmount = number_format($amount / FATOR_METRO_KM, 2, ',', '.');
    break;
  case 'km-metro':
    $finalAmount = number_format($amount * FATOR_METRO_KM, 2, ',', '.');
    break;
  case 'celsius-fahrenheit':
    $finalAmount = number_format($amount * 1.8 + 32, 2, ',', '.');
    break;
  case 'fahrenheit-celsius':
    $finalAmount = number_format(($amount - 32) * 0.5556, 2, ',', '.');
    break;
  default:
    break;
}

echo "Sua medida final ficou: $finalAmount";
