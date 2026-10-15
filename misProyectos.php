<?php
session_start();
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Invitado';

// $id_proyecto = isset($_SESSION['id_proyecto']) ? $_SESSION['id_proyecto'] : 'null';
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mis proyectos · HerramientaGP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="./style.css?v=<?php echo filemtime(__DIR__ . "/style.css"); ?>">
    <link rel="stylesheet" href="./myStyle.css?v=<?php echo filemtime(__DIR__ . "/myStyle.css"); ?>">
</head>

<body>
    <nav class="navbar navbar-light bg-primary fixed-top">
        <div class="container-fluid d-flex justify-content-around">
            <a class="navbar-brand" href="./misProyectos.php" aria-label="Mis proyectos" title="Mis proyectos">
                <i class="bi bi-house fs-3 text-white"></i>
            </a>
            <a class="navbar-brand" href="./index.html" aria-label="Página principal">
                <img src="./img/logo_3-blanco.jpg" alt="Logo de HerramientaGP" width="50" height="50"
                    class="rounded-circle d-inline-block align-text-top">
            </a>
            <div id="usuario" class="dropdown usuario" data-username="<?php echo htmlspecialchars($username); ?>">
                <p id="parrafo" class="text-white"></p>
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false" aria-label="Menú de usuario">
                    <i class="bi bi-person fs-3 text-white"></i>
                </button>
                <ul class="dropdown-menu dropDownMenu">
                    <li><a class="dropdown-item" href="./logout.php">Cerrar Sesión</a></li>
                </ul>
            </div>


        </div>
    </nav>

    <main class="mp-page">
        <header class="mp-panel mp-header">
            <div>
                <h1 class="mp-title">Mis proyectos</h1>
                <p class="mp-subtitle">
                    Hola, <strong><?php echo htmlspecialchars($username); ?></strong>
                    <span id="contador-proyectos"></span>
                </p>
            </div>
            <!-- Oculto hasta saber si hay proyectos: sin proyectos se usa el botón "Crear el primero" -->
            <button type="button" id="btnNuevoProyecto" class="btn btn-primary mp-btn-nuevo d-none"
                data-bs-toggle="modal" data-bs-target="#exampleModal">
                <i class="bi bi-plus-lg"></i> Nuevo proyecto
            </button>
        </header>

        <section class="mp-panel" aria-label="Listado de proyectos">
            <div id="listado-proyectos" class="mp-grid" aria-live="polite" aria-busy="true">
                <!-- Tarjetas de carga, se sustituyen al recibir los proyectos -->
                <div class="mp-card mp-skeleton"></div>
                <div class="mp-card mp-skeleton"></div>
                <div class="mp-card mp-skeleton"></div>
            </div>
        </section>
    </main>

    <!-- Modal añadir proyecto -->

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="modalCrearLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered auth-modal-dialog">
            <div class="modal-content auth-modal">
                <header class="auth-header">
                    <h1 class="auth-title" id="modalCrearLabel">Crea tu proyecto</h1>
                    <p class="auth-subtitle">Ponle un nombre para empezar a organizar tareas</p>
                    <button type="button" class="btn-close btn-close-white auth-modal-close" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </header>

                <form class="auth-form" action="./php_controllers/userController.php" method="POST">
                    <input type="hidden" id="id" name="id" value="1">

                    <!-- Campo para el nombre -->
                    <div class="mb-4">
                        <label for="nom_proyecto" class="form-label auth-label">Nombre del proyecto</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-folder2-open"></i></span>
                            <input type="text" class="form-control auth-input" id="nom_proyecto" name="nom_proyecto"
                                maxlength="50" placeholder="Ej. Web del restaurante" aria-describedby="nomProyectoAyuda"
                                required>
                        </div>
                        <div id="nomProyectoAyuda" class="auth-help">Máximo 50 caracteres.</div>
                    </div>

                    <div class="auth-modal-actions">
                        <button type="button" class="btn btn-outline-primary auth-submit"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="crear-proyecto" class="btn btn-primary auth-submit">
                            <i class="bi bi-plus-lg"></i> Crear proyecto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Modal añadir col -->

    <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="modalColLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered auth-modal-dialog">
            <div class="modal-content auth-modal">
                <header class="auth-header">
                    <h1 class="auth-title" id="modalColLabel">Añade participantes</h1>
                    <p class="auth-subtitle" id="colProyectoNombre"></p>
                    <button type="button" class="btn-close btn-close-white auth-modal-close" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </header>

                <form class="auth-form" action="./php_controllers/userController.php" method="POST">
                    <input type="hidden" id="idProyecto" name="idProyecto">

                    <!-- Campo para el colaborador -->
                    <div class="mb-4">
                        <label for="usuarioSelect" class="form-label auth-label">Colaborador</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-plus"></i></span>
                            <select class="form-select auth-input" id="usuarioSelect" name="colaborador"
                                aria-describedby="colAyuda" required>

                            </select>
                        </div>
                        <div id="colAyuda" class="auth-help">Solo aparecen usuarios que aún no están en el proyecto.</div>
                    </div>

                    <div class="auth-modal-actions">
                        <button type="button" class="btn btn-outline-primary auth-submit"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="aniadir-col" id="btnAniadirCol" class="btn btn-primary auth-submit">
                            <i class="bi bi-person-plus"></i> Añadir
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Modal confirmar borrado -->

    <div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-danger" id="modalEliminarLabel">
                        <i class="bi bi-exclamation-triangle"></i> Eliminar proyecto
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-1">¿Seguro que quieres eliminar <strong id="eliminarNombre"></strong>?</p>
                    <p class="text-muted small mb-0">Se borrarán también todas sus tareas y participantes. Esta acción
                        no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <form action="./php_controllers/userController.php" method="POST">
                        <input type="hidden" id="eliminarId" name="id_proyecto">
                        <button type="button" class="btn btn-secondary text-primary"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="delete-proyecto" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.js"></script>
    <script src="./script.js?v=<?php echo filemtime(__DIR__ . "/script.js"); ?>"></script>
</body>



</html>
