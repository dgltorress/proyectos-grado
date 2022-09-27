<a href="index.php"><img src="img/logoPag.png" alt="Logo" title="PI - Pictures &amp; Images"></a> <!--Logo del sitio-->
<nav class="barraBusqueda">
    <form method="GET" action="resultado.php"> <!--Formulario de búsqueda-->
        <input type="search" placeholder="Encuentra una fotografía...">
        <button class="boton" title="Buscar"><span class="icon icon-search"></span></button>
    </form>
    <a href="buscar.php" title="Búsqueda avanzada"><span class="icon icon-filter"></span></a>
</nav>

<nav class="botonesCabecera">
    <a href="foto.php">Foto aleatoria</a>
    <a href="subirFoto.php?usr=<?php $nombreUsuario = verNombreUsuario(); ?>">Subir foto</a>
</nav>

<nav class="dropdown"> <!--Menú desplegable del usuario-->
    <span class="icon icon-user-circle-o iconoUsuario">
        <!--Nombre del usuario-->
        <span><?= $nombreUsuario ?></span>
        <span class="icon icon-down-open"></span>
    </span>
    <ul class="drop-content">
        <li><a href="perfil.php?=id">Mi perfil</a></li>
        <li><a href="albumes.php?usr=<?= $nombreUsuario ?>">Mis álbumes</a></li>
        <li><a href="fotos.php?usr=<?= $nombreUsuario ?>">Mis fotos</a></li>
        <li><a href="crear.php?usr=<?= $nombreUsuario ?>">Nuevo álbum</a></li>
        <li><a href="solicitud.php?usr=<?= $nombreUsuario ?>">Imprimir álbum</a></li>
        <li><a href="configurar.php">Configuración</a></li>
        <li><a href="php/controlSalida.php">Cerrar sesión</a></li>
    </ul>
</nav>
