document.addEventListener('DOMContentLoaded', () => {
    // Seleccionar el contenedor de usuario
    const userDropdown = document.querySelector('#usuario');
    if (userDropdown) {
        // Obtener el nombre del usuario desde el atributo data-username
        const username = userDropdown.dataset.username;

        // Seleccionar el párrafo donde se muestra el nombre
        const userText = userDropdown.querySelector('#parrafo');
        if (userText) {
            // Actualizar el texto del usuario
            userText.textContent = username ? `${username}` : 'Invitado';
        }
    }
});


// id_estado -> columna del tablero
const ESTADOS = {
    1: { nombre: 'Por hacer', lista: '#todo-tarea' },
    2: { nombre: 'En progreso', lista: '#doing-tarea' },
    3: { nombre: 'Hecha', lista: '#done-tarea' },
};
const ESTADO_HECHA = 3;

// Colores por tipo de tarea (id_tipo), con contraste suficiente para texto blanco
const COLORES_TIPO = ['#3F6E8C', '#5B7553', '#7A4A6E', '#8C6D4F', '#b22729', '#0B1A33'];

// Crea un elemento con clase y texto opcionales
function crearElemento(etiqueta, clase, texto) {
    const elemento = document.createElement(etiqueta);
    if (clase) elemento.className = clase;
    if (texto !== undefined) elemento.textContent = texto;
    return elemento;
}

// "2025-02-07" -> Date local (new Date("2025-02-07") la interpretaría en UTC)
function parsearFecha(texto) {
    if (!texto) return null;
    const [anio, mes, dia] = texto.split('-').map(Number);
    return new Date(anio, mes - 1, dia);
}

function formatearFecha(fecha) {
    return fecha.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' });
}

function mostrarAviso(texto) {
    const aviso = document.getElementById('aviso');
    aviso.textContent = texto;
    aviso.classList.add('visible');
    clearTimeout(mostrarAviso.temporizador);
    mostrarAviso.temporizador = setTimeout(() => aviso.classList.remove('visible'), 3500);
}

// Contadores de cada columna y resumen de la cabecera
function actualizarContadores() {
    let total = 0;
    for (const [idEstado, estado] of Object.entries(ESTADOS)) {
        const cantidad = document.querySelectorAll(`${estado.lista} .mt-card[data-id-tarea]`).length;
        document.getElementById(`contador-${idEstado}`).textContent = cantidad;
        document.getElementById(`tab-contador-${idEstado}`).textContent = cantidad;
        total += cantidad;
    }
    const hechas = document.querySelectorAll(`${ESTADOS[ESTADO_HECHA].lista} .mt-card[data-id-tarea]`).length;
    document.getElementById('resumen-tareas').textContent = total === 0
        ? 'Sin tareas todavía'
        : `${total} tarea${total === 1 ? '' : 's'} · ${hechas} hecha${hechas === 1 ? '' : 's'}`;
}

// La tarjeta muestra "vencida" si la fecha de fin ya pasó y no está hecha
function actualizarVencimiento(card) {
    const fechaEl = card.querySelector('.mt-fecha');
    if (!fechaEl || !card.dataset.fechaFin) return;
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    const vencida = parsearFecha(card.dataset.fechaFin) < hoy && Number(card.dataset.estado) !== ESTADO_HECHA;
    fechaEl.classList.toggle('mt-vencida', vencida);
    fechaEl.title = vencida ? 'La fecha de fin ya ha pasado' : '';
}

// Opciones "Mover a…" del menú: todas las columnas menos la actual
function actualizarMenuMover(card) {
    const contenedor = card.querySelector('.mt-mover');
    contenedor.replaceChildren(crearElemento('li', 'dropdown-header', 'Mover a'));
    for (const [idEstado, estado] of Object.entries(ESTADOS)) {
        if (Number(idEstado) === Number(card.dataset.estado)) continue;
        const boton = crearElemento('button', 'dropdown-item');
        boton.type = 'button';
        boton.append(crearElemento('span', `mt-dot mt-dot-${idEstado} me-2`), estado.nombre);
        boton.addEventListener('click', () => moverTarea(card, Number(idEstado)));
        const item = crearElemento('li');
        item.appendChild(boton);
        contenedor.appendChild(item);
    }
}

