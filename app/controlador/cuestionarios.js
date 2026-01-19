// =============================
// VARIABLES PRINCIPALES
// =============================
let indicePreguntaActual = 0;
const totalPreguntas = preguntas.length;
const respuestasUsuario = new Array(totalPreguntas).fill(null);
let idEvaluacion = null;

// Mostrar total de preguntas
document.getElementById("total-preguntas").innerText = totalPreguntas;

// =============================
// INICIO
// =============================
document.addEventListener("DOMContentLoaded", () => {
  verificarProgreso();
});

// =============================
// VERIFICAR PROGRESO
// =============================
function verificarProgreso() {
  fetch(
    `../api/evaluacion/obtener_evaluacion_progreso.php?idCuestionario=${idCuestionario}`,
  )
    .then((res) => res.json())
    .then((data) => {
      if (data.existe) {
        idEvaluacion = data.id_evaluacion;
        mostrarModalContinuar();
      } else {
        mostrarModalBienvenida();
      }
    })
    .catch(() => {
      mostrarModalBienvenida();
    });
}

// =============================
// MODAL BIENVENIDA
// =============================
function mostrarModalBienvenida() {
  Swal.fire({
    title: "Bienvenido al cuestionario",
    html: `
      <p>Este cuestionario se guardará automáticamente.</p>
      <p>Podrás continuar después si sales.</p>
    `,
    icon: "info",
    confirmButtonText: "Comenzar",
    allowOutsideClick: false,
  }).then((result) => {
    if (result.isConfirmed) {
      iniciarEvaluacion();
    }
  });
}

// =============================
// INICIAR / MARCAR PENDIENTE
// =============================
function iniciarEvaluacion() {
  fetch("../api/evaluacion/iniciar_evaluacion.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      idCuestionario: idCuestionario,
    }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        idEvaluacion = data.idEvaluacion;
        mostrarPregunta();
      } else {
        Swal.fire("Error", data.message, "error");
      }
    })
    .catch(() => {
      Swal.fire("Error", "No se pudo iniciar la evaluación", "error");
    });
}

// =============================
// MODAL CONTINUAR / REINICIAR
// =============================
function mostrarModalContinuar() {
  Swal.fire({
    title: "Cuestionario en progreso",
    text: "Tienes un cuestionario sin finalizar",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Continuar",
    cancelButtonText: "Reiniciar",
    allowOutsideClick: false,
  }).then((result) => {
    if (result.isConfirmed) {
      cargarRespuestasGuardadas();
    } else {
      reiniciarCuestionario();
    }
  });
}

// =============================
// CARGAR RESPUESTAS GUARDADAS
// =============================
function cargarRespuestasGuardadas() {
  fetch(
    `../api/respuesta/obtener_respuestas_guardadas.php?idEvaluacion=${idEvaluacion}`,
  )
    .then((res) => res.json())
    .then((data) => {
      Object.keys(data).forEach((idPregunta) => {
        const index = preguntas.findIndex(
          (p) => Number(p.id_pregunta) === Number(idPregunta),
        );
        if (index !== -1) {
          respuestasUsuario[index] = Number(data[idPregunta]);
        }
      });

      const primeraSinResponder = respuestasUsuario.findIndex(
        (r) => r === null,
      );
      indicePreguntaActual =
        primeraSinResponder !== -1 ? primeraSinResponder : totalPreguntas - 1;

      mostrarPregunta();
    })
    .catch(() => {
      mostrarPregunta();
    });
}

// =============================
// REINICIAR CUESTIONARIO
// =============================
function reiniciarCuestionario() {
  fetch("../api/cuestionario/reiniciar_cuestionario.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `idEvaluacion=${idEvaluacion}`,
  })
    .then((res) => res.json())
    .then(() => {
      respuestasUsuario.fill(null);
      indicePreguntaActual = 0;
      iniciarEvaluacion();
    });
}

// =============================
// MOSTRAR PREGUNTA
// =============================
function mostrarPregunta() {
  const preguntaActual = preguntas[indicePreguntaActual];

  document.getElementById("numero-pregunta").innerText =
    indicePreguntaActual + 1;
  document.getElementById("pregunta").innerText = preguntaActual.pregunta;

  const contenedorOpciones = document.getElementById("opciones");
  contenedorOpciones.innerHTML = "";

  preguntaActual.opciones.forEach((opcion) => {
    const boton = document.createElement("button");
    boton.innerText = opcion.etiqueta;
    boton.classList.add("btn", "btn-primary", "m-1");

    if (
      respuestasUsuario[indicePreguntaActual] !== null &&
      Number(respuestasUsuario[indicePreguntaActual]) ===
        Number(opcion.id_opcion)
    ) {
      boton.style.background = "#28a745";
    }

    boton.onclick = () => {
      respuestasUsuario[indicePreguntaActual] = Number(opcion.id_opcion);

      document
        .querySelectorAll("#opciones button")
        .forEach((b) => (b.style.background = "#007bff"));

      boton.style.background = "#28a745";

      guardarRespuestaParcial(
        preguntaActual.id_pregunta,
        opcion.id_opcion,
        opcion.valor,
      );
    };

    contenedorOpciones.appendChild(boton);
  });

  document.getElementById("boton-anterior").style.display =
    indicePreguntaActual === 0 ? "none" : "inline-block";

  document.getElementById("boton-siguiente").style.display =
    indicePreguntaActual === totalPreguntas - 1 ? "none" : "inline-block";

  document.getElementById("boton-finalizar").style.display =
    indicePreguntaActual === totalPreguntas - 1 ? "inline-block" : "none";
}

// =============================
// NAVEGACIÓN
// =============================
function mostrarSiguientePregunta() {
  if (respuestasUsuario[indicePreguntaActual] === null) {
    Swal.fire("Respuesta requerida", "Selecciona una opción", "warning");
    return;
  }
  indicePreguntaActual++;
  mostrarPregunta();
}

function mostrarAnteriorPregunta() {
  indicePreguntaActual--;
  mostrarPregunta();
}

// =============================
// GUARDADO PARCIAL
// =============================
function guardarRespuestaParcial(idPregunta, idOpcion, valor) {
  fetch("../api/respuesta/guardar_respuesta_parcial.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      idEvaluacion,
      idCuestionario,
      idPregunta,
      idOpcion,
      valor,
    }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.idEvaluacion) {
        idEvaluacion = data.idEvaluacion;
      }
    });
}

// =============================
// FINALIZAR
// =============================
function finalizarCuestionario() {
  if (respuestasUsuario.includes(null)) {
    Swal.fire(
      "Faltan respuestas",
      "Debes responder todas las preguntas",
      "warning",
    );
    return;
  }

  const datos = {
    idCuestionario: idCuestionario,
    respuestas: respuestasUsuario,
  };

  fetch("../api/guardar_respuestas.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(datos),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        Swal.fire(
          "Cuestionario finalizado",
          "Gracias por completar el cuestionario",
          "success",
        ).then(() => {
          window.location.href = "agradecimiento.php";
        });
      } else {
        Swal.fire("Error", data.error, "error");
      }
    })
    .catch(() => {
      Swal.fire("Error", "No se pudo guardar el cuestionario", "error");
    });
}
