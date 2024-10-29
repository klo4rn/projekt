<?php
require_once 'database.php';

if (isset($_POST['add_shipping_method'])) {
    $name = $_POST['name'];
    $cost = $_POST['cost'];

    $query = $db->prepare("INSERT INTO shipping_methods (name, cost) VALUES (?, ?)");
    $query->bind_param('sd', $name, $cost);
    $query->execute();

    header("Location: shipping_methods.php");
    exit();
}

if (isset($_POST['edit_shipping_method'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $cost = $_POST['cost'];

    $query = $db->prepare("UPDATE shipping_methods SET name = ?, cost = ? WHERE id = ?");
    $query->bind_param('sdi', $name, $cost, $id);
    $query->execute();

    header("Location: shipping_methods.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $query = $db->prepare("DELETE FROM shipping_methods WHERE id = ?");
    $query->bind_param('i', $id);
    $query->execute();

    header("Location: shipping_methods.php");
    exit();
}

$shipping_methods_result = $db->query("SELECT * FROM shipping_methods");

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zarządzanie Metodami Dostawy</title>
    <link rel="stylesheet" href="styling.css">
    
</head>
<body>

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
        <?php while ($method = $shipping_methods_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $method['id']; ?></td>
                <td><?php echo htmlspecialchars($method['name']); ?></td>
                <td><?php echo number_format($method['cost'], 2); ?> PLN</td>
                <td>
                    <a href="shipping_methods.php?edit=<?php echo $method['id']; ?>">Edytuj</a> |
                    <a href="shipping_methods.php?delete=<?php echo $method['id']; ?>" onclick="return confirm('Czy na pewno chcesz usunąć tę metodę dostawy?');">Usuń</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php if (isset($_GET['edit'])): ?>
    <?php
        $id = $_GET['edit'];
        $query = $db->prepare("SELECT * FROM shipping_methods WHERE id = ?");
        $query->bind_param('i', $id);
        $query->execute();
        $result = $query->get_result();
        $method = $result->fetch_assoc();
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
