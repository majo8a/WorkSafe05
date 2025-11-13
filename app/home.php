<?php require_once 'encabezado.php'; ?>
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<body>
  <div class="blog-contenido">
    <div class="blog-header">
      <h2 class="titulo-blog">Conoce la importancia de la NOM-035</h2>
    </div>

    <div class="blog-container">
      <img src="../src/img/img1.png" alt="blog" class="img-blog">

      <div class="blog-text">
        <p class="blog">
          La Norma Oficial Mexicana <span class="subrayado">NOM-035-STPS-2018</span>, Factores de Riesgo Psicosocial en el Trabajo
          – Identificación, Análisis y Prevención, es una regulación que busca proteger la salud mental y el bienestar de los trabajadores en México.
          <br><br>
          Este cuestionario es una herramienta esencial dentro de la implementación de la NOM-035.
          Su objetivo es conocer la percepción de los trabajadores respecto a aspectos como la carga de trabajo, liderazgo, relaciones laborales y condiciones del puesto.
          <br><br>
          Al participar, se contribuye a:
          <br>
          • <span class="subrayado">Detectar riesgos psicosociales que puedan afectar la salud y el clima laboral.</span>
          <br>
          • <span class="subrayado">Promover un ambiente de trabajo sano y productivo, fortaleciendo la cultura organizacional.</span>
          <br>
          • <span class="subrayado">Cumplir con las disposiciones legales de la Secretaría del Trabajo y Previsión Social (STPS).</span>
          <br>
          Tu colaboración es confidencial y de gran importancia, ya que permitirá diseñar acciones y estrategias que beneficien tanto a los trabajadores como a la organización.
        </p>
        <button class="inicioTest" href="cuestionario.php">Iniciar Cuestionario</button>
      </div>
    </div>

    <br>
    <div class="blog-tarjetas">
      <h2 class="titulo-tarjetas">Beneficios</h2>

      <div class="tarjetas-container">
        <!-- Tarjeta 1 -->
        <div class="card">
          <i class="bi bi-collection"></i>
          <h5 class="card-title">Centralización de información</h5>
          <p class="card-text">
            Facilita la gestión y el acceso a todos los resultados en un solo lugar, evitando dispersión de datos y mejorando la organización.
          </p>
        </div>

        <!-- Tarjeta 2 -->
        <div class="card">
          <i class="bi bi-shield-lock"></i>
          <h5 class="card-title">Seguridad y confidencialidad</h5>
          <p class="card-text">
            Brinda confianza a los colaboradores al garantizar que sus respuestas están protegidas, lo cual es clave para obtener información veraz y fomentar la participación.
          </p>
        </div>

        <!-- Tarjeta 3 -->
        <div class="card">
          <i class="bi bi-graph-up-arrow"></i>
          <h5 class="card-title">Seguimiento continuo</h5>
          <p class="card-text">
            Permite monitorear de manera constante los factores de riesgo psicosocial, detectando a tiempo áreas de mejora y evitando que pequeños problemas se conviertan en crisis.
          </p>
        </div>
      </div>

       <h2 class="titulo-resultados" style="color: #011640; text-align: center; font-size: 3rem; padding: 2rem; font-family: Space Grotesk, sans-serif; text-transform: uppercase; text-shadow: rgb(0, 0, 0) 1px 1px; @media (max-width: 768px) {font-size: 2rem; padding: 1rem;}">Resultados</h2>

      <div class="blog-container">
        <div class="resultados">
          <p class="blog-resultados">
            <span class="subrayado2">WorkSafe05</span> es un software gratuito que permite a los colaboradores responder cuestionarios sobre factores de riesgo psicosocial de manera sencilla y segura desde cualquier dispositivo.
            <br><br>
            La implementación de la <span class="subrayado2">NOM-035</span> es obligatoria para todos los centros de trabajo en México, y esta herramienta contribuye al cumplimiento normativo y a la promoción de un entorno laboral saludable.
          </p>
        </div>
        <img src="../src/img/img2.png" alt="blog" class="img-blog">
      </div>
    </div>
</body>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php require_once 'footer.php'; ?>
</body>
</html>
