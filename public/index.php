<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rueda de Proyectos</title>
    <link rel="stylesheet" type="text/css" href="./assets/css/index.css">
</head>
<body>

    <header class="site-header">
        <div class="container">
            <nav class="top-nav">
                <a href="#" class="brand">IX Rueda de Proyectos</a>
                <ul class="nav-links">
                    <li><a href="#inscripcion">Inscripción</a></li>
                    <li><a href="#proyectos">Proyectos</a></li>
                    <li><a href="#contacto">Contacto</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">

        <!-- INTRO + PÓSTER -->
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
                <a href="register.php" class="btn btn-outline">Más información</a>
            </div>
        </section>

        <section id="proyectos" class="projects">
            <h2>Proyectos destacados de ruedas de proyectos pasadas</h2>
            <div class="projects-grid">
                <article class="project-card">
                    <img src="assets/img/proyectos/proyecto1.jpg" alt="Miniatura proyecto 1">
                    <h3>Primer rueda de proyectos</h3>
                    <p>Breve descripción del proyecto 1.</p>
                </article>
                <article class="project-card">
                    <img src="assets/img/proyectos/proyecto2.jpg" alt="Miniatura proyecto 2">
                    <h3>Segunda rueda de proyectos</h3>
                    <p>Breve descripción del proyecto 2.</p>
                </article>
                <article class="project-card">
                    <img src="assets/img/proyectos/proyecto3.jpg" alt="Miniatura proyecto 3">
                    <h3>Tercer rueda de proyectos</h3>
                    <p>Breve descripción del proyecto 3.</p>
                </article>
                <article class="project-card">
                    <img src="assets/img/proyectos/proyecto4.jpg" alt="Miniatura proyecto 4">
                    <h3>Cuarta rueda de proyectos</h3>
                    <p>Breve descripción del proyecto 4.</p>
                </article>
                <article class="project-card">
                    <img src="assets/img/proyectos/proyecto5.jpg" alt="Miniatura proyecto 5">
                    <h3>Quinta rueda de proyectos</h3>
                    <p>Breve descripción del proyecto 5.</p>
                </article>
                <article class="project-card">
                    <img src="assets/img/proyectos/proyecto6.jpg" alt="Miniatura proyecto 6">
                    <h3>Sexta rueda de proyectos</h3>
                    <p>Breve descripción del proyecto 6.</p>
                </article>
            </div>
        </section>

                <div class="hero">
            <img src="assets/img/Unipaz-Avanza.png" alt="Rueda de proyectos - evento" class="hero-img">
            <div class="hero-content">
                <h1>Novena Rueda de Proyectos</h1>
                <p>Presenta tus ideas, mejora tu proyecto y conéctate con la comunidad.</p>
                <a href="datos_personales.php" class="btn btn-cta">Inscríbete ahora</a>
            </div>
        </div>

        <!-- CONTACTO -->
        <section id="contacto" class="contact">
            <h2>Contacto</h2>
            <p>Correo: contacto@unipaz.edu · Tel: 000-000-000</p>
        </section>

    </main>

    <!-- FOOTER -->
    <footer class="site-footer">
        <p>&copy; <?= date('Y') ?> Rueda de Proyectos. Todos los derechos reservados.</p>
    </footer>

</body>
</html>