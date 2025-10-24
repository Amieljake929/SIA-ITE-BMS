<?php
include dirname(__DIR__, 2) . '/login/db_connect.php';

$result = $conn->query('DESCRIBE registration_anti_rabies');
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . ' - ' . $row['Type'] . PHP_EOL;
}
$conn->close();
?>
