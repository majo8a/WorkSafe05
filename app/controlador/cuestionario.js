var App = angular.module("app", []);

App.controller("CuestionarioCtrl", function ($scope, $http) {
  // Inicializaciones
  $scope.cuestionarios = [];
  $scope.cuestionario = {};
  $scope.cuestionarioMod = {};
  $scope.preguntas = [];
  $scope.nuevaPregunta = { texto_pregunta: "", tipo_calificacion: "Likert", puntaje_maximo: 4, orden: null, dimension: "", dominio: "", categoria: "", grupo_aplicacion: "" };
  $scope.preguntasDelCuestionario = [];
  $scope.cuestionarioSeleccionado = null;

    // Inicializa nueva pregunta con opciones base
  $scope.resetNuevaPregunta = function() {
    $scope.nuevaPregunta = {
      texto_pregunta: "",
      tipo_calificacion: "Likert",
      puntaje_maximo: 4,
      orden: null,
      dimension: "",
      dominio: "",
      categoria: "",
      grupo_aplicacion: "",
      opciones: [
        { etiqueta: "Siempre", valor: 4 },
        { etiqueta: "Casi siempre", valor: 3 },
        { etiqueta: "Algunas veces", valor: 2 },
        { etiqueta: "Casi nunca", valor: 1 },
        { etiqueta: "Nunca", valor: 0 }
      ]
    };
  };

  $scope.resetNuevaPregunta = function () {
  $scope.nuevaPregunta = {
    texto_pregunta: "",
    tipo_calificacion: "Likert",
    puntaje_maximo: 4,
    orden: null,
    dimension: "",
    dominio: "",
    categoria: "",
    grupo_aplicacion: "",
    opciones: []
  };

  $scope.cambiarTipoPregunta();

  // 🔥 Regresar foco al textarea
  setTimeout(function () {
    const input = document.getElementById("textoPreguntaInput");
    if (input) input.focus();
  }, 0);
};

  // Funciones de opciones
  $scope.agregarOpcion = function () {
    if (!$scope.nuevaPregunta.opciones) $scope.nuevaPregunta.opciones = [];
    $scope.nuevaPregunta.opciones.push({ etiqueta: "", valor: 0 });
  };

  $scope.eliminarOpcion = function (index) {
    $scope.nuevaPregunta.opciones.splice(index, 1);
  };

  // CONSULTAR cuestionarios
  $scope.consultar = function () {
    $http.post("../api/cuestionario/consultarCuestionario.php").then(function (res) {
      $scope.cuestionarios = res.data;
    }, function () {
      alert("Error al consultar cuestionarios");
    });
  };
  $scope.consultar();

  // SELECCIONAR -> abre modal modificar y fija seleccionado
  $scope.seleccionar = function (c) {
    $scope.cuestionarioMod = angular.copy(c);
    $scope.cuestionarioSeleccionado = c;
    // al abrir, traemos sus preguntas para mostrar en el modal
    $scope.cargarPreguntas(c.id_cuestionario, function () {
      new bootstrap.Modal(document.getElementById("ModalMod")).show();
    });
  };

  // Cargar preguntas del cuestionario (reutilizable)
  $scope.cargarPreguntas = function (id_cuestionario, cb) {
    $http.get("../api/cuestionario/preguntasCuestionario.php?id_cuestionario=" + id_cuestionario)
      .then(function (res) {
        if (res.data.status === "success") {
          $scope.preguntasDelCuestionario = res.data.preguntas;
        } else {
          $scope.preguntasDelCuestionario = [];
          alert("⚠️ " + (res.data.message || "No se pudieron cargar preguntas"));
        }
        if (typeof cb === "function") cb();
      }, function () {
        alert("Error al obtener preguntas");
        if (typeof cb === "function") cb();
      });
  };

  // MODIFICAR cuestionario (guarda cambios)
  $scope.modificarCuestionario = function () {
    if (!$scope.cuestionarioMod.id_cuestionario) {
      alert("Cuestionario no seleccionado.");
      return;
    }
    $http.post("../api/cuestionario/modificarCuestionario.php", $scope.cuestionarioMod)
      .then(function (res) {
        if (res.data.status === "success") {
          alert("✅ Cuestionario actualizado");
          $scope.consultar();
          bootstrap.Modal.getInstance(document.getElementById("ModalMod")).hide();
        } else {
          alert("⚠️ " + (res.data.message || "Error al actualizar"));
        }
      }, function () {
        alert("Error al conectar para modificar cuestionario");
      });
  };

  // AGREGAR pregunta desde el modal Modificar
 $scope.agregarPreguntaModal = function () {

  if (!$scope.cuestionarioSeleccionado || !$scope.cuestionarioSeleccionado.id_cuestionario) {
    alert("Selecciona un cuestionario primero.");
    return;
  }

  if (!$scope.nuevaPregunta.texto_pregunta ||
      $scope.nuevaPregunta.texto_pregunta.trim() === "") {
    alert("Escribe el texto de la pregunta.");
    return;
  }

  // 📌 ORDEN AUTOMÁTICO
  var orden = $scope.preguntasDelCuestionario.length + 1;

  var payload = {
    id_cuestionario: $scope.cuestionarioSeleccionado.id_cuestionario,
    texto_pregunta: $scope.nuevaPregunta.texto_pregunta,
    tipo_calificacion: $scope.nuevaPregunta.tipo_calificacion,
    puntaje_maximo: $scope.nuevaPregunta.puntaje_maximo,
    orden: orden,
    dimension: $scope.nuevaPregunta.dimension || "",
    dominio: $scope.nuevaPregunta.dominio || "",
    categoria: $scope.nuevaPregunta.categoria || "",
    grupo_aplicacion: $scope.nuevaPregunta.grupo_aplicacion || "",
    opciones: $scope.nuevaPregunta.opciones || []
  };

  $http.post("../api/pregunta/guardarPregunta.php", payload)
    .then(function (res) {
      if (res.data.status === "success") {

        alert("✅ Pregunta agregada correctamente");

        // 🔄 Recargar preguntas
        $scope.cargarPreguntas($scope.cuestionarioSeleccionado.id_cuestionario);

        // 🧹 Reset formulario
        $scope.resetNuevaPregunta();

        // 🔥 Foco automático
        setTimeout(function () {
          const input = document.getElementById("textoPreguntaInputMod");
          if (input) input.focus();
        }, 0);

      } else {
        alert("⚠️ " + (res.data.message || "Error al agregar la pregunta"));
      }
    }, function () {
      alert("❌ Error al guardar la pregunta");
    });
};


  $scope.nuevaPregunta = angular.copy($scope.nuevaPregunta); // o $scope.resetNuevaPregunta()


   // EDITAR PREGUNTA DESDE EL MODAL VER PREGUNTAS
  $scope.editarPregunta = function(p) {
    const nuevoTexto = prompt("Editar texto de la pregunta:", p.texto_pregunta);
    if (nuevoTexto === null) return; // cancelado

    const nuevoPuntaje = prompt("Nuevo puntaje máximo:", p.puntaje_maximo || 4);
    const nuevoOrden = prompt("Nuevo orden:", p.orden || "");

    const payload = {
      id_pregunta: p.id_pregunta,
      texto_pregunta: nuevoTexto.trim(),
      puntaje_maximo: parseInt(nuevoPuntaje) || 4,
      orden: nuevoOrden ? parseInt(nuevoOrden) : null,
      tipo_calificacion: p.tipo_calificacion
    };

    $http.post("../api/pregunta/modificarPregunta.php", payload).then(
      function(res) {
        if (res.data.status === "success") {
          alert("✅ Pregunta actualizada correctamente");
          $scope.cargarPreguntas($scope.cuestionarioSeleccionado.id_cuestionario);
        } else {
          alert("⚠️ " + (res.data.message || "Error al actualizar la pregunta"));
        }
      },
      function() {
        alert("❌ Error al conectar con el servidor para modificar la pregunta");
      }
    );
  };

  //ELIMINAR PREGUNTA DESDE EL MODAL VER PREGUNTA
  $scope.eliminarPregunta = function(id_pregunta) {
    if (!confirm("¿Deseas eliminar esta pregunta?")) return;

    $http.post("../api/pregunta/eliminarPregunta.php", { id_pregunta: id_pregunta })
    .then(function(res) {
        if (res.data.status === "success") {
          alert("🗑️ Pregunta eliminada correctamente");
          $scope.cargarPreguntas($scope.cuestionarioSeleccionado.id_cuestionario);
        } else {
          alert("⚠️ " + (res.data.message || "No se pudo eliminar la pregunta"));
        }
      },
      function() {
        alert("❌ Error al intentar eliminar la pregunta");
      }
    );
  };
  // AGREGAR PREGUNTAS TEMPORALES (en modal NUEVO)
$scope.agregarPreguntaTemp = function () {
    if (!$scope.nuevaPregunta.opciones || $scope.nuevaPregunta.opciones.length === 0) {
        $scope.nuevaPregunta.opciones = [
            { etiqueta: "Siempre", valor: 4 },
            { etiqueta: "Casi siempre", valor: 3 },
            { etiqueta: "Algunas veces", valor: 2 },
            { etiqueta: "Casi nunca", valor: 1 },
            { etiqueta: "Nunca", valor: 0 }
        ];
    }

    const nueva = angular.copy($scope.nuevaPregunta);
    $scope.preguntas.push(nueva);
    $scope.resetNuevaPregunta();
};
$scope.resetNuevaPregunta = function () {
  $scope.nuevaPregunta = {
    texto_pregunta: "",
    tipo_calificacion: "Likert",
    puntaje_maximo: 4,
    orden: null,
    dimension: "",
    dominio: "",
    categoria: "",
    grupo_aplicacion: "",
    opciones: []
  };

  $scope.cambiarTipoPregunta();
};

// CAMBIO DE TIPO
$scope.cambiarTipoPregunta = function () {

  if ($scope.nuevaPregunta.tipo_calificacion === "Likert") {
    $scope.nuevaPregunta.puntaje_maximo = 4;
    $scope.nuevaPregunta.opciones = [
      { etiqueta: "Siempre", valor: 4 },
      { etiqueta: "Casi siempre", valor: 3 },
      { etiqueta: "Algunas veces", valor: 2 },
      { etiqueta: "Casi nunca", valor: 1 },
      { etiqueta: "Nunca", valor: 0 }
    ];
  }

  else if ($scope.nuevaPregunta.tipo_calificacion === "Binaria") {
    $scope.nuevaPregunta.puntaje_maximo = 1;
    $scope.nuevaPregunta.opciones = [
      { etiqueta: "Sí", valor: 1 },
      { etiqueta: "No", valor: 0 }
    ];
  }

  else if ($scope.nuevaPregunta.tipo_calificacion === "Texto") {
    $scope.nuevaPregunta.puntaje_maximo = null;
    $scope.nuevaPregunta.opciones = [];
  }
};




  $scope.eliminarPreguntaTemp = function (index) {
    $scope.preguntas.splice(index, 1);
  };

  // GUARDAR TODO: Cuestionario + sus preguntas

  $scope.guardarTodo = function () {
    if (!$scope.cuestionario.nombre || !$scope.cuestionario.descripcion) {
      alert("Completa los datos del cuestionario.");
      return;
    }

    const payload = {
      cuestionario: $scope.cuestionario,
      preguntas: $scope.preguntas
    };

    $http.post("../api/cuestionario/guardarCuestionario.php", payload)
      .then(function (res) {
        if (res.data.status === "success") {
          alert("✅ Cuestionario y preguntas guardados correctamente.");
          $scope.cuestionario = {};
          $scope.preguntas = [];
          $scope.resetNuevaPregunta();
          $scope.consultar();
          bootstrap.Modal.getInstance(document.getElementById("myModal")).hide();
        } else {
          alert("⚠️ " + (res.data.message || "No se pudo guardar el cuestionario"));
        }
      }, function () {
        alert("❌ Error al guardar el cuestionario.");
      });
  };


  // Ver preguntas desde la tabla principal (abre modal VerPreguntas)
  $scope.verPreguntas = function (id) {
    var idCuest = id;
    $scope.cuestionarioSeleccionado = { id_cuestionario: idCuest };
    $http.get("../api/cuestionario/preguntasCuestionario.php?id_cuestionario=" + idCuest)
      .then(function (res) {
        if (res.data.status === "success") {
          $scope.preguntasDelCuestionario = res.data.preguntas;
          new bootstrap.Modal(document.getElementById("modalVerPreguntas")).show();
        } else {
          alert("⚠️ " + (res.data.message || "Error al cargar preguntas"));
        }
      }, function () {
        alert("Error al obtener preguntas");
      });
  };
  // ELIMINAR CUESTIONARIO //
$scope.eliminar = function (c) {
  if (!confirm("¿Deseas eliminar el cuestionario '" + c.nombre + "'? Esta acción no se puede deshacer.")) {
    return;
  }

  // enviamos JSON por POST
  $http.post("../api/cuestionario/eliminarCuestionario.php", { id_cuestionario: c.id_cuestionario })
    .then(function (res) {
      if (res.data.status === "success") {
        alert("🗑️ Cuestionario eliminado correctamente.");
        $scope.consultar(); // refresca la lista
      } else {
        alert("⚠️ " + (res.data.message || "No se pudo eliminar el cuestionario."));
      }
    }, function () {
      alert("❌ Error al intentar eliminar el cuestionario.");
    });
};
// ===============================
// AGREGAR PREGUNTA (ORDEN AUTO)
// ===============================
$scope.agregarPreguntaTemp = function () {

  if (!$scope.nuevaPregunta.texto_pregunta ||
      $scope.nuevaPregunta.texto_pregunta.trim() === "") {
    return;
  }

  const nueva = angular.copy($scope.nuevaPregunta);

  // 📌 ORDEN AUTOMÁTICO
  nueva.orden = $scope.preguntas.length + 1;

  $scope.preguntas.push(nueva);

  $scope.resetNuevaPregunta();
    // mantener foco
  setTimeout(function () {
    const input = document.getElementById("textoPreguntaInput");
    if (input) input.focus();
  }, 0);
};

// ===============================
// ELIMINAR Y REORDENAR
// ===============================
$scope.eliminarPreguntaTemp = function (index) {
  $scope.preguntas.splice(index, 1);

  // 🔁 Reordenar automáticamente
  $scope.preguntas.forEach(function (p, i) {
    p.orden = i + 1;
  });

  // mantener foco
  setTimeout(function () {
    const input = document.getElementById("textoPreguntaInput");
    if (input) input.focus();
  }, 0);
};

// INICIALIZACIÓN
$scope.resetNuevaPregunta();

});
