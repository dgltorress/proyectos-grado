<?php
    require_once("php/conexionBD.php");
    require_once("php/funciones.php");

    require_once("php/articulo.php");

    $autenticado = estaAutenticado();

    $numFoto = 1; // PROVISIONAL PARA LA VERSIÓN ESTÁTICA
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <?php
            $titulo = "Resultados";
            $descripcion = "Resultados de la búsqueda.";

            require_once("php/head.php");
        ?>
        <style>
            .fotosRecientes{width:90%;margin:auto;}
            .fotoReciente{width:30%;}
        </style>
    </head>
    <body>
        <header><?php elegirCabecera( $autenticado ); ?></header>
        <div id="divIndice">
            <main>
                <section> <!--Resultados-->
                    <h1>Resultados</h1>
                    <div class="filtros">
                        <header>Filtros:</header>
                        <?php
                            $hayTitulo = !empty($_GET['titulo']);
                            $hayFecha = !empty($_GET['fecha']);
                            $hayPais = !empty($_GET['pais']);
                        ?>
                        <ul>
                            <?php if( $hayTitulo ) echo "<li>Título: <strong>\"".$_GET['titulo']."\"</strong></li>";
                                  if( $hayFecha  ){
                                      $fechaParseada = parsearFecha($_GET['fecha']);
                                      if( $fechaParseada != "FORMATO DE FECHA INCORRECTO"){
                                        echo "<li>Fecha: <strong>".$fechaParseada."</strong></li>";
                                      }
                                  }
                                  if( $hayPais   ){
                                      $nombrePais = @mysqli_fetch_row( @mysqli_query( $conexion ,
                                      "SELECT nombre FROM paises WHERE id = ".intval( $_GET['pais'] ) ) )[0];
                                      if( $nombrePais ) echo "<li>País: <strong>$nombrePais</strong></li>";
                                  } ?>
                        </ul>
                    </div>
                    <div class="fotosRecientes">
                        <?php
                            // Consulta preparada para evitar inyección de SQL
                            $consulta = @mysqli_prepare( $conexion,
                                "SELECT f.id AS idFoto,f.titulo,u.id AS idAutor,u.foto AS pfp,
                                u.nombre AS autor,f.fRegistro,f.fichero AS url,f.alternativo,
                                p.nombre AS pais
                                FROM fotos AS f
                                JOIN paises AS p ON f.pais = p.id
                                JOIN albumes AS a ON f.album = a.id
                                JOIN usuarios AS u ON a.usuario = u.id
                                WHERE (f.titulo LIKE ?)
                                AND   (DATE_FORMAT( f.fRegistro , '%Y-%m-%d' ) = ? OR ? = '')
                                AND   (p.id = ? OR ? < 0)
                                ORDER BY f.fRegistro DESC
                                LIMIT 5" ) OR die( "No se ha podido preparar la consulta." );

                            $pTitulo = $hayTitulo ? '%'.$_GET['titulo'].'%' : '%';
                            $pFecha = $hayFecha ? $_GET['fecha'] : '';
                            $pPais = $hayPais ? $_GET['pais'] : -1;
                            
                            // Se sutituyen los parámetros y se ejecuta la sentencia
                            @mysqli_stmt_bind_param( $consulta , "sssii" , $pTitulo , $pFecha , $pFecha , $pPais , $pPais );
                            @mysqli_execute( $consulta );
                            
                            // Obtiene los resultados y analiza las filas
                            $fotos = @mysqli_stmt_get_result( $consulta );
                            if( $fotos->num_rows > 0 ){
                                while( $foto = @mysqli_fetch_assoc( $fotos ) ) nuevoArticulo( $foto , $autenticado );
                            }
                            else{
                                echo '<article id="ArtError">
                                        <header>
                                            <h2>0 resultados encontrados</h2>
                                        </header>
                                        <p>No se ha encontrado ningún resultado que se corresponda con los datos introducidos.
                                        Prueba de nuevo en búsqueda avanzada o regresa al índice.</p>
                                        <footer>
                                            <a href="buscar.php">Búsqueda avanzada</a>
                                            <span></span>
                                            <a href="index.php">Regresar al índice</a>
                                        </footer>
                                      </article>';
                            }
                            @mysqli_free_result( $fotos );
                        ?>
                    </div>
                </section>
                <div class="paginado"> <!--Paginado-->
                    <button class="boton" type="button">&laquo;</button>
                    <button class="boton" type="button">&#8249;</button>
                    <span>1</span>
                    <button class="boton" type="button">&#8250;</button>
                    <button class="boton" type="button">&raquo;</button>
                </div>
            </main>
        </div>
        <footer><?php require_once("php/pie.php"); ?></footer>
    </body>
</html>