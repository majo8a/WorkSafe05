<?php require_once 'encabezado.php'; ?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Gestión de Capacitaciones</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregar">
            + Nueva Capacitación
        </button>
    </div>

    <!-- Tabla -->
    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered table-striped" id="tablaCapacitaciones">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tema</th>
                        <th>Fechas</th>
                        <th>Modalidad</th>
                        <th>Asistentes</th>
                        <th>Constancias</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbodyCapacitaciones">
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL AGREGAR -->
<div class="modal fade" id="modalAgregar" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="formAgregar">

            <div class="modal-header">
                <h5 class="modal-title">Registrar Capacitación</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <label>Tema</label>
                <input type="text" class="form-control mb-2" name="tema" required>

                <label>Descripción</label>
                <textarea class="form-control mb-2" name="descripcion" required></textarea>

                <label>Fecha Inicio</label>
                <input type="date" class="form-control mb-2" name="fecha_inicio" required>

                <label>Fecha Fin</label>
                <input type="date" class="form-control mb-2" name="fecha_fin" required>

                <label>Modalidad</label>
                <select class="form-control mb-2" name="tipo_modalidad" required>
                    <option value="Presencial">Presencial</option>
                    <option value="Virtual">Virtual</option>
                    <option value="Mixta">Mixta</option>
                </select>

                <input type="hidden" name="id_usuario_asignador" value="<?=$_SESSION['id_usuario'] ?? $_SESSION['id']?>">
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary">Guardar</button>
            </div>

        </form>
    </div>
</div>

<!-- MODAL ASISTENCIA -->
<div class="modal fade" id="modalAsistencia" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="formAsistencia">

            <div class="modal-header">
                <h5 class="modal-title">Registrar Asistencia</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="id_capacitacion" id="asistencia_id_capacitacion">

                <label>Usuario</label>
                <input type="number" class="form-control mb-2" name="id_usuario" required>

                <label>¿Asistió?</label>
                <select class="form-control mb-2" name="asistio">
                    <option value="1">Sí</option>
                    <option value="0">No</option>
                </select>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary">Registrar</button>
            </div>

        </form>
    </div>
</div>

<!-- MODAL SUBIR CONSTANCIA -->
<div class="modal fade" id="modalConstancia" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="formConstancia" enctype="multipart/form-data">

            <div class="modal-header">
                <h5 class="modal-title">Subir Constancia</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <input type="hidden" name="id_capacitacion" id="constancia_id_capacitacion">

                <label>ID Usuario</label>
                <input class="form-control mb-2" name="id_usuario" required>

                <label>Archivo PDF</label>
                <input type="file" class="form-control mb-2" name="archivo" accept="application/pdf" required>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary">Subir</button>
            </div>

        </form>
    </div>
</div>

<script src="controlador/capacitaciones.js"></script>

</body>
