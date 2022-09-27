<?php
    require_once("php/conexionBD.php");
    require_once("php/funciones.php");

    // Impide acceder como invitado
    if( !estaAutenticado() ) header( "Location: index.php?msg=5" );

    $albumes = @mysqli_query( $conexion ,
    "SELECT a.id,titulo FROM albumes as a
    JOIN usuarios AS u ON a.usuario = u.id
    WHERE u.nombre = '". $_GET['usr']."'" ) OR die( "No se ha podido realizar la consulta." );
    $hayAlbumes = !empty( $albumes );
    $seleccionAlbum = 0;
    if( isset( $_GET['alb'] ) ) $seleccionAlbum = intval( $_GET['alb'] );
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <?php
            $titulo = "Solicitud de impresión";
            $descripcion = "Solicita la impresión y envío de un álbum.";

            require_once("php/head.php");
        ?>
    </head>
    <body>
        <header><?php require_once("php/cabecera_registro.php"); ?></header>
        <div>
            <main> <!--Formulario-->
                <h1>Sube una foto</h1>
                <section>
                    <h2>Detalles</h2>
                    <form method="POST" action="php/controlSubirFoto.php" style="width:50%;" enctype="multipart/form-data">
                        <figure style="text-align:center;">
                            <label for="pfp" id="pfpreview"><img src="img/subir.jpg" alt="Foto de perfil"></label>
                            <input id="pfp" name="pfp" type="file" accept="image/*">
                        </figure>
                        <label for="alb">Álbum destino *</label>
                        <select id="alb" name="album"> <!-- required -->
                            <option value="">---</option>
                            <?php
                                if( $hayAlbumes ){
                                    while( $album = mysqli_fetch_assoc( $albumes ) ){
                                        if( $seleccionAlbum == $album['id'] ){
                                            echo '<option value="'.$album['id'].'" selected>'.$album['titulo'].'</option>';
                                        }
                                        else{
                                            echo '<option value="'.$album['id'].'">'.$album['titulo'].'</option>';
                                        }
                                    }
                                }
                            ?>
                        </select>
                        <label for="ttl">Título *</label>
                        <input id="ttl" name="titulo" type="text" minlength="1" maxlength="50"> <!-- required -->
                        <label for="alt">Texto alternativo *</label>
                        <textarea id="alt" name="alternativo" maxlength="255"></textarea> <!-- required -->
                        <label for="desc">Descripción</label>
                        <textarea id="desc" name="descripcion" maxlength="254"></textarea>
                        <label for="date">Fecha</label>
                        <input id="date" name="fecha" type="datetime-local">
                        <label for="cnt">País</label>
                        <select id="cnt" name="pais">
                            <option value="">---</option>
                            <?php crearListaPaises( $conexion ); ?>
                        </select>
                    <p class="requerido">* Campo requerido</p>
                    <button class="boton">Enviar</button>
                    </form>
                </section>
            </main>
        </div>
        <footer><?php require_once("php/pie.php"); ?></footer>
    </body>
</html>