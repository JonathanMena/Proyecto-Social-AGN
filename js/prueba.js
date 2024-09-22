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
                                <p>Referencia: ${serie.codigo}</p>
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
        // Agregar evento click a la caja
        box.addEventListener('click', function() {
            // Si la caja ya tiene la clase 'active', la elimina
            if (this.classList.contains('active')) {
                this.classList.remove('active');
                this.style.height = ''; // Restablecer la altura
            } else {
                // Si no, elimina 'active' de todas las cajas y la añade a la caja actual
                boxes.forEach(b => {
                    b.classList.remove('active');
                    b.style.height = ''; // Restablecer la altura de todas las cajas
                });
                this.classList.add('active');
                this.style.height = '500px'; // Ajustar la altura según sea necesario
            }
            ajustarFooter();
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

    // Verificar que los campos no estén vacíos
    if (!descripcion.trim() || !codigo.trim()) {
        alert('Por favor, completa todos los campos antes de guardar.');
        return;
    }

    // Crear la expresión regular para validar el formato del código
    const regex = /^[A-U]\d+-(\d{1,2})-(\d{4})$/; // A a V - uno o más dígitos - mes (1 o 2 dígitos) - año (4 dígitos)

    // Verificar si el formato del código es válido
    const match = codigo.match(regex);
    if (!match) {
        alert('Código no válido. El formato debe ser A-V1 o más dígitos-mes-año, por ejemplo: A1-09-2024 o B10-5-2024');
        return;
    }

    const mes = parseInt(match[1], 10); // Extraer el mes del código

    // Validar que el mes esté entre 1 y 12
    if (mes < 1 || mes > 12) {
        alert('Mes no válido. Debe estar entre 1 y 12.');
        return;
    }

    // Aquí puedes agregar la lógica para realizar la actualización
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
        fetchSeries(); // Actualizar la lista después de editar
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
                        <p>Referencia: ${serie.codigo}</p>
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
        if (element.style.display === "none" || element.style.display === "") {
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
