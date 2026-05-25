<?php
/*Script para registrar un usuario en la base de datos */

	$servidor = "localhost";
	$database = "proyecto_comics";
	$usuario = "root";
	$contraseña = "";
	session_start();
	try{
		if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['enviar'])){
        $id = $_POST["id_usuario"] ?? 0;
		
		$dsn = "mysql:host=$servidor;dbname=$database";
		$conexion = new PDO($dsn, $usuario, $contraseña);
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


        <header class="cabecera d-flex flex-column justify-content-center align-items-center">
            <section class="titulo">
                <a class="textoTitulo textoNav navbar-brand" href="../index.php">ComicZone</a>  
                        
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
                                            <li><a class=\"dropdown-item\" href=\"./miPerfil.php\">Mi perfil</a></li>
                                            <li><a class=\"dropdown-item\" href=\"./modificar.php\">Modificar cuenta</a></li>
                                            <li><a class=\"dropdown-item\" href=\"./logout.php\">Cerrar sesión</a></li>
                                            <li><a class=\"dropdown-item\" href=\"./borrarPerfil.php\">Borrar cuenta</a></li>
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
                    <h5 class="modal-title" id="exampleModalLabel">Inicia sesión</h5>

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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>
    </section>