<?php
session_start();
include 'db_connection.php'; // Archivo que contiene la conexión a la base de datos

// Recibe los datos del formulario
$username = $_POST['username'];
$password = md5($_POST['password']); // Asegúrate de usar el mismo método de cifrado que en la BD

// Consulta a la base de datos
$query = "SELECT * FROM usuarios WHERE username = '$username' AND password = '$password' LIMIT 1";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    // Guardar los datos de sesión
    $_SESSION['loggedIn'] = true;
    $_SESSION['username'] = $user['username'];
    $_SESSION['nombre_completo'] = $user['nombre_completo'];
    $_SESSION['rol'] = $user['rol'];

    // Responder con éxito
    echo json_encode(['success' => true, 'message' => 'Login exitoso']);
} else {
    // Si los datos no son correctos
    echo json_encode(['success' => false, 'message' => 'Usuario o contraseña incorrectos']);
}

mysqli_close($conn);
?>
