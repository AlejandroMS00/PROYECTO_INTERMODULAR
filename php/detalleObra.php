<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$servidor = "localhost";
$database = "proyecto_comics";
$usuario = "root";
$contrasenya = "";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: miPerfil.php");
    exit();
}

$id_comic = $_GET['id'];

try {
    $id_usuario = $_SESSION["id"] ?? 0;
		
	$dsn = "mysql:host=$servidor;dbname=$database";
	$conexion = new PDO($dsn, $usuario, $contrasenya);
	$conexion -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $ya_dio_like = false;
    $total_likes = 0;

    $sentenciaLikes = $conexion->prepare("SELECT COUNT(*) FROM me_gusta WHERE id_comic = ?");
    $sentenciaLikes->execute([$id_comic]);
    $total_likes = $sentenciaLikes->fetchColumn();

    if ($id_usuario != 0) {
        $sentenciaCheck = $conexion->prepare("SELECT * FROM me_gusta WHERE id_comic = ? AND id_usuario = ?");
        $sentenciaCheck->execute([$id_comic, $id_usuario]);
        $ya_dio_like = $sentenciaCheck->rowCount() > 0;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle_like'])) {
        if ($id_usuario != 0) {
            if ($ya_dio_like) {
                $conexion->prepare("DELETE FROM me_gusta WHERE id_comic = ? AND id_usuario = ?")->execute([$id_comic, $id_usuario]);
            } else {
                $conexion->prepare("INSERT INTO me_gusta (id_comic, id_usuario) VALUES (?, ?)")->execute([$id_comic, $id_usuario]);
            }
            header("Location: detalleObra.php?id=".$id_comic);
        } else {
            header("Location: ../index.php");
        }
    }

    $sql = "SELECT c.*, g.nombre AS nombre_genero, u.nombre AS nombre_autor 
            FROM comic c 
            JOIN tiene t ON c.id_comic = t.id_comic 
            JOIN genero g ON t.id_genero = g.id_genero 
            JOIN usuario u ON c.id_usuario = u.id_usuario 
            WHERE c.id_comic = :id_comic";
            
    $sentencia = $conexion->prepare($sql);
    $sentencia->execute([':id_comic' => $id_comic]);
    $comic = $sentencia->fetch();

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comentar'])) {
        $mi_id = $_SESSION["id"] ?? $_SESSION["id_usuario"] ?? 0;
        $textoComentario = trim($_POST['texto']);

        if ($mi_id != 0 && !empty($textoComentario)) {
            $sqlInsert = "INSERT INTO comentario (id_comic, id_usuario, f_publicacion, texto) 
                            VALUES (:id_comic, :id_usuario, NOW(), :texto)";
            $sentenciaInsert = $conexion->prepare($sqlInsert);
            $sentenciaInsert->execute([
                ':id_comic' => $id_comic, 
                ':id_usuario' => $mi_id,
                ':texto' => htmlspecialchars($textoComentario)
            ]);

            header("Location: detalleObra.php?id=" . $id_comic);
        }
    }

    $sqlComentarios = "SELECT c.texto, c.f_publicacion, u.nombre, u.foto_perfil, u.id_usuario 
                        FROM comentario c 
                        JOIN usuario u ON c.id_usuario = u.id_usuario 
                        WHERE c.id_comic = :id_comic 
                        ORDER BY c.f_publicacion DESC";
    $sentenciaComentarios = $conexion->prepare($sqlComentarios);
    $sentenciaComentarios->execute([':id_comic' => $id_comic]);
    $lista_comentarios = $sentenciaComentarios->fetchAll(PDO::FETCH_ASSOC);

    if (!$comic) {
        header("Location: miPerfil.php");
    }

} catch (PDOException $e) {
    echo "Error al cargar la obra: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link href="../bootstrap-5.3.8-dist/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/landPage.css">
</head>
<body class="bg-black text-white">

<?php include_once("../modulos/header.php"); ?>

<main class="container my-5">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow border-0">
                <img src="<?= $comic['portada'] ?>" class="img-fluid rounded shadow-sm">
            </div>
        </div>

        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h1 class="display-4 fw-bold"><?= htmlspecialchars($comic['nombre']) ?></h1>
                    <p class="fs-5">Por <strong><?= htmlspecialchars($comic['nombre_autor']) ?></strong></p>
                </div>
                <span class="badge bg-primary fs-6"><?= htmlspecialchars($comic['nombre_genero']) ?></span>
            </div>

            <hr>

            <div class="my-4">
                <h4 class="fw-bold">Sinopsis</h4>
                <p class="text-light">
                    <?= nl2br(htmlspecialchars($comic['descripcion'])) ?></p>
            </div>

            <div class="d-flex gap-3 mt-5">
                <a href="<?= $comic['archivo_comic'] ?>" target="_blank" class="btn btn-light btn-lg px-5 fw-bold">
                    <i class="bi bi-book-half me-2"></i> LEER AHORA
                </a>
                <form action="detalleObra.php?id=<?= $id_comic ?>" method="POST" class="d-inline">
                    <button type="submit" name="toggle_like" class="btn <?= $ya_dio_like ? 'btn-danger' : 'btn-outline-danger' ?> btn-lg">
                        <i class="bi <?= $ya_dio_like ? 'bi-heart-fill' : 'bi-heart' ?>"></i> <?= $total_likes ?>
                    </button>
                </form>
            </div>

            <div class="mt-4 text-muted">
                <p class="text-light">Publicado el: <?= date("d/m/Y", strtotime($comic['f_publicacion'])) ?></p>
            </div>
        </div>
    </div>

    <section class="comentarios-section mt-5 border-secondary pt-4">
        <h3 class="mb-4 fw-bold">Comentarios (<?= count($lista_comentarios) ?>)</h3>
        <?php 
            $usuario_logueado = $_SESSION["id"] ?? $_SESSION["id_usuario"] ?? 0;
            if($usuario_logueado != 0): 
            ?>
                <form action="detalleObra.php?id=<?= $id_comic ?>" method="POST" class="mb-5">
                    <div class="mb-3">
                        <textarea class="form-control bg-dark text-white border-secondary" name="texto" rows="3" placeholder="¿Qué te ha parecido esta historia?" required></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" name="comentar" class="btn btn-primary fw-bold px-4">Comentar</button>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert bg-dark border-secondary text-center mb-5">
                    Debes iniciar sesión para dejar un comentario.
                </div>
            <?php endif; ?>

            <div class="lista-comentarios">
                <?php foreach($lista_comentarios as $comentario):
                    $foto_usuario = !empty($comentario['foto_perfil']) ? $comentario['foto_perfil'] : '..recursos/personas/default.jpg';
                    $fecha_formateada = date("d/m/Y H:i", strtotime($comentario['f_publicacion']));
                ?>
                    <div class="d-flex mb-4">
                        <img src="<?= $foto_usuario ?>" class="rounded-circle me-3 border border-secondaryshadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                            
                        <div class="bg-black border border-secondary p-3 rounded shadow-sm w-100">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0">
                                    <a href="perfilCreador.php?id=<?= $comentario['id_usuario'] ?>"class="text-decoration-none text-info">
                                        <?= htmlspecialchars($comentario['nombre']) ?>
                                    </a>
                                </h6>
                                <p class="text-muted"><?= $fecha_formateada ?></p>
                            </div>
                            <p class="mb-0 text-light"><?= htmlspecialchars($comentario['texto']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
</main>

<?php include_once("../modulos/footer.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>