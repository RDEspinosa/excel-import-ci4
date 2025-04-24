<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title><?= lang('Errors.pageNotFound') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Materialize CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        body {
            background: #f44336;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: white;
            text-align: center;
            flex-direction: column;
            padding: 20px;
        }

        .icon-404 {
            font-size: 6rem;
        }

        .search-box {
            max-width: 500px;
            margin: 30px auto;
        }

        .btn-flat.white-text:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body>

    <div>
        <i class="material-icons icon-404">error_outline</i>
        <h3>404 - Página no encontrada</h3>
        <p>Lo sentimos, la ruta que intentaste visitar no existe.</p>

        <div class="search-box">
            <form method="get" action="<?= base_url('dashboard') ?>">
                <div class="input-field white-text">
                    <input id="buscar" type="text" name="busqueda" class="white-text" placeholder="Buscar en el sistema...">
                    <label for="buscar" class="active white-text">🔎 Búsqueda rápida</label>
                </div>
            </form>
        </div>

        <div>
            <a href="<?= base_url('/') ?>" class="btn white red-text text-darken-4">🏠 Inicio</a>
            <a href="<?= base_url('/dashboard') ?>" class="btn-flat white-text">📊 Ir al Dashboard</a>
        </div>
    </div>

    <div class="wrap">
        <p>
            <?php if (ENVIRONMENT !== 'production') : ?>
                <?= nl2br(esc($message)) ?>
            <?php else : ?>
                <?= lang('Errors.sorryCannotFind') ?>
            <?php endif; ?>
        </p>
    </div>

    <!-- Materialize JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
