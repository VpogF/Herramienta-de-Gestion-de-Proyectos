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

                const formText = document.createElement('form');
                const buttonIngresar = document.createElement('button');
                buttonIngresar.type = 'submit';
                buttonIngresar.className = 'btn btn-pink text-danger card-item-boton';
                buttonIngresar.textContent = 'Ingresar';

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
                inputHidden.name = 'id_proyecto';
                inputHidden.value = proyecto.id_proyecto; 

                const buttonClose = document.createElement('button');
                buttonClose.type = 'submit';
                buttonClose.className = 'close-btn';
                buttonClose.name = 'delete-proyecto';

                const iconClose = document.createElement('i');
                iconClose.className = 'bi bi-x';

                buttonClose.appendChild(iconClose);
                formClose.appendChild(inputHidden);
                formClose.appendChild(buttonClose);
                cardTextIngresar.appendChild(formClose);

                // Añadir la sección de texto-ingresar al contenedor principal
                card.appendChild(cardTextIngresar);

                listadoProyecto.appendChild(card);

            }



        })
})





