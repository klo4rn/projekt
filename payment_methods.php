<?php
require_once 'database.php';

if (isset($_POST['add_payment_method'])) {
    $name = $_POST['name'];

    $query = $db->prepare("INSERT INTO payment_methods (name) VALUES (?)");
    $query->bind_param('s', $name);
    $query->execute();

    header("Location: payment_methods.php");
    exit();
}

if (isset($_POST['edit_payment_method'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];

    $query = $db->prepare("UPDATE payment_methods SET name = ? WHERE id = ?");
    $query->bind_param('si', $name, $id);
    $query->execute();

    header("Location: payment_methods.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $query = $db->prepare("DELETE FROM payment_methods WHERE id = ?");
    $query->bind_param('i', $id);
    $query->execute();

    header("Location: payment_methods.php");
    exit();
}

$payment_methods_result = $db->query("SELECT * FROM payment_methods");

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zarządzanie Metodami Płatności</title>
    <link rel="stylesheet" href="styling.css"> 
</head>
<body>

<h1>Zarządzanie Metodami Płatności</h1>

<div class="form-container">
    <h2>Dodaj Nową Metodę Płatności</h2>
    <form action="payment_methods.php" method="POST">
        <label for="name">Nazwa Metody Płatności:</label>
        <input type="text" id="name" name="name" required>

        <button type="submit" name="add_payment_method">Dodaj Metodę Płatności</button>
    </form>
</div>

<h2>Lista Metod Płatności</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nazwa</th>
            <th>Akcje</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($method = $payment_methods_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $method['id']; ?></td>
                <td><?php echo htmlspecialchars($method['name']); ?></td>
                <td>
                    <a href="payment_methods.php?edit=<?php echo $method['id']; ?>">Edytuj</a> |
                    <a href="payment_methods.php?delete=<?php echo $method['id']; ?>" onclick="return confirm('Czy na pewno chcesz usunąć tę metodę płatności?');">Usuń</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php if (isset($_GET['edit'])): ?>
    <?php
        $id = $_GET['edit'];
        $query = $db->prepare("SELECT * FROM payment_methods WHERE id = ?");
        $query->bind_param('i', $id);
        $query->execute();
        $result = $query->get_result();
        $method = $result->fetch_assoc();
    ?>
    <div class="form-container">
        <h2>Edytuj Metodę Płatności</h2>
        <form action="payment_methods.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $method['id']; ?>">
            
            <label for="name">Nazwa Metody Płatności:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($method['name']); ?>" required>
            
            <button type="submit" name="edit_payment_method">Zapisz Zmiany</button>
        </form>
    </div>
<?php endif; ?>

</body>
</html>
