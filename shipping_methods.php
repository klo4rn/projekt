<?php
require_once 'database.php';

if (isset($_POST['add_shipping_method'])) {
    $name = $_POST['name'];
    $cost = $_POST['cost'];

    $query = $pdo->prepare("INSERT INTO shipping_methods (name, cost) VALUES (:name, :cost)");
    $query->bindValue(':name', $name);
    $query->bindValue(':cost', $cost);
    $query->execute();

    header("Location: shipping_methods.php");
    exit();
}

if (isset($_POST['edit_shipping_method'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $cost = $_POST['cost'];

    $query = $pdo->prepare("UPDATE shipping_methods SET name = :name, cost = :cost WHERE id = :id");
    $query->bindValue(':name', $name);
    $query->bindValue(':cost', $cost);
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();

    header("Location: shipping_methods.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $query = $pdo->prepare("DELETE FROM shipping_methods WHERE id = :id");
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();

    header("Location: shipping_methods.php");
    exit();
}

$shipping_methods_result = $pdo->query("SELECT * FROM shipping_methods");

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zarządzanie Metodami Dostawy</title>
    <link rel="stylesheet" href="styling.css">
</head>
<body>
<button class="przenies" onclick="window.location.href='admin.php'">Powrót</button>

<h1>Zarządzanie Metodami Dostawy</h1>

<div class="form-container">
    <h2>Dodaj Nową Metodę Dostawy</h2>
    <form action="shipping_methods.php" method="POST">
        <label for="name">Nazwa:</label>
        <input type="text" id="name" name="name" required>

        <label for="cost">Koszt (PLN):</label>
        <input type="number" id="cost" name="cost" step="0.01" min="0" required>

        <button type="submit" name="add_shipping_method">Dodaj Metodę Dostawy</button>
    </form>
</div>

<h2>Lista Metod Dostawy</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nazwa</th>
            <th>Koszt (PLN)</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($method = $shipping_methods_result->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?php echo $method['id']; ?></td>
                <td><?php echo htmlspecialchars($method['name']); ?></td>
                <td><?php echo number_format($method['cost'], 2); ?> PLN</td>
                <td>
                    <a class='odsylacz' href="shipping_methods.php?edit=<?php echo $method['id']; ?>">Edytuj</a> |
                    <a class='odsylacz' href="shipping_methods.php?delete=<?php echo $method['id']; ?>" onclick="return confirm('Czy na pewno chcesz usunąć tę metodę dostawy?');">Usuń</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php if (isset($_GET['edit'])): ?>
    <?php
        $id = $_GET['edit'];
        $query = $pdo->prepare("SELECT * FROM shipping_methods WHERE id = :id");
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $method = $query->fetch(PDO::FETCH_ASSOC);
    ?>
    <div class="form-container">
        <h2>Edytuj Metodę Dostawy</h2>
        <form action="shipping_methods.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $method['id']; ?>">
            
            <label for="name">Nazwa:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($method['name']); ?>" required>
            
            <label for="cost">Koszt (PLN):</label>
            <input type="number" id="cost" name="cost" step="0.01" min="0" value="<?php echo number_format($method['cost'], 2); ?>" required>
            
            <button type="submit" name="edit_shipping_method">Zapisz Zmiany</button>
        </form>
    </div>
<?php endif; ?>


</body>
</html>
