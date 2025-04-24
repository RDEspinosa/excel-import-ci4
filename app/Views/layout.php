<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Mi App') ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Materialize CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f7fc;
            padding: 30px;
        }

        .table-container {
            overflow-x: auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .action-btns a {
            margin: 0 5px;
        }

        .btn-small i {
            vertical-align: middle;
        }

        .back-link {
            margin-top: 20px;
            display: inline-block;
        }

        h1 {
            text-align: center;
            margin-bottom: 40px;
        }

        @media screen and (max-width: 768px) {
            table {
                font-size: 14px;
            }
        }
    </style>
</head>
<body class="container">

    <nav class="blue darken-3">
    <div class="nav-wrapper container">
        <a href="<?= base_url('/') ?>" class="brand-logo">Mi App</a>

        <!-- Botón de menú para mobile -->
        <a href="#" data-target="mobile-nav" class="sidenav-trigger"><i class="material-icons">menu</i></a>

        <!-- Menú desktop -->
        <ul id="nav-mobile" class="right hide-on-med-and-down">
        <li><a href="<?= base_url('excel') ?>">Importar Excel</a></li>
        <li><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
        <li><a href="<?= base_url('ver/clientes') ?>">Clientes</a></li>
        <li><a href="<?= base_url('ver/productos') ?>">Productos</a></li>
        <li><a href="<?= base_url('ver/ventas') ?>">Ventas</a></li>
        </ul>
    </div>
    </nav>

    <!-- Menú lateral para móviles -->
    <ul class="sidenav" id="mobile-nav">
    <li><a href="<?= base_url('excel') ?>">Importar Excel</a></li>
    <li><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
    <li><a href="<?= base_url('ver/clientes') ?>">Clientes</a></li>
    <li><a href="<?= base_url('ver/productos') ?>">Productos</a></li>
    <li><a href="<?= base_url('ver/ventas') ?>">Ventas</a></li>
    </ul>


    <?= $this->renderSection('content') ?>

    <div id="modalEditar" class="modal">
        <div class="modal-content">
            <h4>✏️ Editar Registro</h4>
            <form id="formEditar">
                <div id="camposEditar"></div>
                <input type="hidden" id="edit-id" name="id">
                <input type="hidden" id="edit-tabla" name="tabla">
            </form>
        </div>
        <div class="modal-footer">
            <button class="modal-close btn grey">Cancelar</button>
            <button type="submit" form="formEditar" class="btn blue">Guardar Cambios</button>
        </div>
    </div>


    <?php if (session()->getFlashdata('success')): ?>
    <div class="card-panel green lighten-4 green-text text-darken-4">
        <?= session()->getFlashdata('success') ?>
    </div>
    <?php elseif (session()->getFlashdata('error')): ?>
        <div class="card-panel red lighten-4 red-text text-darken-4">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>


    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Materialize JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            M.updateTextFields();

            // Inicializar sidenav para móviles
            var elems = document.querySelectorAll('.sidenav');
            M.Sidenav.init(elems);

            // INICIALIZA MODAL
            const modalEditar = document.querySelector('#modalEditar');
            const instanciaModal = M.Modal.init(modalEditar);

            const formEditar = document.getElementById('formEditar');
            const camposEditar = document.getElementById('camposEditar');

            const botonesEliminar = document.querySelectorAll('.btn-eliminar');

            botonesEliminar.forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();

                    const id = this.dataset.id;
                    const tabla = this.dataset.tabla;

                    if (confirm('¿Estás seguro de que deseas eliminar este registro?')) {
                        fetch(`<?= base_url('eliminar') ?>/${id}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                _method: 'DELETE', // 👈 simulamos DELETE
                                tabla: tabla
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                const fila = this.closest('tr');
                                fila.style.transition = 'opacity 0.4s ease';
                                fila.style.opacity = 0;
                                setTimeout(() => fila.remove(), 400);
                                M.toast({ html: '✅ Registro eliminado', classes: 'green' });
                            } else {
                                M.toast({ html: '❌ ' + data.message, classes: 'red' });
                            }
                        })
                        .catch(err => {
                            M.toast({ html: '⚠️ Error en la solicitud AJAX', classes: 'orange' });
                            console.error(err);
                        });
                    }
                });
            });

            // Botones editar
            document.querySelectorAll('.btn-editar').forEach(btn => {
                btn.addEventListener('click', function () {
                    const id = this.dataset.id;
                    const tabla = this.dataset.tabla;

                    fetch(`<?= base_url('obtener') ?>/${tabla}/${id}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                camposEditar.innerHTML = '';
                                document.getElementById('edit-id').value = id;
                                document.getElementById('edit-tabla').value = tabla;

                                for (const [key, value] of Object.entries(data.datos)) {
                                    if (key === 'id') continue;
                                    camposEditar.innerHTML += `
                                        <div class="input-field">
                                            <input type="text" name="${key}" id="edit-${key}" value="${value}">
                                            <label for="edit-${key}" class="active">${key}</label>
                                        </div>
                                    `;
                                }

                                instanciaModal.open();
                            } else {
                                M.toast({ html: '❌ ' + data.message, classes: 'red' });
                            }
                        });
                });
            });

            // Envío del formulario
            formEditar.addEventListener('submit', function (e) {
                e.preventDefault();

                const id = document.getElementById('edit-id').value;
                const tabla = document.getElementById('edit-tabla').value;
                const formData = new FormData(formEditar);
                const datos = {};

                for (let [key, value] of formData.entries()) {
                    if (key !== 'id' && key !== 'tabla') {
                        datos[key] = value;
                    }
                }

                fetch(`<?= base_url('actualizar') ?>/${tabla}/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(datos)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        M.toast({ html: '✅ Registro actualizado', classes: 'green' });
                        instanciaModal.close();
                        setTimeout(() => location.reload(), 800);
                    } else {
                        M.toast({ html: '❌ ' + data.message, classes: 'red' });
                    }
                })
                .catch(err => {
                    console.error(err);
                    M.toast({ html: '⚠️ Error en la solicitud', classes: 'orange' });
                });
            });

        });

        document.getElementById('buscador').addEventListener('input', function() {
            let filtro = this.value.toLowerCase();
            let filas = document.querySelectorAll('table tbody tr');

            filas.forEach(fila => {
                let texto = fila.innerText.toLowerCase();
                fila.style.display = texto.includes(filtro) ? '' : 'none';
            });
        });

    </script>

</body>
</html>