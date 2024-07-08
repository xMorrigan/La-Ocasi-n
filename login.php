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
</head>
<body>
	<div class="limiter">
	  <div class="container-login100">
		<div class="wrap-login100">
		  <form class="login100-form validate-form">
		  <form method ="post" action=""> 
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
			  <input class="input100" type="text" name="password">
			  <span class="focus-input100" data-placeholder="Contraseña"></span>
			</div>
  
			<div class="container-login100-form-btn">
			  <div class="wrap-login100-form-btn">
				<a href="index.html" class="login100-form-bgbtn">
				  <div class="login100-form-bgbtn"></div>
				</a>
				<button name ="btningresar" class="login100-form-btn" type="sumbit" value="Iniciar Sesion" >
			  </div>
			</div>
		</from>
  
			<script>
			  function validarEmail() {
				const email = document.getElementById("email").value;
				if (!email.includes("@") || !email.includes(".")) {
				  alert("El correo electrónico debe contener al menos un arroba (@) y un punto (.)");
				  return false; // Evita que se siga el enlace href="#"
				}
  
				// Si la validación pasa, permite la redirección a index.html
				window.location.href = "index.html";
			  }
			</script>
  
			<div class="text-center p-t-115">
			  <span class="txt1">
				No tienes una cuenta?
			  </span>
  
			  <a class="txt2" href="registrate.html">>
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
