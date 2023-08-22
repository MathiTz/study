<?php
session_start();
validSession();

//$formatter = new IntlDateFormatter('pt_BR',
//  IntlDateFormatter::FULL,
//  IntlDateFormatter::NONE,
//  'America/Sao_Paulo',
//  IntlDateFormatter::GREGORIAN);
$date = (new Datetime())->getTimestamp();
$today = strftime('%d de %B de %Y', $date);
//$today = $formatter->format($date);

loadTemplateView('day_records', ['today' => $today]);
