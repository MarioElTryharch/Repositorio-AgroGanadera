<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Agro Ganadero - AgroInventario</title>
    <link rel="stylesheet" href="styles.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
    <link rel="icon" href="img/Imagen1.ico" type="image/x-icon" />
    <link rel="icon" href="img/Imagen1.ico" type="image/png" sizes="32x32" />
    <link rel="apple-touch-icon" href="img/Imagen1.ico" sizes="180x180" />
  </head>

  <style>
    /* Estilos para el inventario */
.filtros-inventario {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
    flex-wrap: wrap;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.filtros-inventario .form-group {
    margin-bottom: 0;
}

.resumen-inventario {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin: 20px 0;
}

.resumen-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    text-align: center;
    border-left: 4px solid #007bff;
}

.resumen-card h4 {
    margin: 0 0 10px 0;
    font-size: 0.9em;
    color: #6c757d;
}

.resumen-valor {
    font-size: 2em;
    font-weight: bold;
    margin: 0;
    color: #007bff;
}

/* Colores para tipos de movimiento */
.text-success { color: #28a745 !important; }
.text-danger { color: #dc3545 !important; }
.text-warning { color: #ffc107 !important; }

/* Badges para categorías */
.badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75em;
}

.badge-secondary { background-color: #6c757d; }
  </style>
  <body>
    <header>
      <div class="header-container">
        <a href="index.php" class="logo-link">
          <img src="img/logo.png" alt="Agro Ganadero Logo" class="logo-img" />
        </a>
        <button class="mobile-menu-toggle" aria-label="Menú">
          <i class="fas fa-bars"></i>
        </button>
        <nav class="main-nav" style="background: white">
          <ul>
            <li><a href="agro-vacuno.php">AgroVacuno</a></li>
            <li><a href="agro-bufalino.php">AgroBufalino</a></li>
            <li><a href="agro-cultivo.php">AgroAgricultivo</a></li>
            <li class="active">
              <a href="agro-inventario.php">AgroInventario</a>
            </li>
            <li><a href="agro-empleados.php">AgroEmpleados</a></li>

            <li class="login"><a href="login.php">Iniciar Sesión</a></li>
            <li class="register"><a href="register.php">Registrarse</a></li>
          </ul>
        </nav>
      </div>
    </header>

    <main class="inventario-container">
      <!-- Encabezado de la página -->
      <div class="inventario-header">
        <h1><i class="fas fa-boxes"></i> AgroInventario</h1>
        <div class="inventario-actions">
          <button class="btn btn-primary" id="btnNuevoItem">
            <i class="fas fa-plus"></i> Nuevo Ítem
          </button>
          <button class="btn btn-secondary" id="btnReporteInventario">
            <i class="fas fa-file-export"></i> Exportar
          </button>
          <button class="btn btn-outline" id="btnAjusteInventario">
            <i class="fas fa-exchange-alt"></i> Ajuste de Inventario
          </button>
        </div>
      </div>


        <!-- Sección 1: Resumen de inventario General -->
      <div class="hacienda-menu">
      <div class="menu-section">
        <h2>
          <i class="fas fa-boxes"></i> Resumen de Inventario
        </h2>
        <div class="menu-content">
          <div class="content-section">
          
              <h2><i class="fas fa-chart-pie"></i> Estadísticas Generales</h2>
                                      <!-- Tarjetas de resumen -->
                            <div class="data-cards">
                                <div class="data-card">
                                    <h3>Total de Ítems</h3>
                                    <p class="data-value" id="totalItems">0</p>
                                </div>
                                <div class="data-card highlight">
                                    <h3>Valor Total Inventario</h3>
                                    <p class="data-value" id="valorTotalInventario">$0.00</p>
                                </div>
                                <div class="data-card warning">
                                    <h3>Ítems Bajo Stock</h3>
                                    <p class="data-value" id="itemsBajoStock">0</p>
                                </div>
                                <div class="data-card danger">
                                    <h3>Ítems Agotados</h3>
                                    <p class="data-value" id="itemsAgotados">0</p>
                                </div>
                                <div class="data-card success">
                                    <h3>Rotación Mensual</h3>
                                    <p class="data-value" id="rotacionMensual">0%</p>
                                </div>
                                <div class="data-card info">
                                    <h3>Ganancia Neta Mes</h3>
                                    <p class="data-value" id="gananciaNetaMes">$0.00</p>
                                </div>
                            </div>

                              <!-- Filtros y búsqueda -->
                            <div class="inventario-filtros">
                                <div class="filtro-group">
                                    <input type="text" id="buscarProducto" placeholder="Buscar producto..." onkeyup="filtrarInventario()">
                                </div>
                                <div class="filtro-group">
                                    <select id="filtroCategoria" onchange="filtrarInventario()">
                                        <option value="todas">Todas las categorías</option>
                                        <option value="alimentos">Alimentos</option>
                                        <option value="medicamentos">Medicamentos</option>
                                        <option value="materiales">Materiales</option>
                                        <option value="equipos">Equipos</option>
                                        <option value="insumos">Insumos</option>
                                    </select>
                                </div>
                                <div class="filtro-group">
                                    <select id="filtroEstado" onchange="filtrarInventario()">
                                        <option value="todos">Todos los estados</option>
                                        <option value="normal">Stock Normal</option>
                                        <option value="bajo">Stock Bajo</option>
                                        <option value="agotado">Agotado</option>
                                    </select>
                                </div>
                                <button class="btn btn-primary" onclick="exportarInventario()">
                                    <i class="fas fa-file-export"></i> Exportar
                                </button>
                            </div>


                            <!-- Agregar después de la tabla de Control de Vacunación -->
                <div class="tabla-container" id="tablaInventario">
                    <div class="tabla-header">
                        <h3><i class="fas fa-boxes"></i> Movimientos de Inventario</h3>
                        <button class="btn btn-primary" title="Agregar Movimiento de Inventario" onclick="abrirModal('modalInventario')">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="filtros-inventario">
                        <div class="form-group">
                            <label for="filtroTipoMovimiento">Tipo de Movimiento:</label>
                            <select id="filtroTipoMovimiento" onchange="filtrarInventario()">
                                <option value="todos">Todos</option>
                                <option value="entrada">Entradas</option>
                                <option value="salida">Salidas</option>
                                <option value="ajuste">Ajustes</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filtroCategoria">Categoría:</label>
                            <select id="filtroCategoria" onchange="filtrarInventario()">
                                <option value="todos">Todas</option>
                                <option value="alimentos">Alimentos</option>
                                <option value="medicamentos">Medicamentos</option>
                                <option value="materiales">Materiales</option>
                                <option value="equipos">Equipos</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Proveedor/Cliente</th>
                                    <th>Motivo</th>
                                    <th>Stock Actual</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaInventarioBody">
                                <!-- Los datos se cargarán dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Resumen de Inventario -->
                <div class="resumen-inventario">
                    <div class="resumen-card">
                        <h4><i class="fas fa-arrow-down"></i> Entradas del Mes</h4>
                        <p class="resumen-valor" id="entradasMes">0</p>
                    </div>
                    <div class="resumen-card">
                        <h4><i class="fas fa-arrow-up"></i> Salidas del Mes</h4>
                        <p class="resumen-valor" id="salidasMes">0</p>
                    </div>
                    <div class="resumen-card">
                        <h4><i class="fas fa-box"></i> Stock Crítico</h4>
                        <p class="resumen-valor" id="stockCritico">0</p>
                    </div>
                    <div class="resumen-card">
                        <h4><i class="fas fa-exclamation-triangle"></i> Próximos a Vencer</h4>
                        <p class="resumen-valor" id="proximosVencer">0</p>
                    </div>
                </div>

                <!-- MODAL PARA INVENTARIO -->
                <div id="modalInventario" class="modal">
                    <div class="modal-content" style="max-width: 900px;">
                        <div class="modal-header">
                            <h3><i class="fas fa-boxes"></i> Registro de Movimiento de Inventario</h3>
                            <button class="close-modal" onclick="cerrarModal('modalInventario')">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="ficha-tabs">
                                <button class="ficha-tab active" onclick="cambiarPestania(event, 'inventario-datos')">Datos del Movimiento</button>
                                <button class="ficha-tab" onclick="cambiarPestania(event, 'inventario-detalles')">Detalles Adicionales</button>
                            </div>

                            <!-- Sección 1: Datos del Movimiento -->
                            <div id="inventario-datos" class="tab-content active">
                                <form id="formInventarioDatos">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="tipoMovimiento">Tipo de Movimiento *</label>
                                            <select id="tipoMovimiento" name="tipo_movimiento" required onchange="toggleCamposMovimiento()">
                                                <option value="">Seleccionar tipo</option>
                                                <option value="entrada">Entrada</option>
                                                <option value="salida">Salida</option>
                                                <option value="ajuste">Ajuste de Inventario</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="fechaMovimiento">Fecha del Movimiento *</label>
                                            <input type="datetime-local" id="fechaMovimiento" name="fecha_movimiento" required>
                                        </div>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="categoriaProducto">Categoría del Producto *</label>
                                            <select id="categoriaProducto" name="categoria_producto" required>
                                                <option value="">Seleccionar categoría</option>
                                                <option value="alimentos">Alimentos</option>
                                                <option value="medicamentos">Medicamentos</option>
                                                <option value="materiales">Materiales</option>
                                                <option value="equipos">Equipos</option>
                                                <option value="insumos">Insumos</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="producto">Producto *</label>
                                            <input type="text" id="producto" name="producto" required placeholder="Nombre del producto">
                                        </div>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="cantidadMovimiento">Cantidad *</label>
                                            <input type="number" id="cantidadMovimiento" name="cantidad_movimiento" min="0.01" step="0.01" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="unidadMedida">Unidad de Medida *</label>
                                            <select id="unidadMedida" name="unidad_medida" required>
                                                <option value="">Seleccionar unidad</option>
                                                <option value="kg">Kilogramos (kg)</option>
                                                <option value="l">Litros (L)</option>
                                                <option value="unidad">Unidades</option>
                                                <option value="caja">Cajas</option>
                                                <option value="saco">Sacos</option>
                                                <option value="rollo">Rollos</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="form-row" id="campoProveedor" style="display: none;">
                                        <div class="form-group">
                                            <label for="proveedorCliente">Proveedor/Cliente *</label>
                                            <input type="text" id="proveedorCliente" name="proveedor_cliente" placeholder="Nombre del proveedor o cliente">
                                        </div>
                                        <div class="form-group">
                                            <label for="numeroFactura">Número de Factura/Recibo</label>
                                            <input type="text" id="numeroFactura" name="numero_factura" placeholder="Número de documento">
                                        </div>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="precioUnitario">Precio Unitario ($)</label>
                                            <input type="number" id="precioUnitario" name="precio_unitario" min="0" step="0.01">
                                        </div>
                                        <div class="form-group">
                                            <label for="valorTotal">Valor Total ($)</label>
                                            <input type="number" id="valorTotal" name="valor_total" min="0" step="0.01" readonly>
                                        </div>
                                    </div>
                                    
                                    <div class="form-actions">
                                        <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalInventario')">Cancelar</button>
                                        <button type="button" class="btn btn-next" onclick="cambiarPestania(event, 'inventario-detalles')">Siguiente</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Sección 2: Detalles Adicionales -->
                            <div id="inventario-detalles" class="tab-content">
                                <form id="formInventarioDetalles">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="motivoMovimiento">Motivo del Movimiento *</label>
                                            <select id="motivoMovimiento" name="motivo_movimiento" required>
                                                <option value="">Seleccionar motivo</option>
                                                <option value="compra">Compra</option>
                                                <option value="venta">Venta</option>
                                                <option value="consumo">Consumo Interno</option>
                                                <option value="donacion">Donación</option>
                                                <option value="transferencia">Transferencia</option>
                                                <option value="perdida">Pérdida</option>
                                                <option value="vencimiento">Vencimiento</option>
                                                <option value="ajuste-stock">Ajuste de Stock</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="responsableMovimiento">Responsable *</label>
                                            <input type="text" id="responsableMovimiento" name="responsable_movimiento" required>
                                        </div>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="loteProducto">Lote/Serie del Producto</label>
                                            <input type="text" id="loteProducto" name="lote_producto" placeholder="Número de lote o serie">
                                        </div>
                                        <div class="form-group">
                                            <label for="fechaVencimiento">Fecha de Vencimiento</label>
                                            <input type="date" id="fechaVencimiento" name="fecha_vencimiento">
                                        </div>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="ubicacionAlmacen">Ubicación en Almacén</label>
                                            <input type="text" id="ubicacionAlmacen" name="ubicacion_almacen" placeholder="Estante, rack, etc.">
                                        </div>
                                        <div class="form-group">
                                            <label for="condicionAlmacenamiento">Condición de Almacenamiento</label>
                                            <select id="condicionAlmacenamiento" name="condicion_almacenamiento">
                                                <option value="">Seleccionar condición</option>
                                                <option value="ambiente">Ambiente</option>
                                                <option value="refrigerado">Refrigerado</option>
                                                <option value="congelado">Congelado</option>
                                                <option value="seco">Lugar Seco</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group full-width">
                                            <label for="observacionesInventario">Observaciones</label>
                                            <textarea id="observacionesInventario" name="observaciones_inventario" rows="3" placeholder="Observaciones adicionales sobre el movimiento..."></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="documentoAdjunto">Documento Adjunto</label>
                                            <input type="file" id="documentoAdjunto" name="documento_adjunto" accept=".pdf,.jpg,.png,.doc,.docx">
                                            <small class="text-muted">Factura, recibo, documento de transferencia</small>
                                        </div>
                                    </div>
                                    
                                    <div class="form-actions">
                                        <button type="button" class="btn btn-secondary" onclick="cambiarPestania(event, 'inventario-datos')">Anterior</button>
                                        <button type="button" class="btn btn-primary" onclick="guardarMovimientoInventario()">Guardar Movimiento</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                            <!-- Filtros y búsqueda -->
                            <div class="content-section">
                              <div class="section-header">
                                <h2><i class="fas fa-filter"></i> Filtros</h2>
                                <div class="section-actions">
                                  <div class="search-filter">
                                    <input
                                      type="text"
                                      placeholder="Buscar ítem..."
                                      id="buscarItem"
                                    />
                                    <button class="btn btn-primary btn-filter">
                                      <i class="fas fa-search"></i>
                                    </button>
                                  </div>
                                  <select id="filtroCategoria" class="form-control">
                                    <option value="">Todas las categorías</option>
                                    <option value="alimentos">Alimentos</option>
                                    <option value="medicamentos">Medicamentos</option>
                                    <option value="equipos">Equipos</option>
                                    <option value="insumos">Insumos Agrícolas</option>
                                  </select>
                                  <button class="btn btn-outline">
                                    <i class="fas fa-sync-alt"></i> Actualizar
                                  </button>
                                </div>
                              </div>

                              <!-- Tabla de inventario -->
                            <div class="tabla-container">
                                <div class="tabla-header">
                                    <h3><i class="fas fa-boxes"></i> Detalle del Inventario</h3>
                                    <button class="btn btn-outline btn-icon" onclick="abrirModal('modalNuevoProducto')">
                                        <i class="fas fa-plus"></i> Nuevo Producto
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="data-table">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>Categoría</th>
                                                <th>Stock Actual</th>
                                                <th>Stock Mínimo</th>
                                                <th>Precio Costo</th>
                                                <th>Precio Venta</th>
                                                <th>Valor en Stock</th>
                                                <th>Estado</th>
                                                <th>Último Movimiento</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tablaInventarioBody">
                                            <!-- Los datos se cargarán dinámicamente -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>





            </div>
          </div>
        </div>
      </div>

        <!-- Sección 2: Categorías y Clasificación -->
        <div class="menu-section">
          <h2>
            <i class="fas fa-tags"></i> Categorías y Clasificación
            <i class="fas fa-caret-right"></i>
          </h2>
          <div class="menu-content">
            <div class="content-section">
              <div class="section-header">
                <h2><i class="fas fa-sitemap"></i> Gestión de Categorías</h2>
                <div class="section-actions">
                  <button class="btn btn-primary" id="btnNuevaCategoria">
                    <i class="fas fa-plus"></i> Nueva Categoría
                  </button>
                </div>
              </div>

              <!-- Tabla de categorías -->
              <div class="table-container">
                <table class="data-table">
                  <thead>
                    <tr>
                      <th>Código</th>
                      <th>Nombre</th>
                      <th>Ítems Asociados</th>
                      <th>Descripción</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>ALM</td>
                      <td>Alimentos</td>
                      <td>42</td>
                      <td>Alimentos para el ganado</td>
                      <td>
                        <button class="btn-icon" title="Editar">
                          <i class="fas fa-edit"></i>
                        </button>
                      </td>
                    </tr>
                    <tr>
                      <td>MED</td>
                      <td>Medicamentos</td>
                      <td>35</td>
                      <td>Medicinas veterinarias</td>
                      <td>
                        <button class="btn-icon" title="Editar">
                          <i class="fas fa-edit"></i>
                        </button>
                      </td>
                    </tr>
                    <tr>
                      <td>EQP</td>
                      <td>Equipos</td>
                      <td>27</td>
                      <td>Equipos y herramientas</td>
                      <td>
                        <button class="btn-icon" title="Editar">
                          <i class="fas fa-edit"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>


         <!-- Sección 2: Movimientos y Transacciones -->
    <div class="menu-section">
        <h2>
            <i class="fas fa-exchange-alt"></i> Movimientos y Transacciones
            <i class="fas fa-caret-right"></i>
        </h2>
        <div class="menu-content" style="display: none;">
            <!-- Resumen de movimientos -->
            <div class="data-cards">
                <div class="data-card">
                    <h3>Entradas del Mes</h3>
                    <p class="data-value" id="entradasMes">0</p>
                </div>
                <div class="data-card">
                    <h3>Salidas del Mes</h3>
                    <p class="data-value" id="salidasMes">0</p>
                </div>
                <div class="data-card highlight">
                    <h3>Valor Entradas</h3>
                    <p class="data-value" id="valorEntradas">$0.00</p>
                </div>
                <div class="data-card highlight">
                    <h3>Valor Salidas</h3>
                    <p class="data-value" id="valorSalidas">$0.00</p>
                </div>
            </div>

            <!-- Filtros de movimientos -->
            <div class="movimientos-filtros">
                <div class="filtro-group">
                    <label>Fecha desde:</label>
                    <input type="date" id="fechaDesde" onchange="cargarMovimientos()">
                </div>
                <div class="filtro-group">
                    <label>Fecha hasta:</label>
                    <input type="date" id="fechaHasta" onchange="cargarMovimientos()">
                </div>
                <div class="filtro-group">
                    <label>Tipo:</label>
                    <select id="filtroTipoMovimiento" onchange="cargarMovimientos()">
                        <option value="todos">Todos los tipos</option>
                        <option value="entrada">Entradas</option>
                        <option value="salida">Salidas</option>
                        <option value="venta">Ventas</option>
                        <option value="ajuste">Ajustes</option>
                    </select>
                </div>
                <button class="btn btn-primary" onclick="abrirModal('modalNuevoMovimiento')">
                    <i class="fas fa-plus"></i> Nuevo Movimiento
                </button>
            </div>

            <!-- Tabla de movimientos -->
            <div class="tabla-container">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Producto</th>
                                <th>Tipo</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>Valor Total</th>
                                <th>Proveedor/Cliente</th>
                                <th>Motivo</th>
                                <th>Stock Posterior</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaMovimientosBody">
                            <!-- Los datos se cargarán dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección 3: Reportes y Ganancias -->
    <div class="menu-section">
        <h2>
            <i class="fas fa-chart-line"></i> Reportes y Ganancias
            <i class="fas fa-caret-right"></i>
        </h2>
        <div class="menu-content" style="display: none;">
            <!-- Métricas financieras -->
            <div class="data-cards">
                <div class="data-card success">
                    <h3>Ventas del Mes</h3>
                    <p class="data-value" id="ventasMes">$0.00</p>
                </div>
                <div class="data-card">
                    <h3>Costos del Mes</h3>
                    <p class="data-value" id="costosMes">$0.00</p>
                </div>
                <div class="data-card highlight">
                    <h3>Ganancia Bruta</h3>
                    <p class="data-value" id="gananciaBruta">$0.00</p>
                </div>
                <div class="data-card info">
                    <h3>Margen de Ganancia</h3>
                    <p class="data-value" id="margenGanancia">0%</p>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="reportes-grid">
                <div class="grafico-container">
                    <h4>Evolución de Ventas vs Costos</h4>
                    <canvas id="graficoVentasCostos" width="400" height="200"></canvas>
                </div>
                <div class="grafico-container">
                    <h4>Productos Más Rentables</h4>
                    <canvas id="graficoProductosRentables" width="400" height="200"></canvas>
                </div>
                <div class="grafico-container">
                    <h4>Rotación por Categoría</h4>
                    <canvas id="graficoRotacionCategorias" width="400" height="200"></canvas>
                </div>
                <div class="grafico-container">
                    <h4>Stock vs Ventas</h4>
                    <canvas id="graficoStockVentas" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Tabla de productos más vendidos -->
            <div class="tabla-container">
                <h4>Top 10 Productos Más Rentables</h4>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Unidades Vendidas</th>
                                <th>Ventas Totales</th>
                                <th>Costos Totales</th>
                                <th>Ganancia</th>
                                <th>Margen</th>
                                <th>Rotación</th>
                            </tr>
                        </thead>
                        <tbody id="tablaTopProductos">
                            <!-- Los datos se cargarán dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

        <!-- Sección 3: Movimientos de Inventario -->
        <div class="menu-section">
          <h2>
            <i class="fas fa-exchange-alt"></i> Movimientos
            <i class="fas fa-caret-right"></i>
          </h2>
          <div class="menu-content">
            <div class="data-cards">
              <div class="data-card">
                <h3>Entradas (30 días)</h3>
                <p class="data-value">48</p>
              </div>
              <div class="data-card highlight">
                <h3>Salidas (30 días)</h3>
                <p class="data-value">56</p>
              </div>
              <div class="data-card">
                <h3>Último Movimiento</h3>
                <p class="data-value">Hoy, 10:45 AM</p>
              </div>
            </div>

            <div class="content-section">
              <div class="section-header">
                <h2>
                  <i class="fas fa-list-alt"></i> Historial de Movimientos
                </h2>
                <div class="section-actions">
                  <select id="filtroMovimientos" class="form-control">
                    <option value="">Todos los movimientos</option>
                    <option value="entrada">Entradas</option>
                    <option value="salida">Salidas</option>
                    <option value="ajuste">Ajustes</option>
                  </select>
                  <input type="date" id="fechaDesde" class="form-control" />
                  <input type="date" id="fechaHasta" class="form-control" />
                  <button class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrar
                  </button>
                </div>
              </div>

              <!-- Tabla de movimientos -->
              <div class="table-container">
                <table class="data-table">
                  <thead>
                    <tr>
                      <th>Fecha</th>
                      <th>Tipo</th>
                      <th>Ítem</th>
                      <th>Cantidad</th>
                      <th>Responsable</th>
                      <th>Motivo</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>15/06/2025</td>
                      <td><span class="status-badge active">Entrada</span></td>
                      <td>Concentrado para vacas</td>
                      <td>+20</td>
                      <td>Juan Pérez</td>
                      <td>Compra</td>
                    </tr>
                    <tr>
                      <td>14/06/2025</td>
                      <td><span class="status-badge inactive">Salida</span></td>
                      <td>Antibiótico Bovino</td>
                      <td>-2</td>
                      <td>María Gómez</td>
                      <td>Uso veterinario</td>
                    </tr>
                    <tr>
                      <td>12/06/2025</td>
                      <td>
                        <span class="status-badge maintenance">Ajuste</span>
                      </td>
                      <td>Ordeñadora manual</td>
                      <td>+1</td>
                      <td>Carlos Rojas</td>
                      <td>Corrección de inventario</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Sección 4: Alertas y Reabastecimiento -->
        <div class="menu-section">
          <h2>
            <i class="fas fa-bell"></i> Alertas
            <i class="fas fa-caret-right"></i>
          </h2>
          <div class="menu-content">
            <div class="content-section">
              <div class="section-header">
                <h2>
                  <i class="fas fa-exclamation-triangle"></i> Ítems que
                  necesitan atención
                </h2>
                <div class="section-actions">
                  <button class="btn btn-primary" id="btnConfigurarAlertas">
                    <i class="fas fa-cog"></i> Configurar Alertas
                  </button>
                </div>
              </div>

              <!-- Tabla de alertas -->
              <div class="table-container">
                <table class="data-table">
                  <thead>
                    <tr>
                      <th>Ítem</th>
                      <th>Stock Actual</th>
                      <th>Stock Mínimo</th>
                      <th>Diferencia</th>
                      <th>Última Compra</th>
                      <th>Proveedor</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="alert-row">
                      <td>Antibiótico Bovino</td>
                      <td>8</td>
                      <td>10</td>
                      <td>-2</td>
                      <td>10/06/2025</td>
                      <td>Farmacia Ganadera</td>
                      <td>
                        <button class="btn btn-outline btn-sm">
                          <i class="fas fa-phone"></i> Contactar
                        </button>
                      </td>
                    </tr>
                    <tr class="alert-row">
                      <td>Vacuna Aftosa</td>
                      <td>3</td>
                      <td>15</td>
                      <td>-12</td>
                      <td>05/05/2025</td>
                      <td>Lab. Veterinario</td>
                      <td>
                        <button class="btn btn-outline btn-sm">
                          <i class="fas fa-shopping-cart"></i> Ordenar
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Modal para nuevo ítem -->
    <div class="modal" id="modalNuevoItem">
      <div class="modal-content">
        <div class="modal-header">
          <h3><i class="fas fa-box"></i> Nuevo Ítem de Inventario</h3>
          <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
          <form id="formNuevoItem">
            <div class="form-row">
              <div class="form-group">
                <label for="itemCodigo">Código</label>
                <input type="text" id="itemCodigo" required />
              </div>
              <div class="form-group">
                <label for="itemNombre">Nombre</label>
                <input type="text" id="itemNombre" required />
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="itemCategoria">Categoría</label>
                <select id="itemCategoria" required>
                  <option value="">Seleccione...</option>
                  <option value="alimentos">Alimentos</option>
                  <option value="medicamentos">Medicamentos</option>
                  <option value="equipos">Equipos</option>
                  <option value="insumos">Insumos Agrícolas</option>
                </select>
              </div>
              <div class="form-group">
                <label for="itemSubcategoria">Subcategoría</label>
                <input type="text" id="itemSubcategoria" />
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="itemCantidad">Cantidad</label>
                <input type="number" id="itemCantidad" min="0" required />
              </div>
              <div class="form-group">
                <label for="itemUnidad">Unidad de Medida</label>
                <select id="itemUnidad" required>
                  <option value="unidad">Unidad</option>
                  <option value="kg">Kilogramos</option>
                  <option value="l">Litros</option>
                  <option value="saco">Sacos</option>
                  <option value="frasco">Frascos</option>
                </select>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="itemMinimo">Stock Mínimo</label>
                <input type="number" id="itemMinimo" min="0" required />
              </div>
              <div class="form-group">
                <label for="itemUbicacion">Ubicación</label>
                <input type="text" id="itemUbicacion" required />
              </div>
            </div>

            <div class="form-group">
              <label for="itemDescripcion">Descripción</label>
              <textarea id="itemDescripcion" rows="3"></textarea>
            </div>

            <div class="form-actions">
              <button type="button" class="btn btn-secondary close-modal">
                Cancelar
              </button>
              <button type="submit" class="btn btn-primary">
                Guardar Ítem
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal para nueva categoría -->
    <div class="modal" id="modalNuevaCategoria">
      <div class="modal-content">
        <div class="modal-header">
          <h3><i class="fas fa-tag"></i> Nueva Categoría</h3>
          <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
          <form id="formNuevaCategoria">
            <div class="form-row">
              <div class="form-group">
                <label for="categoriaCodigo">Código (3 letras)</label>
                <input
                  type="text"
                  id="categoriaCodigo"
                  maxlength="3"
                  required
                />
              </div>
              <div class="form-group">
                <label for="categoriaNombre">Nombre</label>
                <input type="text" id="categoriaNombre" required />
              </div>
            </div>

            <div class="form-group">
              <label for="categoriaDescripcion">Descripción</label>
              <textarea id="categoriaDescripcion" rows="3" required></textarea>
            </div>

            <div class="form-actions">
              <button type="button" class="btn btn-secondary close-modal">
                Cancelar
              </button>
              <button type="submit" class="btn btn-primary">
                Crear Categoría
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal para ajuste de inventario -->
    <div class="modal" id="modalAjusteInventario">
      <div class="modal-content">
        <div class="modal-header">
          <h3><i class="fas fa-exchange-alt"></i> Ajuste de Inventario</h3>
          <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
          <form id="formAjusteInventario">
            <div class="form-row">
              <div class="form-group">
                <label for="ajusteTipo">Tipo de Ajuste</label>
                <select id="ajusteTipo" required>
                  <option value="">Seleccione...</option>
                  <option value="entrada">Entrada</option>
                  <option value="salida">Salida</option>
                  <option value="correccion">Corrección</option>
                </select>
              </div>
              <div class="form-group">
                <label for="ajusteFecha">Fecha</label>
                <input type="date" id="ajusteFecha" required />
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="ajusteItem">Ítem</label>
                <select id="ajusteItem" required>
                  <option value="">Seleccione un ítem...</option>
                  <option value="ALM-001">Concentrado para vacas</option>
                  <option value="MED-012">Antibiótico Bovino</option>
                  <option value="EQP-005">Ordeñadora manual</option>
                </select>
              </div>
              <div class="form-group">
                <label for="ajusteCantidad">Cantidad</label>
                <input type="number" id="ajusteCantidad" required />
              </div>
            </div>

            <div class="form-group">
              <label for="ajusteMotivo">Motivo</label>
              <textarea id="ajusteMotivo" rows="3" required></textarea>
            </div>

            <div class="form-actions">
              <button type="button" class="btn btn-secondary close-modal">
                Cancelar
              </button>
              <button type="submit" class="btn btn-primary">
                Registrar Ajuste
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal para configurar alertas -->
    <div class="modal" id="modalConfigurarAlertas">
      <div class="modal-content">
        <div class="modal-header">
          <h3><i class="fas fa-bell"></i> Configurar Alertas de Inventario</h3>
          <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
          <form id="formConfigurarAlertas">
            <div class="alert-settings-section">
              <h4><i class="fas fa-sliders-h"></i> Configuración General</h4>
              <div class="form-group">
                <label for="alertasActivas">Activar alertas</label>
                <select id="alertasActivas" required>
                  <option value="si">Sí</option>
                  <option value="no">No</option>
                </select>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label for="diasAntesAlerta">Días antes para alerta</label>
                  <input
                    type="number"
                    id="diasAntesAlerta"
                    min="1"
                    max="30"
                    value="7"
                  />
                </div>
                <div class="form-group">
                  <label for="notificarPor">Notificar por</label>
                  <select id="notificarPor" multiple>
                    <option value="email" selected>Email</option>
                    <option value="sms">SMS</option>
                    <option value="app">Notificación en App</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="alert-settings-section">
              <h4><i class="fas fa-envelope"></i> Destinatarios de Alertas</h4>
              <div class="form-group">
                <label>Personas a notificar</label>
                <div class="recipients-list">
                  <div class="recipient-item">
                    <input type="checkbox" id="recipient1" checked />
                    <label for="recipient1">admin@agroganadero.com</label>
                  </div>
                  <div class="recipient-item">
                    <input type="checkbox" id="recipient2" checked />
                    <label for="recipient2">inventario@agroganadero.com</label>
                  </div>
                  <div class="recipient-item">
                    <input type="checkbox" id="recipient3" />
                    <label for="recipient3">compras@agroganadero.com</label>
                  </div>
                </div>
                <button type="button" class="btn btn-outline btn-sm">
                  <i class="fas fa-plus"></i> Agregar Email
                </button>
              </div>
            </div>

            <div class="alert-settings-section">
              <h4>
                <i class="fas fa-exclamation-triangle"></i> Tipos de Alertas
              </h4>
              <div class="form-group">
                <div class="alert-type-item">
                  <input type="checkbox" id="alertStockMinimo" checked />
                  <label for="alertStockMinimo"
                    >Stock por debajo del mínimo</label
                  >
                </div>
                <div class="alert-type-item">
                  <input type="checkbox" id="alertStockAgotado" checked />
                  <label for="alertStockAgotado">Stock agotado</label>
                </div>
                <div class="alert-type-item">
                  <input type="checkbox" id="alertVencimiento" />
                  <label for="alertVencimiento"
                    >Productos próximos a vencer</label
                  >
                </div>
                <div class="alert-type-item">
                  <input type="checkbox" id="alertMovimientos" />
                  <label for="alertMovimientos">Movimientos inusuales</label>
                </div>
              </div>
            </div>

            <div class="form-actions">
              <button type="button" class="btn btn-secondary close-modal">
                Cancelar
              </button>
              <button type="submit" class="btn btn-primary">
                Guardar Configuración
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <footer>
      <div class="footer-container">
        <p>© 2025 Agro Ganadero. Todos los derechos reservados.</p>
        <p>Desarrollado por Levi Danieli, Reymond Rendiles y Mario Ramos</p>
      </div>
    </footer>


    <script src="script.js"></script>
    <script>
      // Funciones para el manejo de inventario
function cargarMovimientosInventario() {
    const tipo = document.getElementById('filtroTipoMovimiento').value;
    const categoria = document.getElementById('filtroCategoria').value;
    
    fetch(`api/Agro_Vacuno/inventario_movimientos.php?tipo=${tipo}&categoria=${categoria}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('tablaInventarioBody');
            tbody.innerHTML = '';
            
            data.forEach(movimiento => {
                const tr = document.createElement('tr');
                const tipoClass = movimiento.tipo_movimiento === 'entrada' ? 'text-success' : 
                                movimiento.tipo_movimiento === 'salida' ? 'text-danger' : 'text-warning';
                
                tr.innerHTML = `
                    <td>${movimiento.fecha_movimiento}</td>
                    <td>${movimiento.producto}</td>
                    <td><span class="badge badge-secondary">${movimiento.categoria}</span></td>
                    <td><span class="${tipoClass}">${movimiento.tipo_movimiento.toUpperCase()}</span></td>
                    <td>${movimiento.cantidad} ${movimiento.unidad_medida}</td>
                    <td>${movimiento.proveedor_cliente || '-'}</td>
                    <td>${movimiento.motivo_movimiento}</td>
                    <td>
                        <span class="${movimiento.stock_actual <= 10 ? 'text-danger font-weight-bold' : ''}">
                            ${movimiento.stock_actual || 0} ${movimiento.unidad_medida}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="editarMovimiento(${movimiento.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarMovimiento(${movimiento.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        });
}

function cargarResumenInventario() {
    fetch('api/Agro_Vacuno/inventario_resumen.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('entradasMes').textContent = data.entradas_mes;
            document.getElementById('salidasMes').textContent = data.salidas_mes;
            document.getElementById('stockCritico').textContent = data.stock_critico;
            document.getElementById('proximosVencer').textContent = data.proximos_vencer;
        });
}

function toggleCamposMovimiento() {
    const tipoMovimiento = document.getElementById('tipoMovimiento').value;
    const campoProveedor = document.getElementById('campoProveedor');
    
    if (tipoMovimiento === 'entrada' || tipoMovimiento === 'salida') {
        campoProveedor.style.display = 'flex';
        document.getElementById('proveedorCliente').required = true;
    } else {
        campoProveedor.style.display = 'none';
        document.getElementById('proveedorCliente').required = false;
    }
}

function calcularValorTotal() {
    const cantidad = parseFloat(document.getElementById('cantidadMovimiento').value) || 0;
    const precio = parseFloat(document.getElementById('precioUnitario').value) || 0;
    document.getElementById('valorTotal').value = (cantidad * precio).toFixed(2);
}

function guardarMovimientoInventario() {
    const formData = new FormData();
    const datos = {
        tipo_movimiento: document.getElementById('tipoMovimiento').value,
        fecha_movimiento: document.getElementById('fechaMovimiento').value,
        categoria: document.getElementById('categoriaProducto').value,
        producto: document.getElementById('producto').value,
        cantidad: document.getElementById('cantidadMovimiento').value,
        unidad_medida: document.getElementById('unidadMedida').value,
        proveedor_cliente: document.getElementById('proveedorCliente').value,
        numero_factura: document.getElementById('numeroFactura').value,
        precio_unitario: document.getElementById('precioUnitario').value,
        valor_total: document.getElementById('valorTotal').value,
        motivo_movimiento: document.getElementById('motivoMovimiento').value,
        responsable_movimiento: document.getElementById('responsableMovimiento').value,
        lote_producto: document.getElementById('loteProducto').value,
        fecha_vencimiento: document.getElementById('fechaVencimiento').value,
        ubicacion_almacen: document.getElementById('ubicacionAlmacen').value,
        condicion_almacenamiento: document.getElementById('condicionAlmacenamiento').value,
        observaciones: document.getElementById('observacionesInventario').value
    };
    
    fetch('api/Agro_Vacuno/guardar_movimiento_inventario.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(datos)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Movimiento registrado correctamente');
            cerrarModal('modalInventario');
            cargarMovimientosInventario();
            cargarResumenInventario();
        } else {
            alert('Error: ' + result.message);
        }
    });
}

function filtrarInventario() {
    cargarMovimientosInventario();
}

// Inicializar cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    cargarMovimientosInventario();
    cargarResumenInventario();
    
    // Event listeners para cálculos automáticos
    document.getElementById('cantidadMovimiento').addEventListener('input', calcularValorTotal);
    document.getElementById('precioUnitario').addEventListener('input', calcularValorTotal);
});




    </script>

<script>

</script>
  </body>

</html>