// ===== Detalle de tarea =====

// Datos completos de cada tarea por id (la tarjeta solo muestra un resumen)
const datosTareas = new Map();
// Tarjeta cuyo detalle está abierto
let tarjetaDetalle = null;

function formatearFechaLarga(fecha) {
    return fecha.toLocaleDateString('es-ES', { weekday: 'short', day: 'numeric', month: 'long', year: 'numeric' });
}

function pintarDetalle(card) {
    const tarea = datosTareas.get(card.dataset.idTarea);
    const idEstado = Number(card.dataset.estado);

    const tipo = document.getElementById('detalleTipo');
    tipo.textContent = tarea.nom_tipo;
    tipo.style.setProperty('--mt-color-tipo', card.style.getPropertyValue('--mt-color-tipo'));
    document.getElementById('detalleTitulo').textContent = tarea.nom_tarea;

    const descripcion = document.getElementById('detalleDescripcion');
    const tieneDescripcion = tarea.descripcion && tarea.descripcion.trim();
    descripcion.textContent = tieneDescripcion ? tarea.descripcion : 'Esta tarea no tiene descripción.';
    descripcion.classList.toggle('mt-detalle-vacio', !tieneDescripcion);

    const estado = document.getElementById('detalleEstado');
    estado.replaceChildren(crearElemento('span', `mt-dot mt-dot-${idEstado}`), ` ${ESTADOS[idEstado].nombre}`);

    const encargado = document.getElementById('detalleEncargado');
    if (tarea.nom_user) {
        const inicial = crearElemento('span', 'mt-mini-avatar', tarea.nom_user.charAt(0).toUpperCase());
        inicial.setAttribute('aria-hidden', 'true');
        encargado.replaceChildren(inicial, ` ${tarea.nom_user}`);
        encargado.classList.remove('mt-sin-asignar');
    } else {
        encargado.replaceChildren('Sin asignar');
        encargado.classList.add('mt-sin-asignar');
    }

    const ini = parsearFecha(tarea.fecha_ini);
    const fin = parsearFecha(tarea.fecha_fin);
    document.getElementById('detalleInicio').textContent = ini ? formatearFechaLarga(ini) : 'Sin fecha';

    const detalleFin = document.getElementById('detalleFin');
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    const vencida = fin && fin < hoy && idEstado !== ESTADO_HECHA;
    detalleFin.textContent = fin ? formatearFechaLarga(fin) : 'Sin fecha';
    if (vencida) detalleFin.append(' ', crearElemento('span', 'mt-badge-vencida', 'Vencida'));
    detalleFin.classList.toggle('mt-vencida', Boolean(vencida));

    // Botones "Mover a" con las otras columnas
    const mover = document.getElementById('detalleMover');
    mover.replaceChildren();
    for (const [id, datos] of Object.entries(ESTADOS)) {
        if (Number(id) === idEstado) continue;
        const boton = crearElemento('button', 'btn mt-btn-mover');
        boton.type = 'button';
        boton.append(crearElemento('span', `mt-dot mt-dot-${id}`), ` ${datos.nombre}`);
        boton.addEventListener('click', () => moverTarea(card, Number(id)));
        mover.appendChild(boton);
    }

    // El botón eliminar abre el modal de confirmación con los datos de esta tarea
    const eliminar = document.getElementById('detalleEliminar');
    eliminar.setAttribute('data-id-tarea', tarea.id_tarea);
    eliminar.setAttribute('data-nombre-tarea', tarea.nom_tarea);
}

function abrirDetalle(card) {
    tarjetaDetalle = card;
    pintarDetalle(card);
    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDetalleTarea')).show();
}

const modalDetalle = document.getElementById('modalDetalleTarea');
if (modalDetalle) {
    // Al cerrar, el foco vuelve a la tarjeta (si sigue en el tablero)
    modalDetalle.addEventListener('hidden.bs.modal', () => {
        const card = tarjetaDetalle;
        tarjetaDetalle = null;
        if (card && card.isConnected) card.querySelector('.mt-card-open').focus({ preventScroll: true });
    });
}

