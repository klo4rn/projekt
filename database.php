<?php
$serwer='localhost';
$login='root';
$nazwa_bazy='sklep';
$haslo='';

$pdo = new PDO('mysql:host='.$serwer.';dbname='.$nazwa_bazy,$login,$haslo);

?>