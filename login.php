<?php
    include 'conexion.php';
    session_start();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Verifica que email y password no estén vacíos (por si falló la validación de JavaScript)
        if (!empty($email) && !empty($password)) {
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
        } else {
            echo "<script>alert('Correo o contraseña vacíos');</script>";
        }
    }

    mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>

	<title>Login </title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/png" href="images/icons/edificio.jpg"/>
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
	<link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
	<link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
	<link rel="stylesheet" type="text/css" href="csss/util.css">
	<link rel="stylesheet" type="text/css" href="csss/main.css">
	<script src="js/validacion.js"></script>
	<script>
    function validar_login(event) {
        let correo  = document.getElementById("email").value;
        let pwd  = document.getElementById("password").value;
        let re = /\S+@\S+\.\S+/;

        if (correo === "" || pwd === "") {
            alert("Debe rellenar los campos faltantes");
            event.preventDefault();
        } else if (!re.test(correo)) {
            alert("El correo electrónico debe contener un @.");
            event.preventDefault(); 
            return false;
        }
        
        // Si pasa todas las validaciones, el formulario se envía
        return true;
    }
</script>


<body>
	<div class="limiter">
	  <div class="container-login100">
		<div class="wrap-login100">
		<form method="post" action="validar_login.php" class="login100-form validate-form" onsubmit="return validar_login(event)">
		<span class="login100-form-title p-b-26">
			  Bienvenido!
			  <img src="img/LOGO.png" class="img-fluid rounded" class="align-items-center" style="width: 50px; height: 50px;" alt="">
			  <?php
				include("conexion.php");
				include("controlador.php");
			  ?>
			</span>
			<div class="wrap-input100 validate-input" data-validate="Valid email is: a@b.c">
			  <input class="input100" type="text" name="email" id="email">
			  <span class="focus-input100" data-placeholder="Correo Electronico"></span>
			</div>
  
			<div class="wrap-input100 validate-input" data-validate="Enter password">
			  <span class="btn-show-pass">
				<i class="zmdi zmdi-eye"></i>
			  </span>
			  <input class="input100" type="password" name="password" id="password">
			  <span class="focus-input100" data-placeholder="Contraseña"></span>
			</div>
  
			<div class="container-login100-form-btn">
			  <div class="wrap-login100-form-btn">
				<a href="index.php" class="login100-form-bgbtn">
				  <div class="login100-form-bgbtn"></div>
				</a>
				<button name ="btningresar" class="login100-form-btn" type="sumbit" value="Iniciar Sesion" >Iniciar Sesion</button>
			  </div>
			</div>
		</form>
  
  
			<div class="text-center p-t-115">
			  <span class="txt1">
				No tienes una cuenta?
			  </span>
  
			  <a class="txt2" href="registrate.php">
				Registrate
			  </a>
			</div>
		  </form>
		</div>
	  </div>
	</div>
  
	<div id="dropDownSelect1"></div>
	
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
	<script src="vendor/animsition/js/animsition.min.js"></script>
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
	<script src="vendor/select2/select2.min.js"></script>
	<script src="vendor/daterangepicker/moment.min.js"></script>
	<script src="vendor/daterangepicker/daterangepicker.js"></script>
	<script src="vendor/countdowntime/countdowntime.js"></script>
	<script src="js/main.js"></script>
</body>
</html>
