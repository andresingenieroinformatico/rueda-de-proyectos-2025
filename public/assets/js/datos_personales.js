function mostrarCampos() {
    const num = parseInt(document.getElementById("numEstudiantes").value);
    const contenedor = document.getElementById("contenedorEstudiantes");
    contenedor.innerHTML = "";

    for (let i = 1; i <= num; i++) {
        const bloque = document.createElement("div");
        bloque.className = "student-block";
        bloque.innerHTML = `
        <h3>Estudiante ${i}</h3>
        <div class="form-group">
            <label for="nombre${i}">Nombre completo:</label>
            <input type="text" id="nombre${i}" name="nombre${i}" required>
        </div>
        <div class="form-group">
            <label for="correo${i}">Correo institucional:</label>
            <input type="email" id="correo${i}" name="correo${i}" placeholder="ejemplo@unipaz.edu.co" required>
        </div>
        <div class="form-group">
            <label for="id${i}">Número de identificación:</label>
            <input type="text" id="id${i}" name="id${i}" required>
        </div>
        <div class="form-group">
            <label for="jornada${i}">Jornada:</label>
            <select id="jornada${i}" name="jornada${i}" required>
            <option value="">Seleccione</option>
            <option value="diurna">Diurna</option>
            <option value="nocturna">Nocturna</option>
            </select>
        </div>
        <div class="form-group">
            <label for="telefono${i}">Teléfono:</label>
            <input type="tel" id="telefono${i}" name="telefono${i}" required>
        </div>
        `;
        contenedor.appendChild(bloque);
    }
    }

    function enviarFormulario() {
      // Aquí podrías validar o guardar los datos si lo conectas con backend
      window.location.href = "finalizacion.html"; // Redirección al HTML deseado
      return false; // Evita el envío real del formulario
    }

    window.onload = mostrarCampos;