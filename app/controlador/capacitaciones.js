document.addEventListener("DOMContentLoaded", () => {
    cargarCapacitaciones();
});

// 📌 Cargar tabla
function cargarCapacitaciones() {
    fetch('../api/capacitacion/listarCapacitaciones.php')
        .then(r => r.json())
        .then(resp => {
            if (!resp.success) return;

            let tbody = document.getElementById("tbodyCapacitaciones");
            tbody.innerHTML = "";

            resp.data.forEach(c => {
                tbody.innerHTML += `
                    <tr>
                        <td>${c.id_capacitacion}</td>
                        <td>${c.tema}</td>
                        <td>${c.fecha_inicio} - ${c.fecha_fin}</td>
                        <td>${c.tipo_modalidad}</td>
                        <td>${c.asistentes}</td>
                        <td>${c.constancias}</td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="abrirAsistencia(${c.id_capacitacion})">Asistencia</button>
                            <button class="btn btn-sm btn-warning" onclick="abrirConstancia(${c.id_capacitacion})">Constancia</button>
                        </td>
                    </tr>`;
            });
        });
}

// 📌 Registrar nueva capacitación
document.getElementById("formAgregar").addEventListener("submit", function(e){
    e.preventDefault();

    let datos = Object.fromEntries(new FormData(this).entries());

    fetch('../api/capacitacion/guardarCapacitacion.php', {
        method: "POST",
        body: JSON.stringify(datos)
    })
    .then(r => r.json())
    .then(resp => {
        if (resp.success) {
            alert("Capacitación registrada");
            cargarCapacitaciones();
            bootstrap.Modal.getInstance(document.getElementById("modalAgregar")).hide();
        } else {
            alert(resp.error);
        }
    });
});

// 📌 Asistencia
function abrirAsistencia(id) {
    document.getElementById("asistencia_id_capacitacion").value = id;
    new bootstrap.Modal(document.getElementById("modalAsistencia")).show();
}

document.getElementById("formAsistencia").addEventListener("submit", function(e){
    e.preventDefault();

    let datos = Object.fromEntries(new FormData(this).entries());

    fetch('../api/registrarAsistencia.php', {
        method: "POST",
        body: JSON.stringify(datos)
    })
    .then(r => r.json())
    .then(resp => {
        if (resp.success) {
            alert("Asistencia registrada");
            cargarCapacitaciones();
            bootstrap.Modal.getInstance(document.getElementById("modalAsistencia")).hide();
        } else alert(resp.error);
    });
});

// 📌 Subir constancia
function abrirConstancia(id) {
    document.getElementById("constancia_id_capacitacion").value = id;
    new bootstrap.Modal(document.getElementById("modalConstancia")).show();
}

document.getElementById("formConstancia").addEventListener("submit", function(e){
    e.preventDefault();

    let datos = new FormData(this);

    fetch('../api/capacitacion/subirConstancia.php', {
        method: "POST",
        body: datos
    })
    .then(r => r.json())
    .then(resp => {
        if (resp.success) {
            alert("Constancia subida");
            cargarCapacitaciones();
            bootstrap.Modal.getInstance(document.getElementById("modalConstancia")).hide();
        } else {
            alert(resp.error);
        }
    });
});