function crearTarjetaTarea(tarea) {
    const card = crearElemento('article', 'mt-card');
    card.draggable = true;
    card.dataset.idTarea = tarea.id_tarea;
    card.dataset.estado = tarea.id_estado;
    card.dataset.fechaFin = tarea.fecha_fin || '';
    card.style.setProperty('--mt-color-tipo', COLORES_TIPO[(Number(tarea.id_tipo) - 1) % COLORES_TIPO.length]);

    // Fila superior: tipo + menú
    const arriba = crearElemento('div', 'mt-card-top');
    arriba.appendChild(crearElemento('span', 'mt-tipo', tarea.nom_tipo));

    const menu = crearElemento('div', 'dropdown');
    const botonMenu = crearElemento('button', 'mp-icon-btn');
    botonMenu.type = 'button';
    botonMenu.setAttribute('data-bs-toggle', 'dropdown');
    botonMenu.setAttribute('aria-expanded', 'false');
    botonMenu.setAttribute('aria-label', `Opciones de ${tarea.nom_tarea}`);
    botonMenu.title = 'Opciones';
    botonMenu.appendChild(crearElemento('i', 'bi bi-three-dots-vertical'));

    const lista = crearElemento('ul', 'dropdown-menu dropdown-menu-end');
    const mover = crearElemento('div', 'mt-mover');
    const separador = crearElemento('li');
    separador.appendChild(crearElemento('hr', 'dropdown-divider'));

    const botonEliminar = crearElemento('button', 'dropdown-item text-danger');
    botonEliminar.type = 'button';
    botonEliminar.setAttribute('data-bs-toggle', 'modal');
    botonEliminar.setAttribute('data-bs-target', '#modalEliminarTarea');
    botonEliminar.setAttribute('data-id-tarea', tarea.id_tarea);
    botonEliminar.setAttribute('data-nombre-tarea', tarea.nom_tarea);
    botonEliminar.append(crearElemento('i', 'bi bi-trash me-2'), 'Eliminar');
    const itemEliminar = crearElemento('li');
    itemEliminar.appendChild(botonEliminar);

    lista.append(mover, separador, itemEliminar);
    menu.append(botonMenu, lista);
    arriba.appendChild(menu);

    // Contenido: el título es un botón para poder abrir el detalle con el teclado
    const titulo = crearElemento('h3', 'mt-card-title');
    const botonDetalle = crearElemento('button', 'mt-card-open', tarea.nom_tarea);
    botonDetalle.type = 'button';
    botonDetalle.title = tarea.nom_tarea;
    botonDetalle.setAttribute('aria-haspopup', 'dialog');
    titulo.appendChild(botonDetalle);
    card.append(arriba, titulo);

    // La descripción siempre ocupa su hueco (2 líneas) para que todas las tarjetas midan lo mismo
    const tieneDescripcion = tarea.descripcion && tarea.descripcion.trim();
    card.appendChild(crearElemento('p', tieneDescripcion ? 'mt-card-desc' : 'mt-card-desc mt-card-desc-vacia',
        tieneDescripcion ? tarea.descripcion : 'Sin descripción'));

    // Pie: fechas y encargado
    const pie = crearElemento('div', 'mt-card-meta');

    const ini = parsearFecha(tarea.fecha_ini);
    const fin = parsearFecha(tarea.fecha_fin);
    if (ini || fin) {
        const textoFechas = ini && fin ? `${formatearFecha(ini)} → ${formatearFecha(fin)}`
            : fin ? `Hasta ${formatearFecha(fin)}` : `Desde ${formatearFecha(ini)}`;
        const fechas = crearElemento('span', 'mt-fecha');
        fechas.append(crearElemento('i', 'bi bi-calendar3'), ` ${textoFechas}`);
        pie.appendChild(fechas);
    }

    const encargado = crearElemento('span', 'mt-encargado');
    if (tarea.nom_user) {
        const inicial = crearElemento('span', 'mt-mini-avatar', tarea.nom_user.charAt(0).toUpperCase());
        inicial.setAttribute('aria-hidden', 'true');
        encargado.append(inicial, crearElemento('span', 'mt-encargado-nombre', tarea.nom_user));
        encargado.title = `Encargado: ${tarea.nom_user}`;
    } else {
        encargado.classList.add('mt-sin-asignar');
        encargado.append(crearElemento('i', 'bi bi-person-dash'), ' Sin asignar');
    }
    pie.appendChild(encargado);
    card.appendChild(pie);

    actualizarMenuMover(card);
    actualizarVencimiento(card);

    // Clic en cualquier parte de la tarjeta abre el detalle (menos en el menú ⋯).
    // Tras un arrastre el navegador no lanza click, así que no interfiere con mover la tarjeta.
    datosTareas.set(String(tarea.id_tarea), tarea);
    card.addEventListener('click', (e) => {
        if (e.target.closest('.dropdown')) return;
        abrirDetalle(card);
    });

    // Arrastrar
    card.addEventListener('dragstart', (e) => {
        e.dataTransfer.setData('text/plain', tarea.id_tarea);
        e.dataTransfer.effectAllowed = 'move';
        card.classList.add('arrastrando');
    });
    card.addEventListener('dragend', () => card.classList.remove('arrastrando'));

    return card;
}

