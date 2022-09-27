<?php
    require_once("php/funciones.php");
    if( !estaAutenticado() ) header( "Location: index.php?msg=5" );

    require_once("php/conexionBD.php");
    require_once("php/articulo.php");


    $usuarios = @mysqli_query( $conexion ,
    "SELECT nombre,fRegistro,foto FROM usuarios
    WHERE id = ".intval($_GET['id'] ) ) OR die( "No se ha podido realizar la consulta." );
    $usuario = @mysqli_fetch_assoc( $usuarios );
    @mysqli_free_result( $usuarios );
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <?php
            $titulo = "Perfil";
            $descripcion = "Un perfil privado de usuario.";

            require_once("php/head.php");
        ?>
    </head>
    <body>
        <header><?php require_once("php/cabecera_registro.php"); ?></header>
        <div>
            <main>
                <section> <!--Formulario de registro-->
                    <h1>Perfil de <?php echo $usuario['nombre']; ?></h1>
                    <p>En este apartado encontrarás enlaces a los albumes de: </p>
                    <div id="perfil">
                        <div>
                            <img src="<?php echo $usuario['foto']; ?>" alt="Foto de perfil de usuario">
                            <p><strong><?php echo $usuario['nombre']; ?></strong></p>
                            <p>Fecha de registro: <strong><?php echo parsearFechaCompleta($usuario['fRegistro']); ?></strong></p>
                        </div>
                        <nav>
                        <?php                     
                        $datosAlbums = @mysqli_query( $conexion, "SELECT * from albumes as a where a.usuario=".$_GET['id']."" ) OR die( "No se ha podido realizar la consulta." );
                        while( $album = @mysqli_fetch_assoc( $datosAlbums ) ) listaAlbums( $album);
                        @mysqli_free_result( $datosAlbums );
                        ?> 
                        </nav>
                    </div>
                </section>
            </main>
        </div>
        <footer><?php require_once("php/pie.php"); ?></footer>
    </body>
</html>