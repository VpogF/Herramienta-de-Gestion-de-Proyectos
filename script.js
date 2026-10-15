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


// Colores de los avatares (todos con contraste suficiente para texto blanco)
const COLORES_PROYECTO = ['#0B1A33', '#b22729', '#3F6E8C', '#8C6D4F', '#5B7553', '#7A4A6E'];

// Crea un elemento con clase y texto opcionales
function crearElemento(etiqueta, clase, texto) {
    const elemento = document.createElement(etiqueta);
    if (clase) elemento.className = clase;
    if (texto !== undefined) elemento.textContent = texto;
    return elemento;
}

// "Gestión web" -> "GW", "Tienda" -> "TI"
function obtenerIniciales(nombre) {
    const palabras = nombre.trim().split(/\s+/).filter(Boolean);
    if (palabras.length === 0) return '?';
    if (palabras.length === 1) return palabras[0].slice(0, 2).toUpperCase();
    return (palabras[0][0] + palabras[1][0]).toUpperCase();
}

function crearTarjetaProyecto(proyecto) {
    const id = proyecto.id_proyecto;
    const nombre = proyecto.nom_proyecto;
    const total = Number(proyecto.total_tareas) || 0;
    const hechas = Number(proyecto.tareas_hechas) || 0;
    const miembros = Number(proyecto.total_miembros) || 0;
    const porcentaje = total > 0 ? Math.round((hechas / total) * 100) : 0;

    const card = crearElemento('article', 'mp-card');
    card.style.setProperty('--mp-color', COLORES_PROYECTO[id % COLORES_PROYECTO.length]);

    // Toda la tarjeta es el botón para entrar al proyecto
    const form = crearElemento('form', 'mp-card-form');
    form.action = './php_controllers/userController.php';
    form.method = 'POST';

    const inputNombre = document.createElement('input');
    inputNombre.type = 'hidden';
    inputNombre.name = 'nom_proyecto';
    inputNombre.value = nombre;

    const botonEntrar = crearElemento('button', 'mp-card-main');
    botonEntrar.type = 'submit';
    botonEntrar.name = 'acceder-proyecto';
    botonEntrar.value = id;
    botonEntrar.setAttribute('aria-label', `Abrir el proyecto ${nombre}`);

    const avatar = crearElemento('span', 'mp-avatar', obtenerIniciales(nombre));
    avatar.setAttribute('aria-hidden', 'true');

    const titulo = crearElemento('span', 'mp-card-name', nombre);
    titulo.title = nombre;

    const meta = crearElemento('span', 'mp-card-meta');
    const textoTareas = total > 0 ? `${hechas}/${total} tareas` : 'Sin tareas';
    const metaTareas = crearElemento('span');
    metaTareas.append(crearElemento('i', 'bi bi-check2-square'), ` ${textoTareas}`);
    const metaMiembros = crearElemento('span');
    metaMiembros.append(crearElemento('i', 'bi bi-people'), ` ${miembros}`);
    metaMiembros.title = `${miembros} participante${miembros === 1 ? '' : 's'}`;
    meta.append(metaTareas, metaMiembros);

    const progreso = crearElemento('span', 'mp-progress');
    const barra = crearElemento('span', 'mp-progress-bar');
    barra.style.width = `${porcentaje}%`;
    progreso.appendChild(barra);

    botonEntrar.append(avatar, titulo, meta, progreso);
    form.append(inputNombre, botonEntrar);

    // Menú de opciones (acciones secundarias y destructivas)
    const menu = crearElemento('div', 'dropdown mp-card-menu');

    const botonMenu = crearElemento('button', 'mp-icon-btn');
    botonMenu.type = 'button';
    botonMenu.setAttribute('data-bs-toggle', 'dropdown');
    botonMenu.setAttribute('aria-expanded', 'false');
    botonMenu.setAttribute('aria-label', `Opciones de ${nombre}`);
    botonMenu.title = 'Opciones';
    botonMenu.appendChild(crearElemento('i', 'bi bi-three-dots-vertical'));

    const lista = crearElemento('ul', 'dropdown-menu dropdown-menu-end');

    const botonColaborador = crearElemento('button', 'dropdown-item');
    botonColaborador.type = 'button';
    botonColaborador.setAttribute('data-bs-toggle', 'modal');
    botonColaborador.setAttribute('data-bs-target', '#exampleModal2');
    botonColaborador.setAttribute('data-id-proyecto', id);
    botonColaborador.setAttribute('data-nombre-proyecto', nombre);
    botonColaborador.append(crearElemento('i', 'bi bi-person-plus me-2'), 'Añadir participante');

    const botonEliminar = crearElemento('button', 'dropdown-item text-danger');
    botonEliminar.type = 'button';
    botonEliminar.setAttribute('data-bs-toggle', 'modal');
    botonEliminar.setAttribute('data-bs-target', '#modalEliminar');
    botonEliminar.setAttribute('data-id-proyecto', id);
    botonEliminar.setAttribute('data-nombre-proyecto', nombre);
    botonEliminar.append(crearElemento('i', 'bi bi-trash me-2'), 'Eliminar');

    const itemColaborador = crearElemento('li');
    itemColaborador.appendChild(botonColaborador);
    const separador = crearElemento('li');
    separador.appendChild(crearElemento('hr', 'dropdown-divider'));
    const itemEliminar = crearElemento('li');
    itemEliminar.appendChild(botonEliminar);
    lista.append(itemColaborador, separador, itemEliminar);

    menu.append(botonMenu, lista);
    card.append(form, menu);

    return card;
}

