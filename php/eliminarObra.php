<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_comic'])) {
    
    $servidor = "localhost";
    $database = "proyecto_comics";
    $usuario = "root";
    $contrasenya = ""; 
    
    try {
        $mi_id = $_SESSION["id"] ?? $_SESSION["id_usuario"] ?? 0;
        $id_comic = $_POST['id_comic'];
        
        if ($mi_id == 0) {
            header("Location: ../index.php");
        }
        
		$dsn = "mysql:host=$servidor;dbname=$database";
		$conexion = new PDO($dsn, $usuario, $contrasenya);
		$conexion -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sqlCheck = "SELECT id_comic FROM comic WHERE id_comic = :id_comic AND id_usuario = :id_usuario";
        $sentenciaCheck = $conexion->prepare($sqlCheck);
        $sentenciaCheck->execute([
            ':id_comic' => $id_comic,
            ':id_usuario' => $mi_id
        ]);
        
        if ($sentenciaCheck->rowCount() > 0) {
            $conexion->prepare("DELETE FROM tiene WHERE id_comic = :id_comic")->execute([':id_comic' => $id_comic]);

            $conexion->prepare("DELETE FROM comentario WHERE id_comic = :id_comic")->execute([':id_comic' => $id_comic]);
            
            $sqlDelete = "DELETE FROM comic WHERE id_comic = :id_comic";
            $sentenciaDelete = $conexion->prepare($sqlDelete);
            $sentenciaDelete->execute([':id_comic' => $id_comic]);
        }
        
        header("Location: miPerfil.php");
    } catch(PDOException $e) {
        echo "Error al intentar borrar la obra: " . $e->getMessage();
    }
} else {
    header("Location: ../index.php");
}
