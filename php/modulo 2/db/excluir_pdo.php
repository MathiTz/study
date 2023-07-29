<div class="titulo">PDO: Excluir</div>

<?php
require_once("conexao_pdo.php");

$sql = "DELETE FROM cadastro WHERE id = :id";
$conexao = novaConexao();
$stmt = $conexao->prepare($sql);

if ($stmt->execute([':id' => 10])) {
  echo "Sucess :)";
} else {
  echo "Erro :(";
  print_r($stmt->errorInfo());
}