// Mueve la tarjeta en pantalla y guarda el nuevo estado; si falla, la devuelve a su columna
function moverTarea(card, idEstadoNuevo) {
    const idEstadoAnterior = Number(card.dataset.estado);
    if (idEstadoAnterior === idEstadoNuevo) return;

    const colocar = (idEstado) => {
        card.dataset.estado = idEstado;
        document.querySelector(ESTADOS[idEstado].lista).appendChild(card);
        actualizarMenuMover(card);
        actualizarVencimiento(card);
        actualizarContadores();
        // Si el detalle de esta tarea está abierto, refleja el estado nuevo (o el anterior si falló)
        if (tarjetaDetalle === card) pintarDetalle(card);
    };

    colocar(idEstadoNuevo);

    fetch('/proyecto-inter-modular/api/tareas/actualizarEstado.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_tarea: Number(card.dataset.idTarea), id_estado: idEstadoNuevo }),
    })
        .then(function (respuesta) {
            if (!respuesta.ok) throw new Error(`HTTP ${respuesta.status}`);
            // En móvil la tarjeta desaparece de la pestaña actual: confirmamos a dónde ha ido
            if (vistaMovil.matches) mostrarAviso(`Tarea movida a «${ESTADOS[idEstadoNuevo].nombre}»`);
        })
        .catch(function (error) {
            console.log('Error:', error);
            colocar(idEstadoAnterior);
            mostrarAviso('No se pudo mover la tarea. Inténtalo de nuevo.');
        });
}

// ===== Pestañas (solo visibles en móvil, ver .mt-tabs en myStyle.css) =====

const vistaMovil = window.matchMedia('(max-width: 767px)');
const pestanias = [...document.querySelectorAll('.mt-tab')];

function activarPestania(pestania, darFoco) {
    for (const p of pestanias) {
        const activa = p === pestania;
        p.setAttribute('aria-selected', activa);
        p.tabIndex = activa ? 0 : -1;
        document.getElementById(p.getAttribute('aria-controls')).classList.toggle('mt-col-activa', activa);
    }
    if (darFoco) pestania.focus();
}

pestanias.forEach((pestania, indice) => {
    pestania.addEventListener('click', () => activarPestania(pestania, false));
    // Flechas izquierda/derecha para cambiar de pestaña (patrón de pestañas accesible)
    pestania.addEventListener('keydown', (e) => {
        const salto = e.key === 'ArrowRight' ? 1 : e.key === 'ArrowLeft' ? -1 : 0;
        if (!salto) return;
        e.preventDefault();
        activarPestania(pestanias[(indice + salto + pestanias.length) % pestanias.length], true);
    });
});

