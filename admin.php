<?php
session_start();


if (!isset($_SESSION['zalogowany_uzytkownik']) || $_SESSION['zalogowany_uzytkownik']['id'] != 1) {
    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Administratora</title>
    <link rel="stylesheet" href="styling.css">
</head>
<body>
    <h1>Witamy w panelu administracyjnym</h1>
    <nav>
        <ul>
            <li><a href="products.php"> Produkty</a></li>
            <li><a href="categories.php">Kategorie</a></li>
            <li><a href="parameters.php">Parametry</a></li>
            <li><a href="customers.php">Konta użytkownika</a></li>
            <li><a href="orders.php">Zarządzanie Zamówieniami</a></li>
            <li><a href="shipping_methods.php"> Sposoby Dostaw</a></li>
            <li><a href="payment_methods.php">Sposoby Płatności</a></li>
            <li><a href="pages.php">Podstronony Informacyjne</a></li>
            <li><a href="index.php">Strona głowna</a></li>
        </ul>
        
</nav>
</body>
</html>
