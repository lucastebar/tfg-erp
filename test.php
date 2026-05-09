<?php
echo "MYSQL_HOST: " . getenv('MYSQL_HOST') . "<br>";
echo "MYSQL_USER: " . getenv('MYSQL_USER') . "<br>";
echo "MYSQL_DATABASE: " . getenv('MYSQL_DATABASE') . "<br>";

$pdo = new PDO(
    'mysql:host=' . getenv('MYSQL_HOST') . ';dbname=' . getenv('MYSQL_DATABASE'),
    getenv('MYSQL_USER'),
    getenv('MYSQL_PASSWORD')
);

$result = $pdo->query('SELECT email, password FROM usuarios LIMIT 1');
$user = $result->fetch(PDO::FETCH_ASSOC);

echo "<br>Usuario de prueba: " . $user['email'];
echo "<br>Hash en BD: " . substr($user['password'], 0, 20) . "...";
?>