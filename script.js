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
    fetch('/proyecto-inter-modular/api/proyectos/obtenerProyectos.php')
        .then(function (respuesta) {
            return respuesta.json();
        })
        .then(function (proyectos) {


            const listadoProyecto = document.querySelector('#listado-proyectos');

            for (const proyecto of proyectos) {

                const card = document.createElement('div');
                card.className = 'card card-item';

                // Crear la sección del ícono
                const cardIcon = document.createElement('div');
                cardIcon.className = 'card-icon';

                const h3Icon = document.createElement('h3');
                h3Icon.textContent = 'Pr'; // Texto del ícono
                cardIcon.appendChild(h3Icon);
                card.appendChild(cardIcon);

                // Crear la sección del texto y botón
                const cardTextIngresar = document.createElement('div');
                cardTextIngresar.className = 'card-text-ingresar';

                // Subsección de texto
                const cardText = document.createElement('div');
                cardText.className = 'card-text';

                const h5Title = document.createElement('h5');
                h5Title.className = 'text-primary card-title';
                h5Title.textContent = proyecto.nom_proyecto; // Título del proyecto
                h5Title.dataset.nombreProyecto = proyecto.nom_proyecto;

                const formText = document.createElement('form');
                formText.className = "ingresar-btn";
                formText.action = './php_controllers/userController.php';
                formText.method = 'POST';

                const inputHidden1 = document.createElement('input');
                inputHidden1.type = 'hidden';
                inputHidden1.name = 'nom_proyecto';
                inputHidden1.value = proyecto.nom_proyecto;

                const buttonIngresar = document.createElement('button');
                buttonIngresar.type = 'submit';
                buttonIngresar.className = 'btn btn-pink text-danger card-item-boton';
                buttonIngresar.name = 'acceder-proyecto';
                buttonIngresar.value = proyecto.id_proyecto;
                buttonIngresar.textContent = 'Ingresar';

                formText.appendChild(inputHidden1);
                formText.appendChild(buttonIngresar);
                cardText.appendChild(h5Title);
                cardText.appendChild(formText);
                cardTextIngresar.appendChild(cardText);

                // Subsección del botón de cerrar
                const formClose = document.createElement('form');
                formClose.className = 'close-btn';
                formClose.action = './php_controllers/userController.php';
                formClose.method = 'POST';

                const inputHidden = document.createElement('input');
                inputHidden.type = 'hidden';
                inputHidden.id = proyecto.id_proyecto;
                inputHidden.name = 'id_proyecto';
                inputHidden.value = proyecto.id_proyecto;

                const buttonClose = document.createElement('button');
                buttonClose.type = 'submit';
                buttonClose.className = 'close-btn';
                buttonClose.name = 'delete-proyecto';

                const iconClose = document.createElement('i');
                iconClose.className = 'bi bi-x';

                const buttonAgrecarCol = document.createElement('button')
                buttonAgrecarCol.type = 'submit'
                buttonAgrecarCol.className = 'aniadir-boton-usuario'; // Asignar clases
                buttonAgrecarCol.name = 'aniadir-usuario';
                buttonAgrecarCol.setAttribute('data-bs-toggle', 'modal'); // Asignar atributos personalizados
                buttonAgrecarCol.setAttribute('data-bs-target', '#exampleModal2');
                buttonAgrecarCol.setAttribute('data-id-proyecto', proyecto.id_proyecto);

                // Crear el ícono
                const icon = document.createElement('i');
                icon.className = 'bi bi-person-plus-fill';

                buttonAgrecarCol.appendChild(icon);



                buttonClose.appendChild(iconClose);
                formClose.appendChild(inputHidden);
                formClose.appendChild(buttonClose);
                cardTextIngresar.appendChild(buttonAgrecarCol);
                cardTextIngresar.appendChild(formClose);

                // Añadir la sección de texto-ingresar al contenedor principal
                card.appendChild(cardTextIngresar);

                listadoProyecto.appendChild(card);

            }



        })
})

const exampleModal = document.getElementById('exampleModal2')
if (exampleModal) {
  exampleModal.addEventListener('show.bs.modal', event => {
    // Button that triggered the modal
    const button = event.relatedTarget
    // Extract info from data-bs-* attributes
    const idProyecto = button.getAttribute('data-id-proyecto')
    // If necessary, you could initiate an Ajax request here
    // and then do the updating in a callback.
    const inputHiddenId = document.getElementById('idProyecto')
    inputHiddenId.value = idProyecto;
    // Update the modal's content.
    // const modalTitle = exampleModal.querySelector('.modal-title')
    // const modalBodyInput = exampleModal.querySelector('.modal-body input')

    // modalTitle.textContent = `New message to ${recipient}`
    // modalBodyInput.value = recipient
  })
}


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




