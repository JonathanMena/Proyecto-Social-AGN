<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Archivo General de la Nación</title>
  <link rel="stylesheet" href="CSS/Style.css" />
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- jQuery -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <!-- Bootstrap Bundle con Popper.js -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="icon" href="img/Logo-AGN.png" type="image/png" />
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
      <div class="row justify-content-center align-items-center w-100">
        <div class="col-md-6 text-center">
          <a href=".">
            <img src="img/Logo AGN blanco.png" alt="Logo" class="img-fluid" style="max-width: 250px" />
          </a>
        </div>
        <div class="col-md-12 text-center">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0 d-flex justify-content-around">
            <li class="nav-item">
              <button class="btn text-white" onclick="mostrarInformacion('direccion')" title="A106.1.1">
                DIRECCIÓN<i id="icono-direccion" class="bi bi-caret-down"></i>
              </button>
            </li>
            <li class="nav-item">
              <button class="btn text-white" onclick="mostrarInformacion('departamento')" title="A106.1.2">
                PROCESOS TÉCNICOS<br> ARCHIVÍSTICOS INTERNOS<i id="icono-departamento" class="bi bi-caret-down"></i>
              </button>
            </li>
            <li class="nav-item">
              <button class="btn text-white" onclick="mostrarInformacion('admo')" title="A106.1.3">
                ADMINISTRACIÓN<i id="icono-administracion" class="bi bi-caret-down"></i>
              </button>
            </li>
            <li class="nav-item">
              <button class="btn text-white" onclick="mostrarInformacion('preservación')" title="A106.1.4">
                PRESERVACIÓN<br>DOCUMENTAL<i id="icono-preservacion" class="bi bi-caret-down"></i>
              </button>
            </li>
            <li class="nav-item">
              <button class="btn text-white" onclick="mostrarInformacion('biblioteca')" title="A106.1.5">
                BIBLIOTECA ESPECIALIZADA<br>EN HISTORIA<i id="icono-biblioteca" class="bi bi-caret-down"></i>
              </button>
            </li>
            <li class="nav-item">
              <button class="btn text-white" onclick="mostrarInformacion('atención')" title="A106.1.6">
                ATENCIÓN AL USUARIO<br> EN SALA DE CONSULTA<i id="icono-cliente" class="bi bi-caret-down"></i>
              </button>
            </li>
            <li class="nav-item dropdown">
              <button class="btn text-white dropdown-toggle" id="navbarDropdownMenuLink" role="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                FORMULARIOS
              </button>
              <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                <li><a class="dropdown-item"  onclick="mostrarInformacion('formularios 1')" >Form Añadir a BD</a></li>
                <li><a class="dropdown-item" onclick="mostrarInformacion('formularios 2')">Form Bienes Muebles</a></li>
                <li><a class="dropdown-item" onclick="mostrarInformacion('formularios 3')">Form de Memorandum</a></li>
                <!-- Agrega más elementos de menú según tus necesidades -->
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </nav>
  <div class="content">
    <div id="info-direccion" style="display: none">
      <div class="row justify-content-center">
        <div class="col-md-6 mb-4">
          <div class="text-center">
            <button class="btn text-dark mb-3" id="btn-1">
              <h4>Correspondencia</h4>A106.1.1-01
            </button>
            <div class="container mt-4">
              <div id="serie-documental-container-1"></div>
            </div>
          </div>
        </div>
        <div class="col-md-6 mb-4">
          <div class="text-center">
            <button id="btn-2" class="btn text-dark mb-3">
              <h4>Informes y Proyectos</h4>A106.1.1-02
            </button>
            <div class="container mt-4">
              <div id="serie-documental-container-2" class="mt-4"></div>
            </div>
          </div>
        </div>
        <div class="col-md-6 mb-4">
          <div class="text-center">
            <button id="btn-3" class="btn text-dark">
              <h4>Normativa</h4>A106.1.1-03
            </button>
            <div class="container mt-4">
              <div id="serie-documental-container-3" class="mt-4"></div>
            </div>
          </div>
        </div>
        <div class="col-md-6 mb-4">
          <div class="text-center">
            <button id="btn-4" class="btn text-dark">
              <h4>Servicios Archivisticos</h4>A106.1.1-04
            </button>
            <div class="container mt-4">
              <div id="serie-documental-container-4" class="mt-4"></div>
            </div>
          </div>
        </div>
        <div class="col-md-12 mb-4">
          <div class="text-center">
            <button id="btn-5" class="btn text-dark">
              <h4>Expedientes de Procesos administrativos</h4>A106.1.1-05
            </button>
            <div class="container mt-4">
              <div id="serie-documental-container-5" class="mt-4"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div id="info-departamento" style="display: none">
      <div class="row justify-content-center">
        <div class="col-md-6 mb-4">
          <div class="text-center">
            <button class="btn text-dark mb-3" id="btn-6">
              <h4>Expediente comisión Técnica</h4>A106.1.2-01
            </button>
            <div class="container mt-4">
              <div id="serie-documental-container-6"></div>
            </div>
          </div>
        </div>
        <div class="col-md-6 mb-4">
          <div class="text-center">
            <button id="btn-7" class="btn text-dark">
              <h4>Descripción Archivística</h4>A106.1.2-02
            </button>
            <div class="container mt-4">
              <div id="serie-documental-container-7" class="mt-4"></div>
            </div>
          </div>
        </div>
        <div class="col-md-6 mb-4">
          <div class="text-center">
            <button class="btn text-dark mb-3" id="btn-8">
              <h4>Correspondencia</h4>A106.1.2-03
            </button>
            <div class="container mt-4">
              <div id="serie-documental-container-8"></div>
            </div>
          </div>
        </div>
        <div class="col-md-6 mb-4">
          <div class="text-center">
            <button class="btn text-dark mb-3" id="btn-9">
              <h4>Expediente Investigación Institucional: <br>Historica y Documental</h4>A106.1.2-04
            </button>
            <div class="container mt-4">
              <div id="serie-documental-container-9"></div>
            </div>
          </div>
        </div>
        <div class="col-md-12 mb-4">
          <div class="text-center">
            <button id="btn-10" class="btn text-dark">
              <h4>Expedientes de formación y Capacitación externa</h4>A106.1.2-05
            </button>
            <div class="container mt-4">
              <div id="serie-documental-container-10" class="mt-4"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div id="info-admo" style="display: none">
      <div class="row justify-content-center">
        <div class="col-md-6 mb-4">
          <div class="text-center">
            <button class="btn text-dark mb-3" id="btn-11">
              <h4>Correspondencia</h4>A106.1.3-01
            </button>
            <div class="container mt-4">
              <div id="serie-documental-container-11"></div>
            </div>
          </div>
        </div>
        <div class="col-md-6 mb-4">
          <div class="text-center">
            <button id="btn-12" class="btn text-dark">
              <h4>Expedientes de procesos<br />administrativos</h4>A106.1.3-02
            </button>
            <div class="container mt-4">
              <div id="serie-documental-container-12" class="mt-4"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div id="info-preservación" style="display: none">
      <div class="row justify-content-center">
        <div class="col-md-6 mb-4">
          <div class="text-center">
            <button class="btn text-dark mb-3" id="btn-13">
              <h4>Correspondencia</h4>A106.1.4-01
            </button>
            <div class="container mt-4">
              <div id="serie-documental-container-13"></div>
            </div>
          </div>
        </div>
        <div class="col-md-6 mb-4">
          <div class="text-center">
            <button id="btn-14" class="btn text-dark">
              <h4>Expediente de conservación y Restauración</h4>A106.1.4-02
            </button>
            <div class="container mt-4">
              <div id="serie-documental-container-14" class="mt-4"></div>
            </div>
          </div>
        </div>
        <div class="col-md-6 mb-4">
          <div class="text-center">
            <button class="btn text-dark mb-3" id="btn-15">
              <h4>Expediente de Digitalización</h4>A106.1.4-03
            </button>
            <div class="container mt-4">
              <div id="serie-documental-container-15"></div>
            </div>
          </div>
        </div>
        <div class="col-md-6 mb-4">
          <div class="text-center">
            <button class="btn text-dark mb-3" id="btn-16">
              <h4>Expediente de formación y capacitación externa</h4>A106.1.4-04
            </button>
            <div class="container mt-4">
              <div id="serie-documental-container-16"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div id="info-biblioteca" style="display: none">
      <div class="row justify-content-center">
        <div class="col-md-6 mb-4">
          <div class="text-center">
            <button class="btn text-dark mb-3" id="btn-17">
              <h4>Correspondencia</h4>A106.1.5-01
            </button>
            <div class="container mt-4">
              <div id="serie-documental-container-17"></div>
            </div>
          </div>
        </div>
        <div class="col-md-6 mb-4">
          <div class="text-center">
            <button id="btn-18" class="btn text-dark">
              <h4>Expedientes de formación<br>y capacitación externa</h4>A106.1.5-02
            </button>
            <div class="container mt-4">
              <div id="serie-documental-container-18" class="mt-4"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div id="info-atención" style="display: none">
      <div class="row justify-content-center">
        <div class="col-md-6 mb-4">
          <div class="text-center">
            <button class="btn text-dark mb-3" id="btn-19">
              <h4>Correspondencia</h4>A106.1.6-01
            </button>
            <div class="container mt-4">
              <div id="serie-documental-container-19"></div>
            </div>
          </div>
        </div>
        <div class="col-md-6 mb-4">
          <div class="text-center">
            <button id="btn-20" class="btn text-dark">
              <h4>Expedientes estadistico</h4>A106.1.6-02
            </button>
            <div class="container mt-4">
              <div id="serie-documental-container-20" class="mt-4"></div>
            </div>
          </div>
        </div>
        <div class="col-md-12 mb-4">
          <div class="text-center">
            <button id="btn-21" class="btn text-dark">
              <h4>Expediente de Formación y capacitación externa</h4>A106.1.6-03
            </button>
            <div class="container mt-4">
              <div id="serie-documental-container-21" class="mt-4"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    

    <div id="info-formularios 1" style="display: none">
      <div class="text-center">
        <h4>Añadir a la base de datos</h4>
        <form id="detalle-form">
          <div class="mb-3">
            <label for="serie_id" class="form-label">Serie Id</label>
            <select class="form-select" id="serie_id" name="serie_id" aria-label="Size 3 select example" required>
              <option selected>seleccionar la área</option>
              <option value="1" >Correspondencia Direccion</option> 
              <option value="2">Informes y Proyectos</option>
              <option value="3">Normativa</option>
              <option value="4">Servicios Archivisticos</option>
              <option value="5">Expedientes Administrativos</option>
              <option value="6">Expediente comisión técnica</option>
              <option value="7">Descripción Archivísticas</option>
              <option value="8">Correspondencia Pros. Téc. Arch.</option>
              <option value="9">Expe. Invs. Insti. His. Doc.</option>
              <option value="10">Expe. Formación capacitación Ext. Pros. Tec.</option>
              <option value="11">Correspondencia Admi.</option>
              <option value="12">Expedientes Procesos Adm.</option>
              <option value="13">Correspondencia Pres. Docum.</option>
              <option value="14">Expe. de conser. y rest.</option>
              <option value="15">Expe de Digitalización</option>
              <option value="16">Expe. formación capacitación Ext. Pres. Docum.</option>
              <option value="17">Correspondencia Biblio. Esp. Hist.</option>
              <option value="18">Expe. Formación capacitación Ext. Biblio. Esp.</option>
              <option value="19">Correspondencia Ate. Usu. Sala de Consulta</option>
              <option value="20">Expeditente estadistico</option>
              <option value="21">Expe. formación capacitación ext. Ate. usu. sala de consulta</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" required></textarea>
          </div>
          <div class="mb-3">
            <label for="codigo" class="form-label">Código</label>
            <input type="text" class="form-control" id="codigo" name="codigo" required />
          </div>
          <button type="submit" class="btn btn-primary">Añadir</button>
        </form>
        <div id="response-message" class="mt-3"></div>
      </div>

    </div>    <div id="info-formularios 2" style="display: none">
      <iframe src="FORM MOV BIENES 2024 (2).pdf" width="100%" height="850px"></iframe>
    </div>

    <div id="info-formularios 3" style="display:none">
    <div class="text-center">
    <form id="memo" class="form" name="pdf" action="PDF_editable.php" method="POST">
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
                <label for="remitente" class="label">Remitente</label>
                <input type="text" class="input" name="remitente" id="remitente" required>
            </div>
            <div class="form-group">
                <label for="asunto" class="label">Asunto</label>
                <input type="text" class="input" name="asunto" id="asunto" required>
            </div>
            <div class="form-group">
                <label for="mensaje" class="label">Mensaje</label>
                <input type="text" class="input" name="mensaje" id="mensaje" required>
            </div>
         
            <div class="form-group">
                <button class="button" type="submit">Confirmar</button>
            </div>
        </form>
    </div>
    </div>
