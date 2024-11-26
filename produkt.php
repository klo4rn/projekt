<?php
session_start();
require_once 'database.php';
$zalogowany = isset($_SESSION['zalogowany_uzytkownik']);
$czy_admin = $zalogowany && $_SESSION['zalogowany_uzytkownik']['id'] == 1;
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header("Location: glowna.php");
    exit;
}

$stmt = $pdo->prepare("SELECT id, name, cena, image FROM products WHERE id = :id");
$stmt->execute([':id' => $product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: index.php");
    exit;
}

$stmt_params = $pdo->prepare("SELECT name, value FROM product_parameters 
                              INNER JOIN parameters ON product_parameters.parameter_id = parameters.id 
                              WHERE product_id = :product_id");
$stmt_params->execute([':product_id' => $product_id]);
$parameters = $stmt_params->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['add_to_cart'])) {
    if (isset($_SESSION['zalogowany_uzytkownik'])) {
        $user_id = $_SESSION['zalogowany_uzytkownik']['id'];

        $stmt_check = $pdo->prepare("SELECT * FROM cart_items WHERE user_id = :user_id AND product_id = :product_id");
        $stmt_check->execute([':user_id' => $user_id, ':product_id' => $product_id]);
        
        if ($stmt_check->rowCount() > 0) {
            $stmt_update = $pdo->prepare("UPDATE cart_items SET quantity = quantity + 1 WHERE user_id = :user_id AND product_id = :product_id");
            $stmt_update->execute([':user_id' => $user_id, ':product_id' => $product_id]);
        } else {
            $stmt_add = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (:user_id, :product_id, 1)");
            $stmt_add->execute([':user_id' => $user_id, ':product_id' => $product_id]);
        }
    } else {
        $cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];
        
        if (isset($cart[$product_id])) {
            $cart[$product_id]++;
        } else {
            $cart[$product_id] = 1;
        }

        setcookie('cart', json_encode($cart), time() + 3600, '/'); 
    }

    header("Location: produkt.php?id=" . $product_id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produkt: <?php echo htmlspecialchars($product['name']); ?></title>
    <link rel="stylesheet" href="produkt.css">
</head>
<body>
    <header>
        <div class="logo">
            <h1>WhiskyStore</h1>
        </div>
        <div class="user-panel">
            <a href="kosz.php" class="btn">Kosz</a>
            <?php if ($zalogowany): ?>
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

    <div class="product-details">
        <h2><?php echo htmlspecialchars($product['name']); ?></h2>
        <div class="product-info">
            <div class="zdjecie">
                <img width="25%" height="25%" src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <div class="product-description">
                <p><strong>Cena:</strong> <?php echo htmlspecialchars($product['cena']); ?> PLN</p>
                <h3>Parametry produktu:</h3>
                <ul>
                    <?php foreach ($parameters as $param): ?>
                        <li><strong><?php echo htmlspecialchars($param['name']); ?>:</strong> <?php echo htmlspecialchars($param['value']); ?></li>
                    <?php endforeach; ?>
                </ul>
                <form method="POST">
                    <button type="submit" name="add_to_cart" class="btn">Dodaj do kosza</button>
                </form>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 WhiskyStore. Wszystkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>
