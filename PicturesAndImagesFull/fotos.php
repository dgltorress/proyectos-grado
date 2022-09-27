<?php
    require_once("php/conexionBD.php");
    require_once("php/funciones.php");

    require_once("php/articulo.php");

    // Impide acceder como invitado
    if( !estaAutenticado() ) header( "Location: index.php?msg=5" );

    $datosUsuario = @mysqli_fetch_assoc( @mysqli_query( $conexion,
    'SELECT id,nombre FROM usuarios WHERE nombre = "'.$_GET['usr'].'"') );
    $hayUsuario = !empty( $datosUsuario );

    $fotos;
    if( $hayUsuario ){
        $fotos = @mysqli_query( $conexion ,
            'SELECT f.id AS idFoto,f.titulo AS tituloFoto,
                    a.id AS idAlbum,a.titulo AS tituloAlbum,
                    f.fichero AS url,f.alternativo,p.nombre AS pais
            FROM albumes AS a
            JOIN fotos AS f ON a.id = f.album
            JOIN paises AS p ON p.id = f.pais
            WHERE a.usuario = '.$datosUsuario['id'].'
            ORDER BY f.fRegistro DESC
            LIMIT 20' ) OR die( "No se ha podido realizar la consulta." );
    }
    $hayFotos = !empty( $fotos );
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <?php
            if( $hayUsuario ) $titulo = "Fotos de ".$datosUsuario['nombre'];
            else $titulo = "Fotos";
            $descripcion = "Visualiza una lista de tus álbumes.";

            require_once("php/head.php");
        ?>
        <style>
            .fotosRecientes{width:90%;margin:auto;}
            .fotoReciente{width:30%;}

            .fotoReciente>header>a,.autoria{display:inline-block;}
            .autoria{margin-left:1em;}
        </style>
    </head>
    <body>
        <header><?php require_once("php/cabecera_registro.php"); ?></header>
        <div id="divIndice">
            <main>
                <section> <!--Últimos álbumes-->
                    <h1>Fotos de <?php if( $hayFotos ) echo $datosUsuario['nombre']; ?></h1>
                    <div class="fotosRecientes">
                        <?php
                            if( $hayFotos ){
                                while( $foto = mysqli_fetch_assoc( $fotos ) ) nuevoArticuloSimpleAlbum( $foto );
                                mysqli_free_result( $fotos );
                            }
                        ?>
                    </div>
                </section>
            </main>
        </div>
        <footer><?php require_once("php/pie.php"); ?></footer>
    </body>
</html>