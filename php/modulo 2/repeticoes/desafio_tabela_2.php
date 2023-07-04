<div class="titulo">Desafio Tabela 2</div>

<style>
  table {
    border: 1px solid #444;
    border-collapse: collapse;
    margin: 20px 0;
  }

  table tr {
    border: 1px solid #444;
  }

  table td {
    padding: 10px 20px;
  }

  form * {
    font-size: 1.8rem;
  }

  form > div {
    margin-bottom: 10px;
  }
</style>

<form action="#" method="post">
  <div><label for="rows">Number of rows: </label><input type="number" value="<?= $_POST['rows'] ?? 10 ?>" name="rows" min="1"></div>
  <div><label for="columns">Number of columns: </label><input type="number" value="<?= $_POST['rows'] ?? 10 ?>" name="columns" min="1"></div>

  <button type="submit">Submit</button>
</form>

<table>
  <?php
  $rows = intval($_POST['rows']);
  $columns = intval($_POST['columns']);

  $matrix = array();
  for ($i = 0; $i < $rows; $i++) {
    $rowNumbers = array();

    for ($j = 0; $j < $columns; $j++) {
      $rowNumbers[] = $j * random_int(1, 10);
    }

    $matrix[] = $rowNumbers;
  }

  echo "<tbody>";
  foreach ($matrix as $index => $line) {
    $color = $index % 2 === 1 ? "lightblue" : "white";

    echo "<tr style='background-color: {$color}'>";
    foreach ($line as $value) {
      echo "<td>$value</td>";
    }
    echo "</tr>";
  }
  echo "</tbody>";
  ?>
</table>
