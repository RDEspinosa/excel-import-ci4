<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Database;

class TestDB extends Controller
{
    public function index()
    {
        try {
            $db = Database::connect();
            $query = $db->query('SHOW TABLES');
            $tables = $query->getResult();

            echo "✅ Conexión exitosa. Tablas encontradas:<br><ul>";
            foreach ($tables as $table) {
                foreach ($table as $name) {
                    echo "<li>$name</li>";
                }
            }
            echo "</ul>";
        } catch (\Exception $e) {
            echo "❌ Error al conectar a la base de datos: " . $e->getMessage();
        }
    }
}