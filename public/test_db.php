<?php
require_once __DIR__ . '/../app/config/db.php';

try {
    $pdo = DB::conn();
    echo "<h2 style='color:green'> Conexión exitosa a la base de datos 'finanzas'.</h2>";
    
    // Verificar que las tablas existen 
    $stmt = $pdo->query("SHOW TABLES");
    $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if ($tablas) {
        echo "<p>Tablas encontradas:</p><ul>";
        foreach ($tablas as $t) {
            echo "<li>" . htmlspecialchars($t) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color:orange'>La base de datos está vacía (no hay tablas todavía).</p>";
    }

} catch (Exception $e) {
    echo "<h2 style='color:red'> Error: " . $e->getMessage() . "</h2>";
}
