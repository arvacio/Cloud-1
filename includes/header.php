<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Telefónica</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header>
    <h1><a href="index.php">📒 Agenda Telefónica</a></h1>
    <?php if (!empty($_SESSION['usuario'])): ?>
        <div class="sesion">
            <?= e($_SESSION['nombre']) ?> | <a href="logout.php">Cerrar sesión</a>
        </div>
    <?php endif; ?>
</header>
<main>
