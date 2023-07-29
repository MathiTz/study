<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
      integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

<div class="titulo">Excluir Registro #02</div>
<style>
  table > * {
    font-size: 1.2rem;
  }
</style>
<?php

require_once("conexao.php");

$conexao = novaConexao();
$registros = [];

if($_GET['excluir']) {
  $excluirSQL = "DELETE FROM cadastro WHERE id = ?";
  $stmt = $conexao->prepare($excluirSQL);
  $stmt->bind_param("i", $_GET["excluir"]);
  $stmt->execute();
}

$sql = "SELECT id, nome, email, nascimento FROM cadastro";
$resultado = $conexao->query($sql);


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
  <th>ID</th>
  <th>Nome</th>
  <th>Email</th>
  <th>Nascimento</th>
  <th>Ações</th>
  </thead>
  <tbody>
  <?php foreach ($registros as $registro): ?>
    <tr>
      <td>
        <?= $registro['id'] ?>
      </td>
      <td>
        <?= $registro['nome'] ?>
      </td>
      <td>
        <?= $registro['email'] ?>
      </td>
      <td>
        <?= date('d/m/Y', strtotime($registro['nascimento'])) ?>
      </td>
      <td>
        <a href="/exercicio.php?dir=db&file=excluir_2&excluir=<?= $registro['id'] ?>" class="btn btn-danger">Excluir</a>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