function crearEstadoVacio() {
    const vacio = crearElemento('div', 'mp-empty');
    vacio.appendChild(crearElemento('i', 'bi bi-kanban mp-empty-icon'));
    vacio.appendChild(crearElemento('h2', 'mp-empty-title', 'Aún no tienes proyectos'));
    vacio.appendChild(crearElemento('p', 'mp-empty-text', 'Crea tu primer proyecto para empezar a organizar tareas con tu equipo.'));

    const boton = crearElemento('button', 'btn btn-primary mp-btn-nuevo');
    boton.type = 'button';
    boton.setAttribute('data-bs-toggle', 'modal');
    boton.setAttribute('data-bs-target', '#exampleModal');
    boton.append(crearElemento('i', 'bi bi-plus-lg'), ' Crear el primero');
    vacio.appendChild(boton);

    return vacio;
}

function crearEstadoError() {
    const error = crearElemento('div', 'mp-empty');
    error.appendChild(crearElemento('i', 'bi bi-wifi-off mp-empty-icon'));
    error.appendChild(crearElemento('h2', 'mp-empty-title', 'No se pudieron cargar los proyectos'));
    error.appendChild(crearElemento('p', 'mp-empty-text', 'Comprueba que has iniciado sesión y vuelve a intentarlo.'));

    const boton = crearElemento('button', 'btn btn-primary', 'Reintentar');
    boton.type = 'button';
    boton.addEventListener('click', () => location.reload());
    error.appendChild(boton);

    return error;
}


window.addEventListener('load', () => {
    const listadoProyecto = document.querySelector('#listado-proyectos');
    const contador = document.querySelector('#contador-proyectos');

    fetch('/proyecto-inter-modular/api/proyectos/obtenerProyectos.php')
        .then(function (respuesta) {
            if (!respuesta.ok) throw new Error(`HTTP ${respuesta.status}`);
            return respuesta.json();
        })
        .then(function (proyectos) {
            // Quitar las tarjetas de carga
            listadoProyecto.replaceChildren();
            listadoProyecto.setAttribute('aria-busy', 'false');

            if (contador) {
                contador.textContent = ` · ${proyectos.length} proyecto${proyectos.length === 1 ? '' : 's'}`;
            }

            if (proyectos.length === 0) {
                listadoProyecto.classList.add('mp-grid-vacio');
                listadoProyecto.appendChild(crearEstadoVacio());
                return;
            }

            for (const proyecto of proyectos) {
                listadoProyecto.appendChild(crearTarjetaProyecto(proyecto));
            }
            // Con al menos un proyecto, el botón de crear vive en la cabecera
            document.querySelector('#btnNuevoProyecto').classList.remove('d-none');
        })
        .catch(function (error) {
            console.log('Error:', error);
            listadoProyecto.replaceChildren(crearEstadoError());
            listadoProyecto.classList.add('mp-grid-vacio');
            listadoProyecto.setAttribute('aria-busy', 'false');
        })
})

const modalCrear = document.getElementById('exampleModal')
if (modalCrear) {
    // Cursor directo al nombre al abrir, y formulario limpio al cerrar
    modalCrear.addEventListener('shown.bs.modal', () => document.getElementById('nom_proyecto').focus())
    modalCrear.addEventListener('hidden.bs.modal', () => modalCrear.querySelector('form').reset())
}

const modalEliminar = document.getElementById('modalEliminar')
if (modalEliminar) {
    modalEliminar.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget
        document.getElementById('eliminarId').value = button.getAttribute('data-id-proyecto')
        document.getElementById('eliminarNombre').textContent = `«${button.getAttribute('data-nombre-proyecto')}»`
    })
}

const exampleModal = document.getElementById('exampleModal2')
if (exampleModal) {
    exampleModal.addEventListener('show.bs.modal', event => {
        // Button that triggered the modal
        const button = event.relatedTarget
        // Extract info from data-bs-* attributes
        const idProyecto = button.getAttribute('data-id-proyecto')
        const inputHiddenId = document.getElementById('idProyecto')
        inputHiddenId.value = idProyecto;
        document.getElementById('colProyectoNombre').textContent = `Al proyecto «${button.getAttribute('data-nombre-proyecto')}»`

        // Vaciar el select antes de rellenarlo (si no, se duplican las opciones cada vez que se abre)
        const listadoUsuario = document.querySelector('#usuarioSelect');
        const botonAniadir = document.getElementById('btnAniadirCol');
        listadoUsuario.replaceChildren(new Option('Cargando…', ''));
        botonAniadir.disabled = true;

        fetch(`/proyecto-inter-modular/api/tareas/obtenerUsuario.php?id_proyecto=${idProyecto}`)
            .then(function (respuesta) {
                return respuesta.json();
            })
            .then(function (usuarios) {
                listadoUsuario.replaceChildren();

                if (usuarios.length === 0) {
                    listadoUsuario.appendChild(new Option('No hay usuarios disponibles', ''));
                    return;
                }

                listadoUsuario.appendChild(new Option('Selecciona un usuario', ''));
                for (const usuario of usuarios) {
                    listadoUsuario.appendChild(new Option(usuario.nom_user, usuario.id_user));
                }
                botonAniadir.disabled = false;
            })
            .catch(function (error) {
                console.log('Error:', error);
                listadoUsuario.replaceChildren(new Option('Error al cargar usuarios', ''));
            })

    })
}
