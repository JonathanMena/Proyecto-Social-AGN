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

// Manejar solicitud GET para obtener detalles de una serie documental
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['serie_id'])) {
    $serie_id = intval($_GET['serie_id']);
    $conn = getDBConnection($servername, $dbname, $username, $password);

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

        if ($result) {
            echo json_encode($result);
        } else {
            echo json_encode(['error' => 'No se encontraron detalles para la serie documental con ID ' . $serie_id]);
        }

    } catch(PDOException $e) {
        echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
    }

    $conn = null;

// Manejar solicitud GET para verificar si el código existe (excluyendo un ID específico)
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['codigo']) && isset($_GET['excludeId'])) {
    $codigo = $_GET['codigo'];
    $excludeId = intval($_GET['excludeId']);
    $conn = getDBConnection($servername, $dbname, $username, $password);

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
        echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
    }

    $conn = null;

// Manejar solicitud POST para insertar nuevos detalles
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection($servername, $dbname, $username, $password);
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['descripcion']) && isset($data['serie_id']) && isset($data['codigo'])) {
        $descripcion = $data['descripcion'];
        $serie_id = intval($data['serie_id']);
        $codigo = $data['codigo'];

        try {
            // Insertar nuevo detalle
            $stmt = $conn->prepare("
                INSERT INTO detalles (serie_id, descripcion, codigo) 
                VALUES (:serie_id, :descripcion, :codigo)
            ");
            $stmt->bindParam(':serie_id', $serie_id, PDO::PARAM_INT);
            $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
            $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => 'Información añadida con éxito']);
            } else {
                echo json_encode(['error' => 'Error al insertar los datos']);
            }

        } catch(PDOException $e) {
            echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
        }

    } else {
        echo json_encode(['error' => 'Datos del formulario incompletos']);
    }

    $conn = null;

// Manejar solicitud PUT para actualizar detalles existentes
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $conn = getDBConnection($servername, $dbname, $username, $password);
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id']) && isset($data['descripcion']) && isset($data['codigo'])) {
        $id = intval($data['id']);
        $descripcion = $data['descripcion'];
        $codigo = $data['codigo'];

        try {
            // Validar que el código no esté duplicado
            $stmt = $conn->prepare("
                SELECT COUNT(*) 
                FROM detalles 
                WHERE codigo = :codigo AND id != :id
            ");
            $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                echo json_encode(['error' => 'El código ya está siendo utilizado por otro detalle.']);
                $conn = null;
                exit();
            }

            // Actualizar detalle existente
            $stmt = $conn->prepare("
                UPDATE detalles 
                SET descripcion = :descripcion, codigo = :codigo 
                WHERE id = :id
            ");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
            $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => 'Información actualizada con éxito']);
            } else {
                echo json_encode(['error' => 'Error al actualizar los datos']);
            }

        } catch(PDOException $e) {
            echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
        }

    } else {
        echo json_encode(['error' => 'Datos del formulario incompletos']);
    }

    $conn = null;

// Manejar solicitud DELETE para borrar detalles existentes
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $conn = getDBConnection($servername, $dbname, $username, $password);
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id'])) {
        $id = intval($data['id']);

        try {
            // Borrar detalle existente
            $stmt = $conn->prepare("
                DELETE FROM detalles 
                WHERE id = :id
            ");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => 'Información borrada con éxito']);
            } else {
                echo json_encode(['error' => 'Error al borrar los datos']);
            }

        } catch(PDOException $e) {
            echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
        }

    } else {
        echo json_encode(['error' => 'Datos del formulario incompletos']);
    }

    $conn = null;

} else {
    echo json_encode(['error' => 'Solicitud no válida']);
}
?>
