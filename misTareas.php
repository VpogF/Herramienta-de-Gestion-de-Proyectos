<?php
session_start();
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Invitado';

$id_proyecto = isset($_SESSION['id_proyecto']) ? $_SESSION['id_proyecto'] : 'null';

$nom_proyecto = isset($_SESSION['nom_proyecto']) ? $_SESSION['nom_proyecto'] : 'Cargando';

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HerramientaGP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="./myStyle.css">
</head>

<body>
    <nav class="navbar navbar-light bg-primary fixed-top">
        <div class="container-fluid d-flex justify-content-around">
            <a class="navbar-brand" href="/misProyectos.html">
                <i class="bi bi-house fs-3 text-white"></i>
            </a>
            <a class="navbar-brand" href="/index.html">
                <img src="./img/logo_3-blanco.jpg" alt="" width="50" height="50" class="rounded-circle"
                    class="d-inline-block align-text-top">
            </a>
            <div id="usuario" class="dropdown usuario" data-username="<?php echo htmlspecialchars($username); ?>">
                <p id="parrafo" class="text-white"></p>
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="bi bi-person fs-3 text-white"></i>
                </button>
                <ul class="dropdown-menu dropDownMenu">
                    <li><a class="dropdown-item" href="./logout.php">Cerrar Sesión</a></li>
                </ul>
            </div>


        </div>
    </nav>

    <div class="proyectos-container">
        <div class="bg-beige tareas-titulo-container">
            <h3 class="text-primary ps-4"><?php echo $nom_proyecto ?></h3>
            <div class="aniadir">
                <!-- Button trigger modal -->
                <button type="button" class="bg-beige aniadir-boton" data-bs-toggle="modal"
                    data-bs-target="#exampleModal">
                    <i class="bi bi-plus-square"></i>
                </button>
            </div>
        </div>
        <div class="tareas-listado-container">
            <div id="listado-tareas" class="bg-beige listado-tarea-columna">
                <div class="col-list-titulo">
                    <p style="margin-bottom: 0rem;">TO DO</p>
                </div>
                <div class="col-list">
                    <div class="tarea-prueba">

                    </div>
                    <div class="tarea-prueba">

                    </div>
                    <div class="tarea-prueba">

                    </div>
                    <div class="tarea-prueba">

                    </div>
                    <div class="tarea-prueba">

                    </div>
                    <div class="tarea-prueba">

                    </div>
                </div>
            </div>
            <div id="listado-tareas" class="bg-beige listado-tarea-columna">
                <div class="col-list-titulo">
                    <p style="margin-bottom: 0rem;">DOING</p>
                </div>
            </div>
            <div id="listado-tareas" class="bg-beige listado-tarea-columna">
                <div class="col-list-titulo">
                    <p style="margin-bottom: 0rem;">DONE</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-beige">
                <div class="modal-header bg-primary">
                    <h1 class="modal-title fs-5 text-pink" id="exampleModalLabel">Crea una tarea</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="./php_controllers/userController.php" method="POST">
                        <div>
                            <input type="hidden" id="id" name="id" value="1">
                            <input type="hidden" id="id_estado" name="id_estado" value="1">
                            <input type="hidden" id="id_proyecto" name="id_proyecto" value="<?php echo $nom_proyecto ?>">

                            <!-- Campo para el nombre -->
                            <label for="exampleFormControlInput1" class="form-label text-danger label-class">Nombre
                                Tarea:</label>
                            <input type="text" class="form-control input-class" id="nom_tarea" name="nom_tarea"
                                required>
                            <label for="exampleFormControlInput1"
                                class="form-label text-danger label-class">Descripción:</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                            <label for="exampleSelect" class="form-label text-danger label-class">Encargado:</label>
                            <select class="form-select" id="exampleSelect" name="encargado">
                                <option value="opcion1">Usuario 1</option>
                                <option value="opcion2">Usuario 2</option>
                                <option value="opcion3">Usuario 3</option>
                            </select>
                            <div class="row">
                                <div class="col">
                                    <label for="exampleFormControlInput1"
                                        class="form-label text-danger label-class">Fecha Inicio:</label>
                                    <input type="date" class="form-control" id="dateInput">
                                </div>
                                <div class="col">
                                    <label for="exampleFormControlInput1"
                                        class="form-label text-danger label-class">Fecha Fin:</label>
                                    <input type="date" class="form-control" id="dateInput">
                                </div>
                            </div>
                            <label for="exampleSelect" class="form-label text-danger label-class">Tipo:</label>
                            <select class="form-select" id="exampleSelect" name="tipo">
                                <option value="opcion1">Frontend</option>
                                <option value="opcion2">Backend</option>
                                <option value="opcion3">Diseño</option>
                            </select>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary text-primary"
                                    data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" name='crear-tarea'
                                    class="btn btn-pink text-danger">Confirmar</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>



    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.js"></script>
    <!-- <script src="./script.js"></script> -->
</body>



</html>