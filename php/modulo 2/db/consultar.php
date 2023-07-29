<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
      integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

<div class="titulo">Consultar Registros</div>

<?php

require_once("conexao.php");

$sql = "SELECT * FROM cadastro";

$conexao = novaConexao();
$resultado = $conexao->query($sql);

$registros = [];

if ($resultado->num_rows > 0) {
  while ($row = $resultado->fetch_assoc()) {
    $registros[] = $row;
  }
} else if ($conexao->error) {
  echo "Erro: " . $conexao->error;
}

$conexao->close();
?>

<table class="table table-hover table-striped table-bordered">
  <thead>
  <th>Código</th>
  <th>Nome</th>
  <th>Nascimento</th>
  <th>E-mail</th>
  </thead>
  <tbody>
  <?php foreach ($registros as $registro): ?>
    <tr>
      <td><?= $registro['id'] ?></td>
      <td><?= $registro['nome'] ?></td>
      <td><?= date('d/m/Y', strtotime($registro['nascimento'])); ?></td>
      <td><?= $registro['email'] ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<style>
  table > * {
    font-size: 1.2rem;
  }
</style>
