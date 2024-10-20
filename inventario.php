<?php 
session_start();
$varsession = $_SESSION['id'];
if ($varsession == null || $varsession == '') {
    echo "<script>alert('Debes iniciar sesión para añadir al carrito');</script>";
    echo "<script>window.location ='login.php';</script>";
    exit();
}

include 'conexion.php';

// Consulta de productos
$productosC = "SELECT * FROM productos";
$productos = mysqli_query($conexion, $productosC);
$productos1 = mysqli_fetch_array($productos);

$busqueda = isset($_GET['busqueda']) ? mysqli_real_escape_string($conexion, $_GET['busqueda']) : '';
$consulta_busqueda = "SELECT * FROM productos WHERE nombre LIKE '%$busqueda%'";
$resultado_busqueda = mysqli_query($conexion, $consulta_busqueda);

// Función para sanitizar entradas
function sanitizar($conexion, $dato) {
    return mysqli_real_escape_string($conexion, trim($dato));
}

// Validación y manejo del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si es una inserción o una actualización
    $isUpdate = isset($_POST['id']);
    $nombre = sanitizar($conexion, $_POST['nombre'] ?? '');
    $descripcion = sanitizar($conexion, $_POST['descripcion'] ?? '');
    $precio = sanitizar($conexion, $_POST['precio'] ?? '');
    $categoria = sanitizar($conexion, $_POST['categoria'] ?? '');
    $stock_minimo = sanitizar($conexion, $_POST['stock_minimo'] ?? '');
    $stock_maximo = sanitizar($conexion, $_POST['stock_maximo'] ?? '');
    $existencia = sanitizar($conexion, $_POST['existencia'] ?? '');

    // Validar que todos los campos obligatorios estén completos
    if (!empty($nombre) && !empty($descripcion) && !empty($precio) && !empty($categoria) && !empty($stock_minimo) && !empty($existencia)) {
        $directorio = 'img';
        $foto = 'img/default.jpg'; // Imagen por defecto

        // Si se sube una imagen, manejar la subida
        if (isset($_FILES['img']) && is_uploaded_file($_FILES['img']['tmp_name'])) {
            $ext = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
            $nombre_foto = uniqid('img_', true) . '.' . $ext;
            $foto = $directorio . "/" . $nombre_foto;
            if (move_uploaded_file($_FILES['img']['tmp_name'], $foto)) {
                // Imagen subida exitosamente
            } else {
                // Manejar error en la subida
                echo "<script>alert('Error al subir la imagen.');</script>";
                exit();
            }
        }

        if ($isUpdate) {
            $id = intval($_POST['id']);
            $imagen_existente = sanitizar($conexion, $_POST['archivo_mod'] ?? '');

            if ($foto !== 'img/default.jpg') {
                $update_query = "UPDATE productos SET nombre='$nombre', descripcion='$descripcion', precio='$precio', categoria='$categoria', stock_minimo='$stock_minimo', stock_maximo='$stock_maximo', existencia='$existencia', img='$foto' WHERE id='$id'";
            } else {
                $update_query = "UPDATE productos SET nombre='$nombre', descripcion='$descripcion', precio='$precio', categoria='$categoria', stock_minimo='$stock_minimo', stock_maximo='$stock_maximo', existencia='$existencia' WHERE id='$id'";
            }

            if (mysqli_query($conexion, $update_query)) {
                echo "<script> alert('Producto actualizado'); </script>";
                echo "<script> window.location='inventario.php'; </script>";
            } else {
                echo "<script> alert('Error al actualizar el producto.'); </script>";
            }
        } else {
            // Insertar el producto con o sin imagen
            $insertar = "INSERT INTO productos (nombre, descripcion, precio, categoria, stock_minimo, stock_maximo, existencia, img) 
                         VALUES ('$nombre', '$descripcion', '$precio', '$categoria', '$stock_minimo', '$stock_maximo', '$existencia', '$foto')";

            if (mysqli_query($conexion, $insertar)) {
                echo "<script> alert('Producto registrado'); </script>";
                echo "<script> window.location='inventario.php'; </script>";
            } else {
                echo "<script> alert('Error al registrar el producto.'); </script>";
            }
        }
    } else {
        // Si algún campo obligatorio está vacío
        echo "<script> alert('Por favor, completa todos los campos obligatorios.'); </script>";
    }
}

// Manejo de eliminación de productos
if (isset($_REQUEST['eliminar'])) {
    $eliminar = intval($_REQUEST['eliminar']);
    // Opcional: Eliminar la imagen asociada
    $producto = mysqli_query($conexion, "SELECT img FROM productos WHERE id = $eliminar");
    if ($producto && mysqli_num_rows($producto) > 0) {
        $prod = mysqli_fetch_assoc($producto);
        if ($prod['img'] !== 'img/default.jpg' && file_exists($prod['img'])) {
            unlink($prod['img']);
        }
    }
    mysqli_query($conexion, "DELETE FROM productos WHERE id = $eliminar");
    echo "<script> alert('Producto borrado'); </script>";
    echo "<script> window.location='inventario.php'; </script>";
}

