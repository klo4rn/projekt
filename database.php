<?php
$serwer='ct8.pl';
$login='m50688_sklep';
$nazwa_bazy='m50688_sklep';
$haslo='Sklepik1';

$pdo = new PDO('mysql:host='.$serwer.';dbname='.$nazwa_bazy,$login,$haslo);

?>