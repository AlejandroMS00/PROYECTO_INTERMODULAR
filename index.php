<?php
	$servidor = "localhost";
	$database = "proyecto_comics";
	$usuario = "root";
	$contrasenya = "";
	if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
	try{
		if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['enviar'] == "Enviar"){
        $id = $_POST["id_usuario"] ?? 0;
		
		$dsn = "mysql:host=$servidor;dbname=$database";
		$conexion = new PDO($dsn, $usuario, $contrasenya);
		$conexion -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $nombre = filter_var($_POST['nombre'], FILTER_SANITIZE_STRING);
        $correo= filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL);

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            echo "El correo electrónico no es válido.";
        }

			$nombreIn = $_POST["nombre"];
			$correoIn = $_POST["correo"];
			$contrasenaIn = password_hash($_POST["contrasena"], PASSWORD_DEFAULT);
			
			$sql = "INSERT INTO usuario (nombre, correo, contrasena) values (:nombre, :correo, :contrasena)";
	
			$sentencia = $conexion->prepare($sql);

			$sentencia-> bindParam(":nombre", $nombreIn);
			$sentencia-> bindParam(":correo", $correoIn);
			$sentencia-> bindParam(":contrasena", $contrasenaIn);

			$isOk = $sentencia -> execute();
			$idGenerado = $conexion -> lastInsertId();
			
		}
		
		
	}catch(PDOException $e){
		echo "Falló la conexión ".$e->getMessage();
	}
	?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="./bootstrap-5.3.8-dist/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="./css/landPage.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <title>Document</title>
