<?php

namespace App\Controllers;
use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Config\Database;
use CodeIgniter\Files\File;

class Excel extends BaseController
{
    public function index()
    {
        return view('upload_excel');
    }

    /* public function import()
    {
        $file = $this->request->getFile('excel_file');

        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'Error al subir el archivo.');
        }

        $spreadsheet = IOFactory::load($file->getTempName());

        // Ejemplo de cómo leer hojas:
        foreach ($spreadsheet->getSheetNames() as $sheetIndex => $sheetName) {
            $sheet = $spreadsheet->getSheet($sheetIndex);
            $data = $sheet->toArray();

            echo "<h2>Hoja: $sheetName</h2>";
            echo "<pre>";
            print_r($data);
            echo "</pre>";
        }

        return 'Procesamiento completo (demo)';
    } */
    public function import()
    {
        $file = $this->request->getFile('excel_file');
    
        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'Error al subir el archivo.');
        }
    
        $spreadsheet = IOFactory::load($file->getTempName());
        $db = Database::connect();
    
        // Mapas esperados por tabla
        $estructuraEsperada = [
            'clientes' => ['nombre', 'email', 'telefono', 'direccion'],
            'producto' => ['nombre', 'descripcion', 'precio', 'stock'],
            'ventas'   => ['producto_id', 'cliente_id', 'cantidad', 'fecha'],
        ];
    
        $resumen = [];
    
        foreach ($spreadsheet->getSheetNames() as $sheetIndex => $sheetName) {
            $nombreTabla = strtolower($sheetName);
            $resumen[$nombreTabla] = [
                'insertados' => 0,
                'errores' => [],
            ];
    
            if (!array_key_exists($nombreTabla, $estructuraEsperada)) {
                $resumen[$nombreTabla]['errores'][] = "Tabla '$nombreTabla' no reconocida.";
                continue;
            }
    
            $sheet = $spreadsheet->getSheet($sheetIndex);
            $data = $sheet->toArray();
    
            // Validar columnas
            $columnas = array_map('trim', $data[0]);
            if ($columnas !== $estructuraEsperada[$nombreTabla]) {
                $resumen[$nombreTabla]['errores'][] = "Columnas no coinciden. Esperado: " . implode(', ', $estructuraEsperada[$nombreTabla]);
                continue;
            }

            $idsValidos = [
                'clientes' => array_column($db->table('clientes')->select('id')->get()->getResultArray(), 'id'),
                'producto' => array_column($db->table('producto')->select('id')->get()->getResultArray(), 'id'),
            ];
            
    
            // Insertar registros
            /* for ($i = 1; $i < count($data); $i++) {
                $fila = $data[$i];
                $registro = array_combine($columnas, $fila);
    
                // Validar campos obligatorios (todos menos stock y descripcion que pueden ser nulos)
                foreach ($estructuraEsperada[$nombreTabla] as $campo) {
                    if (empty($registro[$campo]) && $campo !== 'descripcion' && $campo !== 'stock') {
                        $resumen[$nombreTabla]['errores'][] = "Fila {$i}: Campo '$campo' vacío.";
                        continue 2; // Salta esta fila
                    }
                }
    
                try {
                    $db->table($nombreTabla)->insert($registro);
                    $resumen[$nombreTabla]['insertados']++;
                } catch (\Exception $e) {
                    $resumen[$nombreTabla]['errores'][] = "Fila {$i}: " . $e->getMessage();
                }
            } */
           // Insertar registros
            for ($i = 1; $i < count($data); $i++) {
                $fila = $data[$i];
                $registro = array_combine($columnas, $fila);

                // Validar campos obligatorios
                foreach ($estructuraEsperada[$nombreTabla] as $campo) {
                    if (empty($registro[$campo]) && $campo !== 'descripcion' && $campo !== 'stock') {
                        $resumen[$nombreTabla]['errores'][] = "Fila {$i}: Campo '$campo' vacío.";
                        continue 2; // Salta esta fila
                    }
                }

                // Validaciones específicas por tabla
                $erroresFila = [];

                if ($nombreTabla === 'clientes') {
                    if (!filter_var($registro['email'], FILTER_VALIDATE_EMAIL)) {
                        $erroresFila[] = "Email inválido.";
                    }
                }

                if ($nombreTabla === 'producto') {
                    if (!is_numeric($registro['precio'])) {
                        $erroresFila[] = "Precio debe ser numérico.";
                    }
                    if (!is_numeric($registro['stock'])) {
                        $registro['stock'] = 0; // Por si viene vacío o mal, poner stock en 0
                    }
                }

                if ($nombreTabla === 'ventas') {           
                    if (!is_numeric($registro['producto_id']) || !is_numeric($registro['cliente_id'])) {
                        $erroresFila[] = "producto_id y cliente_id deben ser numéricos.";
                    }
                    if (!in_array($registro['producto_id'], $idsValidos['producto'])) {
                        $erroresFila[] = "Producto ID no existe: {$registro['producto_id']}";
                    }
                    
                    if (!in_array($registro['cliente_id'], $idsValidos['clientes'])) {
                        $erroresFila[] = "Cliente ID no existe: {$registro['cliente_id']}";
                    }
                    if (!is_numeric($registro['cantidad']) || $registro['cantidad'] <= 0) {
                        $erroresFila[] = "Cantidad inválida.";
                    }
                    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $registro['fecha'])) {
                        $erroresFila[] = "Fecha debe tener el formato YYYY-MM-DD.";
                    }
                }

                if (!empty($erroresFila)) {
                    $resumen[$nombreTabla]['errores'][] = "Fila {$i}: " . implode(', ', $erroresFila);
                    continue;
                }

                try {
                    $db->table($nombreTabla)->insert($registro);
                    $resumen[$nombreTabla]['insertados']++;
                } catch (\Exception $e) {
                    $resumen[$nombreTabla]['errores'][] = "Fila {$i}: " . $e->getMessage();
                }
            }

        }
    
        // Mostrar resumen
        /* echo "<h1>Resumen de Importación</h1>";
        foreach ($resumen as $tabla => $resultado) {
            echo "<h2>$tabla</h2>";
            echo "<p>Registros insertados: {$resultado['insertados']}</p>";
            if (count($resultado['errores'])) {
                echo "<p>Errores:</p><ul>";
                foreach ($resultado['errores'] as $error) {
                    echo "<li>$error</li>";
                }
                echo "</ul>";
            }
        } */
        //return view('import_result', ['resumen' => $resumen]);

        // Guardar errores en CSV
        $errorLines = [];
        foreach ($resumen as $tabla => $resultado) {
            foreach ($resultado['errores'] as $error) {
                $errorLines[] = [$tabla, $error];
            }
        }

        $errorFile = null;
        if (count($errorLines) > 0) {
            $timestamp = date('Ymd_His');
            $errorFileName = "error_log_{$timestamp}.csv";
            $errorFilePath = WRITEPATH . "errors/{$errorFileName}";

            $fp = fopen($errorFilePath, 'w');
            fputcsv($fp, ['Tabla', 'Error']);
            foreach ($errorLines as $line) {
                fputcsv($fp, $line);
            }
            fclose($fp);

            $errorFile = base_url("errors/{$errorFileName}");
        }
        
        // Guardar en la base de datos el historial de importación
        $tablasProcesadas = array_keys($resumen);
        $registrosInsertados = 0;
        $erroresTotales = 0;

        foreach ($resumen as $tabla => $datos) {
            $registrosInsertados += $datos['insertados'];
            $erroresTotales += count($datos['errores']);
        }

        $nombreArchivoOriginal = $file->getClientName(); // nombre del archivo subido

        $db->table('importaciones')->insert([
            'archivo_nombre'       => $nombreArchivoOriginal,
            'tablas_procesadas'    => implode(', ', $tablasProcesadas),
            'registros_insertados' => $registrosInsertados,
            'errores'              => $erroresTotales,
            'errores_csv'          => $errorFile ?? null,
        ]);


        /*return view('import_result', [
            'resumen' => $resumen,
            'errorFile' => $errorFile
        ]);*/
        // Mensajes de sesión
        if ($registrosInsertados > 0) {
            session()->setFlashdata('success', "Importación completada: $registrosInsertados registros insertados.");
        } else {
            session()->setFlashdata('error', "No se insertaron registros. Revisa el archivo de errores.");
        }

        if ($errorFile) {
            session()->setFlashdata('errorFile', $errorFile);
        }

        return redirect()->to('/excel');

    }

    /* public function dashboard()
    {
        $db = \Config\Database::connect();
        $importaciones = $db->table('importaciones')->orderBy('fecha_importacion', 'DESC')->get()->getResult();

        return view('dashboard', ['importaciones' => $importaciones]);
    } */
    /* public function dashboard()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('importaciones');
    
        // Filtros GET
        $fechaInicio = $this->request->getGet('fecha_inicio');
        $fechaFin = $this->request->getGet('fecha_fin');
        $archivo = $this->request->getGet('archivo');
        $tabla = $this->request->getGet('tabla');
    
        if ($fechaInicio) {
            $builder->where('fecha_importacion >=', $fechaInicio . ' 00:00:00');
        }
    
        if ($fechaFin) {
            $builder->where('fecha_importacion <=', $fechaFin . ' 23:59:59');
        }
    
        if ($archivo) {
            $builder->like('archivo_nombre', $archivo);
        }
    
        if ($tabla) {
            $builder->like('tablas_procesadas', $tabla);
        }
    
        $builder->orderBy('fecha_importacion', 'DESC');
        $importaciones = $builder->get()->getResult();
    
        return view('dashboard', [
            'importaciones' => $importaciones,
            'filtros' => [
                'fecha_inicio' => $fechaInicio,
                'fecha_fin'    => $fechaFin,
                'archivo'      => $archivo,
                'tabla'        => $tabla,
            ]
        ]);
    } */
    public function dashboard()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('importaciones');
    
        // Filtros GET
        $fechaInicio = $this->request->getGet('fecha_inicio');
        $fechaFin = $this->request->getGet('fecha_fin');
        $archivo = $this->request->getGet('archivo');
        $tabla = $this->request->getGet('tabla');
    
        if ($fechaInicio) {
            $builder->where('fecha_importacion >=', $fechaInicio . ' 00:00:00');
        }
    
        if ($fechaFin) {
            $builder->where('fecha_importacion <=', $fechaFin . ' 23:59:59');
        }
    
        if ($archivo) {
            $builder->like('archivo_nombre', $archivo);
        }
    
        if ($tabla) {
            $builder->like('tablas_procesadas', $tabla);
        }
    
        $builder->orderBy('fecha_importacion', 'DESC');
        $importaciones = $builder->get()->getResult();
    
        // Estadísticas
        $totalImportaciones = $builder->countAllResults();
        $totalInsertados = $builder->selectSum('registros_insertados')->get()->getRow()->registros_insertados ?? 0;
        $totalErrores = $builder->selectSum('errores')->get()->getRow()->errores ?? 0;
        $ultimoArchivo = $builder->orderBy('id', 'desc')->limit(1)->get()->getRow()->archivo_nombre ?? 'N/A';
    
        return view('dashboard', [
            'importaciones' => $importaciones,
            'filtros' => [
                'fecha_inicio' => $fechaInicio,
                'fecha_fin'    => $fechaFin,
                'archivo'      => $archivo,
                'tabla'        => $tabla,
            ],
            'totalImportaciones' => $totalImportaciones,
            'totalInsertados'    => $totalInsertados,
            'totalErrores'       => $totalErrores,
            'ultimoArchivo'      => $ultimoArchivo,
        ]);
    }
    
    
    public function verClientes()
    {
        $db = \Config\Database::connect();
        $data = $db->table('clientes')->get()->getResultArray();
        return view('ver_tabla', ['titulo' => 'Clientes', 'datos' => $data]);
    }
    
    public function verProductos()
    {
        $db = \Config\Database::connect();
        $data = $db->table('producto')->get()->getResultArray();
        return view('ver_tabla', ['titulo' => 'Productos', 'datos' => $data]);
    }
    
    public function verVentas()
    {
        $db = \Config\Database::connect();

        //$data = $db->table('ventas')->get()->getResultArray();
        // Consulta con JOIN para obtener nombres de productos y clientes
        $builder = $db->table('ventas');
        $builder->select('ventas.id, producto.nombre as producto_nombre, clientes.nombre as cliente_nombre, ventas.cantidad, ventas.fecha');
        $builder->join('producto', 'producto.id = ventas.producto_id');
        $builder->join('clientes', 'clientes.id = ventas.cliente_id');
        $data = $builder->get()->getResultArray();
        return view('ver_tabla', ['titulo' => 'Ventas', 'datos' => $data]);
    }

    public function eliminar($id = null)
    {
        if ($this->request->isAJAX()) {
            $json = $this->request->getJSON();
            $metodo = $json->_method ?? null;

            if (strtoupper($metodo) !== 'DELETE') {
                return $this->response->setJSON(['success' => false, 'message' => 'Método simulado no permitido']);
            }

            $tabla = $json->tabla ?? null;
            $id = (int) $id;

            if (!$tabla || !$id) {
                return $this->response->setJSON(['success' => false, 'message' => 'Datos inválidos']);
            }

            $permitidas = ['clientes', 'producto', 'ventas'];
            if (!in_array($tabla, $permitidas)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Tabla no permitida']);
            }

            $db = \Config\Database::connect();
            $deleted = $db->table($tabla)->delete(['id' => $id]);

            return $this->response->setJSON(['success' => $deleted]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Método no permitido']);
    }

    public function obtener($tabla, $id)
    {
        $permitidas = ['clientes', 'producto', 'ventas'];
        if (!in_array($tabla, $permitidas)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tabla no permitida']);
        }
    
        $db = \Config\Database::connect();
        $datos = $db->table($tabla)->where('id', $id)->get()->getRowArray();
    
        if ($datos) {
            return $this->response->setJSON(['success' => true, 'datos' => $datos]);
        }
    
        return $this->response->setJSON(['success' => false, 'message' => 'Registro no encontrado']);
    }
    
    public function actualizar($tabla, $id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Método no permitido']);
        }
    
        $permitidas = ['clientes', 'producto', 'ventas'];
        if (!in_array($tabla, $permitidas)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tabla no permitida']);
        }
    
        $datos = $this->request->getJSON(true); // true para array asociativo
    
        $db = \Config\Database::connect();
        $db->table($tabla)->where('id', $id)->update($datos);
    
        return $this->response->setJSON(['success' => true]);
    }
    
    
}