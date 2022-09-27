<?php
    require_once("php/funciones.php");
    // Impide acceder como invitado
    if( !estaAutenticado() ) header( "Location: index.php?msg=5" );
    require_once("php/conexionBD.php");
    $nombreUsuario = verNombreUsuario();
    $usuarios = @mysqli_query( $conexion ,"SELECT * FROM usuarios
    WHERE nombre = '".$nombreUsuario."'" ) OR die( "No se ha podido realizar la consulta." );
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
                    <h1>Perfil</h1>
                    <div id="perfil">
                        <div>
                            <img src="<?= $usuario['foto'] ?>" alt="Foto de perfil de <?= $nombreUsuario ?>">
                            <p><strong><?php  $nombreUsuario ?></strong></p>
                            <p>Último acceso: <strong><?php verUltimoAcceso(); ?></strong></p>
                        </div>
                        <nav>   
                            <a href="editar.php?usr=<?= $nombreUsuario ?>">Mis datos</a>
                            <a href="albumes.php?usr=<?= $nombreUsuario ?>">Mis álbumes</a>
                            <a href="fotos.php?usr=<?= $nombreUsuario ?>">Mis fotos</a>
                            <a href="crear.php?usr=<?= $nombreUsuario ?>">Nuevo álbum</a>
                            <a href="solicitud.php?usr=<?= $nombreUsuario ?>">Imprimir álbum</a>
                            <a href="abandonar.php?usr=<?= $nombreUsuario ?>">Darse de Baja</a>
                            <a href="php/controlSalida.php">Cerrar sesión</a>
                        </nav>
                    </div>
                </section>
            </main>
        </div>
        <footer><?php require_once("php/pie.php"); ?></footer>
    </body>
</html>