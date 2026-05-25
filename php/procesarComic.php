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

    $id_usuario = $_SESSION['id'];
    $titulo = htmlspecialchars($_POST['tituloComic']);
    $descripcion = htmlspecialchars($_POST['descComic']);
    $genero_id = $_POST['generoComic'];
    $precio = $_POST['precioComic'];
    $fechaPublicacion = date('Y-m-d');

    $ruta_portada_bd = '';
    $ruta_pdf_bd = '';
    $subida_ok = true;

    if(isset($_FILES["portadaComic"]) && is_uploaded_file($_FILES["portadaComic"]["tmp_name"])){
    
    $rutaPortadas = "../recursos/portadas/"; 
    $nombrePortada = basename($_FILES["portadaComic"]["name"]);
    $extensionPortada = strtolower(pathinfo($nombrePortada, PATHINFO_EXTENSION));
    $extensionesValidas = ['jpg', 'jpeg', 'png'];
    
    if(!in_array($extensionPortada, $extensionesValidas)){
        echo "Error: La portada solo puede ser JPG o PNG.";
    }
    if($_FILES["portadaComic"]["size"] > 5 * 1024 * 1024){
        echo "Error: La portada supera los 5MB.";
    }
    
    $ruta_portada = $rutaPortadas.$nombrePortada;

    $res = move_uploaded_file($_FILES["portadaComic"]["tmp_name"], $ruta_portada);
    
    if($res){
        $ruta_portada_bd = $ruta_portada;
        echo "Fichero guardado con éxito.";
    } else {
        $subida_ok = false;
        echo "Error al guardar la portada.";
    }
}

if($subida_ok && isset($_FILES["archivoComic"]) && is_uploaded_file($_FILES["archivoComic"]["tmp_name"])){
    
    $rutaPDFs = "../recursos/comics/"; 
    $nombrePDF = basename($_FILES["archivoComic"]["name"]);
    $extensionPDF = strtolower(pathinfo($nombrePDF, PATHINFO_EXTENSION));
    
    if($extensionPDF != 'pdf'){
        echo "Error: El cómic debe estar en formato PDF.";
    }
    if($_FILES["archivoComic"]["size"] > 20 * 1024 * 1024){ 
        echo "Error: El archivo PDF supera los 20MB.";
    }
    
    $ruta_pdf = $rutaPDFs.$nombrePDF;

    $res = move_uploaded_file($_FILES["archivoComic"]["tmp_name"], $ruta_pdf);
    
    if($res){
        $ruta_pdf_bd = $ruta_pdf;
    } else {
        $subida_ok = false;
        echo "Error al guardar el archivo PDF.";
    }
} else {
    $subida_ok = false;
}

if($subida_ok){

    $conexion->beginTransaction();

    $sqlComic = "INSERT INTO comic (id_usuario, nombre, descripcion, portada, archivo_comic, f_publicacion, precio) VALUES (:id_usuario, :nombre, :descripcion, :portada, :archivo_comic, :f_publicacion, :precio)";
        
    $sentenciaComic = $conexion->prepare($sqlComic);
    $sentenciaComic->execute([
        ':id_usuario' => $id_usuario,
        ':nombre' => $titulo,
        ':descripcion' => $descripcion,
        ':portada' => $ruta_portada_bd,
        ':archivo_comic' => $ruta_pdf_bd,
        ':f_publicacion' => $fechaPublicacion,
        ':precio' => $precio
    ]);

    $idComicInsertado = $conexion->lastInsertId();

    $sqlGenero = "INSERT INTO tiene (id_comic, id_genero) VALUES (:id_comic, :id_genero)";
    $sentenciaGenero = $conexion->prepare($sqlGenero);
    $sentenciaGenero->execute([
        ':id_comic' => $idComicInsertado,
        ':id_genero' => $genero_id
    ]);

    $conexion->commit();

    header("Location: miPerfil.php");
} 
}catch(PDOException $e){
    echo "Falló la conexión ".$e->getMessage();
}