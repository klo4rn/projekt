<?php
include('database.php');

// Obsługa zmiennej filtrów
$status_filter = $_GET['status'] ?? '';
$email_search = $_GET['email'] ?? '';

// Budowanie zapytania SQL z filtrami
$query = "SELECT * FROM orders WHERE 1=1";

if (!empty($status_filter)) {
    $query .= " AND status = :status";
}
if (!empty($email_search)) {
    $query .= " AND contact_number LIKE :email";
}

// Przygotowanie zapytania
$stmt = $pdo->prepare($query);

if (!empty($status_filter)) {
    $stmt->bindValue(':status', $status_filter);
}
if (!empty($email_search)) {
    $stmt->bindValue(':email', "%$email_search%");
}

$stmt->execute();
$orders = $stmt->fetchAll();
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

    <!-- Filtry -->
    <form method="GET" action="orders.php">
        <label for="status">Filtruj według statusu:</label>
        <select name="status" id="status">
            <option value="">Wszystkie</option>
            <option value="Nowe" <?= $status_filter == 'Nowe' ? 'selected' : '' ?>>Nowe</option>
            <option value="W realizacji" <?= $status_filter == 'W realizacji' ? 'selected' : '' ?>>W realizacji</option>
            <option value="Zakończone" <?= $status_filter == 'Zakończone' ? 'selected' : '' ?>>Zakończone</option>
        </select>
        
        <label for="email">Szukaj według emaila:</label>
        <input type="text" name="email" id="email" value="<?= htmlspecialchars($email_search) ?>" placeholder="Wprowadź email">

        <button type="submit">Filtruj</button>
    </form>

    <!-- Tabela zamówień -->
    <table>
        <tr>
            <th>ID</th>
            <th>Klient ID</th>
            <th>Status</th>
            <th>Cena Całkowita</th>
            <th>Imię i nazwisko</th>
            <th>Adres</th>
            <th>Email</th>
            <th>Akcje</th>
            <th>Szczegóły zamówienia</th>
        </tr>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?= $order['id'] ?></td>
            <td><?= $order['user_id'] ?? 'Gość' ?></td>
            <td><?= $order['status'] ?></td>
            <td><?= $order['total_price'] ?> PLN</td>
            <td><?= $order['full_name'] ?></td>
            <td><?= $order['address'] ?></td>
            <td><?= $order['contact_number'] ?></td>
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
            <td>
                <?php
                $zamowienie = $pdo->prepare("SELECT products.name, order_items.quantity 
                                             FROM products 
                                             INNER JOIN order_items 
                                             ON products.id = order_items.product_id 
                                             WHERE order_items.order_id = ?");
                $zamowienie->execute([$order['id']]);
                $szczegoly = $zamowienie->fetchAll();
                foreach ($szczegoly as $row): ?>
                    <?= $row['name'] ?> (<?= $row['quantity'] ?>)<br>
                <?php endforeach; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
