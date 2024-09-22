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
    $mes = date('m'); // Obtiene el mes actual (puedes cambiarlo si es necesario)
    $año = date('Y'); // Obtiene el año actual (puedes cambiarlo si es necesario)
    switch ($serie_id) {
        case 1:
            return preg_match('/^A\d{1}\-'. $mes .'\-' . $año . '$/', $codigo);
        case 2:
            return preg_match('/^B\d{1}\-'. $mes .'\-' . $año . '$/', $codigo);
        case 3:
            return preg_match('/^C\d{1}\-'. $mes .'\-' . $año . '$/', $codigo);
        case 4:
            return preg_match('/^D\d{1}\-'. $mes .'\-' . $año . '$/', $codigo);
        case 5:
            return preg_match('/^E\d{1}\-'. $mes .'\-' . $año . '$/', $codigo);
        case 6:
            return preg_match('/^F\d{1}\-'. $mes .'\-' . $año . '$/', $codigo);
        case 7:
            return preg_match('/^G\d{1}\-'. $mes .'\-' . $año . '$/', $codigo);
        case 8:
            return preg_match('/^H\d{1}\-'. $mes .'\-' . $año . '$/', $codigo);
        case 9:
            return preg_match('/^I\d{1}\-'. $mes .'\-' . $año . '$/', $codigo);
        case 10:
            return preg_match('/^J\d{1}\-'. $mes .'\-' . $año . '$/', $codigo);
        case 11:
            return preg_match('/^K\d{1}\-'. $mes .'\-' . $año . '$/', $codigo);
        case 12:
            return preg_match('/^L\d{1}\-'. $mes .'\-' . $año . '$/', $codigo);
        case 13:
            return preg_match('/^M\d{1}\-'. $mes .'\-' . $año . '$/', $codigo);
        case 14:
            return preg_match('/^N\d{1}\-'. $mes .'\-' . $año . '$/', $codigo);
        case 15:
            return preg_match('/^O\d{1}\-'. $mes .'\-' . $año . '$/', $codigo);
        case 16:
            return preg_match('/^P\d{1}\-'. $mes .'\-' . $año . '$/', $codigo);
        case 17:
            return preg_match('/^Q\d{1}\-'. $mes .'\-' . $año . '$/', $codigo);
        case 18:
            return preg_match('/^R\d{1}\-'. $mes .'\-' . $año . '$/', $codigo);
        case 19:
            return preg_match('/^S\d{1}\-'. $mes .'\-' . $año . '$/', $codigo);
        case 20:
            return preg_match('/^T\d{1}\-'. $mes .'\-' . $año . '$/', $codigo);
        case 21:
            return preg_match('/^U\d{1}\-'. $mes .'\-' . $año . '$/', $codigo);
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
