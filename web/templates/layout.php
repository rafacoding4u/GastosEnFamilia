<!DOCTYPE HTML>
<html>
<head>
    <title>TIENDA VIRTUAL</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" type="text/css" href="<?php echo 'css/' . Config::$mvc_vis_css ?>" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    
    <style>/* No me lee el .css así que pongo el style aquí */
        body.reading-mode { 
            background-color: #f0f0f0; /* Fondo muy claro */
            color: #000000; /* Texto negro */
        }
        .menu {
            background-color: #FFDAB9; /* Color asalmonado claro */
        }
        .pie {
            background-color: #FFDAB9;
            color: #000000;
        }
        .menu.reading-mode {
            background-color: #dcdcdc; /* Fondo claro */
        }
        .pie.reading-mode {
            background-color: #dcdcdc;
            color: #000000;
        }
        .tablaP.reading-mode {
            color: #000000;
        }
        .borde.reading-mode {
            border-color: #888888; /* Bordes grises */
        }
        table, th, td {
            border: 1px solid #888888; /* Bordes grises */
        }
        table.reading-mode, th.reading-mode, td.reading-mode {
            border-color: #888888; /* Bordes grises en modo lectura */
        }
    </style>
    <script>
        function confirmDelete(url) {
            if (confirm("¿Estás seguro de que deseas eliminar este elemento?")) {
                window.location.href = url;
            }
        }
        function setTheme(themeName) {
            document.cookie = "theme=" + themeName + "; path=/; max-age=10800"; /* 3 horas */
            if (themeName === 'reading') {
                document.body.classList.add('reading-mode');
                document.querySelectorAll('.menu').forEach(el => el.classList.add('reading-mode'));
                document.querySelectorAll('.pie').forEach(el => el.classList.add('reading-mode'));
                document.querySelectorAll('.tablaP').forEach(el => el.classList.add('reading-mode'));
                document.querySelectorAll('.borde').forEach(el => el.classList.add('reading-mode'));
                document.querySelectorAll('table, th, td').forEach(el => el.classList.add('reading-mode'));
            } else {
                document.body.classList.remove('reading-mode');
                document.querySelectorAll('.menu').forEach(el => el.classList.remove('reading-mode'));
                document.querySelectorAll('.pie').forEach(el => el.classList.remove('reading-mode'));
                document.querySelectorAll('.tablaP').forEach(el => el.classList.remove('reading-mode'));
                document.querySelectorAll('.borde').forEach(el => el.classList.remove('reading-mode'));
                document.querySelectorAll('table, th, td').forEach(el => el.classList.remove('reading-mode'));
            }
        }

        function getTheme() {
            const cookies = document.cookie.split(';').reduce((cookies, cookie) => {
                const [name, value] = cookie.split('=').map(c => c.trim());
                cookies[name] = value;
                return cookies;
            }, {});
            return cookies.theme || 'light';
        }

        document.addEventListener("DOMContentLoaded", function() {
            setTheme(getTheme());

            document.getElementById('readingModeToggle').addEventListener('click', function() {
                const currentTheme = getTheme();
                setTheme(currentTheme === 'light' ? 'reading' : 'light');
            });
        });
    </script>
</head>
<body>
    <div class="container-fluid">
        <div class="container">
            <div class="row">
                <div class="col-md-11">
                    <h1 class="text-center"><b>TIENDA VIRTUAL</b></h1>    
                </div>
                <div class="col-md-1">
                    <button id="readingModeToggle">Modo Lectura</button>
                </div>
            </div>
        </div>
    </div>

    <?php   
    if (!isset($menu)) {
        $menu = 'menuInvitado.php';
    }
    include $menu;
    ?>

    <div class="container-fluid">
        <div class="container">
            <div id="contenido">
            <?php echo $contenido ?>
            </div>
        </div>
    </div>

    <div class="container-fluid pie p-2 my-5">
        <div class="container">
            <h5 class="text-center"> Ten la tienda al alcance de tu mano</h5>
        </div>
    </div>

    <script>/* script para el botón de modo lectura */
        document.addEventListener("DOMContentLoaded", function() {
            var readingModeToggle = document.getElementById('readingModeToggle');
            var readingMode = getCookie('readingMode');

            if (readingMode === 'enabled') {
                document.body.classList.add('reading-mode');
            }

            readingModeToggle.addEventListener('click', function() {
                document.body.classList.toggle('reading-mode');

                if (document.body.classList.contains('reading-mode')) {
                    setCookie('readingMode', 'enabled', 3);
                } else {
                    setCookie('readingMode', 'disabled', 3);
                }
            });
        });
        /* cookies */
        function setCookie(name, value, hours) {
            var expires = "";
            if (hours) {
                var date = new Date();
                date.setTime(date.getTime() + (hours * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "") + expires + "; path=/";
        }

        function getCookie(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }
        
    </script>
</body>
</html>

