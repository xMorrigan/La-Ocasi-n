<?php
if (!empty($_POST["btningresar"])) {
    if (empty($_POST["email"]) and empty($_POST["password"])) {
        echo '<div class="alert alert-danger">LOS CAMPOS ESTAN VACIOS</div>';
    } else {
        $email = $_POST["email"];
        $password = $_POST["password"];
        $sql = $conexion->query("select * from personas where email='$email' and password='$password'");
        if ($datos = $sql->fetch_object()) {
            header("location:index.html");
        } else {
            echo '<div class="alert alert-danger">ACCESO DENEGADO</div>';
        }
    }
}
?>
