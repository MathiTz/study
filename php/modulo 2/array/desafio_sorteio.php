<div class="titulo">Desafio Sorteio</div>

<?php

$nomes = ["Elza", "Rapunzel", "Branca de neve", "Cinderela"];

$randIndex = array_rand($nomes);

echo "<h1>$nomes[$randIndex]</h1>";
