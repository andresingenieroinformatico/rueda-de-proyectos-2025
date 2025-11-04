const cantidadSelect = document.getElementById('cantidad');
    cantidadSelect.addEventListener('change', function() {
        const cantidad = parseInt(this.value);
        for (let i = 1; i <= 4; i++) {
            const bloque = document.getElementById('student' + i);
            bloque.style.display = i <= cantidad ? 'block' : 'none';
            bloque.querySelectorAll('input, select').forEach(el => {
                el.required = i <= cantidad;
            });
        }
    });