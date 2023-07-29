<div class="titulo">Inserir Registro #01</div>

<?php

require_once('conexao.php');

$sql = "INSERT INTO cadastro (
                      nome, nascimento, email, site, filhos, salario
) VALUES (
          'Arthur',
          '2006-04-28',
          'arthur@email.com.br',
          'https://arthur.com.br',
          0,
          100.98
)";

$conexao = novaConexao();
$resultado = $conexao->query($sql);

if ($resultado) {
  echo "Sucesso :)";
} else {
  Echo "Erro: ". $conexao->error;
}

$conexao->close();
