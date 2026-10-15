<?php
session_start();

// Sin sesión o sin proyecto elegido, esta página no puede cargar nada
if (!isset($_SESSION['user_id'])) {
    header('Location: ./inicioSesion.php');
    exit();
}
if (!isset($_SESSION['id_proyecto'])) {
    header('Location: ./misProyectos.php');
    exit();
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Invitado';

$nom_proyecto = isset($_SESSION['nom_proyecto']) ? $_SESSION['nom_proyecto'] : 'Proyecto';

$capitalizado = ucfirst(trim($nom_proyecto));


?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($capitalizado); ?> · HerramientaGP</title>
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

    <main class="mp-page mt-page">
        <header class="mp-panel mp-header">
            <div class="mt-header-text">
                <a class="mt-back" href="./misProyectos.php"><i class="bi bi-arrow-left"></i> Mis proyectos</a>
                <h1 class="mp-title"><?php echo htmlspecialchars($capitalizado); ?></h1>
                <p class="mp-subtitle" id="resumen-tareas">Cargando tareas…</p>
            </div>
            <button type="button" id="btnNuevaTarea" class="btn btn-primary mp-btn-nuevo" data-bs-toggle="modal"
                data-bs-target="#exampleModal">
                <i class="bi bi-plus-lg"></i> Nueva tarea
            </button>
        </header>

        <!-- Pestañas: solo en móvil, donde se ve una columna cada vez -->
        <div class="mt-tabs" role="tablist" aria-label="Columnas del tablero">
            <button type="button" class="mt-tab" role="tab" id="tab-1" data-estado="1" aria-controls="col-1"
                aria-selected="true"><span class="mt-tab-nombre"><span class="mt-dot mt-dot-1" aria-hidden="true"></span> Por hacer</span>
                <span class="mt-count" id="tab-contador-1">0</span></button>
            <button type="button" class="mt-tab" role="tab" id="tab-2" data-estado="2" aria-controls="col-2"
                aria-selected="false" tabindex="-1"><span class="mt-tab-nombre"><span class="mt-dot mt-dot-2" aria-hidden="true"></span> En progreso</span>
                <span class="mt-count" id="tab-contador-2">0</span></button>
            <button type="button" class="mt-tab" role="tab" id="tab-3" data-estado="3" aria-controls="col-3"
                aria-selected="false" tabindex="-1"><span class="mt-tab-nombre"><span class="mt-dot mt-dot-3" aria-hidden="true"></span> Hecha</span>
                <span class="mt-count" id="tab-contador-3">0</span></button>
        </div>

        <!-- Tablero: una columna por estado (id_estado 1, 2 y 3) -->
        <section class="mt-board" aria-label="Tablero de tareas">
            <div class="mp-panel mt-col mt-col-activa" id="col-1" data-estado="1">
                <h2 class="mt-col-head">
                    <span class="mt-dot mt-dot-1" aria-hidden="true"></span> Por hacer
                    <span class="mt-count" id="contador-1">0</span>
                </h2>
                <div class="mt-col-list" id="todo-tarea" data-vacio="Aún no hay tareas" data-vacio-movil="Aún no hay tareas por hacer"><div class="mt-card mp-skeleton"></div><div class="mt-card mp-skeleton"></div></div>
            </div>
            <div class="mp-panel mt-col" id="col-2" data-estado="2">
                <h2 class="mt-col-head">
                    <span class="mt-dot mt-dot-2" aria-hidden="true"></span> En progreso
                    <span class="mt-count" id="contador-2">0</span>
                </h2>
                <div class="mt-col-list" id="doing-tarea" data-vacio="Arrastra aquí las tareas que empieces" data-vacio-movil="No hay tareas en progreso"><div class="mt-card mp-skeleton"></div></div>
            </div>
            <div class="mp-panel mt-col" id="col-3" data-estado="3">
                <h2 class="mt-col-head">
                    <span class="mt-dot mt-dot-3" aria-hidden="true"></span> Hecha
                    <span class="mt-count" id="contador-3">0</span>
                </h2>
                <div class="mt-col-list" id="done-tarea" data-vacio="Arrastra aquí las tareas terminadas" data-vacio-movil="No hay tareas terminadas"><div class="mt-card mp-skeleton"></div></div>
            </div>
        </section>
    </main>

    <!-- Aviso flotante (errores al mover tareas) -->
    <div class="mt-aviso" id="aviso" role="status" aria-live="polite"></div>

    <!-- Modal crear tarea -->

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="modalTareaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered auth-modal-dialog mt-modal-tarea">
            <div class="modal-content auth-modal">
                <header class="auth-header">
                    <!-- Textos que tareas.js cambia en modo edición -->
                    <h1 class="auth-title" id="modalTareaLabel">Crea una tarea</h1>
                    <p class="auth-subtitle" id="modalTareaSubtitulo">Se añadirá a la columna «Por hacer»</p>
                    <button type="button" class="btn-close btn-close-white auth-modal-close" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </header>

                <form class="auth-form" id="formTarea" action="./php_controllers/userController.php" method="POST">
                    <input type="hidden" id="id" name="id" value="1">
                    <input type="hidden" id="id_estado" name="id_estado" value="1">
                    <!-- Vacío al crear; con el id de la tarea en modo edición -->
                    <input type="hidden" id="idTareaEditar" value="">

                    <div class="alert alert-danger d-none d-flex align-items-center gap-2 py-2" role="alert"
                        id="errorTarea"></div>

                    <div class="mb-3">
                        <label for="nom_tarea" class="form-label auth-label">Nombre de la tarea</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                            <input type="text" class="form-control auth-input" id="nom_tarea" name="nom_tarea"
                                maxlength="100" placeholder="Ej. Maquetar la portada" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label auth-label">Descripción <span
                                class="auth-opcional">(opcional)</span></label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="2"
                            maxlength="200"></textarea>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-sm-6">
                            <label for="usuarioSelect" class="form-label auth-label">Encargado</label>
                            <select class="form-select auth-input" id="usuarioSelect" name="encargado">
                                <option value="">Sin asignar</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label for="tipoSelect" class="form-label auth-label">Tipo</label>
                            <select class="form-select auth-input" id="tipoSelect" name="tipo" required>

                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <label for="fechaIni" class="form-label auth-label">Inicio</label>
                            <input type="date" class="form-control auth-input" id="fechaIni" name="fecha-ini">
                        </div>
                        <div class="col-6">
                            <label for="fechaFin" class="form-label auth-label">Fin</label>
                            <input type="date" class="form-control auth-input" id="fechaFin" name="fecha-fin">
                        </div>
                    </div>

                    <div class="auth-modal-actions">
                        <button type="button" class="btn btn-outline-primary auth-submit"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="crear-tarea" id="btnGuardarTarea" class="btn btn-primary auth-submit">
                            <i class="bi bi-plus-lg"></i> Crear tarea
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Modal detalle de tarea (se rellena desde tareas.js al pulsar una tarjeta) -->

    <div class="modal fade" id="modalDetalleTarea" tabindex="-1" aria-labelledby="detalleTitulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable auth-modal-dialog mt-modal-tarea">
            <div class="modal-content auth-modal">
                <header class="auth-header mt-detalle-header">
                    <span class="mt-tipo" id="detalleTipo"></span>
                    <h1 class="auth-title mt-detalle-titulo" id="detalleTitulo"></h1>
                    <button type="button" class="btn-close btn-close-white auth-modal-close" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </header>

                <div class="modal-body auth-form mt-detalle">
                    <h2 class="mt-detalle-label">Descripción</h2>
                    <p class="mt-detalle-desc" id="detalleDescripcion"></p>

                    <dl class="mt-detalle-datos">
                        <div>
                            <dt>Estado</dt>
                            <dd id="detalleEstado"></dd>
                        </div>
                        <div>
                            <dt>Encargado</dt>
                            <dd id="detalleEncargado" class="mt-encargado"></dd>
                        </div>
                        <div>
                            <dt>Inicio</dt>
                            <dd id="detalleInicio"></dd>
                        </div>
                        <div>
                            <dt>Fin</dt>
                            <dd id="detalleFin"></dd>
                        </div>
                    </dl>

                    <h2 class="mt-detalle-label">Mover a</h2>
                    <div class="mt-detalle-mover" id="detalleMover"></div>
                </div>

                <div class="auth-modal-actions mt-detalle-acciones">
                    <button type="button" class="btn btn-outline-danger auth-submit" id="detalleEliminar"
                        data-bs-toggle="modal" data-bs-target="#modalEliminarTarea">
                        <i class="bi bi-trash"></i> Eliminar
                    </button>
                    <!-- Abre el modal de tarea en modo edición (ver show.bs.modal en tareas.js) -->
                    <button type="button" class="btn btn-primary auth-submit" id="detalleEditar"
                        data-bs-toggle="modal" data-bs-target="#exampleModal">
                        <i class="bi bi-pencil"></i> Editar
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal confirmar borrado de tarea -->

    <div class="modal fade" id="modalEliminarTarea" tabindex="-1" aria-labelledby="modalEliminarTareaLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered auth-modal-dialog">
            <div class="modal-content auth-modal">
                <header class="auth-header auth-header-peligro">
                    <h1 class="auth-title" id="modalEliminarTareaLabel">
                        <i class="bi bi-exclamation-triangle"></i> Eliminar tarea
                    </h1>
                    <button type="button" class="btn-close btn-close-white auth-modal-close" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </header>

                <form class="auth-form" action="./php_controllers/userController.php" method="POST">
                    <input type="hidden" id="eliminarTareaId" name="id_tarea">
                    <p class="auth-texto mb-1">¿Seguro que quieres eliminar <strong id="eliminarTareaNombre"></strong>?</p>
                    <p class="auth-help mb-4">Esta acción no se puede deshacer.</p>

                    <div class="auth-modal-actions">
                        <button type="button" class="btn btn-outline-primary auth-submit"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="delete-tarea" class="btn btn-danger auth-submit">
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.js"></script>
    <script src="./tareas.js?v=<?php echo filemtime(__DIR__ . "/tareas.js"); ?>"></script>
</body>



</html>
