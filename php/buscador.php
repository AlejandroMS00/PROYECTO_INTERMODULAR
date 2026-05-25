<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$servidor = "localhost";
$database = "proyecto_comics";
$usuario = "root";
$contrasenya = "";

$busqueda = $_GET['q'] ?? '';

try {
    $dsn = "mysql:host=$servidor;dbname=$database;charset=utf8mb4";
    $conexion = new PDO($dsn, $usuario, $contrasenya);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sqlAutores = "SELECT id_usuario, nombre, foto_perfil, descripcion FROM usuario WHERE nombre LIKE :busqueda";
    $stmtAutores = $conexion->prepare($sqlAutores);
    $stmtAutores->execute([':busqueda' => "%$busqueda%"]);
    $resultados_autores = $stmtAutores->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Error en la búsqueda: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link href="../bootstrap-5.3.8-dist/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/landPage.css">
    <title>Document</title>
</head>
<body class="bg-dark text-white">
    <?php include_once("../modulos/header.php"); ?>
    
    <main class="container my-5">
        <h2 class="mb-4">Resultados para: "<?= htmlspecialchars($busqueda) ?>"</h2>
        
        <h4 class="text-info border-bottom pb-2">Creadores Encontrados</h4>
        <div class="row mb-5">
            <?php if(empty($resultados_autores)): ?>
                <p class="text-muted">No se encontraron creadores.</p>
            <?php else: foreach($resultados_autores as $autor): 
                $foto = !empty($autor['foto_perfil']) ? str_replace('../', '', $autor['foto_perfil']) : '../recursos/personas/default.png';
            ?>
                <div class="col-md-3 mb-3">
                    <div class="card bg-black border-secondary text-center p-3">
                        <img src="<?= $foto ?>" class="rounded-circle mx-auto mb-2 object-fit-cover" style="width:80px; height:80px;">
                        <h6 class="text-white"><?= htmlspecialchars($autor['nombre']) ?></h6>
                        <a href="perfilCreador.php?id=<?= $autor['id_usuario'] ?>" class="btn btn-sm btn-outline-info">Ver perfil</a>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>

        <h4 class="text-info border-bottom pb-2">Cómics Encontrados</h4>
        <div class="row">
            <?php if(empty($resultados_comics)): ?>
                <p class="text-muted">No se encontraron obras.</p>
            <?php else: foreach($resultados_comics as $comic): ?>
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <img src="<?= $comic['portada'] ?>" class="card-img-top object-fit-cover" style="height: 300px;">
                        <div class="card-body bg-black text-white border border-secondary border-top-0">
                            <h6 class="fw-bold"><?= htmlspecialchars($comic['nombre']) ?></h6>
                            <a href="detalleObra.php?id=<?= $comic['id_comic'] ?>" class="btn btn-sm btn-primary w-100 mt-2">Leer</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </main>
</body>
</html>