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


window.addEventListener('load', () => {
    fetch('/proyecto-inter-modular/api/tareas/obtenerTipo.php')
        .then(function (respuesta) {
            return respuesta.json();
        })
        .then(function (tipos) {


            const listadoTipo = document.querySelector('#tipoSelect');

            for (const tipo of tipos) {

                const option = document.createElement('option');
                option.textContent = tipo.nom_tipo;
                option.value = tipo.id_tipo;

                listadoTipo.appendChild(option);
            }
        })
        .catch(function (error) {
            debugger;
            console.log('Error:', error);
        })
})

window.addEventListener('load', () => {
    fetch('/proyecto-inter-modular/api/tareas/obtenerUsuario.php')
        .then(function (respuesta) {
            return respuesta.json();
        })
        .then(function (usuarios) {


            const listadoUsuario = document.querySelector('#usuarioSelect');

            for (const usuario of usuarios) {

                const option = document.createElement('option');
                option.textContent = usuario.nom_user;
                option.value = usuario.id_user;

                listadoUsuario.appendChild(option);
            }
        })
        .catch(function (error) {
            debugger;
            console.log('Error:', error);
        })
})

window.addEventListener('load', () => {
    fetch('/proyecto-inter-modular/api/tareas/obtenerTareas.php')
        .then(function (respuesta) {
            return respuesta.json();
        })
        .then(function (tareas) {


            const listadoTareas = document.querySelector('#todo-tarea');

            for (const tarea of tareas) {

                const tareaCard = document.createElement("div");
                
                tareaCard.className = 'tarea-prueba';

                const tareaText = document.createElement("div");
                tareaText.className = 'texto-card-tarea'


                const nombreTarea = document.createElement("article");
                nombreTarea.innerHTML = `<p id="nombre-tarea">Nombre de la tarea: ${tarea.nom_tarea}</p>`;

                const encargado = document.createElement("article");
                encargado.innerHTML = `<p id="encargado">Encargado: ${tarea.nom_user}</p>`;

                const fechas = document.createElement("article");
                fechas.innerHTML = `
                <p>Fecha de inicio: 
                    <time id="fecha-inicio" datetime="${tarea.fecha_inicio}">${tarea.fecha_inicio}</time>
                </p>
                <p>Fecha de fin: 
                    <time id="fecha-fin" datetime="${tarea.fecha_fin}">${tarea.fecha_fin}</time>
                </p>`;

                const tipoTarea = document.createElement("article");
                tipoTarea.innerHTML = `<p id="tipo-tarea">Tipo de Tarea: ${tarea.nom_tipo}</p>`;

                // Agregar los artículos al contenedor del proyecto
                tareaText.appendChild(nombreTarea);
                tareaText.appendChild(encargado);
                tareaText.appendChild(fechas);
                tareaText.appendChild(tipoTarea);           

                const formClose = document.createElement('form');
                formClose.className = 'close-btn';
                formClose.action = './php_controllers/userController.php';
                formClose.method = 'POST';

                const inputHidden = document.createElement('input');
                inputHidden.type = 'hidden';
                inputHidden.name = 'id_tarea';
                inputHidden.value = tarea.id_tarea; 

                const buttonClose = document.createElement('button');
                buttonClose.type = 'submit';
                buttonClose.className = 'close-btn';
                buttonClose.name = 'delete-tarea';

                const iconClose = document.createElement('i');
                iconClose.className = 'bi bi-x';

                buttonClose.appendChild(iconClose);
                formClose.appendChild(inputHidden);
                formClose.appendChild(buttonClose);

                tareaCard.appendChild(tareaText);
                tareaCard.appendChild(formClose);

                listadoTareas.appendChild(tareaCard);
            
            }
        })
    })
    .catch(function (error) {
        debugger;
        console.log('Error:', error);
    })