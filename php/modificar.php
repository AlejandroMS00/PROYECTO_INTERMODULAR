<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$servidor = "localhost";
$database = "proyecto_comics";
$usuario = "root";
$contrasenya = "";

try {
	$dsn = "mysql:host=$servidor;dbname=$database";
	$conexion = new PDO($dsn, $usuario, $contrasenya);
	$conexion -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $id = $_SESSION["id"];
    
    if ($id == 0) {
        header("Location: ../index.php");
        exit();
    }

    $sql = "SELECT * FROM usuario WHERE id_usuario = :id";
    $sentencia = $conexion->prepare($sql);
    $sentencia->execute([':id' => $id]);
    $usuario_datos = $sentencia->fetch(PDO::FETCH_ASSOC);

    if (!$usuario_datos) {
        echo "Error crítico: No se encontraron los datos del usuario con ID: " . $id;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['modificar'])) {
        $nombreEditado = htmlspecialchars($_POST["nombre"]);
        $correoEditado = htmlspecialchars($_POST["correo"]);
        $descripcionEditada = htmlspecialchars($_POST["descripcion"]);
        
        $sql_update = "UPDATE usuario SET nombre = :nombre, correo = :correo, descripcion = :descripcion";
        $parametros = [
            ":nombre" => $nombreEditado,
            ":correo" => $correoEditado,
            ":descripcion" => $descripcionEditada,
            ":id_usuario" => $id
        ];

        if (!empty($_POST["contrasena"])) {
            $sql_update .= ", contrasena = :contrasena";
            $parametros[":contrasena"] = password_hash($_POST["contrasena"], PASSWORD_DEFAULT);
        }

        if (isset($_FILES["foto_perfil"]) && is_uploaded_file($_FILES["foto_perfil"]["tmp_name"])) {
            $rutaDirectorio = "../recursos/personas/";
            $nombreArchivo = basename($_FILES["foto_perfil"]["name"]);
            $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
            $extensionesValidas = ['jpg', 'jpeg', 'png'];
            
            if (in_array($extension, $extensionesValidas)) {
                $nombreUsuario = "_perfil_" . $nombreArchivo;
                $rutaFinal = $rutaDirectorio . $nombreUsuario;
                if (move_uploaded_file($_FILES["foto_perfil"]["tmp_name"], $rutaFinal)) {
                    $sql_update .= ", foto_perfil = :foto_perfil";
                    $parametros[":foto_perfil"] = $rutaFinal;
                }
            }
        }

        $sql_update .= " WHERE id_usuario = :id_usuario";
        $sentencia_update = $conexion->prepare($sql_update);
        $sentencia_update->execute($parametros);
        
        $_SESSION['usuario'] = $nombreEditado; 
        header("Location: miPerfil.php");
    }
} catch(PDOException $e) {
    die("Fallo en la base de datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../bootstrap-5.3.8-dist/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/landPage.css">
    <title>Document</title>
</head>
<body class="bg-dark text-white">
    <?php include_once("../modulos/header.php"); ?>
    
    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8"> <article class="card bg-black border-secondary shadow-lg">
                    <div class="card-body p-5">
                        <h2 class="mb-4 text-center fw-bold">Editar Perfil</h2>
                        
                        <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method='POST' enctype="multipart/form-data">
                            
                            <div class="mb-3">
                                <label for="foto_perfil" class="form-label fw-bold">Foto de Perfil</label>
                                <input class="form-control" type="file" name="foto_perfil" id="foto_perfil" accept="image/png, image/jpeg">
                            </div>

                            <div class="mb-3">
                                <label for="nombre" class="form-label fw-bold">Nombre y apellidos / Alias</label>
                                <input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($usuario_datos["nombre"] ?? '') ?>" id="nombre" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="correo" class="form-label fw-bold">Correo electrónico</label>
                                <input type="email" class="form-control" name="correo" value="<?= htmlspecialchars($usuario_datos["correo"] ?? '') ?>" id="correo" required>
                            </div>

                            <div class="mb-4">
                                <label for="descripcion" class="form-label fw-bold">Biografía</label>
                                <textarea class="form-control" name="descripcion" id="descripcion" rows="4"><?= htmlspecialchars($usuario_datos["descripcion"] ?? '') ?></textarea>
                            </div>

                            <hr class="border-secondary mb-4">

                            <div class="mb-4">
                                <label for="contrasena" class="form-label fw-bold text-warning">Cambiar Contraseña</label>
                                <input type="password" class="form-control" name="contrasena" id="contrasena" placeholder="Déjalo en blanco para mantener la actual">
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="miPerfil.php" class="btn btn-outline-secondary px-4 fw-bold">Cancelar</a>
                                <input type="submit" name="modificar" value="Guardar Cambios" class="btn btn-primary px-4 fw-bold">
                            </div>
                        </form>
                    </div>
                </article>
            </div>
        </div>
    </main>
    
    <section class="footerDatos">
        <?php include_once("../modulos/footer.php"); ?>
    </section>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>