// =============================
// VARIABLES PRINCIPALES
// =============================
let indicePreguntaActual = 0;
const totalPreguntas = preguntas.length;
const respuestasUsuario = new Array(totalPreguntas).fill(null);
let idEvaluacion = null;

// Mostrar total de preguntas
document.getElementById('total-preguntas').innerText = totalPreguntas;

// =============================
// INICIO
// =============================
document.addEventListener('DOMContentLoaded', () => {
    verificarProgreso();
});


function verificarProgreso() {
    fetch(`../api/evaluacion/obtener_evaluacion_progreso.php?idCuestionario=${idCuestionario}`)
        .then(res => {
            if (!res.ok) throw new Error('Error HTTP');
            return res.json();
        })
        .then(data => {
            if (data.existe) {
                idEvaluacion = data.id_evaluacion;
                mostrarModalContinuar();
            } else {
                Swal.fire({
                    title: 'Iniciar cuestionario',
                    text: '¿Deseas comenzar el cuestionario?',
                    icon: 'info',
                    confirmButtonText: 'Comenzar',
                    allowOutsideClick: false
                }).then(() => {
                    // La evaluación pendiente ya debe existir o crearse
                    // Si no existe, debe crearse previamente en backend
                    mostrarPregunta();
                });
            }
        })
        .catch(err => {
            console.error(err);
            mostrarPregunta();
        });
}

// =============================
// MODAL CONTINUAR / REINICIAR
// =============================
function mostrarModalContinuar() {
    Swal.fire({
        title: 'Cuestionario en progreso',
        text: 'Tienes un cuestionario sin finalizar. ¿Qué deseas hacer?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Continuar',
        cancelButtonText: 'Reiniciar',
        allowOutsideClick: false
    }).then(result => {
        if (result.isConfirmed) {
            cargarRespuestasGuardadas();
        } else {
            reiniciarCuestionario();
        }
    });
}

// =============================
// CARGAR RESPUESTAS GUARDADAS
// usa: obtener_respuestas_guardadas.php
// =============================
function cargarRespuestasGuardadas() {
    fetch(`../api/respuesta/obtener_respuestas_guardadas.php?idEvaluacion=${idEvaluacion}`)
        .then(res => res.json())
        .then(data => {
            Object.keys(data).forEach(idPregunta => {
                const index = preguntas.findIndex(p => p.id_pregunta == idPregunta);
                if (index !== -1) {
                    respuestasUsuario[index] = data[idPregunta];
                }
            });

            const primeraSinResponder = respuestasUsuario.findIndex(r => r === null);
            indicePreguntaActual = primeraSinResponder !== -1
                ? primeraSinResponder
                : totalPreguntas - 1;

            mostrarPregunta();
        });
}

// =============================
// REINICIAR CUESTIONARIO
// usa: reiniciar_cuestionario.php
// =============================
function reiniciarCuestionario() {
    fetch('../api/cuestionario/reiniciar_cuestionario.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `idEvaluacion=${idEvaluacion}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            respuestasUsuario.fill(null);
            indicePreguntaActual = 0;
            mostrarPregunta();
        }
    });
}

// =============================
// MOSTRAR PREGUNTA ACTUAL
// =============================
function mostrarPregunta() {
    const preguntaActual = preguntas[indicePreguntaActual];

    document.getElementById('numero-pregunta').innerText = indicePreguntaActual + 1;
    document.getElementById('pregunta').innerText = preguntaActual.pregunta;

    const contenedorOpciones = document.getElementById('opciones');
    contenedorOpciones.innerHTML = '';

    preguntaActual.opciones.forEach(opcion => {
        const boton = document.createElement('button');
        boton.innerText = opcion.etiqueta;
        boton.classList.add('btn', 'btn-primary', 'm-1');

        if (respuestasUsuario[indicePreguntaActual] === opcion.id_opcion) {
            boton.style.background = '#28a745';
        }

        boton.onclick = () => {
            respuestasUsuario[indicePreguntaActual] = opcion.id_opcion;

            document.querySelectorAll('#opciones button')
                .forEach(b => b.style.background = '#007bff');

            boton.style.background = '#28a745';

            guardarRespuestaParcial(
                preguntaActual.id_pregunta,
                opcion.id_opcion,
                opcion.valor
            );
        };

        contenedorOpciones.appendChild(boton);
    });

    document.getElementById('boton-anterior').style.display =
        indicePreguntaActual === 0 ? 'none' : 'inline-block';

    document.getElementById('boton-siguiente').style.display =
        indicePreguntaActual === totalPreguntas - 1 ? 'none' : 'inline-block';

    document.getElementById('boton-finalizar').style.display =
        indicePreguntaActual === totalPreguntas - 1 ? 'inline-block' : 'none';
}

// =============================
// NAVEGACIÓN
// =============================
function mostrarSiguientePregunta() {
    if (respuestasUsuario[indicePreguntaActual] === null) {
        Swal.fire({
            title: 'Respuesta requerida',
            text: 'Selecciona una respuesta para continuar.',
            icon: 'warning'
        });
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
// GUARDAR RESPUESTA PARCIAL
// usa: guardar_respuesta_parcial.php
// =============================
function guardarRespuestaParcial(idPregunta, idOpcion, valor) {
fetch('../api/respuesta/guardar_respuesta_parcial.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        idEvaluacion,
        idCuestionario,
        idPregunta,
        idOpcion,
        valor
    })
})

    .then(res => res.json())
    .then(data => {
        if (data.idEvaluacion) {
            idEvaluacion = data.idEvaluacion;
        }
    });
}


// =============================
// FINALIZAR CUESTIONARIO
// (solo cambia el estado, las respuestas ya están guardadas)
// =============================
function finalizarCuestionario() {
    if (respuestasUsuario.includes(null)) {
        Swal.fire({
            title: 'Faltan respuestas',
            text: 'Debes responder todas las preguntas antes de finalizar.',
            icon: 'warning'
        });
        return;
    }

    Swal.fire({
        title: 'Cuestionario finalizado',
        text: 'Gracias por completar el cuestionario.',
        icon: 'success'
    }).then(() => {
        window.location.href = 'agradecimiento.php';
    });
}
