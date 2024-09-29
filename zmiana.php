<?php
require_once 'database.php';

$login = $_POST['login'];
$pytanie = $_POST['przypomnienie'];
$odpowiedz = $_POST['pytanie'];
$nhasło = $_POST['nhaslo'];
$starylogin = $pdo->query("SELECT login from konto_uzytkownika where login ='$login'");
$starepytanie = $pdo->query("SELECT id_pytania_pomocniczego from konto_uzytkownika where login ='$login'");
$staraodpowiedz = $pdo->query("SELECT odpowiedz from konto_uzytkownika where login = '$login'");
 foreach($starylogin as $staryloginrow)
 {
    $sl = $staryloginrow["login"];
 };
 foreach($starepytanie as $starepytanierow)
 {
    $sp = $starepytanierow["id_pytania_pomocniczego"];
 }
 foreach($staraodpowiedz as $staraodpowiedzrow){
    $so = $staraodpowiedzrow["odpowiedz"];

 }


if (empty($login) || empty($nhasło) || empty($odpowiedz)) {
    echo "Nie wypełniłeś wszystkiego";
} elseif ($login != $sl && $pytanie!=$sp && $odpowiedz!=$do){
echo "dane są złe";
}else{

    $hasło_hash = password_hash($nhasło, PASSWORD_DEFAULT);

    $zmiana = $pdo->query("UPDATE konto_uzytkownika set hasło = '$hasło_hash'");

    header("Location: okienko.html");
    exit;
}
?>
