<?php
session_start();
require_once 'database.php';

$is_logged_in = isset($_SESSION['zalogowany_uzytkownik']);
$user_id = $is_logged_in ? $_SESSION['zalogowany_uzytkownik']['id'] : null;
$czy_admin = $is_logged_in && $_SESSION['zalogowany_uzytkownik']['id'] == 1;

$total_price = 0; 

if ($is_logged_in) {
    $stmt = $pdo->prepare("SELECT products.id, products.name, products.cena, products.image, cart_items.quantity 
                           FROM cart_items 
                           INNER JOIN products ON cart_items.product_id = products.id 
                           WHERE cart_items.user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];
    $cart_items = [];
    foreach ($cart as $product_id => $quantity) {
        $stmt = $pdo->prepare("SELECT id, name, cena, image FROM products WHERE id = :id");
        $stmt->execute([':id' => $product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($product) {
            $cart_items[] = ['id' => $product['id'], 'name' => $product['name'], 'cena' => $product['cena'], 'image' => $product['image'], 'quantity' => $quantity];
        }
    }
}

if (isset($_POST['remove_item'])) {
    $product_id_to_remove = $_POST['product_id'];
    if ($is_logged_in) {
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->execute([':user_id' => $user_id, ':product_id' => $product_id_to_remove]);
    } else {
        unset($cart[$product_id_to_remove]);
        setcookie('cart', json_encode($cart), time() + 3600, '/');
    }
    header('Location: kosz.php');
    exit;
}

if (isset($_POST['update_quantity'])) {
    $product_id_to_update = $_POST['product_id'];
    $new_quantity = $_POST['new_quantity']; 
    
    if ($new_quantity <= 0) {
        if ($is_logged_in) {
            $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = :user_id AND product_id = :product_id");
            $stmt->execute([':user_id' => $user_id, ':product_id' => $product_id_to_update]);
        } else {
            unset($cart[$product_id_to_update]);
            setcookie('cart', json_encode($cart), time() + 3600, '/');
        }
    } else {
        if ($is_logged_in) {
            $stmt = $pdo->prepare("UPDATE cart_items SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id");
            $stmt->execute([':quantity' => $new_quantity, ':user_id' => $user_id, ':product_id' => $product_id_to_update]);
        } else {
            $cart[$product_id_to_update] = $new_quantity;
            setcookie('cart', json_encode($cart), time() + 3600, '/');
        }
    }
    header('Location: kosz.php');
    exit;
}

foreach ($cart_items as $item) {
    $total_price += $item['cena'] * $item['quantity'];
}

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koszyk</title>
    <link rel="stylesheet" href="kosz.css">
</head>
<body>
    <header>
        <div class="logo">
            <h1>WhiskyStore</h1>
        </div>
        <div class="user-panel">
            <a href="kosz.php" class="btn">Kosz</a>
            <?php if ($is_logged_in): ?>
                <span>Witaj, <?php echo htmlspecialchars($_SESSION['zalogowany_uzytkownik']['login']); ?></span>
                <?php if ($czy_admin): ?>
                    <a href="admin.php" class="btn">Panel administracyjny</a>
                <?php else: ?>
                    <a href="panel_uzytkownika.php" class="btn">Panel użytkownika</a>
                <?php endif; ?>
                <a href="wyloguj.php" class="btn">Wyloguj</a>
            <?php else: ?>
                <a href="logowanie.php" class="btn">Zaloguj się</a>
                <a href="rejestracja.php" class="btn">Zarejestruj się</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="nav-buttons">
        <a href="index.php" class="btn">Główna</a>
        <a href="produkty.php" class="btn">Produkty</a>
        <a href="kontakt.php" class="btn">Kontakt</a>
    </div>

    <div class="cart-container">
        <h2>Twój koszyk</h2>
        <div class="cart-items">
            <?php if (empty($cart_items)): ?>
                <p>Twój koszyk jest pusty.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Produkt</th>
                            <th>Cena</th>
                            <th>Ilość</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo htmlspecialchars($item['cena']); ?> PLN</td>
                                <td>
                                    <form action="kosz.php" method="POST" style="display: inline;">
                                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['id']); ?>">
                                        <input type="number" name="new_quantity" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1" step="1" style="width: 50px;">
                                        <button type="submit" name="update_quantity" class="btn">Zaktualizuj ilość</button>
                                    </form>
                                </td>
                                <td>
                                    <form action="kosz.php" method="POST">
                                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['id']); ?>">
                                        <button type="submit" name="remove_item" class="btn">Usuń</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="total-price">
                    <h3>Łączna kwota: <?php echo number_format($total_price, 2); ?> PLN</h3>
                </div>
                <div class="checkout">
                    <a href="platnosc.php" class="btn">Przejdź do płatności</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 WhiskyStore. Wszystkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>
