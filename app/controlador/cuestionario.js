let indicePreguntaActual = 0;
const totalPreguntas = preguntas.length;
const respuestasUsuario = new Array(totalPreguntas).fill(null); // Almacena la selección del usuario

// Mostrar el número total de preguntas
document.getElementById('total-preguntas').innerText = totalPreguntas;

// Llamar a la función para mostrar la primera pregunta
mostrarPregunta();

function mostrarPregunta() {
  const preguntaActual = preguntas[indicePreguntaActual];

  document.getElementById('numero-pregunta').innerText = indicePreguntaActual + 1;
  document.getElementById('pregunta').innerText = preguntaActual.pregunta;

  const contenedorOpciones = document.getElementById('opciones');
  contenedorOpciones.innerHTML = '';

  preguntaActual.opciones.forEach((opcion, i) => {
    const boton = document.createElement('button');
    boton.innerText = opcion;

    // Si el usuario ya seleccionó esta opción, la marcamos visualmente
    if (respuestasUsuario[indicePreguntaActual] === i) {
      boton.style.background = '#28a745';
    }

    boton.onclick = () => {
      // Guardar la selección del usuario
      respuestasUsuario[indicePreguntaActual] = i;

      // Actualizar visualmente la selección
      document.querySelectorAll('#opciones button').forEach(b => b.style.background = '#007bff');
      boton.style.background = '#28a745';
    };

    contenedorOpciones.appendChild(boton);
  });

  // Actualizar visibilidad del botón "Anterior"
  document.getElementById('boton-anterior').style.display = indicePreguntaActual === 0 ? 'none' : 'inline-block';
}

function mostrarSiguientePregunta() {
  // Validar que el usuario haya seleccionado una opción
  if (respuestasUsuario[indicePreguntaActual] === null) {
    alert("Por favor selecciona una respuesta antes de continuar.");
    return; // No avanzar
  }

  if (indicePreguntaActual < totalPreguntas - 1) {
    indicePreguntaActual++;
    mostrarPregunta();
  } else {
    document.getElementById('boton-reiniciar').style.display = 'inline-block';
  }
}

function mostrarAnteriorPregunta() {
  if (indicePreguntaActual > 0) {
    indicePreguntaActual--;
    mostrarPregunta();
  }
}

function reiniciarJuego() {
  indicePreguntaActual = 0;
  respuestasUsuario.fill(null);
  document.getElementById('boton-reiniciar').style.display = 'none';
  mostrarPregunta();
}
