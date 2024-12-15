<div class="titulo">$_POST</div>

<form action="#" method="post">
  <input type="text" name="nome">
  <input type="text" name="sobrenome">
  <select name="estado">
    <option value="ACRE">Acre</option>
    <option value="BAHIA">Bahia</option>
    <option value="CEARÁ">Ceará</option>
  </select>
  <button>Enviar</button>
</form>

<style>
  form > * {
    font-size: 1.8rem;
  }
</style>

<?php

//echo $_POST; -> array
print_r($_POST);

echo "<br>" . count($_POST);
