<a href="index.php"><img src="img/logoPag.png" alt="Logo" title="PI - Pictures &amp; Images"></a> <!--Logo del sitio-->
<nav class="barraBusqueda">
    <form method="GET" action="resultado.php"> <!--Formulario de búsqueda-->
        <input type="search" placeholder="Encuentra una fotografía...">
        <button class="boton" title="Buscar"><span class="icon icon-search"></span></button>
    </form>
    <a href="buscar.php" title="Búsqueda avanzada"><span class="icon icon-filter"></span></a>
</nav>
<nav class="botonesCabecera">
    <?php if( !isset( $titulo ) || $titulo != "Iniciar sesión" ) echo "<a href=\"acceso.php\">Iniciar sesión</a>"; ?> <!--Botones de acceso y registro-->
    <?php if( !isset( $titulo ) || $titulo != "Registro" ) echo "<a href=\"registro.php\">Registrarse</a>"; ?>
    <a href="foto.php">Foto aleatoria</a>
</nav>
