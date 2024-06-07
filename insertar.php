<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "archivo_general";

// Función para manejar la conexión a la base de datos
function getDBConnection($servername, $dbname, $username, $password) {
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
        exit();
    }
}

// Función para validar el código
function isValidCode($serie_id, $codigo) {
    switch ($serie_id) {
        case 1:
            return preg_match('/^A106\.1\.1\-01-\d{2}$/', $codigo);
        case 2:
            return preg_match('/^A106\.1\.1\-02-\d{2}$/', $codigo);
        case 3:
            return preg_match('/^A106\.1\.1\-03-\d{2}$/', $codigo);
        case 4:
            return preg_match('/^A106\.1\.1\-04-\d{2}$/', $codigo);
        case 5:
            return preg_match('/^A106\.1\.1\-05-\d{2}$/', $codigo);
        case 6:
            return preg_match('/^A106\.1\.2\-01-\d{2}$/', $codigo);
        case 7:
            return preg_match('/^A106\.1\.2\-02-\d{2}$/', $codigo);
        case 8:
            return preg_match('/^A106\.1\.2\-03-\d{2}$/', $codigo);
        case 9:
            return preg_match('/^A106\.1\.2\-04-\d{2}$/', $codigo);
        case 10:
            return preg_match('/^A106\.1\.2\-05-\d{2}$/', $codigo);
        case 11:
            return preg_match('/^A106\.1\.3\-01-\d{2}$/', $codigo);
        case 12:
            return preg_match('/^A106\.1\.3\-02-\d{2}$/', $codigo);
        case 13:
            return preg_match('/^A106\.1\.4\-01-\d{2}$/', $codigo);
        case 14:
            return preg_match('/^A106\.1\.4\-02-\d{2}$/', $codigo);
        case 15:
            return preg_match('/^A106\.1\.4\-03-\d{2}$/', $codigo);
        case 16:
            return preg_match('/^A106\.1\.4\-04-\d{2}$/', $codigo);
        case 17:
            return preg_match('/^A106\.1\.5\-01-\d{2}$/', $codigo);
        case 18:
            return preg_match('/^A106\.1\.5\-02-\d{2}$/', $codigo);
        case 19:
            return preg_match('/^A106\.1\.6\-01-\d{2}$/', $codigo);
        case 20:
            return preg_match('/^A106\.1\.6\-02-\d{2}$/', $codigo);
        case 21:
            return preg_match('/^A106\.1\.6\-03-\d{2}$/', $codigo);
        default:
            return false;
    }
}
// Función para verificar si el código ya existe
function isCodeUnique($conn, $codigo) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM detalles WHERE codigo = :codigo");
    $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchColumn() == 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['serie_id']) && isset($_POST['descripcion']) && isset($_POST['codigo'])) {
        $serie_id = intval($_POST['serie_id']);
        $descripcion = $_POST['descripcion'];
        $codigo = $_POST['codigo'];

        // Validar el formato del código
        if (!isValidCode($serie_id, $codigo)) {
            echo json_encode(['error' => 'Formato de código inválido']);
            exit();
        }

        try {
            $conn = getDBConnection($servername, $dbname, $username, $password);

            // Verificar si el código ya existe
            if (!isCodeUnique($conn, $codigo)) {
                echo json_encode(['error' => 'El código ya existe']);
                $conn = null;
                exit();
            }

            // Inserción de datos
            $stmt = $conn->prepare("
                INSERT INTO detalles (serie_id, descripcion, codigo) 
                VALUES (:serie_id, :descripcion, :codigo)
            ");
            $stmt->bindParam(':serie_id', $serie_id, PDO::PARAM_INT);
            $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
            $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
            $stmt->execute();

            echo json_encode(['success' => 'Datos insertados correctamente']);

        } catch(PDOException $e) {
            echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
        }

        // Cerrar conexión
        $conn = null;
    } else {
        echo json_encode(['error' => 'Datos incompletos']);
    }
} else {
    echo json_encode(['error' => 'Método de solicitud no permitido']);
}
?>
