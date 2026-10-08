<?php
// Protege las páginas del CRUD: si no hay sesión iniciada, regresa al login
require_once __DIR__ . '/sesion.php';

if (empty($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}
