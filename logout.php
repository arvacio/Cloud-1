<?php
require_once 'includes/sesion.php';

// Vacía la sesión, la borra de la tabla "sesiones" y elimina la cookie
$_SESSION = [];
session_destroy();
setcookie(session_name(), '', time() - 3600, '/');

header('Location: login.php');
exit;
