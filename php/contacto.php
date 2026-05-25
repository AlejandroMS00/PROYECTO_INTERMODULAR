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
    
        <section class="sectionContacto d-flex justify-content-center">
            <article class="cajaForm d-flex justify-content-center">
                <form class="formContacto">
                        <h3 class="negrita">Envíanos un mensaje</h3>
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" aria-describedby="nombreHelp">
                        </div>
                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="correo" aria-describedby="emailHelp">
                            <div id="emailHelp" class="form-text">No compartiremos tu correo con nadie.</div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password">
                        </div>
                        <div class="mb-3">
                            <label for="asunto" class="form-label">Asunto</label>
                            <input type="text" class="form-control" id="asunto" aria-describedby="asuntoHelp">
                        </div>
                        <div class="form-floating">
                            <textarea class="form-control" placeholder="Mensaje" id="mensaje" style="height: 200px"></textarea>
                            <label for="mensaje">Tu mensaje</label>
                        </div><br>
                        <button type="submit" class="botonEnviar btn btn-primary">Enviar</button>
                        </form>
            </article>
        </section>
    </main>
    <?php include_once(".././modulos/footer.php"); ?>

<script src="./js/landPage.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>