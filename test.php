<?php
$email = 'admin@tfg.local';
$password = 'P@ssword123..'; // La que usas en el login

$pdo = new PDO(
    'mysql:host=' . getenv('MYSQL_HOST') . ';dbname=' . getenv('MYSQL_DATABASE'),
    getenv('MYSQL_USER'),
    getenv('MYSQL_PASSWORD')
);

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo "Usuario no encontrado";
    exit;
}

echo "Email: " . $usuario['email'] . "<br>";
echo "Hash en BD: " . $usuario['password'] . "<br>";
echo "Hash length: " . strlen($usuario['password']) . "<br>";
echo "Password input: " . $password . "<br>";
echo "Password length: " . strlen($password) . "<br>";

$verify = password_verify($password, $usuario['password']);
echo "password_verify result: " . ($verify ? 'TRUE' : 'FALSE') . "<br>";

if (!$verify) {
    echo "Intentando hash manual: " . password_hash($password, PASSWORD_BCRYPT) . "<br>";
}
?>