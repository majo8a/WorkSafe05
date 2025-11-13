<?php require_once 'encabezado.php'; ?>

<link rel="stylesheet" href="../build/css/app.css">
<nav class="navbar d-lg-none navbar-light bg-light p-2">
    <button class="btn btn-outline-primary" type="button" id="btnMenu">
        ☰ Menú
    </button>
</nav>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column p-3" id="sidebar">
        <h4 class="text-center">Menú</h4>
        <hr>
        <a href="configuracion.php">⚙️ Historial de movimientos</a>
        <a href="usuarios.php">👥 Usuarios</a>
        <a href="cuestionario.php">📋 Cuestionarios</a>
        <a href="bitacoras.php">🔗 Bitacora</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
     <script>
    // Toggle del sidebar en móviles
    const btnMenu = document.getElementById('btnMenu');
    const sidebar = document.getElementById('sidebar');

    btnMenu.addEventListener('click', () => {
        sidebar.classList.toggle('sidebar-open');
    });
</script>
    </body>

    </html>