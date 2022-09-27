<?php
    require_once("php/conexionBD.php");
    require_once("php/funciones.php");
    require_once("php/articulo.php");
    // Impide acceder como invitado
    if( !estaAutenticado() ) header( "Location: index.php?msg=5" );
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <?php
            $titulo = "Configurar";
            $descripcion = "Permite al usuario definir un estilo.";

            require_once("php/head.php");
        ?>
    </head>
    <body>
        <header><?php require_once("php/cabecera_registro.php"); ?></header>
        <div>
            <main>
                <section> <!--Últimas fotos-->
                    <h1>Configurar</h1>
                    <article id="Estilo">
                        <header>
                            <h2>Estilos</h2>
                        </header>
                        <dl >
                        <?php                     
                        $datosEstilo = @mysqli_query( $conexion, "SELECT * from estilos" ) OR die( "No se ha podido realizar la consulta." );
                        while( $estilo = @mysqli_fetch_assoc( $datosEstilo ) ) nuevoEstilo( $estilo);
                        @mysqli_free_result( $datosEstilo );
                        ?>
                        </dl>
                    </article>
                </section>
            </main>
        </div>
        <footer><?php require_once("php/pie.php"); ?></footer>
    </body>
</html>