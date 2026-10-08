<?php
require_once 'includes/sesion.php';

// Si ya inició sesión, pasa directo a la agenda
if (!empty($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

$usuario = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario  = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    // Buscar al usuario (consulta preparada)
    $stmt = conectar()->prepare('SELECT usuario, password, nombre FROM usuarios WHERE usuario = :usuario');
    $stmt->execute([':usuario' => $usuario]);
    $fila = $stmt->fetch();

    // Comparar la contraseña escrita con el hash guardado
    if ($fila && password_verify($password, $fila['password'])) {
        session_regenerate_id(true);          // nuevo id de sesión al entrar
        $_SESSION['usuario'] = $fila['usuario'];
        $_SESSION['nombre']  = $fila['nombre'];
        header('Location: index.php');
        exit;
    }

    $error = 'Usuario o contraseña incorrectos.';
}

require 'includes/header.php';
?>
<div class="login">
    <h2>Iniciar sesión</h2>

    <?php if ($error): ?>
        <div class="alerta error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" class="formulario">
        <label>Usuario
            <input type="text" name="usuario" maxlength="50" required autofocus value="<?= e($usuario) ?>">
        </label>
        <label>Contraseña
            <input type="password" name="password" required>
        </label>
        <div class="acciones">
            <button type="submit" class="btn">Entrar</button>
        </div>
    </form>
</div>
<?php require 'includes/footer.php'; ?>
