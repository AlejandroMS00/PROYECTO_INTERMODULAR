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
<?php
    $servidor = "localhost";
    $database = "proyecto_comics";
    $usuario = "root";
    $contrasenya = "";
    
    try {
        $id_usuario = $_SESSION["id"] ?? $_SESSION["id_usuario"] ?? 0;
        
        if ($id_usuario === 0) {
            header("Location: ../index.php");
            exit();
        }
        
		$dsn = "mysql:host=$servidor;dbname=$database";
		$conexion = new PDO($dsn, $usuario, $contrasenya);
		$conexion -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sqlDatos = "SELECT nombre, descripcion, foto_perfil FROM usuario WHERE id_usuario = :id";
        $sentenciaDatos = $conexion->prepare($sqlDatos);
        $sentenciaDatos->execute([':id' => $id_usuario]);
        $miUsuario = $sentenciaDatos->fetch(PDO::FETCH_ASSOC);

        $mi_foto = !empty($miUsuario['foto_perfil']) ? $miUsuario['foto_perfil'] : '../recursos/personas/default.png';
        
        $sqlComics = "SELECT c.*, g.nombre AS nombre_genero 
            FROM comic c 
            JOIN tiene t ON c.id_comic = t.id_comic 
            JOIN genero g ON t.id_genero = g.id_genero 
            WHERE c.id_usuario = :id_usuario 
            ORDER BY c.f_publicacion DESC";
            
        $sentenciaComics = $conexion->prepare($sqlComics);
        $sentenciaComics->execute([':id_usuario' => $id_usuario]);
        $mis_comics = $sentenciaComics->fetchAll();
        
    } catch(PDOException $e) {
        echo "Falló la base de datos: " . $e->getMessage();
    }
?>

<main class="mainPerfil container my-5">
    <div class="row">
        
        <div class="col-md-4 mb-4">
            <div class="card text-center shadow-sm border-0">
                <div class="cardCreador_sup"></div>
                
                <div class="card-body mt-n4">
                    <img src="<?= $mi_foto ?>" class="fotoCardCreador rounded-circle border border-white border-4 shadow-sm">
                    
                    <h3 class="card-title mt-3 fw-bold"><?= htmlspecialchars($_SESSION['usuario']) ?></h3>
                    <p class="textoPerfil2 card-text px-2"><?= $miUsuario['descripcion'] ?></p>
                    
                    <div class="d-grid gap-2 mt-4">
                        <a href="modificar.php" class="btn btn-outline-dark fw-bold">Editar Perfil</a>
                        
                        <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalPublicar">
                            Publicar Nuevo Cómic
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <h2 class="obrasTexto mb-4 border-bottom pb-2">Mis Obras Publicadas</h2>
            
            <div class="row">
        <?php foreach ($mis_comics as $comic): ?>
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
                        <a href="editarObra.php?id=<?= $comic['id_comic'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                    </div>
                    <form action="eliminarObra.php" method="POST" class="d-inline">
                        <input type="hidden" name="id_comic" value="<?= $comic['id_comic'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Borrar obra">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
                </div>
            </div>
        <?php endforeach; ?>

    <div class="col-md-6 mb-4">
        <div class="subirComic card h-100 shadow-sm d-flex justify-content-center align-items-center text-muted" data-bs-toggle="modal" data-bs-target="#modalPublicar">
            <div class="text-center">
                <i class="bi bi-plus-circle display-4"></i>
                <p class="mt-2">Subir nuevo cómic</p>
            </div>
        </div>
    </div>
</div>

    </div>
    <div class="modal fade" id="modalPublicar" tabindex="-1" aria-labelledby="modalPublicarLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered"> <div class="modal-content border-0 shadow-lg">
            
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold" id="modalPublicarLabel">Publicar Nuevo Cómic</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            
            <div class="modal-body p-4">
                <form action="procesarComic.php" method="POST" enctype="multipart/form-data">
                
                <div class="row">
                    <div class="col-md-7 mb-3">
                        <label for="tituloComic" class="form-label fw-bold">Título de la Obra</label>
                        <input type="text" class="form-control" name="tituloComic" id="tituloComic" placeholder="Ej: Las crónicas de..." required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="portadaComic" class="form-label fw-bold">Portada (JPG/PNG)</label>
                        <input class="form-control" type="file" id="portadaComic" name="portadaComic" accept="image/png, image/jpeg" required>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label for="portadaComic" class="form-label fw-bold">Archivo (PDF)</label>
                        <input class="form-control" type="file" name="archivoComic" id="archivoComic" accept="application/pdf" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="descComic" class="form-label fw-bold">Sinopsis / Descripción</label>
                    <textarea class="form-control" name="descComic" id="descComic" rows="3" placeholder="¿De qué trata tu historia? Engancha a tus lectores..." required></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                    <label for="generoComic" class="form-label fw-bold">Género Principal</label>
                    <select class="form-select" name="generoComic" id="generoComic" required>
                        <option value="" selected disabled>Selecciona un género...</option>
                        <option value="1">Superhéroes</option>
                        <option value="2">Romance</option>
                        <option value="3">Ciencia ficción</option>
                        <option value="4">Terror / Misterior</option>
                        <option value="5">Acción / Aventura</option>
                        <option value="6">Slice of Life (Costmbrista)</option>
                        <option value="7">Humor</option>
                        <option value="8">Otro</option>
                    </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                    <label for="precioComic" class="form-label fw-bold">Disponibilidad</label>
                    <select class="form-select" name="precioComic" id="precioComic" required>
                        <option value="gratis" selected>Gratis (Para todo el público)</option>
                        <option value="suscripcion">Exclusivo para suscriptores</option>
                    </select>
                    </div>
                </div>
                            
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Publicar Obra</button>
                </div>
                </form>
            </div>
            
            </div>
        </div>
    </div>
</div>
</main>
<footer>
    <?php include_once(".././modulos/footer.php"); ?>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>