<!-- Modal para editar detalles -->
<div id="editModal" style="display:none;">
    <input type="hidden" id="editId">
    <label for="editDescripcion">Descripción:</label>
    <input type="text" id="editDescripcion">
    <label for="editCodigo">Código:</label>
    <input type="text" id="editCodigo">
    <button onclick="updateDetail()">Guardar Cambios</button>
    <button onclick="document.getElementById('editModal').style.display='none'">Cancelar</button>
</div>
    <script>
      document
        .getElementById("serie_id")
        .addEventListener("change", function () {
          const serieId = this.value;
          const codigoInput = document.getElementById("codigo");
          switch (serieId) {
            case "1":
              codigoInput.value = "A106.1.1-01-";
              codigoInput.readOnly = false;
              break;
            case "2":
              codigoInput.value = "A106.1.1-02-";
              codigoInput.readOnly = false;
              break;
            case "3":
              codigoInput.value = "A106.1.1-03-";
              codigoInput.readOnly = false;
              break;
            case "4":
              codigoInput.value = "A106.1.1-04-";
              codigoInput.readOnly = false;
              break;
            case "5":
              codigoInput.value = "A106.1.1-05-";
              codigoInput.readOnly = false;
              break;
            case "6":
              codigoInput.value = "A106.1.2-01-";
              codigoInput.readOnly = false;
              break;
            case "7":
              codigoInput.value = "A106.1.2-02-";
              codigoInput.readOnly = false;
              break;
            case "8":
              codigoInput.value = "A106.1.2-03-";
              codigoInput.readOnly = false;
              break;
            case "9":
              codigoInput.value = "A106.1.2-04-";
              codigoInput.readOnly = false;
              break;
            case "10":
              codigoInput.value = "A106.1.2-05-";
              codigoInput.readOnly = false;
              break;
            case "11":
              codigoInput.value = "A106.1.3-01-";
              codigoInput.readOnly = false;
              break;
            case "12":
              codigoInput.value = "A106.1.3-02-";
              codigoInput.readOnly = false;
              break;
            case "13":
              codigoInput.value = "A106.1.4-01-";
              codigoInput.readOnly = false;
              break;
            case "14":
              codigoInput.value = "A106.1.4-02-";
              codigoInput.readOnly = false;
              break;
            case "15":
              codigoInput.value = "A106.1.4-03-";
              codigoInput.readOnly = false;
              break;
            case "16":
              codigoInput.value = "A106.1.4-04-";
              codigoInput.readOnly = false;
              break;
            case "17":
              codigoInput.value = "A106.1.5-01-";
              codigoInput.readOnly = false;
              break;
            case "18":
              codigoInput.value = "A106.1.5-02-";
              codigoInput.readOnly = false;
              break;
            case "19":
              codigoInput.value = "A106.1.6-01-";
              codigoInput.readOnly = false;
              break;
            case "20":
              codigoInput.value = "A106.1.6-02-";
              codigoInput.readOnly = false;
              break;
            case "21":
              codigoInput.value = "A106.1.6-03-";
              codigoInput.readOnly = false;
              break;
          }
        });
    </script>
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
  <script src="js/prueba.js"></script>
</body>
</html>