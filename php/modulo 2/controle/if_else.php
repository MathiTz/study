<div class="titulo">
  If Else
</div>

<?php

if (true) {
  echo 'Serei impresso?<br>';
}

if (false) {
  echo "Verdadeiro - Part A<br>";
  echo "Verdadeiro - Part B<br>";
} else {
  echo "False - Part A<br>";
  echo "False - Part B<br>";
}

if (false) {
  echo "Passo A<br>";
} else if (true) {
  echo "Passo B<br>";
} elseif (true) {
  echo "Passo C<br>";
} else {
  echo "Último Passo<br>";
}


echo "Fim<br>";
