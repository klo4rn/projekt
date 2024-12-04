<?php
session_start();
require_once 'database.php';

$is_logged_in = isset($_SESSION['zalogowany_uzytkownik']);
$user_id = $is_logged_in ? $_SESSION['zalogowany_uzytkownik']['id'] : null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $email = $_POST['contact_number']; 
    $payment_method_id = $_POST['payment_method'];
    $shipping_method_id = $_POST['shipping_method'];

    if ($is_logged_in) {
        $stmt = $pdo->prepare("SELECT product_id, quantity FROM cart_items WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];
        $cart_items = [];
        foreach ($cart as $product_id => $quantity) {
            $stmt = $pdo->prepare("SELECT id FROM products WHERE id = :id");
            $stmt->execute([':id' => $product_id]);
            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                $cart_items[] = ['product_id' => $product_id, 'quantity' => $quantity];
            }
        }
    }

    if (empty($cart_items)) {
        echo "<script type='text/javascript'>window.alert('koszyk jest pusty');
          window.location.href='index.php';</script>";
    }

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, full_name, address, contact_number, total_price, status) 
                           VALUES (:user_id, :full_name, :address, :contact_number, :total_price, 'Nowe')");
    $full_name = $first_name . ' ' . $last_name;
    $stmt->execute([
        ':user_id' => $user_id,
        ':full_name' => $full_name,
        ':address' => $address,
        ':contact_number' => $email,
        ':total_price' => calculateTotalPrice($cart_items)
    ]);
    
    $order_id = $pdo->lastInsertId();

    foreach ($cart_items as $item) {
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) 
                               SELECT :order_id, :product_id, :quantity, cena FROM products WHERE id = :product_id");
        $stmt->execute([
            ':order_id' => $order_id,
            ':product_id' => $item['product_id'],
            ':quantity' => $item['quantity']
        ]);
    }

    $stmt = $pdo->prepare("INSERT INTO order_payment (order_id, payment_method_id) VALUES (:order_id, :payment_method_id)");
    $stmt->execute([':order_id' => $order_id, ':payment_method_id' => $payment_method_id]);

    $stmt = $pdo->prepare("INSERT INTO order_shipping (order_id, shipping_method_id) VALUES (:order_id, :shipping_method_id)");
    $stmt->execute([':order_id' => $order_id, ':shipping_method_id' => $shipping_method_id]);

    if ($is_logged_in) {
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
    } else {
        setcookie('cart', json_encode([]), time() - 3600, '/');
    }
    echo "<script type='text/javascript'>window.alert('Zamowienie zostało poprawnie złożone');
          window.location.href='index.php';</script>";
   
    
    exit;
}
$tytul = "Potwierdzenie";
$tresc = "Twoje zamowienie jest w trakcie realizacji";
if($_POST)
{
mail($email,$tytul,$tresc);
}
function calculateTotalPrice($cart_items) {
    global $pdo;
    $total_price = 0;
    foreach ($cart_items as $item) {
        $stmt = $pdo->prepare("SELECT cena FROM products WHERE id = :id");
        $stmt->execute([':id' => $item['product_id']]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        $total_price += $product['cena'] * $item['quantity'];
    }
    return $total_price;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Podsumowanie Zamówienia</title>
    <link rel="stylesheet" href="platnosc.css">
</head>
<body>
    <header>
        <h1>WhiskyStore - Podsumowanie Zamówienia</h1>
    </header>
    <main>
        <h2>Wypełnij dane do zamówienia</h2>
        <form action="" method="POST">
            <label for="first_name">Imię:</label>
            <input type="text" id="first_name" name="first_name" required><br>

            <label for="last_name">Nazwisko:</label>
            <input type="text" id="last_name" name="last_name" required><br>

            <label for="address">Adres:</label>
            <input type="text" id="address" name="address" required><br>

            <label for="contact_number">Email:</label>
            <input type="text" id="contact_number" name="contact_number" required><br>

            <label for="payment_method">Wybierz metodę płatności:</label>
            <?php
            $stmt = $pdo->query("SELECT * FROM payment_methods");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<input type='radio' name='payment_method' value='{$row['id']}' required> {$row['name']}<br>";
            }
            ?>

            <label for="shipping_method">Wybierz metodę dostawy:</label>
            <?php
            $stmt = $pdo->query("SELECT * FROM shipping_methods");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<input type='radio' name='shipping_method' value='{$row['id']}' required> {$row['name']} - {$row['cost']} PLN<br>";
            }
            ?>

            <button type="submit">Złóż zamówienie</button>
        </form>
    </main>
</body>
</html>
