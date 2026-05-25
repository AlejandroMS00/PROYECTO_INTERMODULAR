<?php
	$servidor = "localhost";
	$database = "proyecto_comics";
	$usuario = "root";
	$contrasenya = "";
	
	try{
        session_start();
		$id = $_SESSION["id"] ?? 0;
		
		$dsn = "mysql:host=$servidor;dbname=$database";
		$conexion = new PDO($dsn, $usuario, $contrasenya);
		$conexion -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$sql = "DELETE FROM usuario WHERE id_usuario = ?";
		
		$sentencia = $conexion->prepare($sql);
		$sentencia->execute([$id]);
        $_SESSION=[];
        session_destroy();
		
		header("Location:../index.php");
	
	}catch(PDOException $e){
		echo "Falló la conexión ".$e->getMessage();
	}