</head>
    <body>
    <header class="cabecera d-flex flex-column justify-content-center align-items-center">
            <section class="titulo">
                <a class="textoTitulo textoNav navbar-brand" href="./index.php">ComicZone</a>  
                        
            </section>

            <nav class="cabecera1 navbar navbar-expand-lg">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse d-flex flex-row justify-content-center align-items-center" id="navbarSupportedContent">
                        <ul class="linksNav navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <div class="dropdown">
                                <button class="lupita btn dropdown" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                    </svg>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                    <li>  
                                        <div class="container-fluid">
                                            <form class="d-flex" role="search" action="php/buscador.php" method="GET">
                                                <input class="buscador form-control me-2" type="search" name="q" placeholder="Buscar cómics o autores..." required/>
                                                <button class="btn btn-primary" type="submit">Buscar</button>
                                            </form>
                                        </div>
                                    </li>
                                </ul>
                        </div>
                            </li>
                            <li class="nav-item">
                                <?php
                            if(isset($_SESSION['id'])){
                                echo "<li class=\"nav-item dropdown\">
                                        <a class=\"textoNav nav-link dropdown\" href=\"#\" role=\"button\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                                            Cuenta
                                        </a>
                                        <ul class=\"dropdown-menu\">
                                            <li><a class=\"dropdown-item\" href=\"./php/miPerfil.php\">Mi perfil</a></li>
                                            <li><a class=\"dropdown-item\" href=\"./php/modificar.php\">Modificar perfil</a></li>
                                            <li><a class=\"dropdown-item\" href=\"./php/logout.php\">Cerrar sesión</a></li>
                                            <li><a class=\"dropdown-item\" href=\"./php/borrarPerfil.php\">Borrar cuenta</a></li>
                                        </ul>
                                    </li>";
                            }else {
                                echo "<button type=\"button\" class=\"btn botonSesion mx-2\" data-bs-toggle=\"modal\" data-bs-target=\"#modalSesion\">Sesión</button>";
                            }
                        ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
    <main>
        <section class="modalSesion">
            <div class="modal fade" id="modalSesion" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Inicia sesión</h5>

                </div>
                <div class="modal-body">
                    <form action="./php/login.php" method='POST'>
                        <div class="mb-3">
                            <label for="correoLogin" class="form-label">Correo electronico</label>
                            <input type="email" class="form-control" name="correoLogin" id="correoLogin" aria-describedby="emailHelp">
                        </div>
                        <div class="mb-3">
                            <label for="passLogin" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" name="passLogin" id="passLogin">
                        </div>
                        <input type="submit" name="login" value="Iniciar sesión">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalRegistro">Registrarse</button>
                </div>
                </div>
            </div>
        </div>
    </section>

        <section class="modalRegistro">
            <div class="modal fade" id="modalRegistro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Registrarse</h5>

                </div>
                <div class="modal-body">
                    <form action = <?php echo $_SERVER["PHP_SELF"]?> method='POST'>
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre y apellidos</label>
                            <input type="text" class="form-control" name="nombre" id="nombre" aria-describedby="emailHelp" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" name="correo" id="correo" aria-describedby="emailHelp">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" name="contrasena" id="contrasena" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Repita la contraseña</label>
                            <input type="password" class="form-control" id="password" required>
                        </div>
                        <input type="submit" name ="enviar" value="Enviar" class="btn btn-primary">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
                </div>
            </div>
        </div>
    </section>
    <section class="introduccion d-flex">
        <div class="filtro"></div>
        <article class="textoIntro">
        <h1 class="tituloLand">Da vida a tus historias. Descubre nuevos mundos.</h1>
        <p class="textoLand">Bienvenido a ComicZone, la plataforma donde lectores y creadores conectan. Aquí podrás publicar tus propios cómics, descubrir nuevas historias y apoyar a tus autores favoritos. Crea tu perfil, organiza tus lecturas en curso y únete a una comunidad apasionada por la narrativa gráfica.</p>
        <p class="textoLand">¿Tienes una historia que contar o buscas una nueva aventura en la que sumergirte? En ComicZone rompemos la barrera entre el autor y el lector. Comparte tu talento con el mundo, recibe feedback directo a través de comentarios y chats, y monetiza tu creatividad. Tu próximo cómic favorito te está esperando.</p>
        </article>
        <img class="fotoLand" src="recursos/fotos/telefono.jpg" alt="">
    </section>

    <section class="creadores carruselCards d-flex flex-column text-justified align-items-center justify-content-center">
    <h2 class="textoCreadores">Nuevos Talentos en ComicZone:</h2><br>
    
    <?php
    try {
		$dsn = "mysql:host=$servidor;dbname=$database";
		$conexion = new PDO($dsn, $usuario, $contrasenya);
		$conexion -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sqlCreadores = "SELECT id_usuario, nombre, descripcion, foto_perfil 
                        FROM usuario 
                        ORDER BY id_usuario DESC 
                        LIMIT 5";
        
        $sentenciaCreadores = $conexion->query($sqlCreadores);
        $creadores_destacados = $sentenciaCreadores->fetchAll(PDO::FETCH_ASSOC);

    } catch(PDOException $e) {
        echo "<p class='text-danger'>No se pudieron cargar los creadores en este momento.</p>";
        $creadores_destacados = []; 
    }
    ?>

    <div id="carruselCreadores" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
                
            <?php 
            $esElPrimero = true; 
            foreach($creadores_destacados as $creador): 
                    
                $ruta_foto = !empty($creador['foto_perfil']) ? str_replace('../', '', $creador['foto_perfil']) : 'recursos/personas/default.png';
                $descripcion = !empty($creador['descripcion']) ? htmlspecialchars($creador['descripcion']) :'¡Acabo de unirme a ComicZone! Pronto empezaré a subir mis historias.';
                    
                $claseActive = $esElPrimero ? "active" : "";
                $esElPrimero = false; 
            ?>

            <div class="carousel-item <?= $claseActive ?>" data-bs-interval="4000">
                <article class="cardCreador card mb-3" style="max-width: 540px; margin: 0 auto;">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="<?= $ruta_foto ?>" class="img-fluid rounded-start h-10object-fit-cover" alt="Foto de <?= htmlspecialchars($creador['nombre']) ?>">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="card-title fw-bold"><?= htmlspecialchars($creador['nombre'])?></h5>
                                <p class="card-text"><?= substr($descripcion, 0, 150) ?><?= strlen($descripcion) > 150 ? '...' : '' ?></p>
                                        
                                <a class="btn btn-primary mt-2" href="php/perfilCreador.php?id=<?= $creador['id_usuario'] ?>" role="button">Ver perfil</a>
                            </div>
                        </div>
                    </div>
                </article>
            </div>

        <?php endforeach; ?>

        </div>

        <?php if (!empty($creadores_destacados) && count($creadores_destacados) > 1): ?>
            <button class="carousel-control-prev" type="button" data-bs-target="#carruselCreadores" data-bs-slide="prev">
                <span class="carousel-control-prev-icon rounded-circle bg-dark p-2" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carruselCreadores" data-bs-slide="next">
                <span class="carousel-control-next-icon rounded-circle bg-dark p-2" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        <?php endif; ?>
    </div>
</section>
<section class="registro d-flex justify-content-center align-items-center">
    <article class="ventanaRegistro d-flex flex-column justify-content-center align-items-center">
        <h3>Únete ya y empieza a crear</h3><br>
        <button class="btn btn-outline-dark btn-lg rounded-pill" data-bs-toggle="modal" data-bs-target="#modalRegistro">Comenzar</button>
        <p>¿Ya tienes una cuenta? <a class="linkSesion btn botonSesion mx-2" data-bs-toggle="modal" data-bs-target="#modalSesion">Iniciar sesión</a></p>

    </article>
</section>

</main>
<footer class="footer container-fluid p-3">
    <div class="container text-center">
        <div class="row">
            <div class="contacto col-sm">
            <a href="./php/sobreNosotros.php" class="text-reset text-decoration-none">Sobre nosotros</a> | 
            <a href="./php/contacto.php" class="text-reset text-decoration-none">Contacto</a>
        </div>
            <div class="logos col-sm">                
                        <i class="bi bi-instagram"></i>
                        <i class="bi bi-twitter-x"></i>
                        <i class="bi bi-facebook"></i></div>
            <div class="textoFooter col-sm"><p>&copy;2026 | ComicZone Todos los derechos reservados</p></div>
        </div>
    </div>
</footer>

<script src="./js/landPage.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


