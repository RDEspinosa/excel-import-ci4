<!DOCTYPE html>
<html>
<head>
    <title>Resumen de Importación</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .tabla-resumen { margin-bottom: 30px; }
        .tabla-resumen h2 { color: #2c3e50; }
        .correcto { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Resumen de Importación</h1>

    <?php foreach ($resumen as $tabla => $resultado): ?>
        <div class="tabla-resumen">
            <h2><?= ucfirst($tabla) ?></h2>
            <p class="correcto">✅ Registros insertados: <?= $resultado['insertados'] ?></p>

            <?php if (count($resultado['errores']) > 0): ?>
                <p class="error">⚠️ Errores:</p>
                <ul>
                    <?php foreach ($resultado['errores'] as $error): ?>
                        <li class="error"><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <a href="<?= base_url('excel') ?>">⬅ Volver a subir otro archivo</a>

    <?php if (!empty($errorFile)): ?>
        <p><strong>Descargar errores:</strong> 
            <a href="<?= $errorFile ?>" target="_blank">📥 Ver archivo CSV</a>
        </p>
    <?php endif; ?>

    <p style="margin-top: 20px;">
        <a href="<?= base_url('dashboard') ?>">Ver historial de importaciones</a>
    </p>


</body>
</html>