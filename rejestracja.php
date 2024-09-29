<?php
require_once 'database.php';

$login = $_POST['login'];
$hasło = $_POST['haslo'];
$email = $_POST['email'];
$adres = $_POST['adres'];
$data = $_POST['data_uro'];
$pytanie = $_POST['przypomnienie'];
$odpowiedz = $_POST['pytanie'];
$loginzbazy = $pdo->query("SELECT login from konto_uzytkownika where login = '$login'");
$komunikat = '';
if($loginzbazy->fetchColumn()){
    $komunikat= "dany login jest zajety";

}elseif (empty($login) || empty($hasło) || empty($email) || empty($adres) || empty($odpowiedz)) {
    echo "Nie wypełniłeś wszystkiego";//
} elseif ($data > date('Y-m-d')){
echo "zla data";
}elseif (strpos($email, '@') == false) {
    echo "bledy email";
}
else {

    $hasło_hash = password_hash($hasło, PASSWORD_DEFAULT);

    $dodawanie = $pdo->exec("INSERT INTO konto_uzytkownika (login, hasło, email, adres, data_urodzenia, id_pytania_pomocniczego, odpowiedz) 
        VALUES ('$login', '$hasło_hash', '$email', '$adres', '$data', '$pytanie', '$odpowiedz')");

    header("Location: okienko.html");
    exit;
}
?>
