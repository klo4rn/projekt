<?php
include('database.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_note'])) {
    $user_id = $_POST['user_id'];
    $note = $_POST['note'];
    $stmt = $pdo->prepare("UPDATE konto_uzytkownika SET note = ? WHERE id = ?");
    $stmt->execute([$note, $user_id]);
    echo "Notatka została dodana!";
}

$customers = $pdo->query("SELECT * FROM konto_uzytkownika")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zarządzanie Klientami</title>
    <link rel="stylesheet" href="styling.css">
</head>
<body>
    <h1>Zarządzanie Klientami</h1>

    <table>
        <tr>
            <th>ID</th>
            <th>Imię</th>
            <th>Nazwisko</th>
            <th>Email</th>
            <th>Notatka</th>
        </tr>
        <?php foreach ($customers as $customer): ?>
        <tr>
            <td><?= $customer['id'] ?></td>
            <td><?= $customer['login'] ?></td>
            <td><?= $customer['haslo'] ?></td>
            <td><?= $customer['email'] ?></td>
            <td><?= $customer['note'] ?? 'Brak' ?></td>
            <td>
                <form action="customers.php" method="POST">
                    <input type="hidden" name="user_id" value="<?= $customer['id'] ?>">
                    <input type="text" name="note" placeholder="Dodaj notatkę">
                    <button type="submit" name="add_note">Zapisz</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