// Las columnas aceptan tarjetas arrastradas
document.querySelectorAll('.mt-col').forEach(columna => {
    const idEstado = Number(columna.dataset.estado);

    columna.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        columna.classList.add('mt-drop-activa');
    });
    columna.addEventListener('dragleave', (e) => {
        // Solo al salir de la columna, no al pasar por encima de sus tarjetas
        if (!columna.contains(e.relatedTarget)) columna.classList.remove('mt-drop-activa');
    });
    columna.addEventListener('drop', (e) => {
        e.preventDefault();
        columna.classList.remove('mt-drop-activa');
        const card = document.querySelector(`.mt-card[data-id-tarea="${e.dataTransfer.getData('text/plain')}"]`);
        if (card) moverTarea(card, idEstado);
    });
});


window.addEventListener('load', () => {
    fetch('/proyecto-inter-modular/api/tareas/obtenerTipo.php')
        .then(function (respuesta) {
            return respuesta.json();
        })
        .then(function (tipos) {
            const listadoTipo = document.querySelector('#tipoSelect');
            for (const tipo of tipos) {
                listadoTipo.appendChild(new Option(tipo.nom_tipo, tipo.id_tipo));
            }
        })
        .catch(function (error) {
            console.log('Error:', error);
        })
})

window.addEventListener('load', () => {
    fetch('/proyecto-inter-modular/api/tareas/obtenerUsuarioTarea.php')
        .then(function (respuesta) {
            return respuesta.json();
        })
        .then(function (usuarios) {
            const listadoUsuario = document.querySelector('#usuarioSelect');
            for (const usuario of usuarios) {
                listadoUsuario.appendChild(new Option(usuario.nom_user, usuario.id_user));
            }
        })
        .catch(function (error) {
            console.log('Error:', error);
        })
})

window.addEventListener('load', () => {
    const listas = Object.values(ESTADOS).map(estado => document.querySelector(estado.lista));

    fetch('/proyecto-inter-modular/api/tareas/obtenerTareas.php')
        .then(function (respuesta) {
            if (!respuesta.ok) throw new Error(`HTTP ${respuesta.status}`);
            return respuesta.json();
        })
        .then(function (tareas) {
            // Quitar las tarjetas de carga
            listas.forEach(lista => lista.replaceChildren());

            for (const tarea of tareas) {
                const estado = ESTADOS[tarea.id_estado] || ESTADOS[1];
                document.querySelector(estado.lista).appendChild(crearTarjetaTarea(tarea));
            }
            actualizarContadores();
        })
        .catch(function (error) {
            console.log('Error:', error);
            listas.forEach(lista => lista.replaceChildren());
            document.getElementById('resumen-tareas').textContent = 'No se pudieron cargar las tareas. Recarga la página.';
        })
})


