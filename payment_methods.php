<?php
require_once 'database.php';

if (isset($_POST['add_payment_method'])) {
    $name = $_POST['name'];

    $query = $pdo->prepare("INSERT INTO payment_methods (name) VALUES (:name)");
    $query->bindValue(':name', $name);
    $query->execute();

    header("Location: payment_methods.php");
    exit();
}

if (isset($_POST['edit_payment_method'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];

    $query = $pdo->prepare("UPDATE payment_methods SET name = :name WHERE id = :id");
    $query->bindValue(':name', $name);
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();

    header("Location: payment_methods.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $query = $pdo->prepare("DELETE FROM payment_methods WHERE id = :id");
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();

    header("Location: payment_methods.php");
    exit();
}

$payment_methods_result = $pdo->query("SELECT * FROM payment_methods");
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zarządzanie Metodami Płatności</title>
    <link rel="stylesheet" href="styling.css"> 
</head>
<body>
<button class="przenies" onclick="window.location.href='admin.php'">Powrót</button>

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
        <?php while ($method = $payment_methods_result->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?php echo $method['id']; ?></td>
                <td><?php echo htmlspecialchars($method['name']); ?></td>
                <td>
                    <a class='odsylacz'href="payment_methods.php?edit=<?php echo $method['id']; ?>">Edytuj</a> |
                    <a class='odsylacz' href="payment_methods.php?delete=<?php echo $method['id']; ?>" onclick="return confirm('Czy na pewno chcesz usunąć tę metodę płatności?');">Usuń</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php if (isset($_GET['edit'])): ?>
    <?php
        $id = $_GET['edit'];
        $query = $pdo->prepare("SELECT * FROM payment_methods WHERE id = :id");
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $method = $query->fetch(PDO::FETCH_ASSOC);
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