// Manejo de edición de productos
if (isset($_REQUEST['editar'])) {
    $editar = intval($_REQUEST['editar']);
    $registro = mysqli_query($conexion, "SELECT * FROM productos WHERE id = $editar");
    $reg = mysqli_fetch_array($registro);
}
?>


<!DOCTYPE html>
<html lang="en">
    
<head>
    <meta charset="utf-8">
    <title>Dashboard</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">


    <link href="css/bootstrap.min.css" rel="stylesheet">

    <link href="css/style.css" rel="stylesheet">

    <script>
        function preguntar(){
            return confirm("¿Desea eliminar el registro?");
        }
        function validar_producto(event) {
            let stock_maximo = document.getElementById("stock_maximo").value;
            let stock_minimo = document.getElementById("stock_minimo").value;
            let nombre = document.getElementById("nombre").value;
            let precio = document.getElementById("precio").value;
            let existencia = document.getElementById("existencia").value;
        
            if (nombre === "") {
                alert("Por favor, ingresa el nombre del producto.");
                return false; // Detener el envío
            }
        
            if (precio === "") {
                alert("Debes de ingresar un precio.");
                return false; // Detener el envío
            }
        
            if (stock_maximo === "" || stock_minimo === "") {
                alert("Por favor, debes de ingresar el Máximo y el Mínimo de stock.");
                return false; // Detener el envío
            }
        
            if (existencia === "") {
                alert("Debes de añadir la cantidad de existencia.");
                return false; // Detener el envío
            }
        
            return true; // Si todas las validaciones pasan, el formulario se envía
        }

    </script>

</head>

