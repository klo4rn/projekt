<?php
include('database.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_parameter'])) {
    $parameter_name = $_POST['parameter_name'];
    $stmt = $pdo->prepare("INSERT INTO parameters (name) VALUES (?)");
    $stmt->execute([$parameter_name]);
    echo "Dodano nowy parametr!";
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM parameters WHERE id = ?");
    $stmt->execute([$id]);
    echo "Parametr został usunięty!";
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zarządzanie Parametrami Produktów</title>
    <link rel="stylesheet" href="styling.css">
</head>
<body>
<button class="przenies" onclick="window.location.href='admin.php'">Powrót</button>
<h1>Zarządzanie Parametrami Produktów</h1>
<div class="form-container">
    
<h2>Dodawanie paramentrów produktów</h2>
    

    <form action="parameters.php" method="POST">
        <label for="parameter_name">Nazwa parametru:</label>
        <input type="text" name="parameter_name" id="parameter_name" required>
        <button type="submit" name="add_parameter">Dodaj parametr</button>
    </form>
</div>
    <h2>Istniejące Parametry</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nazwa</th>
        </tr>
        <?php
        $parameters = $pdo->query("SELECT * FROM parameters")->fetchAll();
        foreach ($parameters as $param) {
            echo "<tr><td>{$param['id']}</td><td>{$param['name']}</td>";
            echo "<td><a href='parameters.php?delete={$param['id']}'>Usuń</a></td></tr>";
        }
        ?>
    </table>
   
</body>
</html>
