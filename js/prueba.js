document.addEventListener('DOMContentLoaded', function() {
    ajustarFooter();

    const buttons = document.querySelectorAll('button[id^="btn-"]');

    buttons.forEach(button => {
        button.addEventListener('click', function() {
            const serieId = this.id.split('-')[1]; // Obtener el ID de la serie del ID del botón
            const containerId = 'serie-documental-container-' + serieId;

            // Ocultar todos los contenedores de serie-documental-container
            const allContainers = document.querySelectorAll('[id^="serie-documental-container-"]');
            allContainers.forEach(container => {
                container.style.display = 'none';
            });

            // Mostrar solo el contenedor correspondiente al botón actual
            const container = document.getElementById(containerId);
            container.style.display = 'block';

            // Realizar la solicitud Fetch
            fetch(`bd.php?serie_id=${serieId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                // Procesar la respuesta JSON
                const container = document.getElementById(containerId);
                container.innerHTML = ''; // Limpiar el contenedor

                if (data.error) {
                    console.error(data.error);
                } else {
                    data.forEach(serie => {
                        const card = document.createElement('div');
                        card.className = 'card mt-2';

                        const cardBody = document.createElement('div');
                        cardBody.className = 'card-body';
                        cardBody.innerHTML = `
                            <p>${serie.descripcion}</p>
                            <p>Código: ${serie.codigo}</p>
                            <button onclick="showEditModal(${serie.id}, ${serie.serie_id}, '${serie.descripcion}', '${serie.codigo}')">Editar</button>
                            <button onclick="deleteDetail(${serie.id}, ${serie.serie_id})">Borrar</button>
                        `;

                        card.appendChild(cardBody);
                        container.appendChild(card);
                    });
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });

    const form = document.getElementById('detalle-form');
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        
        const formData = new FormData(form);

        fetch('insertar.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const responseMessage = document.getElementById('response-message');
            if (data.error) {
                responseMessage.textContent = data.error;
                responseMessage.classList.add('text-danger');
            } else {
                responseMessage.textContent = 'Datos insertados correctamente';
                responseMessage.classList.add('text-success');
                form.reset();
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Ajustar el footer
    window.addEventListener('resize', ajustarFooter);
    document.addEventListener('DOMContentLoaded', ajustarFooter);
});

function ajustarFooter() {
    var footer = document.querySelector('.footer');
    var bodyHeight = document.body.offsetHeight;
    var windowHeight = window.innerHeight;
    if (bodyHeight < windowHeight) {
        footer.style.position = 'absolute';
        footer.style.bottom = '0';
    } else {
        footer.style.position = 'relative';
    }
}

function showEditModal(id, descripcion, codigo) {
    document.getElementById('editId').value = id;
    document.getElementById('editDescripcion').value = descripcion;
    document.getElementById('editCodigo').value = codigo;
    document.getElementById('editModal').style.display = 'block';
}

function updateDetail() {
    const id = document.getElementById('editId').value;
    const descripcion = document.getElementById('editDescripcion').value;
    const codigo = document.getElementById('editCodigo').value;

    // Realizar la validación del código antes de actualizar en la base de datos
    fetch(`bd.php?codigo=${codigo}&excludeId=${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            // Si la consulta devuelve resultados, significa que el código ya está en uso por otro detalle
            if (data.length > 0) {
                throw new Error('El código ya está siendo utilizado por otro detalle.');
            }

            // Si la validación pasa, procedemos a actualizar el detalle en la base de datos
            fetch('bd.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id, descripcion, codigo })
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => { throw new Error(text) });
                }
                return response.json();
            })
            .then(data => {
                alert(data.success || data.error);
                document.getElementById('editModal').style.display = 'none';
                fetchSeries();
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        })
        .catch(error => {
            alert('Error: Codigo ya ingresado ' + error.message);
        });
}

function deleteDetail(id, serieId) {
    if (confirm('¿Estás seguro de que deseas borrar este detalle?')) {
        fetch('bd.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id, serieId }) // Incluir serie_id en la solicitud
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => { throw new Error(text) });
            }
            return response.json();
        })
        .then(data => {
            alert(data.success || data.error);
            fetchSeries();
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    }
}

function fetchSeries() {
    fetch('bd.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            const serieList = document.getElementById('serieList');
            serieList.innerHTML = '';

            data.forEach(serie => {
                const serieDiv = document.createElement('div');
                serieDiv.innerHTML = `
                    <p>ID: ${serie.id}</p>
                    <p>Nombre: ${serie.nombre}</p>
                    <p>Descripción: ${serie.descripcion}</p>
                    <p>Código: ${serie.codigo}</p>
                    <button onclick="showEditModal(${serie.id}, ${serie.serie_id}, '${serie.descripcion}', '${serie.codigo}')">Editar</button>
                    <button onclick="deleteDetail(${serie.id}, ${serie.serie_id})">Borrar</button>
                `;
               
                serieList.appendChild(serieDiv);
            });
        })
        .catch(error => console.error('Error:', error));
}

document.getElementById('detalle-form').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);
    fetch('api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('response-message').textContent = data.success ? 'Información añadida con éxito' : 'Error: ' + data.error;
    })
    .catch(error => {
        document.getElementById('response-message').textContent = 'Error: ' + error.message;
    });
});

document.querySelector('memo').addEventListener('submit', function(event) {
    event.preventDefault(); // Evitar el envío normal del formulario

    // Obtener los datos del formulario
    const formData = new FormData(document.querySelector('.form'));

    // Construir la URL con los datos del formulario
    const queryParams = new URLSearchParams(formData).toString();

});

function mostrarInformacion(info) {
    var element = document.getElementById("info-" + info);
    var elements = document.querySelectorAll('[id^="info-"]');

    elements.forEach(function (el) {
        if (el.id !== "info-" + info) {
            el.style.display = "none";
        }
    });

    if (element) {
        if (element.style.display === "none") {
            element.style.display = "block";
        } else {
            element.style.display = "none";
        }

        var icono = document.getElementById("icono-" + info);
        if (icono) {
            if (element.style.display === "block") {
                icono.classList.replace("bi-caret-down", "bi-caret-up");
            } else {
                icono.classList.replace("bi-caret-up", "bi-caret-down");
            }
        } else {
            console.error("Elemento con ID 'icono-" + info + "' no encontrado.");
        }
    } else {
        console.error("Elemento con ID 'info-" + info + "' no encontrado.");
    }

    ajustarFooter();
}
