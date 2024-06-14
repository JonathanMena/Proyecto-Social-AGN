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
            if (container) {
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
                                <button onclick="showEditModal(${serie.id}, '${serie.descripcion}', '${serie.codigo}')">Editar</button>
                                <button onclick="deleteDetail(${serie.id}, ${serie.serie_id})">Borrar</button>
                            `;

                            card.appendChild(cardBody);
                            container.appendChild(card);
                        });
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    });

    const form = document.getElementById('detalle-form');
    if (form) {
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
                if (responseMessage) {
                    if (data.error) {
                        responseMessage.textContent = data.error;
                        responseMessage.classList.add('text-danger');
                    } else {
                        responseMessage.textContent = 'Datos insertados correctamente';
                        responseMessage.classList.add('text-success');
                        form.reset();
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }

    window.addEventListener('resize', ajustarFooter);

    const boxes = document.querySelectorAll('.box');
    boxes.forEach(box => {
        // Crear el botón de cierre
        const closeButton = document.createElement('button');
        closeButton.textContent = 'Cerrar';
        closeButton.classList.add('close-button');
        box.appendChild(closeButton);

        // Agregar evento click al botón de cierre
        closeButton.addEventListener('click', function(event) {
            event.stopPropagation(); // Prevenir que el click en el botón active el evento del box
            box.classList.remove('active');
        });

        // Agregar evento click a la caja
        box.addEventListener('click', function() {
            boxes.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });
});

function ajustarFooter() {
    const footer = document.querySelector('.footer');
    if (footer) {
        const bodyHeight = document.body.offsetHeight;
        const windowHeight = window.innerHeight;
        if (bodyHeight < windowHeight) {
            footer.style.position = 'absolute';
            footer.style.bottom = '0';
        } else {
            footer.style.position = 'relative';
        }
    }
}

function showEditModal(id, descripcion, codigo) {
    const editId = document.getElementById('editId');
    const editDescripcion = document.getElementById('editDescripcion');
    const editCodigo = document.getElementById('editCodigo');
    const editModal = document.getElementById('editModal');

    if (editId && editDescripcion && editCodigo && editModal) {
        editId.value = id;
        editDescripcion.value = descripcion;
        editCodigo.value = codigo;
        editModal.style.display = 'block';
    }
}

function updateDetail() {
    const id = document.getElementById('editId').value;
    const descripcion = document.getElementById('editDescripcion').value;
    const codigo = document.getElementById('editCodigo').value;
    const allowedCodes = [
        "A106.1.1-",
        "A106.1.2-",
        "A106.1.3-",
        "A106.1.4-",
        "A106.1.5-",
        "A106.1.6-"
    ];

    // Verificar si el prefijo del código ingresado coincide con alguno de los códigos permitidos
    let isValidCode = false;
    for (let i = 0; i < allowedCodes.length; i++) {
        if (codigo.startsWith(allowedCodes[i])) {
            isValidCode = true;
            break;
        }
    }

    // Si el código no es válido, mostrar un mensaje de error y salir
    if (!isValidCode) {
        alert("Código no válido. Por favor, ingresa un código permitido.");
        return;
    }

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
            if (data.exists) {
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
                const editModal = document.getElementById('editModal');
                if (editModal) {
                    editModal.style.display = 'none';
                }
                fetchSeries();
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        })
        .catch(error => {
            alert('Error: ' + error.message);
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
            if (serieList) {
                serieList.innerHTML = '';

                data.forEach(serie => {
                    const serieDiv = document.createElement('div');
                    serieDiv.innerHTML = `
                        <p>ID: ${serie.id}</p>
                        <p>Nombre: ${serie.nombre}</p>
                        <p>Descripción: ${serie.descripcion}</p>
                        <p>Código: ${serie.codigo}</p>
                        <button onclick="showEditModal(${serie.id}, '${serie.descripcion}', '${serie.codigo}')">Editar</button>
                        <button onclick="deleteDetail(${serie.id}, ${serie.serie_id})">Borrar</button>
                    `;

                    serieList.appendChild(serieDiv);
                });
            }
        })
        .catch(error => console.error('Error:', error));
}

const memoForm = document.querySelector('memo');
if (memoForm) {
    memoForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Evitar el envío normal del formulario

        // Obtener los datos del formulario
        const formData = new FormData(document.querySelector('.form'));

        // Construir la URL con los datos del formulario
        const queryParams = new URLSearchParams(formData).toString();

        // Ejecutar la lógica deseada con queryParams
    });
}

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
