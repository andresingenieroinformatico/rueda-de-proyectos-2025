<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rueda de Proyectos</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/home.css">
</head>
<body>
<?php include __DIR__ . '/layouts/header.php'; ?>

    <main class="container">

        <section id="inscripcion" class="intro">
            <div class="poster">
                <img src="assets/img/SISINFO3.png" alt="Póster del evento" class="poster-img">
            </div>
            <div class="intro-text">
                <h2>INSCRÍBETE A LA NOVENA RUEDA DE PROYECTOS</h2>
                <p>¿Estás listo para dar a conocer tus ideas, propuestas, avances y proyectos a la comunidad educativa de UNIPAZ?</p>
                <p>Inscríbete a la novena rueda de proyectos, con la que podrás ver de manera objetiva, mejoras, cambios y desarrollos importantes que podrías realizar a tu proyecto.</p>
                <p>Mejora tu proyecto e innova junto con muchos proyectos más y date a conocer a ti y a tus ideas con esta nueva edición de la rueda de proyectos.</p>
                <p>Todo esto gracias a nuestro semillero de investigación <strong>SISINFO</strong>.</p>
                <a href="<?= BASE_URL ?>?controller=home&action=seleccionar_semestre" class="btn">Más Información</a>
            </div>
        </section>

        <section id="proyectos" class="projects">
            <h2>Proyectos destacados de ruedas de proyectos pasadas</h2>
            <div class="projects-grid">
                <article class="project-card">
                    <img src="assets/img/proyectos/proyecto1.jpg" alt="Miniatura proyecto 1">
                    <h3>Sexta rueda de proyectos</h3>
                    <p>Breve descripción del proyecto 1.</p>
                </article>
                <article class="project-card">
                    <img src="assets/img/proyectos/proyecto2.jpg" alt="Miniatura proyecto 2">
                    <h3>Septima rueda de proyectos</h3>
                    <p>Breve descripción del proyecto 2.</p>
                </article>
                <article class="project-card">
                    <img src="assets/img/proyectos/proyecto3.jpg" alt="Miniatura proyecto 3">
                    <h3>Octava rueda de proyectos</h3>
                    <p>Breve descripción del proyecto 3.</p>
            </div>
        </section>

                <div class="hero">
            <div class="hero-content">
                <h1>Novena Rueda de Proyectos</h1>
                <p>Presenta tus ideas, mejora tu proyecto y conéctate con la comunidad.</p>
                <a href="<?= BASE_URL ?>?controller=home&action=seleccionar_semestre" class="btn">Inscribete Ahora</a>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/layouts/footer.php'; ?>


</body>
</html>