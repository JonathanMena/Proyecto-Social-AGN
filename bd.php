<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "archivo_general";

function getDBConnection($servername, $dbname, $username, $password) {
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        respondWithError('Error: ' . $e->getMessage());
    }
}

function respondWithError($message) {
    echo json_encode(['error' => $message]);
    exit();
}

function respondWithSuccess($message) {
    echo json_encode(['success' => $message]);
    exit();
}

function handleGetRequest($conn) {
    if (isset($_GET['serie_id'])) {
        $serie_id = intval($_GET['serie_id']);
        fetchSeriesDetails($conn, $serie_id);
    } elseif (isset($_GET['codigo']) && isset($_GET['excludeId'])) {
        $codigo = $_GET['codigo'];
        $excludeId = intval($_GET['excludeId']);
        checkIfCodigoExists($conn, $codigo, $excludeId);
    } else {
        respondWithError('Solicitud GET no válida');
    }
}

function fetchSeriesDetails($conn, $serie_id) {
    try {
        $stmt = $conn->prepare("
            SELECT 
                d.id, 
                s.nombre AS serie_nombre, 
                d.descripcion, 
                d.codigo
            FROM 
                detalles d
            INNER JOIN 
                series_documentales s
            ON 
                d.serie_id = s.id
            WHERE 
                d.serie_id = :serie_id
        ");
        $stmt->bindParam(':serie_id', $serie_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result ?: ['error' => 'No se encontraron detalles para la serie documental con ID ' . $serie_id]);
    } catch(PDOException $e) {
        respondWithError('Error: ' . $e->getMessage());
    }
}

function checkIfCodigoExists($conn, $codigo, $excludeId) {
    try {
        $stmt = $conn->prepare("
            SELECT COUNT(*) 
            FROM detalles 
            WHERE codigo = :codigo AND id != :excludeId
        ");
        $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
        $stmt->bindParam(':excludeId', $excludeId, PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        echo json_encode(['exists' => $count > 0]);
    } catch(PDOException $e) {
        respondWithError('Error: ' . $e->getMessage());
    }
}

function handlePostRequest($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['descripcion']) && isset($data['serie_id']) && isset($data['codigo'])) {
        insertNewDetail($conn, $data['descripcion'], intval($data['serie_id']), $data['codigo']);
    } else {
        respondWithError('Datos del formulario incompletos');
    }
}

function insertNewDetail($conn, $descripcion, $serie_id, $codigo) {
    try {
        $stmt = $conn->prepare("
            INSERT INTO detalles (serie_id, descripcion, codigo) 
            VALUES (:serie_id, :descripcion, :codigo)
        ");
        $stmt->bindParam(':serie_id', $serie_id, PDO::PARAM_INT);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
        if ($stmt->execute()) {
            respondWithSuccess('Información añadida con éxito');
        } else {
            respondWithError('Error al insertar los datos');
        }
    } catch(PDOException $e) {
        respondWithError('Error: ' . $e->getMessage());
    }
}

function handlePutRequest($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['id']) && isset($data['descripcion']) && isset($data['codigo'])) {
        updateDetail($conn, intval($data['id']), $data['descripcion'], $data['codigo']);
    } else {
        respondWithError('Datos del formulario incompletos');
    }
}

function updateDetail($conn, $id, $descripcion, $codigo) {
    try {
        $stmt = $conn->prepare("
            SELECT COUNT(*) 
            FROM detalles 
            WHERE codigo = :codigo AND id != :id
        ");
        $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            respondWithError('El código ya está siendo utilizado por otro detalle.');
        }

        $stmt = $conn->prepare("
            UPDATE detalles 
            SET descripcion = :descripcion, codigo = :codigo 
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
        if ($stmt->execute()) {
            respondWithSuccess('Información actualizada con éxito');
        } else {
            respondWithError('Error al actualizar los datos');
        }
    } catch(PDOException $e) {
        respondWithError('Error: ' . $e->getMessage());
    }
}

function handleDeleteRequest($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['id'])) {
        deleteDetail($conn, intval($data['id']));
    } else {
        respondWithError('Datos del formulario incompletos');
    }
}

function deleteDetail($conn, $id) {
    try {
        $stmt = $conn->prepare("
            DELETE FROM detalles 
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            respondWithSuccess('Información borrada con éxito');
        } else {
            respondWithError('Error al borrar los datos');
        }
    } catch(PDOException $e) {
        respondWithError('Error: ' . $e->getMessage());
    }
}

$conn = getDBConnection($servername, $dbname, $username, $password);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        handleGetRequest($conn);
        break;
    case 'POST':
        handlePostRequest($conn);
        break;
    case 'PUT':
        handlePutRequest($conn);
        break;
    case 'DELETE':
        handleDeleteRequest($conn);
        break;
    default:
        respondWithError('Solicitud no válida');
}

$conn = null;
?>
