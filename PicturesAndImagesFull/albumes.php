<?php
    require_once("php/conexionBD.php");
    require_once("php/funciones.php");

    require_once("php/plantillaAlbum.php");

    // Impide acceder como invitado
    if( !estaAutenticado() ) header( "Location: index.php?msg=5" );

    $datosUsuario = @mysqli_fetch_assoc( @mysqli_query( $conexion,
        'SELECT id,nombre FROM usuarios WHERE nombre = "'.$_GET['usr'].'"') );
    $hayDatos = !empty( $datosUsuario );
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <?php
            if( $hayDatos ) $titulo = "Álbumes de ".$datosUsuario['nombre'];
            else $titulo = "Álbumes";
            $descripcion = "Visualiza una lista de tus álbumes.";

            require_once("php/head.php");
        ?>
        <style>
            th:first-child, td:first-child{max-width:20%;max-width:50px;}
            td:first-child{text-align:left;}
            th:nth-child(2), td:nth-child(2){width:70%;max-width:100px;}
            td:nth-child(2){text-align:left;}
            th:last-child, td:last-child{max-width:10%;max-width:10px;}
            td:last-child{text-align:center !important;}

            table{border-collapse:collapse;border-bottom:1px solid var(--tono5);}
            td{border-inline:1px solid var(--tono4);}

            tbody tr:nth-child(even)>td{background-color:var(--tono5);}

            tbody tr{cursor:pointer;}
            tbody tr:hover>td{background-color:var(--tono4);}

            tbody td{padding:0;}

            td>a{
                color:inherit;
                padding:.5em;
                display:block;

                white-space:nowrap;
                overflow:hidden;
                text-overflow:ellipsis;
            }
        </style>
    </head>
    <body>
        <header><?php require_once("php/cabecera_registro.php"); ?></header>
        <div id="divIndice">
            <main>
                <section> <!--Últimos álbumes-->
                    <h1>Álbumes de <?php if( $hayDatos ) echo $datosUsuario['nombre']; ?></h1>
                    <div class="fotosRecientes">
                        <table>
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Descripción</th>
                                    <th>N&ordm; de fotos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    if( $hayDatos ){
                                        $albumes = @mysqli_query( $conexion,
                                        'SELECT a.id,a.titulo,a.descripcion,COUNT(f.id) AS nFotos FROM albumes AS a
                                        LEFT JOIN fotos AS f ON a.id = f.album
                                        WHERE a.usuario = '.$datosUsuario['id'].'
                                        GROUP BY a.id
                                        LIMIT 20' ) OR die( "No se ha podido realizar la consulta." );
                                        while( $album = @mysqli_fetch_assoc( $albumes ) ) nuevoAlbumListado( $album );
                                        @mysqli_free_result( $albumes );
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
        <footer><?php require_once("php/pie.php"); ?></footer>
    </body>
</html>