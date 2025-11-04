<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Administrador</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/login.css">
</head>
<body>
    <?php if (!empty($error)) : ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="index.php?controller=Admin&action=login">
            <h2>Acceso Administrador</h2>
        <label>Usuario:</label>
        <input type="text" name="usuario" required><br><br>

        <label>Contraseña:</label>
        <input type="password" name="clave" required><br><br>

        <button type="submit">Ingresar</button>
    </form>
</body>
</html>
