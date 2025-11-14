<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD con PHP y Fetch - Sistema de Productos</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .card {
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }
        .card-header {
            font-weight: bold;
        }
        .btn-action {
            margin: 2px;
            font-size: 0.875rem;
        }
        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }
        .required::after {
            content: " *";
            color: red;
        }
        .form-text {
            font-size: 0.875rem;
            color: #6c757d;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <!-- Card Principal -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-boxes me-2"></i> Sistema de Gestión de Productos
                        </h3>
                    </div>
                    <div class="card-body">
                        <!-- Formulario de Productos -->
                        <form id="formProducto">
                            <input type="hidden" id="productoId" name="id">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="codigo" class="form-label required">Código del Producto</label>
                                        <input type="text" class="form-control" id="codigo" name="codigo" 
                                               placeholder="Ej: PROD-001" required>
                                        <div class="form-text">Código único identificador del producto</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="producto" class="form-label required">Nombre del Producto</label>
                                        <input type="text" class="form-control" id="producto" name="producto" 
                                               placeholder="Ej: Laptop Dell Inspiron" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="precio" class="form-label required">Precio ($)</label>
                                        <input type="number" class="form-control" id="precio" name="precio" 
                                               step="0.01" min="0.01" placeholder="Ej: 299.99" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="cantidad" class="form-label required">Cantidad</label>
                                        <input type="number" class="form-control" id="cantidad" name="cantidad" 
                                               min="0" placeholder="Ej: 50" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-success me-2" id="btnGuardar">
                                        <i class="fas fa-save me-1"></i> Guardar Producto
                                    </button>
                                    <button type="button" class="btn btn-warning me-2" id="btnActualizar" style="display: none;">
                                        <i class="fas fa-edit me-1"></i> Actualizar Producto
                                    </button>
                                    <button type="button" class="btn btn-secondary" id="btnCancelar" style="display: none;">
                                        <i class="fas fa-times me-1"></i> Cancelar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Búsqueda de Productos -->
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-search me-1"></i> Buscar Producto
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="codigoBuscar" 
                                       placeholder="Ingrese el código del producto a buscar">
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary w-100" id="btnBuscar">
                                    <i class="fas fa-search me-1"></i> Buscar Producto
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Lista de Productos -->
                <div class="card mt-4">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list me-1"></i> Lista de Productos
                        </h5>
                        <button type="button" class="btn btn-light btn-sm" id="btnRefrescar">
                            <i class="fas fa-sync-alt me-1"></i> Refrescar
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Código</th>
                                        <th>Producto</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaProductos">
                                    <tr>
                                        <td colspan="6" class="text-center">Cargando productos...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Nuestro script -->
    <script src="script.js"></script>
</body>
</html>