const modalTarea = document.getElementById('exampleModal')
if (modalTarea) {
    const form = document.getElementById('formTarea');
    const fechaIni = document.getElementById('fechaIni');
    const fechaFin = document.getElementById('fechaFin');

    // La fecha de fin no puede ser anterior a la de inicio
    function comprobarFechas() {
        fechaFin.min = fechaIni.value;
        fechaFin.setCustomValidity(fechaIni.value && fechaFin.value && fechaFin.value < fechaIni.value
            ? 'La fecha de fin no puede ser anterior a la de inicio.' : '');
    }
    fechaIni.addEventListener('input', comprobarFechas);
    fechaFin.addEventListener('input', comprobarFechas);

    // ===== Modo crear / editar =====
    // Crear: envío normal del formulario a userController.php (recarga la página).
    // Editar: se envía por fetch a editarTarea.php y se repinta solo la tarjeta, sin recargar.
    const titulo = document.getElementById('modalTareaLabel');
    const subtitulo = document.getElementById('modalTareaSubtitulo');
    const botonGuardar = document.getElementById('btnGuardarTarea');
    const idTareaEditar = document.getElementById('idTareaEditar');
    const error = document.getElementById('errorTarea');
    let tarjetaEdicion = null;

    function mostrarError(texto) {
        error.replaceChildren(crearElemento('i', 'bi bi-exclamation-circle-fill'), crearElemento('span', '', texto));
        error.classList.toggle('d-none', !texto);
    }

    function prepararCrear() {
        tarjetaEdicion = null;
        idTareaEditar.value = '';
        titulo.textContent = 'Crea una tarea';
        subtitulo.textContent = 'Se añadirá a la columna «Por hacer»';
        botonGuardar.replaceChildren(crearElemento('i', 'bi bi-plus-lg'), ' Crear tarea');
        mostrarError('');
    }

    function prepararEditar(card) {
        const tarea = datosTareas.get(card.dataset.idTarea);
        tarjetaEdicion = card;
        idTareaEditar.value = tarea.id_tarea;
        titulo.textContent = 'Editar tarea';
        subtitulo.textContent = `En la columna «${ESTADOS[Number(card.dataset.estado)].nombre}»`;
        botonGuardar.replaceChildren(crearElemento('i', 'bi bi-check-lg'), ' Guardar cambios');
        mostrarError('');

        document.getElementById('nom_tarea').value = tarea.nom_tarea;
        document.getElementById('descripcion').value = tarea.descripcion || '';
        document.getElementById('usuarioSelect').value = tarea.id_user ? String(tarea.id_user) : '';
        document.getElementById('tipoSelect').value = String(tarea.id_tipo);
        fechaIni.value = tarea.fecha_ini || '';
        fechaFin.value = tarea.fecha_fin || '';
        comprobarFechas();
    }

    // El botón "Editar" del detalle abre este modal: en ese caso se entra en modo edición
    modalTarea.addEventListener('show.bs.modal', (event) => {
        const desdeDetalle = event.relatedTarget && event.relatedTarget.id === 'detalleEditar';
        if (desdeDetalle && tarjetaDetalle) prepararEditar(tarjetaDetalle);
        else prepararCrear();
    });

    form.addEventListener('submit', (e) => {
        if (!tarjetaEdicion) return; // crear: envío normal

        e.preventDefault();
        const card = tarjetaEdicion;
        botonGuardar.disabled = true;
        mostrarError('');

        fetch('/proyecto-inter-modular/api/tareas/editarTarea.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id_tarea: Number(idTareaEditar.value),
                nom_tarea: document.getElementById('nom_tarea').value,
                descripcion: document.getElementById('descripcion').value,
                id_user: document.getElementById('usuarioSelect').value,
                id_tipo: Number(document.getElementById('tipoSelect').value),
                fecha_ini: fechaIni.value,
                fecha_fin: fechaFin.value,
            }),
        })
            .then(function (respuesta) {
                return respuesta.json().then(datos => {
                    if (!respuesta.ok || !datos.ok) throw new Error(datos.error || `HTTP ${respuesta.status}`);
                    return datos.tarea;
                });
            })
            .then(function (tarea) {
                // Sustituye la tarjeta por una nueva con los datos guardados (misma columna y posición)
                const nueva = crearTarjetaTarea(tarea);
                card.replaceWith(nueva);
                actualizarContadores();
                bootstrap.Modal.getInstance(modalTarea).hide();
                mostrarAviso('Cambios guardados');
            })
            .catch(function (err) {
                console.log('Error:', err);
                mostrarError(err.message.startsWith('HTTP') || err instanceof SyntaxError
                    ? 'No se pudieron guardar los cambios. Inténtalo de nuevo.' : err.message);
            })
            .finally(function () {
                botonGuardar.disabled = false;
            });
    });

    // Cursor directo al nombre al abrir, y formulario limpio al cerrar
    modalTarea.addEventListener('shown.bs.modal', () => document.getElementById('nom_tarea').focus())
    modalTarea.addEventListener('hidden.bs.modal', () => {
        // Al cerrar una edición, el foco vuelve a la tarjeta editada
        const editada = tarjetaEdicion && document.querySelector(`.mt-card[data-id-tarea="${idTareaEditar.value}"]`);
        form.reset();
        prepararCrear();
        comprobarFechas();
        if (editada) editada.querySelector('.mt-card-open').focus({ preventScroll: true });
    })
}

const modalEliminarTarea = document.getElementById('modalEliminarTarea')
if (modalEliminarTarea) {
    modalEliminarTarea.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget
        document.getElementById('eliminarTareaId').value = button.getAttribute('data-id-tarea')
        document.getElementById('eliminarTareaNombre').textContent = `«${button.getAttribute('data-nombre-tarea')}»`
    })
}
