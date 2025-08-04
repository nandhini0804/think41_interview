<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'sample';

// Connect to MySQL
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// === Import USERS ===
$usersFile = fopen('users.csv', 'r');
if ($usersFile !== false) {
    $headers = fgetcsv($usersFile); // Get header
    while (($row = fgetcsv($usersFile)) !== false) {
        $data = array_map([$conn, 'real_escape_string'], $row);
        $values = "'" . implode("','", $data) . "'";
        $columns = "`" . implode("`,`", $headers) . "`";
        $sql = "INSERT INTO users ($columns) VALUES ($values)";
        if (!$conn->query($sql)) {
            echo "User insert failed: " . $conn->error . "<br>";
        }
    }
    fclose($usersFile);
}

// === Import ORDERS ===
$ordersFile = fopen('orders.csv', 'r');
if ($ordersFile !== false) {
    $headers = fgetcsv($ordersFile); // Get header
    while (($row = fgetcsv($ordersFile)) !== false) {
        $data = array_map(function ($item) use ($conn) {
            return $item === '' ? 'NULL' : "'" . $conn->real_escape_string($item) . "'";
        }, $row);
        $columns = "`" . implode("`,`", $headers) . "`";
        $values = implode(",", $data);
        $sql = "INSERT INTO orders ($columns) VALUES ($values)";
        if (!$conn->query($sql)) {
            echo "Order insert failed: " . $conn->error . "<br>";
        }
    }
    fclose($ordersFile);
}

echo "Data imported successfully.";
$conn->close();
?>