<body>

    <div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>


    <nav class="navbar bg-body-tertiary fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand fs-1 text-primary mb-0" href="index.php">LA OCASIÓN </a><span class="text-primary-emphasis">Dashboard</s>
                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon text-success"><i class="fa fa-solid fa-bars py-1"></i></span>
                </button>
                <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasNavbarLabel">La ocasión</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Perfil</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Administrador
                        </a>
                        <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="dashboard.php">Inicio del dashboard</a></li>
                        <li><a class="dropdown-item" href="cat_usuarios.php">Usuarios</a></li>
                        <li><a class="dropdown-item" href="inventario.php">Productos</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="logout.php">Cerrar sesión</a></li>
                        </ul>
                    </li>
                    </ul>
                </div>
                </div>
            </div>
            </nav>


    <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Search by keyword</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex align-items-center">
                    <div class="input-group w-75 mx-auto d-flex">
                        <input type="search" class="form-control p-3" placeholder="keywords" aria-describedby="search-icon-1">
                        <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>



        <div class="container-fluid page-header py-5">
            <h1 class="text-center text-white display-6">Inventario</h1>
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="dashboard.php">Inicio</a></li>
                <li class="breadcrumb-item"><a href="inventario.php">Producto</a></li>
            </ol>
        </div>


        
        <div class="container rounded bg-white mt-5 mb-5">
        <div class="row">
            <div class="col-md-3 border-right">
            <form action="inventario.php" method="POST" enctype="multipart/form-data">
                <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                    <input class="form-control" type="file" id="img" name="img" size="50" accept="image/*">
                        <img id="preview" class="rounded-circle mt-5" width="150px"  style="display: <?php if (isset($_REQUEST['editar'])) { echo 'block'; } else { echo 'none'; } ?>;" src="<?php if (isset($_REQUEST['editar'])) echo $reg['img']; ?>" alt="Foto" width="250" height="250"><span> </span></div>
                </div>
       
                <script>
                    document.getElementById('img').addEventListener('change', function(event) {
                        const [file] = event.target.files;
                        const preview = document.getElementById('preview');
                        
                        if (file) {
                            preview.src = URL.createObjectURL(file);
                            preview.style.display = 'block';
                            preview.onload = () => URL.revokeObjectURL(preview.src);
                        } else {
                            preview.style.display = 'none';
                            preview.src = '';
                        }
                    });
                </script>
            <div class="col-md-5 border-right">
                <div class="p-3 py-5">
                    <div class="d-flex justify-content-between align-items-center mb-3 mx-5">
                        <h4 class="text-right">Inventario</h4>
                    </div>
                    
                    <div class="row mt-2">
                        <div class="col-md-6"><label class="labels">Nombre del producto</label>
                        <input id="nombre"  name="nombre" type="text" class="form-control" placeholder="Nombre del producto"  <?php if(isset($_REQUEST['editar'])){ echo "value='".$reg['nombre']."' "; }?>></div>
                        <div class="col-md-6">
                            <label for="categoria" class="labels">Categoría</label>
                            <select id="categoria" name="categoria" class="form-control">
                                <option value="<?php if(isset($_REQUEST['editar'])){ echo $reg['categoria']; }?>"><?php if(isset($_REQUEST['editar'])){ echo $reg['categoria']; }?></option>
                                <option value="botines">botines</option>
                                <option value="sombreros">sombreros</option>
                                <option value="trajes">trajes</option>
                                <option value="camisas">camisas</option>
                            </select>
                        </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 mt-3"><label class="labels">Stock mínimo</label><input id="stock_minimo" name="stock_minimo" type="number" class="form-control" placeholder="Ingresa el stock minimo" <?php if(isset($_REQUEST['editar'])){ echo "value='".$reg['stock_minimo']."' "; }?> ></div>
                            <div class="col-md-12 mt-3"><label class="labels">Existencia</label><input id="existencia" name="existencia" type="number" class="form-control" placeholder="Ingresa la existencia" <?php if(isset($_REQUEST['editar'])){ echo "value='".$reg['existencia']."' "; }?> ></div>
                            <div class="col-md-12 mt-3"><label class="labels">Precio</label><input id="precio" name="precio" type="number" class="form-control" placeholder="Ingresa el precio" <?php if(isset($_REQUEST['editar'])){ echo "value='".$reg['precio']."' "; }?>></div>
                        </div>
                    <div class="row mt-3">
                        <div class="col-md-6"><label class="labels">Descripción</label><textarea id="descripcion" name="descripcion" type="text" class="form-control" placeholder="Descripción del producto"><?php if(isset($_REQUEST['editar'])){ echo $reg['descripcion']; }?></textarea></div>
                    </div>
                    <div class="mt-5 px-10 text-center">
                    <input type="submit"  class="btn btn-primary profile-button" onclick="return validar_producto(event)" 
                    <?php if(isset($_REQUEST['editar'])){ echo "value='Guardar'";}else{"value='Insertar'";}?> id="boton" >    
                    <?php 
                    if(isset($_REQUEST['editar'])) { 
                        echo "<input type='hidden' name='archivo_mod' value='" . $reg['img'] . "'>";
                        echo "<input type='hidden' name='id' value='".$reg['id']."'>"; }
                    ?>
                    </form>
                </div>
            </div>
                
            </div>

            <div class="input-group">
                <form method="GET" action="inventario.php">
                    <div class="row w-100 mb-10">
                        <input name="busqueda" type="text" class="col form-control rounded" placeholder="Buscar" aria-label="Search" aria-describedby="search-addon" />
                        <button type="submit" class="col btn btn-outline-primary" data-mdb-ripple-init><i class="fas fa-search"></i></button>
                    </div>
                </form>
            </div>

            <?php
            if (mysqli_num_rows($resultado_busqueda) > 0) {
            ?>
            <table class="table">
                <thead>
                    <tr>
                    <th scope="col"></th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Precio</th>
                    <th scope="col">Existencia</th>
                    <th scope="col">Stock minimo</th>
                    <th scope="col">Stock maximo</th>
                    <th scope="col">Eliminar</th>
                    <th scope="col">Editar</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($resultado = mysqli_fetch_array($resultado_busqueda)) { ?>
                    <tr>
                    <th scope="row">
                    <div class="d-flex align-items-center">
                        <img src="<?php echo $resultado['img']; ?>" class="img-fluid me-5 rounded-circle" style="width: 80px; height: 80px;" alt="">
                    </div>
                    </th>
                    <td><?php echo $resultado['nombre']; ?></td>
                    <td><?php echo $resultado['precio']; ?></td>
                    <?php
                    $existencia = isset($resultado['existencia']) ? $resultado['existencia'] : 0;
                    $stock_minimo = isset($resultado['stock_minimo']) ? $resultado['stock_minimo'] : 0;
                    $stock_maximo = isset($resultado['stock_maximo']) ? $resultado['stock_maximo'] : 0;

                    $existencia_class = '';
                    if ($existencia < $stock_minimo) {
                        $existencia_class = 'bg-danger text-white';
                    } elseif ($existencia > $stock_maximo) {
                        $existencia_class = 'bg-success text-white';
                    }
                    ?>
                    <td class="<?php echo $existencia_class; ?>"><?php echo $resultado['existencia']; ?></td>
                    <td><?php echo $resultado['stock_minimo']; ?></td>
                    <td><?php echo $resultado['stock_maximo']; ?></td>
                    <td><a onclick="return preguntar()" href="inventario.php?eliminar=<?php echo $resultado['id']; ?>">Eliminar</a></td>
                    <td><a href="inventario.php?editar=<?php echo $resultado['id']; ?>">Editar</a></td>
                    </tr>
                    <?php } ?>
                </tbody>
                </table>
                <?php } else { ?>
                    <p>No se encontraron resultados para la búsqueda.</p>
                <?php } ?>
                </div>
            </div>
        </div>

        
    </div>
    </div>
    </div>



             <div class="container-fluid bg-dark text-white-50 footer pt-5 mt-5">
                <div class="container py-5">
                    <div class="pb-4 mb-4" style="border-bottom: 1px solid rgba(226, 175, 24, 0.5) ;">
                        <div class="row g-4">
                            <div class="col-lg-3">
                                <a href="#">
                                    <h1 class="text-primary mb-0">LA OCASIÓN WESTERN & CHARRO</h1>
                                    <p class="text-secondary mb-0">"Con el porte de un charro y la esencia del rancho, vivimos la tradición mexicana."</p>
                                </a>
                            </div>
                            <div class="col-lg-6">
                                <div class="position-relative mx-auto">
                                    <input class="form-control border-0 w-100 py-3 px-4 rounded-pill" type="number" placeholder="Tu correo">
                                    <button type="submit" class="btn btn-primary border-0 border-secondary py-3 px-4 position-absolute rounded-pill text-white" style="top: 0; right: 0;">Registrate!</button>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="d-flex justify-content-end pt-3">
                                    <a class="btn  btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i class="fab fa-twitter"></i></a>
                                    <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i class="fab fa-facebook-f"></i></a>
                                    <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i class="fab fa-youtube"></i></a>
                                    <a class="btn btn-outline-secondary btn-md-square rounded-circle" href=""><i class="fab fa-linkedin-in"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-5">
                        <div class="col-lg-3 col-md-6">
                            <div class="footer-item">
                                <h4 class="text-light mb-3">Calidad Garantizada!</h4>
                                <p class="mb-4">Desde 1972, en nuestra tienda celebramos la grandeza de los charros mexicanos y la vida en el rancho,
                                    ofreciendo productos auténticos que honran la tradición y el orgullo de nuestra herencia.</p>
                                <a href="" class="btn border-secondary py-2 px-4 rounded-pill text-primary">Leer Más</a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="d-flex flex-column text-start footer-item">
                                <h4 class="text-light mb-3">Detalles de tienda</h4>
                                <a class="btn-link" href="">Contactanos</a>
                                <a class="btn-link" href="">Politica y Privacidad</a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="d-flex flex-column text-start footer-item">
                                <h4 class="text-light mb-3">Crear una cuenta</h4>
                                <a class="btn-link" href="">Mi perfil</a>
                                <a class="btn-link" href="">Detalles de compra</a>
                                <a class="btn-link" href="">Carrito</a>
                                <a class="btn-link" href="">Historial de compras</a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="footer-item">
                                <h4 class="text-light mb-3">Contacto</h4>
                                <p>Dirección: Villahermosa-Teapa 86288 Parrilla II, Tab. México</p>
                                <p>Correo: mine810.40@gmail.com</p>
                                <p>Telefono: 9934475060</p>
                                <p>Payment Accepted</p>
                                <img src="img/payment.png" class="img-fluid" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid copyright bg-dark py-4">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                            <span class="text-light"><a href="#"><i class="fas fa-copyright text-light me-2"></i>COMERCIALIZADORA LA OCASIÓN S.A DE C.V</a>, Todos los Derechos Reservados.</span>
                        </div>
                        <div class="col-md-6 my-auto text-center text-md-end text-white">
                        </div>
                    </div>
                </div>
            </div>


       <a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top"><i class="fa fa-arrow-up"></i></a>   

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Manejar incremento y decremento de cantidad
                function updateQuantity(change, carritoId) {
                    var cantidadInput = document.getElementById('cantidad-' + carritoId);
                    var currentQuantity = parseInt(cantidadInput.value);

                    if (!isNaN(currentQuantity)) {
                        var newQuantity = currentQuantity + change;
                        if (newQuantity > 0) { // Evitar cantidades negativas
                            cantidadInput.value = newQuantity;
                        }
                    }
                }

                // Obtener todos los botones de incremento y decremento
                document.querySelectorAll('button[id^="buttonRes-"], button[id^="buttonSum-"]').forEach(function(button) {
                    button.addEventListener('click', function(event) {
                        event.preventDefault(); // Evitar el comportamiento predeterminado
                        var carritoId = this.id.split('-')[1];
                        var change = this.id.startsWith('buttonRes') ? -1 : 1;
                        updateQuantity(change, carritoId);
                    });
                });
            });
            </script>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>



    <script src="js/main.js"></script>
    </body>

</html>