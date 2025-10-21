let indicePreguntaActual = 0;
const totalPreguntas = preguntas.length;
const respuestasUsuario = new Array(totalPreguntas).fill(null);

document.getElementById('total-preguntas').innerText = totalPreguntas;
mostrarPregunta();

function mostrarPregunta() {
  const preguntaActual = preguntas[indicePreguntaActual];
  document.getElementById('numero-pregunta').innerText = indicePreguntaActual + 1;
  document.getElementById('pregunta').innerText = preguntaActual.pregunta;

  const contenedorOpciones = document.getElementById('opciones');
  contenedorOpciones.innerHTML = '';

preguntaActual.opciones.forEach((opcion) => {
  const boton = document.createElement('button');
  boton.innerText = opcion.etiqueta;

  if (respuestasUsuario[indicePreguntaActual] === opcion.id_opcion) {
    boton.style.background = '#28a745';
  }

  boton.onclick = () => {
    respuestasUsuario[indicePreguntaActual] = opcion.id_opcion;
    document.querySelectorAll('#opciones button').forEach(b => b.style.background = '#007bff');
    boton.style.background = '#28a745';
  };

  contenedorOpciones.appendChild(boton);
});


  // visibilidad de botones
  document.getElementById('boton-anterior').style.display =
    indicePreguntaActual === 0 ? 'none' : 'inline-block';
  document.getElementById('boton-siguiente').style.display =
    indicePreguntaActual === totalPreguntas - 1 ? 'none' : 'inline-block';
  document.getElementById('boton-finalizar').style.display =
    indicePreguntaActual === totalPreguntas - 1 ? 'inline-block' : 'none';
}

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
    idCuestionario: 1, // puedes pasarlo dinámicamente desde PHP si quieres
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
