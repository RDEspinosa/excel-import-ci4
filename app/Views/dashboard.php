<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<h4 class="center-align">📊 Historial de Importaciones</h4>

<!-- Filtros -->
<h6>📁 Filtros</h6>
<form method="get" class="row">
    <!-- Fecha desde -->
    <div class="input-field col s12 m3">
        <input id="fecha_inicio" type="date" name="fecha_inicio" value="<?= esc($filtros['fecha_inicio'] ?? '') ?>">
        <label for="fecha_inicio">Fecha desde:</label>
    </div>

    <!-- Fecha hasta -->
    <div class="input-field col s12 m3">
        <input id="fecha_fin" type="date" name="fecha_fin" value="<?= esc($filtros['fecha_fin'] ?? '') ?>">
        <label for="fecha_fin">Fecha hasta:</label>
    </div>

    <!-- Archivo -->
    <div class="input-field col s12 m3">
        <input id="archivo" type="text" name="archivo" value="<?= esc($filtros['archivo'] ?? '') ?>">
        <label for="archivo">Archivo:</label>
    </div>

    <!-- Tabla -->
    <div class="input-field col s12 m3">
        <input id="tabla" type="text" name="tabla" value="<?= esc($filtros['tabla'] ?? '') ?>">
        <label for="tabla">Tabla:</label>
    </div>

    <!-- Botones -->
    <div class="col s12">
        <button type="submit" class="btn waves-effect waves-light">🔍 Filtrar</button>
        <a href="<?= base_url('dashboard') ?>" class="btn grey lighten-1">🧹 Limpiar</a>
    </div>
</form>


<!-- Cards para estadísticas -->
<div class="row">
    <div class="col s12 m6 l3">
        <div class="card blue lighten-1 white-text">
            <div class="card-content">
                <span class="card-title">Importaciones</span>
                <h5><?= esc($totalImportaciones ?? 0) ?></h5>
            </div>
        </div>
    </div>
    <div class="col s12 m6 l3">
        <div class="card green lighten-1 white-text">
            <div class="card-content">
                <span class="card-title">Registros</span>
                <h5><?= esc($totalInsertados ?? 0) ?></h5>
            </div>
        </div>
    </div>
    <div class="col s12 m6 l3">
        <div class="card red lighten-1 white-text">
            <div class="card-content">
                <span class="card-title">Errores</span>
                <h5><?= esc($totalErrores ?? 0) ?></h5>
            </div>
        </div>
    </div>
    <div class="col s12 m6 l3">
        <div class="card orange lighten-1 white-text">
            <div class="card-content">
                <span class="card-title">Último Archivo</span>
                <h5><?= esc($ultimoArchivo ?? 'N/A') ?></h5>
            </div>
        </div>
    </div>
</div>

<!-- Historial de Importaciones (Tabla) -->
<h4 class="center-align">📄 Historial de Importaciones</h4>
<table class="striped">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Archivo</th>
            <th>Tablas Procesadas</th>
            <th>Registros Insertados</th>
            <th>Errores</th>
            <th>CSV de Errores</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($importaciones as $imp): ?>
            <tr>
                <td><?= date('Y-m-d H:i:s', strtotime($imp->fecha_importacion)) ?></td>
                <td><?= esc($imp->archivo_nombre) ?></td>
                <td><?= esc($imp->tablas_procesadas) ?></td>
                <td class="ok"><?= $imp->registros_insertados ?></td>
                <td class="<?= $imp->errores > 0 ? 'fail' : 'ok' ?>"><?= $imp->errores ?></td>
                <td>
                    <?php if (!empty($imp->errores_csv)): ?>
                        <a href="<?= $imp->errores_csv ?>" target="_blank">📥 Descargar</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $this->endSection() ?>