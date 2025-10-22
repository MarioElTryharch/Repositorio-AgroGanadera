// Función para cargar los datos de vientres en la tabla
function cargarVientres() {
    fetch('api/Agro_Vacuno/Datos/Vientres/cargar_vientres.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tbody = document.getElementById('tablaVientresBody');
                tbody.innerHTML = '';
                
                data.data.forEach(vientre => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${vientre.codigo_id_animal || ''}</td>
                        <td>${vientre.nombre_animal || ''}</td>
                        <td>${vientre.raza_princ || ''}</td>
                        <td>${vientre.edad_meses || ''}</td>
                        <td>${vientre.peso_act || ''}</td>
                        <td><span class="estado-badge estado-activo">${vientre.estado || 'Activo'}</span></td>
                        <td>
                            <div class="acciones-tabla">
                                <button class="btn-accion btn-editar" onclick="editarVientre(${vientre.id_vientre})" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-accion btn-eliminar" onclick="eliminarVientre(${vientre.id_vientre})" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
                
                // Actualizar contador
                document.getElementById('contadorVientres').textContent = data.data.length;
            } else {
                console.error('Error al cargar vientres:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// Función para editar vientre
function editarVientre(id) {
    // Aquí puedes implementar la lógica para editar
    console.log('Editar vientre:', id);
    abrirModal('modalVientres');
    // Cargar datos del vientre en el modal
}

// Función para eliminar vientre
function eliminarVientre(id) {
    if (confirm('¿Está seguro de que desea eliminar este vientre?')) {
        // Aquí puedes implementar la lógica para eliminar
        console.log('Eliminar vientre:', id);
    }
}

// Cargar datos cuando la página esté lista
document.addEventListener('DOMContentLoaded', function() {
    cargarVientres();
});