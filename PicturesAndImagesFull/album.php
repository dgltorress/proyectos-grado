<?php
    require_once("php/conexionBD.php");
    require_once("php/funciones.php");

    require_once("php/articulo.php");

    // Impide acceder como invitado
    if( !estaAutenticado() ) header( "Location: index.php?msg=5" );

    $album = @mysqli_fetch_assoc( @mysqli_query( $conexion ,
        'SELECT a.id,a.titulo,a.descripcion,u.id AS idAutor,u.nombre AS autor,u.foto AS pfp,
        MAX(f.fecha) AS fechaFinal, MIN(f.fecha) AS fechaInicial,
        COUNT(f.id) AS nFotos,TIMEDIFF(MAX(f.fecha),MIN(f.fecha)) AS diferencia
        FROM albumes AS a
        LEFT JOIN fotos AS f ON a.id = f.album
        JOIN usuarios AS u ON u.id = a.usuario
        WHERE a.id = '.intval( $_GET['id'] ).'
        GROUP BY a.id
        LIMIT 20' ) );

    $hayAlbum = !empty( $album );

    $fotos;
    if( $hayAlbum ){
        $fotos = @mysqli_query( $conexion ,
        'SELECT f.id,f.titulo,f.fRegistro,f.fichero AS url,f.alternativo,p.nombre AS pais FROM albumes AS a
        JOIN fotos AS f ON a.id = f.album
        JOIN paises AS p ON p.id = f.pais
        WHERE a.id = '.$album['id'].'
        ORDER BY f.fRegistro DESC
        LIMIT 20' ) OR die( "No se ha podido realizar la consulta." );
    }

    $hayFotos = !empty( $fotos );
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <?php
            $titulo = $hayAlbum ? $album['titulo'] : "Álbum no reconocido";
            $descripcion = "Visualiza un álbum en detalle.";

            require_once("php/head.php");
        ?>
        <style>
            .fotoReciente>header>a,.autoria{display:inline-block;}
            .autoria{margin-left:1em;}
        </style>
    </head>
    <body>
        <header><?php require_once("php/cabecera_registro.php"); ?></header>
        <div id="divIndice">
            <main>
                <section> <!--Información del álbum-->
                    <h1><?php if( $hayAlbum ) echo $album['titulo']; ?></h1>
                    <p class="descripcionAlbum"><?php if( $hayAlbum ) echo $album['descripcion']; ?></p>
                    <div class="fotosRecientes">
                        <?php
                            if( $hayFotos ){
                                $paises = [];
                                while( $foto = mysqli_fetch_assoc( $fotos ) ){
                                    nuevoArticuloSimple( $foto );
                                    array_push( $paises , $foto['pais'] );
                                }
                                // Deja la lista con los países utilizados sin duplicados
                                $paises = array_unique( $paises );

                                mysqli_free_result( $fotos );
                            }
                        ?>
                    </div>
                </section>
            </main>
            <aside> <!--Barra lateral-->
                <?php
                    if( $hayAlbum ){
                        echo '<a href="subirFoto.php?usr='.$album['autor'].'&alb='.$album['id'].'" class="boton enlaceBoton">
                        <span class="icon">+</span> Añadir foto</a>
                        
                        <span class="autoria" style="display:flex;margin:1.3em 0;justify-content:flex-start;">Por&nbsp;
                        <a href="usuario.php?id='.$album['idAutor'].'">
                            <img class="miniaturaPerfil" src="'.$album['pfp'].'" alt="Foto de perfil de '.$album['autor'].'">&nbsp;'.$album['autor'].'
                        </a></span>';
                    }
                ?>
                <h1>En este álbum...</h1>
                <article> <!--Cantidad de fotos-->
                    <header>
                        <h2>Número de fotos</h2>
                    </header>
                    <p class="datoCantidad"><span><?php if( $hayAlbum ) echo $album['nFotos']; ?></span></p>
                </article>
                <article> <!--Diferencia de tiempo-->
                    <header>
                        <h2>Tiempo transcurrido</h2>
                    </header>
                    <!--<p class="datoCantidad"><span><?php //if( $hayAlbum ) echo tiempoLegible( $album['diferencia'] ); ?></span></p>-->
                    <p class="datoCantidad"><span><?php if( $hayAlbum ) echo parsearFechaCompleta( $album['fechaInicial'] ); ?></span></p>
                    <p class="datoCantidad"><span><?php if( $hayAlbum ) echo parsearFechaCompleta( $album['fechaFinal'] ); ?></span></p>
                </article>
                <div> <!--Países utilizados-->
                    <header>
                        <h2>Países</h2>
                    </header>
                    <ul class="etiquetas">
                        <?php if( $hayFotos ) foreach( $paises as $pais ) echo '<li>'.$pais.'</li>'; ?>
                    </ul>
                    <footer></footer>
                </div>
            </aside>
        </div>
        <footer><?php require_once("php/pie.php"); ?></footer>
    </body>
</html>