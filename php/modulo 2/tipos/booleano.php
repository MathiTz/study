<div class="titulo">Tipo Booleano</div>

<?php

echo TRUE;
echo '<br>';
echo FALSE;
echo '<br>';
echo var_dump(true);
echo '<br>';
echo var_dump(false);
echo '<br>';
echo var_dump('false');
echo '<br>';
echo is_bool(false);
echo '<br>';
echo is_bool('true');
echo '<br>';

// fazer as regras de conversões
echo '<p>Regras: </p>';
echo '<br>' . var_dump((bool) 0); // apenas zero é false
echo '<br>' . var_dump((bool) 20);
echo '<br>' . var_dump((bool) -1);
echo '<br>' . var_dump((bool) 0.0);
echo '<br>' . var_dump((bool) 0.0000001);
echo '<br>' . var_dump((bool) ""); // false
echo '<br>' . var_dump((bool) " "); // false
echo '<br>' . var_dump((bool) "0"); // todo resto é true
echo '<br>' . var_dump((bool) "00");
echo '<br>' . var_dump(!!"false");
