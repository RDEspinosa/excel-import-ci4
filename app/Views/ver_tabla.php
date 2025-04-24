<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<?php
    $tabla_mapeada = [
        'Clientes' => 'clientes',
        'Productos' => 'producto',
        'Ventas' => 'ventas'
    ];
?>

<h4 class="center-align">📋 <?= esc($titulo) ?></h4>

<?php if (empty($datos)): ?>
    <div class="card-panel yellow lighten-4">
        <span>No hay registros disponibles.</span>
    </div>
<?php else: ?>
    <div class="table-container">
        <div class="input-field">
            <i class="material-icons prefix">search</i>
            <input type="text" id="buscador" placeholder="Buscar en la tabla...">
        </div>

        <table class="striped highlight responsive-table">
            <thead>
                <tr>
                    <?php foreach (array_keys($datos[0]) as $col): ?>
                        <th><?= esc(ucwords(str_replace('_', ' ', $col))) ?></th>
                    <?php endforeach; ?>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datos as $fila): ?>
                    <tr>
                        <?php foreach ($fila as $valor): ?>
                            <td><?= esc($valor) ?></td>
                        <?php endforeach; ?>
                        <td class="action-btns">
                            <a href="#" 
                                class="btn-small blue btn-editar" 
                                data-id="<?= $fila['id'] ?>" 
                                data-tabla="<?= $tabla_mapeada[$titulo] ?? '' ?>">
                                <i class="material-icons">edit</i>
                            </a>
                            
                            <a href="#" 
                                class="btn-small red btn-eliminar" 
                                data-id="<?= $fila['id'] ?>" 
                                data-tabla="<?= $tabla_mapeada[$titulo] ?? '' ?>">
                                <i class="material-icons">delete</i>
                            </a>


                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>