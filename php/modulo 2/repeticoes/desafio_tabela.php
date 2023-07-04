<div class="titulo">Desafio Tabela</div>

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
</style>

<table>
  <?php

  $matrix = [
    ['01', '02', '03', '04', '05'],
    ['06', '07', '08', '09', '10'],
    ['11', '12', '13', '14', '15'],
    ['16', '17', '19', '19', '20'],
  ];
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
