<?php
include_once 'db.php';


$conn->query("DELETE FROM admin_users WHERE username = 'admin'");


$username = 'admin';
$password = 'admin123';
$email = 'admin@simsanghostel.edu';


$hashed_password = password_hash($password, PASSWORD_DEFAULT);

echo "Generated hash: " . $hashed_password . "<br><br>";

$stmt = $conn->prepare("INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)");
$stmt->bind_param('sss', $username, $hashed_password, $email);

if ($stmt->execute()) {
    echo "✅ Admin created successfully!<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
} else {
    echo "❌ Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
