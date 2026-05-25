<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$servidor = "localhost";
$database = "proyecto_comics";
$usuario = "root";
$contrasenya = "";

try {
    $mi_id = $_SESSION["id"] ?? $_SESSION["id_usuario"] ?? 0;
    
    if ($mi_id == 0) {
        header("Location: ../index.php");
    }

    $id_comic = $_GET['id'] ?? $_POST['id_comic'] ?? 0;

    if ($id_comic == 0) {
        header("Location: miPerfil.php");
    }

		$dsn = "mysql:host=$servidor;dbname=$database";
		$conexion = new PDO($dsn, $usuario, $contrasenya);
		$conexion -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nuevo_titulo = trim($_POST['tituloComic']);
        $nueva_desc = trim($_POST['descComic']);
        $nuevo_genero = $_POST['generoComic'];

        $sqlUpdate = "UPDATE comic SET nombre = :nombre, descripcion = :desc WHERE id_comic = :id_comic AND id_usuario = :id_usuario";
        $sentenciaUpdate = $conexion->prepare($sqlUpdate);
        $sentenciaUpdate->execute([
            ':nombre' => $nuevo_titulo,
            ':desc' => $nueva_desc,
            ':id_comic' => $id_comic,
            ':id_usuario' => $mi_id
        ]);

        $sqlUpdateGen = "UPDATE tiene SET id_genero = :id_genero WHERE id_comic = :id_comic";
        $sentenciaUpdateGen = $conexion->prepare($sqlUpdateGen);
        $sentenciaUpdateGen->execute([
            ':id_genero' => $nuevo_genero,
            ':id_comic' => $id_comic
        ]);

        header("Location: miPerfil.php");
    }

    $sqlComic = "SELECT c.*, t.id_genero FROM comic c JOIN tiene t ON c.id_comic = t.id_comic WHERE c.id_comic = :id_comic AND c.id_usuario = :id_usuario";
    $sentenciaComic = $conexion->prepare($sqlComic);
    $sentenciaComic->execute([':id_comic' => $id_comic, ':id_usuario' => $mi_id]);
    $miObra = $sentenciaComic->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error de base de datos: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../bootstrap-5.3.8-dist/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/landPage.css">
    <title>Document</title>
</head>
<body class="bg-dark text-white">
    <?php include_once("../modulos/header.php"); ?>

    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card bg-black border-secondary shadow-lg text-white">
                    <div class="card-header bg-dark text-white p-3">
                        <h4 class="mb-0 fw-bold">Editar publicación</h4>
                    </div>
                    <div class="card-body p-4">
                        <form action="editarObra.php" method="POST">
                            <input type="hidden" name="id_comic" value="<?= $miObra['id_comic'] ?>">

                            <div class="mb-3">
                                <label for="tituloComic" class="form-label fw-bold">Título de la Obra</label>
                                <input type="text" class="form-control" name="tituloComic" id="tituloComic" value="<?= htmlspecialchars($miObra['nombre']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="descComic" class="form-label fw-bold">Sinopsis / Descripción</label>
                                <textarea class="form-control" name="descComic" id="descComic" rows="4" required><?= htmlspecialchars($miObra['descripcion']) ?></textarea>
                            </div>

                            <div class="mb-4">
                                <label for="generoComic" class="form-label fw-bold">Género Principal</label>
                                <select class="form-select" name="generoComic" id="generoComic" required>
                                    <option value="1">Superhéroes / Ciencia Ficción</option>
                                    <option value="2">Romance</option>
                                    <option value="3">Fantasía / Magia</option>
                                    <option value="4">Terror / Misterio</option>
                                    <option value="5">Acción / Aventura</option>
                                    <option value="6">Slice of Life (Costumbrista)</option>
                                    <option value="7">Humor</option>
                                    <option value="8">Otro</option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="miPerfil.php" class="btn btn-outline-secondary fw-bold">Cancelar</a>
                                <button type="submit" class="btn btn-primary fw-bold px-4">Guardar Cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>