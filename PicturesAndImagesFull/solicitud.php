<?php
    require_once("php/conexionBD.php");
    require_once("php/funciones.php");
    // Impide acceder como invitado
    if( !estaAutenticado() ) header( "Location: index.php?msg=5" );

    $albumes = @mysqli_query( $conexion ,"SELECT a.id,titulo FROM albumes as a
    JOIN usuarios AS u ON a.usuario = u.id
    WHERE u.nombre = '". $_GET['usr']."'"
 ) OR die( "No se ha podido realizar la consulta." );

 $hayAlbumes = !empty( $albumes );


?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <?php
            $titulo = "Solicitud de impresión";
            $descripcion = "Solicita la impresión y envío de un álbum.";

            require_once("php/head.php");
        ?>
        <script>
            function actualizarResolucion( campo ){
                document.getElementById( 'valorResolucion' ).innerText = campo.value + ' DPI';
            }
        </script>
    </head>
    <?php require_once("php/tabla.php") ?>
    <body>
    
        <header><?php require_once("php/cabecera_registro.php"); ?></header>
        <div>
            <main> <!--Formulario-->
                <section>
                    <h1>Obtén tu álbum en formato físico</h1>
                    <p>Aquí puedes solicitar la impresión y envío de cualquiera de <a href="albumes.php?usr=" .  >tus álbumes</a> en la fecha que tú prefieras.</p>
                </section>
                <section>
                    <h2>Solicitud de álbum</h2>
                    <form method="POST" action="respuestaSolicitud.php" style="width:65%;">
                        <fieldset>
                            <legend>Datos del álbum</legend>
                            <label for="alb">Álbum solicitado *</label>
                            <select id="alb" name="album"> <!-- required -->
                                <option value="">---</option>
                                <?php
                                if( $hayAlbumes ){
                                    while( $album = mysqli_fetch_assoc( $albumes ) ) echo '<option value="'.$album['id'].'">'.$album['titulo'].'</option>';
                                }
                                ?>
                            </select>
                            <label for="ttl">Título *</label>
                            <input id="ttl" name="titulo" type="text" maxlength="200" autofocus>  <!-- required -->
                            <label for="desc">Descripción/Dedicatoria</label>
                            <textarea id="desc" name="descripcion" maxlength="4000"></textarea>
                            <label for="col">Color de la portada</label>
                            <input id="col" name="color" type="color" value="#000000">
                            <label for="copy">Número de copias</label>
                            <input id="copy" name="copias" type="number" min="1" max="99" step="1" value="1">
                            <label for="res">Resolución de las fotos</label>
                            <input id="res" name="resolucion" type="range" min="150" max="900" step="150" value="150" oninput="actualizarResolucion( this )">
                            <span id="valorResolucion" style="text-align:center;">150 DPI</span>
                            <label class="casilla">
                                <span>A color</span>
                                <input id="pal" name="paleta" type="checkbox" checked>
                            </label>
                        </fieldset>
                        
                        <fieldset>
                            <legend>Información personal</legend>
                            <label for="nom">Nombre *</label>
                            <input id="nom" name="nombre" type="text"> <!-- required --> <!-- maxlength="100"-->
                            <label for="sur">Apellidos *</label>
                            <input id="sur" name="apellidos" type="text"> <!-- required --> <!--  maxlength="100"-->
                            <label for="mail">Email *</label>
                            <input id="mail" name="correo" type="text" placeholder="destinatario@ejemplo.com"> <!-- required --> <!-- type="email" maxlength="200" -->
                            <label for="tlf">Teléfono</label>
                            <input id="tlf" name="telefono" type="text" placeholder="### ## ## ##" > <!-- type="tel" pattern="\d( ?\d){8,11}" maxlength="12" -->
                            <label for="dat">Fecha de recepción</label>
                            <input id="dat" name="fecha" type="date">
                            <fieldset>
                                <legend>Dirección *</legend>
                                <label for="cnt">País</label>
                                <select id="cnt" name="pais"> <!-- required -->
                                    <option value="" selected>---</option>
                                    <?php crearListaPaises( $conexion ); ?>
                                </select>
                                <label for="prv">Provincia</label>
                                <input id="prv" name="provincia" type="text" > <!-- required --> <!-- maxlength="50"-->
                                <label for="lcd">Localidad</label>
                                <input id="lcd" name="localidad" type="text" > <!-- required --> <!-- maxlength="50"-->
                                <label for="cp">CP</label>
                                <input id="cp" name="postal" type="text"  placeholder="#####"> <!-- required --> <!-- pattern="[0-9]{5}" maxlength="5" -->
                                <label for="str">Calle</label>
                                <textarea id="str" name="calle"  placeholder="c., avda., ..."></textarea> <!-- required --> <!-- maxlength="4000"-->
                                <label for="num">Número</label>
                                <input id="num" name="numero" type="text" > <!-- required --> <!-- pattern="[0-9]{1,3}" maxlength="3"-->
                                <label for="flr">Piso</label>
                                <input id="flr" name="piso" type="text" > <!-- required --> <!-- pattern="[0-9]{1,2}" maxlength="2"-->
                                <label for="dr">Puerta</label>
                                <input id="dr" name="puerta" type="text" > <!-- required --> <!-- pattern="[A-Za-z]" maxlength="1"-->
                            </fieldset>
                        </fieldset>
                        <p class="requerido">* Campo requerido</p>
                        <button class="boton">Enviar</button>
                    </form>
                </section>
            </main>
            <aside> <!--Tarifas-->
                <table>
                    <caption>Tarifas</caption>
                    <thead>
                        <tr>
                            <th scope="col" colspan="2">Concepto</th>
                            <th scope="col">Cargo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="filaConcepto" rowspan="3">Nº de páginas</td>
                            <td>&lt;5</td>
                            <td>0,10&euro;/pág</td>
                        </tr>
                        <tr>
                            <td>5-11</td>
                            <td>0,08&euro;/pág</td>
                        </tr>
                        <tr>
                            <td>&gt;11</td>
                            <td>0,07&euro;/pág</td>
                        </tr>
                        <tr>
                            <td class="filaConcepto" rowspan="2">Paleta</td>
                            <td>Blanco y negro</td>
                            <td>Sin cargos añadidos</td>
                        </tr>
                        <tr>
                            <td>A color</td>
                            <td>0,05&euro;/foto</td>
                        </tr>
                        <tr>
                            <td class="filaConcepto">Extras</td>
                            <td>Resolución >300 DPI</td>
                            <td>0,02&euro;/foto</td>
                        </tr>
                    </tbody>
                </table>
                <table id="TabPrecios">
                    <caption>Precios(€)</caption>
                    <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col" colspan="2">Blanco y negro</th>
                            <th scope="col" colspan="2">Color</th>
                        </tr>
                        <tr>
                            <th scope="col">Nº Páginas</th>
                            <th scope="col">Nº fotos</th>
                            <th scope="col">150-300 DPI</th>
                            <th scope="col">450-900 DPI</th>
                            <th scope="col">150-300 DPI</th>
                            <th scope="col">450-900 DPI</th>
                        </tr>
                        <?php crearTabla(); ?>
                    </thead>
                </table>
            </aside>
        </div>
        <footer><?php require_once("php/pie.php"); ?></footer>
    </body>
</html>