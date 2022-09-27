<?php
    setcookie( "ultimaFotoDia" , -1 , 0 , "/" );
    setcookie( "ultimoConsejo" , -1 , 0 , "/" );

    require_once("php/funciones.php");

    $datosFotoDelDia = obtenerFotoDelDia();
    $consejo         = obtenerConsejo(); 

    $autenticado = estaAutenticado();

    require_once("php/conexionBD.php");
    require_once("php/articulo.php");

    
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <?php
            $titulo = "Inicio";
            $descripcion = "Comparte tus fotos con otros usuarios.";

            require_once("php/head.php");
        ?>
    </head>
    <body>
        <header><?php elegirCabecera( $autenticado ); ?></header>
        <div id="divIndice">
            <main>
                <section> <!--Últimas fotos-->
                    <div class="headerYMensaje">
                        <h1>Más recientes</h1>
                        <!--Mensaje que insta a registrarse-->
                        <?= !$autenticado ? "<p>¿Aún no tienes una cuenta? <a href=\"registro.php\">Regístrate</a></p>" : "" ?>
                    </div>
                    <div class="fotosRecientes">
                        <?php
                            $fotos = @mysqli_query( $conexion,
                                                   "SELECT f.id AS idFoto,f.titulo,u.id AS idAutor,u.foto AS pfp,
                                                   u.nombre AS autor,f.fRegistro,f.fichero AS url,f.alternativo,
                                                   p.nombre AS pais
                                                   FROM fotos AS f
                                                   JOIN paises AS p ON f.pais = p.id
                                                   JOIN albumes AS a ON f.album = a.id
                                                   JOIN usuarios AS u ON a.usuario = u.id
                                                   ORDER BY f.fRegistro DESC
                                                   LIMIT 5" ) OR die( "No se ha podido realizar la consulta." );
                            while( $foto = @mysqli_fetch_assoc( $fotos ) ) nuevoArticulo( $foto , $autenticado );
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
            <aside> <!--Barra lateral-->
                <article> <!--Foto del día-->
                    <header>
                        <h2>Foto del día</h2>
                        <?php
                            $datosFotoBD = @mysqli_fetch_assoc( @mysqli_query( $conexion , 'SELECT fichero,alternativo FROM fotos WHERE id = '.$datosFotoDelDia[0] ) );

                            if( !$datosFotoBD ){
                                $datosFotoBD['fichero'] = "";
                                $datosFotoBD['alternativo'] = "FOTO NO ENCONTRADA";
                            };
                        ?>
                    </header>
                    <a href="foto.php?foto=<?= $datosFotoDelDia[0] ?>" class="figura">
                        <figure>
                            <img src="<?= $datosFotoBD['fichero'] ?>" alt="<?= $datosFotoBD['alternativo'] ?>">
                        </figure>
                    </a>
                    <p style="margin:1.2em 0 1em 0;font-style:italic;"><?= $datosFotoDelDia[2] ?></p>
                    <footer>
                        <span class="autoria">Por&nbsp;<span><?= $datosFotoDelDia[1] ?></span></span>
                    </footer>
                </article>
                <article class="consejo"> <!--Consejo-->
                    <header>
                        <h2>Consejo</h2>
                    </header>
                    <p><?= $consejo[2] ?></p>
                    <p>Categoría: <span><?= $consejo[0] ?></p>
                    <p>Dificultad: <span><?= $consejo[1] ?></p>
                </article>
                <div> <!--Etiquetas populares-->
                    <header>
                        <h2>Etiquetas populares</h2>
                    </header>
                    <ul class="etiquetas">
                        <li><a href="">impactante</a></li>
                        <li><a href="">tierno</a></li>
                        <li><a href="">espectacular</a></li>
                        <li><a href="">capital</a></li>
                        <li><a href="">monumento</a></li>
                        <li><a href="">de invierno</a></li>
                        <li><a href="">navideña</a></li>
                        <li><a href="">rural</a></li>
                    </ul>
                    <footer></footer>
                </div>
            </aside>
        </div>
        <footer><?php require_once("php/pie.php"); ?></footer>
    </body>
</html>