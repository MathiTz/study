<div class="titulo">Imagens</div>

<?php
session_start();

$arquivos = $_SESSION['arquivos'] ?? [];

$allowedTypes = ['jpeg', 'jpg', 'png'];
$pastaUpload = __DIR__ . '/../files/';
$nomeArquivo = $_FILES['arquivo']['name'];
$arquivo = $pastaUpload . $nomeArquivo;
$tmp = $_FILES['arquivo']['tmp_name'];
$type = explode('/', $_FILES['arquivo']['type'])[1];
if (in_array($type, $allowedTypes) && move_uploaded_file($tmp, $arquivo)) {
  echo "<br>Arquivo válido e enviado com sucesso.";
  $arquivos[] = $nomeArquivo;
  $_SESSION['arquivos'] = $arquivos;
} else {
  echo "<br>Erro no upload de arquivo!";
}
?>

<form action="#" method="post" enctype="multipart/form-data">
  <input type="file" name="arquivo">
  <button>Enviar</button>
</form>

<ul>
  <?php foreach ($arquivos as $arquivo): ?>
    <?php if (stripos($arquivo, '.jpg') > 0): ?>
      <li>
        <img alt="<?= $arquivo ?>" src="../files/<?= $arquivo ?>" />
      </li>
    <?php endif ?>
  <?php endforeach ?>
</ul>

<style>
  input, button {
    font-size: 1.2rem;
  }

  li img {
    width: 150px;
  }
</style>
