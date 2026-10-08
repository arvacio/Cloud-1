<?php
// Sesiones guardadas en MySQL (tabla "sesiones") para que las 3 VM las compartan
require_once __DIR__ . '/conexion.php';

class SesionMySQL implements SessionHandlerInterface
{
    private PDO $pdo;
    private int $duracion;

    public function __construct(PDO $pdo, int $duracion)
    {
        $this->pdo      = $pdo;
        $this->duracion = $duracion;
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    // Lee los datos de la sesión (solo si no ha caducado)
    public function read(string $id): string|false
    {
        $stmt = $this->pdo->prepare(
            'SELECT datos FROM sesiones WHERE id = :id AND ultimo_acceso > :limite'
        );
        $stmt->execute([':id' => $id, ':limite' => time() - $this->duracion]);
        $datos = $stmt->fetchColumn();

        return $datos === false ? '' : $datos;
    }

    // Guarda o actualiza los datos de la sesión
    public function write(string $id, string $data): bool
    {
        // Visitante sin login (sesión vacía): no se guarda nada
        if ($data === '') {
            return $this->destroy($id);
        }

        $stmt = $this->pdo->prepare(
            'REPLACE INTO sesiones (id, datos, ultimo_acceso) VALUES (:id, :datos, :ahora)'
        );

        return $stmt->execute([':id' => $id, ':datos' => $data, ':ahora' => time()]);
    }

    // Borra la sesión (al cerrar sesión)
    public function destroy(string $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM sesiones WHERE id = :id');

        return $stmt->execute([':id' => $id]);
    }

    // Limpia las sesiones caducadas
    public function gc(int $max_lifetime): int|false
    {
        $stmt = $this->pdo->prepare('DELETE FROM sesiones WHERE ultimo_acceso < :limite');
        $stmt->execute([':limite' => time() - $max_lifetime]);

        return $stmt->rowCount();
    }
}

$duracion = 3600; // la sesión caduca tras 1 hora sin actividad

ini_set('session.gc_maxlifetime', (string) $duracion);
ini_set('session.gc_probability', '1');   // en Ubuntu viene en 0
ini_set('session.gc_divisor', '100');     // limpia en ~1 de cada 100 peticiones
ini_set('session.cookie_httponly', '1');  // JavaScript no puede leer la cookie

session_set_save_handler(new SesionMySQL(conectar(), $duracion), true);
session_start();
