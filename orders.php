<?php
include('database.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $order_id]);
    echo "Status zamówienia został zaktualizowany!";
}

$orders = $pdo->query("SELECT * FROM orders")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zarządzanie Zamówieniami</title>
    <link rel="stylesheet" href="styling.css">
</head>
<body>
<button class="przenies" onclick="window.location.href='admin.php'">Powrót</button>

    <h1>Zarządzanie Zamówieniami</h1>

    <table>
        <tr>
            <th>ID</th>
            <th>Klient ID</th>
            <th>Status</th>
            <th>Cena Całkowita</th>
            <th>Imie i nazwisko</th>
            <th>Adres</th>
            <th>Email</th>
        </tr>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?= $order['id'] ?></td>
            <td><?= $order['user_id'] ?? 'Gość' ?></td>
            <td><?= $order['status'] ?></td>
            <td><?= $order['total_price'] ?> PLN</td>
            <td><?= $order['full_name'] ?> </td>
            <td><?= $order['address'] ?> </td>
            <td><?= $order['contact_number'] ?> </td>

            <td>
                <form action="orders.php" method="POST">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <select name="status">
                        <option <?= $order['status'] == 'Nowe' ? 'selected' : '' ?>>Nowe</option>
                        <option <?= $order['status'] == 'W realizacji' ? 'selected' : '' ?>>W realizacji</option>
                        <option <?= $order['status'] == 'Zakończone' ? 'selected' : '' ?>>Zakończone</option>
                    </select>
                    <button type="submit" name="update_status">Zapisz</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
