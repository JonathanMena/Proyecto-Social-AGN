document.addEventListener('DOMContentLoaded', function () {
    ajustarFooter();
    setupButtonHandlers();
    setupFormSubmitHandler();
    setupCloseButtonHandlers();
    window.addEventListener('resize', ajustarFooter);
});

function ajustarFooter() {
    const footer = document.querySelector('.footer');
    if (footer) {
        const isBodyShorterThanWindow = document.body.offsetHeight < window.innerHeight;
        footer.style.position = isBodyShorterThanWindow ? 'absolute' : 'relative';
        footer.style.bottom = isBodyShorterThanWindow ? '0' : '';
    }
}

function setupButtonHandlers() {
    document.querySelectorAll('button[id^="btn-"]').forEach(button => {
        button.addEventListener('click', handleButtonClick);
    });
}

function handleButtonClick() {
    const serieId = this.id.split('-')[1];
    const containerId = `serie-documental-container-${serieId}`;

    document.querySelectorAll('[id^="serie-documental-container-"]').forEach(container => {
        container.style.display = 'none';
    });

    const container = document.getElementById(containerId);
    if (container) {
        container.style.display = 'block';
        fetchSerieDetails(serieId, container);
    }
}

function fetchSerieDetails(serieId, container) {
    fetch(`bd.php?serie_id=${serieId}`)
        .then(response => response.ok ? response.json() : Promise.reject(response.statusText))
        .then(data => renderSerieDetails(data, container))
        .catch(error => console.error('Error:', error));
}

function renderSerieDetails(data, container) {
    container.innerHTML = '';

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
}

function setupFormSubmitHandler() {
    const form = document.getElementById('detalle-form');
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
    }
}

function handleFormSubmit(event) {
    event.preventDefault();

    const formData = new FormData(event.target);

    fetch('insertar.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => handleFormResponse(data, event.target))
        .catch(error => console.error('Error:', error));
}

function handleFormResponse(data, form) {
    const responseMessage = document.getElementById('response-message');
    if (responseMessage) {
        responseMessage.textContent = data.error || 'Datos insertados correctamente';
        responseMessage.classList.toggle('text-danger', !!data.error);
        responseMessage.classList.toggle('text-success', !data.error);
        if (!data.error) form.reset();
    }
}

function setupCloseButtonHandlers() {
    document.querySelectorAll('.box').forEach(box => {
        const closeButton = document.createElement('button');
        closeButton.textContent = 'Cerrar';
        closeButton.classList.add('close-button');
        box.appendChild(closeButton);

        closeButton.addEventListener('click', event => {
            event.stopPropagation();
            box.classList.remove('active');
        });

        box.addEventListener('click', () => {
            document.querySelectorAll('.box').forEach(b => b.classList.remove('active'));
            box.classList.add('active');
        });
    });
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
    const allowedCodes = ["A106.1.1-", "A106.1.2-", "A106.1.3-", "A106.1.4-", "A106.1.5-", "A106.1.6-"];

    if (!allowedCodes.some(prefix => codigo.startsWith(prefix))) {
        alert("Código no válido. Por favor, ingresa un código permitido.");
        return;
    }

    validateAndUpdateDetail(id, descripcion, codigo);
}

function validateAndUpdateDetail(id, descripcion, codigo) {
    fetch(`bd.php?codigo=${codigo}&excludeId=${id}`)
        .then(response => response.ok ? response.json() : Promise.reject(response.statusText))
        .then(data => {
            if (data.error) throw new Error(data.error);
            if (data.exists) throw new Error('El código ya está siendo utilizado por otro detalle.');

            return fetch('bd.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, descripcion, codigo })
            });
        })
        .then(response => response.ok ? response.json() : response.text().then(text => { throw new Error(text) }))
        .then(data => {
            alert(data.success || data.error);
            document.getElementById('editModal').style.display = 'none';
            fetchSeries();
        })
        .catch(error => alert('Error: ' + error.message));
}

function deleteDetail(id, serieId) {
    if (confirm('¿Estás seguro de que deseas borrar este detalle?')) {
        fetch('bd.php', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, serieId })
        })
            .then(response => response.ok ? response.json() : response.text().then(text => { throw new Error(text) }))
            .then(data => {
                alert(data.success || data.error);
                fetchSeries();
            })
            .catch(error => alert('Error: ' + error.message));
    }
}

function fetchSeries() {
    fetch('bd.php')
         .then(response => response.ok ? response.json() : Promise.reject(response.statusText))
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

function mostrarInformacion(info) {
    const element = document.getElementById(`info-${info}`);
    const icono = document.getElementById(`icono-${info}`);

    document.querySelectorAll('[id^="info-"]').forEach(el => {
        el.style.display = el.id === `info-${info}` ? 'block' : 'none';
    });

    if (element && icono) {
        const isDisplayed = element.style.display === 'block';
        icono.classList.replace(isDisplayed ? "bi-caret-down" : "bi-caret-up", isDisplayed ? "bi-caret-up" : "bi-caret-down");
    } else {
        console.error(`Elemento con ID 'info-${info}' o 'icono-${info}' no encontrado.`);
    }

    ajustarFooter();
}
