// =============================
// VARIABLES PRINCIPALES
// =============================
let indicePreguntaActual = 0;
const totalPreguntas = preguntas.length;
const respuestasUsuario = new Array(totalPreguntas).fill(null);

// Mostrar total
document.getElementById('total-preguntas').innerText = totalPreguntas;

// Mostrar primera pregunta
mostrarPregunta();

// =============================
// MOSTRAR PREGUNTA ACTUAL
// =============================
function mostrarPregunta() {
  const preguntaActual = preguntas[indicePreguntaActual];
  document.getElementById('numero-pregunta').innerText = indicePreguntaActual + 1;
  document.getElementById('pregunta').innerText = preguntaActual.pregunta;

  const contenedorOpciones = document.getElementById('opciones');
  contenedorOpciones.innerHTML = '';

  preguntaActual.opciones.forEach((opcion) => {
    const boton = document.createElement('button');
    boton.innerText = opcion.etiqueta;
    boton.classList.add('btn', 'btn-primary', 'm-1');

    // Si el usuario ya había seleccionado esta opción
    if (respuestasUsuario[indicePreguntaActual] === opcion.id_opcion) {
      boton.style.background = '#28a745';
    }

    // Evento click para seleccionar opción
    boton.onclick = () => {
      respuestasUsuario[indicePreguntaActual] = opcion.id_opcion;
      document.querySelectorAll('#opciones button').forEach(b => b.style.background = '#007bff');
      boton.style.background = '#28a745';
    };

    contenedorOpciones.appendChild(boton);
  });

  // Control de visibilidad de botones
  document.getElementById('boton-anterior').style.display =
    indicePreguntaActual === 0 ? 'none' : 'inline-block';
  document.getElementById('boton-siguiente').style.display =
    indicePreguntaActual === totalPreguntas - 1 ? 'none' : 'inline-block';
  document.getElementById('boton-finalizar').style.display =
    indicePreguntaActual === totalPreguntas - 1 ? 'inline-block' : 'none';
}

// =============================
// BOTONES DE NAVEGACIÓN
// =============================
function mostrarSiguientePregunta() {
  if (respuestasUsuario[indicePreguntaActual] === null) {
    alert("Por favor selecciona una respuesta antes de continuar.");
    return;
  }

  if (indicePreguntaActual < totalPreguntas - 1) {
    indicePreguntaActual++;
    mostrarPregunta();
  }
}

function mostrarAnteriorPregunta() {
  if (indicePreguntaActual > 0) {
    indicePreguntaActual--;
    mostrarPregunta();
  }
}

// =============================
// FINALIZAR CUESTIONARIO
// =============================
function finalizarCuestionario() {
  if (respuestasUsuario.includes(null)) {
    alert("Debes responder todas las preguntas antes de finalizar.");
    return;
  }

  const datos = {
    idCuestionario: idCuestionario, // ✅ dinámico desde PHP
    respuestas: respuestasUsuario
  };

  fetch('../api/guardar_respuestas.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(datos)
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert("¡Cuestionario finalizado correctamente!");
        window.location.href = "agradecimiento.php";
      } else {
        alert("Ocurrió un error al guardar las respuestas:\n" + data.error);
      }
    })
    .catch(err => console.error('Error en fetch:', err));
}
