<?php
	$servidor = "localhost";
	$database = "proyecto_comics";
	$usuario = "root";
	$contrasenya = "";
	
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	try{
		$id = $_POST["id_usuario"] ?? 0;
		
		$dsn = "mysql:host=$servidor;dbname=$database";
		$conexion = new PDO($dsn, $usuario, $contrasenya);
		$conexion -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['login'] == "Iniciar sesión"){
        	$correo = $_POST["correoLogin"] ?? "";
			$contrasena = $_POST["passLogin"];

			$sql = "SELECT * FROM usuario WHERE correo = ?";

			$sentencia = $conexion -> prepare($sql);
			$sentencia -> setFetchMode(PDO::FETCH_ASSOC);
			$sentencia -> execute([$correo]);
			$usuario = $sentencia -> fetch();
			
			if($usuario && password_verify($contrasena, $usuario['contrasena'])) {
			//Inicio de sesión:
				$_SESSION["id"] = $usuario["id_usuario"];
				$_SESSION["usuario"] = $usuario["nombre"];
				echo $_SESSION["usuario"];
				header("Location:../index.php");
			} else {
				echo "No te has podido logear";
				header("Location:../index.php");
			}
        }
	}catch(PDOException $e){
		echo "Falló la conexión ".$e->getMessage();
	}
	