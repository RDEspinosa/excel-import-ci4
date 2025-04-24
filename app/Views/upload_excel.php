<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<h4 class="center-align">📁 Subir archivo Excel</h4>
<form action="<?= base_url('excel/import') ?>" method="post" enctype="multipart/form-data">
    <div class="file-field input-field">
        <div class="btn blue darken-2">
            <span>Seleccionar Excel</span>
            <input type="file" name="excel_file" accept=".xls,.xlsx" required>
        </div>
        <div class="file-path-wrapper">
            <input class="file-path validate" type="text" placeholder="Sube tu archivo Excel (.xls o .xlsx)">
        </div>
    </div>

    <div class="center-align" style="margin-top: 20px;">
        <button type="submit" class="btn waves-effect waves-light green">
            <i class="material-icons left">cloud_upload</i> Subir y Procesar
        </button>
    </div>
</form>

<div class="center-align" style="margin-top: 30px;">
    <a href="<?= base_url('dashboard') ?>" class="btn-flat blue-text text-darken-2">
        <i class="material-icons left">history</i> Ver historial de importaciones
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errorFile')): ?>
    <div class="alert alert-warning">
        <strong>Errores encontrados:</strong>
        <a href="<?= session()->getFlashdata('errorFile') ?>" target="_blank">Descargar log de errores</a>
    </div>
<?php endif; ?>


<?= $this->endSection() ?>