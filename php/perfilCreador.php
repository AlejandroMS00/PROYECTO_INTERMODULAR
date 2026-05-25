<?php
    $servidor = "localhost";
    $database = "proyecto_comics";
    $usuario = "root";
    $contrasenya = "";
    
    try {
        $id_visitado = $_GET["id"] ?? 0;
        
        $mi_id = $_SESSION["id"] ?? $_SESSION["id_usuario"] ?? 0;
        if ($id_visitado == $mi_id && $mi_id != 0) {
            header("Location: miPerfil.php");
        }

        if ($id_visitado == 0) {
            header("Location: ../index.php");
        }
        
		$dsn = "mysql:host=$servidor;dbname=$database";
		$conexion = new PDO($dsn, $usuario, $contrasenya);
		$conexion -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sqlDatos = "SELECT nombre, descripcion, foto_perfil FROM usuario WHERE id_usuario = :id";
        $sentenciaDatos = $conexion->prepare($sqlDatos);
        $sentenciaDatos->execute([':id' => $id_visitado]);
        $creador = $sentenciaDatos->fetch(PDO::FETCH_ASSOC);

        if (!$creador) {
            echo "<h2>El perfil que buscas no existe.</h2><a href='../index.php'>Volver al inicio</a>";
        }

        $foto_creador = !empty($creador['foto_perfil']) ? $creador['foto_perfil'] : '../recursos/personas/default.png';
        
        $sqlComics = "SELECT c.*, g.nombre AS nombre_genero 
            FROM comic c 
            JOIN tiene t ON c.id_comic = t.id_comic 
            JOIN genero g ON t.id_genero = g.id_genero 
            WHERE c.id_usuario = :id 
            ORDER BY c.f_publicacion DESC";
            
        $sentenciaComics = $conexion->prepare($sqlComics);
        $sentenciaComics->execute([':id' => $id_visitado]);
        $comics_creador = $sentenciaComics->fetchAll(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        echo "Falló la base de datos: " . $e->getMessage();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href=".././bootstrap-5.3.8-dist/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href=".././css/landPage.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <title>Document</title>
</head>
<body>
    <?php include_once(".././modulos/header.php"); ?>
    <main class="mainPerfil container my-5">
    <div class="row">
        
        <div class="col-md-4 mb-4">
            <div class="card text-center shadow-sm border-0">
                <div class="cardCreador_sup"></div>
                
                <div class="card-body mt-n4">
                    <img src="<?= $foto_creador ?>" class="fotoCardCreador rounded-circle border border-white border-4 shadow-sm">
                    
                    <h3 class="card-title mt-3 fw-bold"><?= htmlspecialchars($creador['nombre']) ?></h3>
                    <p class="textoPerfil2 card-text px-2"><?= $creador['descripcion'] ?></p>
                    
                    <div class="d-grid gap-2 mt-4">
                        <button type="button" id="btnSeguir" class="btn btn-primary fw-bold w-100 mt-3" onclick="seguir()">
                            <i class="bi bi-person-plus-fill me-1"></i> Seguir
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <h2 class="obrasTexto mb-4 border-bottom pb-2">Obras Publicadas</h2>
            
            <div class="row">
        <?php foreach ($comics_creador as $comic): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <img src="<?= $comic['portada'] ?>" class="portadaComic card-img-top">
                    
                    <div class="card-body">
                        <span class="badge bg-secondary mb-2"><?= htmlspecialchars($comic['nombre_genero']) ?></span>
                        
                        <h5 class="card-title fw-bold"><?= htmlspecialchars($comic['nombre']) ?></h5>
                        <p class="bioCard card-text text-muted"><?= htmlspecialchars($comic['descripcion']) ?></p>
                    </div>
                    
                    <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center pb-3">
                    <div class="btn-group">
                        <a href="detalleObra.php?id=<?= $comic['id_comic'] ?>" class="btn btn-sm btn-primary fw-bold">
                            Ver detalles
                        </a>
                    </div>
                </div>
                </div>
            </div>
        <?php endforeach; ?>

    </div>
</div>
</main>
<footer>
    <?php include_once(".././modulos/footer.php"); ?>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function seguir() {
    const boton = document.getElementById('btnSeguir');
    
    if (boton.classList.contains('btn-primary')) {
        boton.classList.remove('btn-primary');
        boton.classList.add('btn-secondary');
        boton.innerHTML = '<i class="bi bi-person-check-fill me-1"></i> Siguiendo';
    } else {
        boton.classList.remove('btn-secondary');
        boton.classList.add('btn-primary');
        boton.innerHTML = '<i class="bi bi-person-plus-fill me-1"></i> Seguir';
    }
}
</script>
</body>
</html>
</body>