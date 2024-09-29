<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedIn'])) {
    // Si no ha iniciado sesión, redirige al login
    header('Location: login.html');
    exit();
}

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
  <link rel="stylesheet" href="CSS/Style.css"/>
   <!-- Bootstrap CSS -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- jQuery -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <!-- Bootstrap Bundle con Popper.js -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="icon" href="img/Logo-AGN.png" type="image/png" />
  <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        .container {
            display: flex;
            height: 100vh;
            padding: 20px;
        }
        .btn-submit {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-submit:hover {
            background-color: #218838;
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
        <div class="pdf-list">
            <h2>Lista de PDFs</h2>
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
        <form id="memo" class="form" name="pdf" action="PDF_editable.php" method="POST">
        <h2>Formulario editable</h2>
              <div class="form-group">
                  <label for="relativo" class="label">Codigo</label>
                  <input type="text" class="input" name="relativo" id="relativo" required>
              </div>
              <div class="form-group">
                  <label for="referencia" class="label">Referencia</label>
                  <input type="text" class="input" name="refencia" id="referencia" required>
              </div>
              <div class="form-group">
                  <label for="destinatario" class="label">Destinatario</label>
                  <input type="text" class="input" name="destinatario" id="destinatario" required>
              </div>
              <div class="form-group">
                  <label for="cargo1" class="label">cargo</label>
                  <input type="text" class="input" name="cargo1" id="cargo1" required>
              </div>
              <div class="form-group">
                  <label for="remitente" class="label">Remitente</label>
                  <input type="text" class="input" name="remitente" id="remitente" required>
              </div>
              <div class="form-group">
                  <label for="cargo2" class="label">cargo</label>
                  <input type="text" class="input" name="cargo2" id="cargo2" required>
              </div>
              <div class="form-group">
                <label for="asunto" class="label">Asunto</label>
                <textarea class="input" name="asunto" id="asunto" rows="4" cols="50" required></textarea>
              </div>
              <div class="form-group">
                  <button class="button" type="submit">Confirmar</button>
              </div>
        </form>
    </div>
    <footer class="footer">
        <div class="container">
            <div class="row">
            <div class="col-md-3">
                <img src="img/Logo 1.png" alt="Logo" class="img-fluid" style="max-width: 190px" />
            </div>
            <div class="col-md-3">
                <h5>Contactos</h5>
                <p>comunicaciones@cultura.gob.sv</>
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
    </footer>
</body>
</html>

<?php
// Cerrar conexión
$conn->close();
?>
