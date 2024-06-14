<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "archivo_general";

$message = ""; // Variable para almacenar mensajes

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Manejar la eliminación de PDF
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    
    // Obtener la ruta del archivo
    $stmt = $conn->prepare("SELECT file_path FROM pdfs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows == 1) {
        $stmt->bind_result($filePath); // Asignar el resultado a $filePath
        $stmt->fetch(); // Obtener el valor
        
        // Eliminar el archivo del servidor
        if (unlink($filePath)) {
            // Si se elimina correctamente, eliminar el registro de la base de datos
            $deleteStmt = $conn->prepare("DELETE FROM pdfs WHERE id = ?");
            $deleteStmt->bind_param("i", $id);
            if ($deleteStmt->execute()) {
                $message = "El archivo PDF se ha eliminado correctamente.";
            } else {
                $message = "Error al eliminar el registro de la base de datos: " . $deleteStmt->error;
            }
            $deleteStmt->close();
        } else {
            $message = "Error al eliminar el archivo del servidor.";
        }
    } else {
        $message = "PDF no encontrado.";
    }
    
    $stmt->close();
}

// Manejar la subida de PDF
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['pdf'])) {
    $pdf = $_FILES['pdf']['tmp_name'];
    $pdfName = basename($_FILES['pdf']['name']);
    $uploadDir = 'uploads/';
    $uploadFile = $uploadDir . $pdfName;

    // Crear el directorio de subida si no existe
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (move_uploaded_file($pdf, $uploadFile)) {
        $stmt = $conn->prepare("INSERT INTO pdfs (file_name, file_path) VALUES (?, ?)");
        $stmt->bind_param("ss", $pdfName, $uploadFile);
        
        if ($stmt->execute()) {
            $message = "El archivo PDF se ha subido correctamente.";
        } else {
            $message = "Error al subir el archivo PDF: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $message = "Error al mover el archivo subido.";
    }
}

// Obtener lista de PDFs
$sql = "SELECT id, file_name, file_path FROM pdfs";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Visualiza PDFs en el AGN. Accede y maneja documentos digitalizados de manera sencilla y eficiente con nuestras herramientas avanzadas. Creado por [Calvin Mena].">
  <meta name="keywords" content="AGN Visualizar PDFs, documentos digitalizados, herramientas avanzadas">
  <title>PDFs</title>
  <link rel="stylesheet" href="CSS/Style.css" />
   <!-- Bootstrap CSS -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- jQuery -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <!-- Bootstrap Bundle con Popper.js -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="icon" href="img/Logo-AGN.png" type="image/png" />
  <style>
    html, body {
        height: 100%;
        margin: 0;
    }
    .wrapper {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    .content {
        flex: 1;
    }
    footer {
        background-color: #f1f1f1;
        padding: 10px;
    }
  </style>
</head>

<body>
  <div class="wrapper">
    <nav class="navbar navbar-expand-lg navbar-custom">
      <div class="container-fluid">
        <div class="row justify-content-center align-items-center w-100">
          <div class="col-md-6 text-center">
            <a href=".">
              <img src="img/Logo AGN blanco.png" alt="Logo" class="img-fluid" style="max-width: 250px" />
            </a>
          </div>
        </div>
      </div>
    </nav>
    <div class="content">
      <script>
          function confirmDelete(id) {
              if (confirm("¿Estás seguro de que deseas eliminar este PDF?")) {
                  window.location.href = 'PDFs.php?delete_id=' + id;
              }
          }
      </script>
      <div class="container">
        <h1>Lista de PDFs subidos</h1>
        <ul>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<li>";
                    echo "<a href='".$row['file_path']."' target='_blank'>" . $row['file_name'] . "</a> ";
                    echo "<a href='javascript:void(0);' onclick='confirmDelete(".$row['id'].")'>Eliminar</a>";
                    echo "</li>";
                }
            } else {
                echo "No hay PDFs subidos.";
            }
            ?>
        </ul>
        <?php
        if ($message) {
            echo "<p>" . $message . "</p>";
        }
        ?>
        <form action="PDFs.php" method="post" enctype="multipart/form-data">
            <label for="pdf">Selecciona el archivo PDF:</label>
            <input type="file" name="pdf" id="pdf" accept="application/pdf" required>
            <br><br>
            <input type="submit" value="Subir PDF">
        </form>
      </div>
    </div>
    <footer class="footer">
    <div class="container">
      <div class="row">
        <div class="col-md-3">
          <img src="img/Logo 1.png" alt="Logo" class="img-fluid" style="max-width: 190px" />
        </div>
        <div class="col-md-3">
          <h5>Contactos</h5>
          <p>comunicaciones@cultura.gob.sv</p>
          <p>Teléfono: +503 2221-8847</p>
        </div>
        <div class="col-md-6">
          <h5>
            <a href="https://maps.app.goo.gl/WAkw2KgrLVsAbbYG8" target="_blank" class="location-link">
              <img src="img/logomapa.png" alt="Ubicación" class="location-logo" /></a>Ubicación y Referencia
          </h5>
          <p>
            Archivo General de la Nación. <br />
            17 avenida Sur, Calle Ruben Dario #1003, Edificio Mercury. Esquina
            opuesta al fondo social para la vivienda, en frente de canchas de
            la Universidad Tecnologica.
          </p>
        </div>
      </div>
    </div>
  </footer>
  </div>
</body>
</html>

<?php
// Cerrar conexión
$conn->close();
?>
