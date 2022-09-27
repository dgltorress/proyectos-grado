<?php
        require_once("php/conexionBD.php");
        require_once("php/funciones.php");
    
        require_once("php/articulo.php");
    
        if(!estaAutenticado()) header( "Location: index.php?msg=5" );
        else $autenticado =estaAutenticado();

        $datosFoto;
        if( isset( $_GET["foto"] ) ){
            $datosFoto = @mysqli_query( $conexion ,'SELECT f.id AS idFoto,f.fecha as fechaFoto,f.descripcion AS descripcionFoto ,f.titulo AS tituloFoto,
            a.id AS idAlbum,a.titulo AS tituloAlbum,f.fichero AS url,f.alternativo,
            p.nombre AS pais,
            u.nombre AS Autor,u.id as idAutor, u.foto as pfp
            FROM albumes AS a
            JOIN fotos AS f ON a.id = f.album
            JOIN paises AS p ON p.id = f.pais
            JOIN usuarios AS u ON a.usuario = u.id 
            Where f.id = '.$_GET["foto"]
             ) 
            OR die( "No se ha podido realizar la consulta." );
        }

        $display= !empty( $datosFoto );
?>


<!DOCTYPE html>
<!--
ARCHIVO: foto.php
Página de detalles de una foto

AUTORES: Ian Bucu el 28/09/2021

REVISIONES:
-->
<html lang="es">
    <head>
        <?php
            $titulo = "Foto";
            $descripcion = "Foto subida a la página.";
            require_once("php/head.php");
        ?>
    </head>
    <body>
        <header><?php elegirCabecera( $autenticado ); ?></header>
        <div>
            <main>
                <section> <!--Últimas fotos-->
                    <h1>Detalles</h1>
                    <?php
                        if( $display ){                       
                            while( $Foto = mysqli_fetch_assoc( $datosFoto ) ) nuevoArticuloFoto($Foto);
                            mysqli_free_result( $datosFoto );
                        }
                    ?>

                </section>
            </main>
        </div>
        <footer><?php require_once("php/pie.php"); ?></footer>
    </body>
</html>
