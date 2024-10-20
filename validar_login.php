    <?php
    include 'conexion.php';
    session_start();

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conexion->prepare("SELECT * FROM personas WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $datos = $result->fetch_assoc();

        $_SESSION['id'] = $datos['id'];
        $_SESSION['nombre'] = $datos['nombre'];
        $_SESSION['apellidos'] = $datos['apellidos'];
        $_SESSION['numero'] = $datos['numero'];
        $_SESSION['direccion'] = $datos['direccion'];
        $_SESSION['codigo_postal'] = $datos['codigo_postal'];
        $_SESSION['area'] = $datos['area'];
        $_SESSION['email'] = $datos['email'];
        $_SESSION['rol'] = $datos['rol'];

        if ($datos['rol'] == 'admin') {
            header("Location: dashboard.php");
            exit();
        } else if ($datos['rol'] == 'user') {
            header("Location: index.php");
            exit();
        } else {
            echo "No tiene permisos";
            exit();
        }
    } else {
        echo "<script>alert('Usuario o contraseña incorrectos');</script>";
        echo "<script>window.location = 'login.php';</script>";
        session_destroy();
        exit();
    }

    $stmt->close();
    mysqli_close($conexion);
    ?>
