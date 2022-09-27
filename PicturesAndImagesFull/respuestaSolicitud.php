<?php
    require_once("php/conexionBD.php");
    require_once("php/funciones.php");
    // Impide acceder como invitado o sin datos POST
    if( !estaAutenticado() || empty( $_POST ) ) header( "Location: index.php?msg=5" );
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <?php
            $titulo = "Datos de impresión";
            $descripcion = "Revisa los datos introducidos.";

            require_once("php/head.php");
        ?>
        <style>
            table{
                width:50%;
                margin:1em auto 1em auto;
            }
            .precios{
                text-align:right;
                font-variant:small-caps;
                margin-right:25%;
            }
            .precios>div{
                margin:.8em 0 .8em 0;
            }
            .precios>div>span:last-child{
                margin-left:1em;
                font-size:24px;
                font-weight:bold;
            }
            tr>td:first-child{
                text-align:right !important;
                padding-right:2em;
                width:50%;
            }
            tr>td:nth-child(2){
                text-align:left !important;
                padding-left:2em;
                font-weight:bold;
            }
            .boton{
                width:50%;
                
                font-size:24px;
                font-variant:small-caps;

                padding:.2em;
                margin:1em auto .5em auto;
            }
        </style> 
    </head>
    <body>
        <header><?php require_once("php/cabecera_registro.php"); ?></header>
        <div>
            <main> <!--Formulario-->
                <section>
                    <h1>Confirma los datos</h1>
                    <p>Revisa los datos introducidos.</p>
                    <table>
                        <caption>Información personal</caption>
                        <thead>
                            <tr>
                                <th scope="col">Concepto</th>
                                <th scope="col">Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if( !empty( $_POST["nombre"] ) ): ?>
                                <tr>
                                    <td>Nombre</td>
                                    <td><?php echo $_POST["nombre"]; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if( !empty( $_POST["apellidos"] ) ): ?>
                                <tr>
                                    <td>Apellidos</td>
                                    <td><?php echo $_POST["apellidos"]; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if( !empty( $_POST["correo"] ) ): ?>
                                <tr>
                                    <td>Email</td>
                                    <td><?php echo $_POST["correo"]; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if( !empty( $_POST["telefono"] ) ): ?>
                                <tr>
                                    <td>Teléfono</td>
                                    <td><?php echo parsearTelefono( $_POST["telefono"] ); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if( !empty( $_POST["fecha"] ) ): ?>
                                <tr>
                                    <td>Fecha de recepción</td>
                                    <td><time datetime=""><?php echo parsearFecha( $_POST["fecha"] ); ?></time></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td colspan="2" class="middleThead" style="text-align:center !important;padding:.5em 0 .5em 0;">
                                    Dirección
                                </td>
                            </tr>
                            <?php if( !empty( $_POST["pais"] ) ): ?>
                                <tr>
                                    <td>País</td>
                                    <td><?php echo $_POST["pais"]; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if( !empty( $_POST["provincia"] ) ): ?>
                                <tr>
                                    <td>Provincia</td>
                                    <td><?php echo $_POST["provincia"]; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if( !empty( $_POST["localidad"] ) ): ?>
                                <tr>
                                    <td>Localidad</td>
                                    <td><?php echo $_POST["localidad"]; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if( !empty( $_POST["postal"] ) ): ?>
                                <tr>
                                    <td>Código postal</td>
                                    <td><?php echo $_POST["postal"]; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if( !empty( $_POST["calle"] ) ): ?>
                                <tr>
                                    <td>Calle</td>
                                    <td><?php echo $_POST["calle"]; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if( !empty( $_POST["numero"] ) ): ?>
                                <tr>
                                    <td>Número</td>
                                    <td><?php echo $_POST["numero"]; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if( !empty( $_POST["piso"] ) ): ?>
                                <tr>
                                    <td>Piso</td>
                                    <td><?php echo $_POST["piso"]; ?>&ordm;</td>
                                </tr>
                            <?php endif; ?>
                            <?php if( !empty( $_POST["puerta"] ) ): ?>
                                <tr>
                                    <td>Puerta</td>
                                    <td><?php echo strtoupper( $_POST["puerta"] ); ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <table>
                        <caption>Datos del álbum</caption>
                        <thead>
                            <tr>
                                <th scope="col">Concepto</th>
                                <th scope="col">Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if( !empty( $_POST["album"] ) ): ?>
                                <tr>
                                    <td>Álbum solicitado</td>
                                    <td><?php echo tituloAlbum( $_POST["album"] , $conexion ); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if( !empty( $_POST["titulo"] ) ): ?>
                                <tr>
                                    <td>Título</td>
                                    <td><?php echo $_POST["titulo"]; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if( !empty( $_POST["descripcion"] ) ): ?>
                                <tr>
                                    <td>Descripción</td>
                                    <td><?php echo $_POST["descripcion"]; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if( !empty( $_POST["color"] ) ): ?>
                                <tr>
                                    <td>Color de la portada</td>
                                    <td style="display:flex;align-items:center;">
                                        <div class="colorSample" style="background-color:<?php echo $_POST["color"]; ?>;"></div>
                                        <?php echo $_POST["color"]; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <table>
                        <caption>Precio</caption>
                        <thead>
                            <tr>
                                <th scope="col">Concepto</th>
                                <th scope="col">Valor</th>
                                <th scope="col">Cargo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $nFotos = fotosEnAlbum( $_POST["album"] , $conexion );
                                $nPaginas = intdiv( $nFotos - 1 , 3 ) + 1;
                            ?>
                                <tr>
                                    <td>Páginas</td>
                                    <td><?php echo "$nPaginas ($nFotos fotos, 3/página)"; ?></td>
                                    <td><?php $precioPaginas = calcularPrecioPaginas( $nPaginas );
                                                echo number_format( (float)$precioPaginas , 2 , "," , "." ); ?>&euro;</td>
                                </tr>
                            <?php if( !empty( $_POST["resolucion"] ) ): ?>
                                <tr>
                                    <td>Resolución</td>
                                    <td><?php echo $_POST["resolucion"]; ?> DPI</td>
                                    <td><?php $precioDPI = calcularPrecioResolucion( $_POST["resolucion"] , $nFotos );
                                              echo number_format( (float)$precioDPI , 2 , "," , "." ); ?>&euro;</td>
                                </tr>
                            <?php endif; ?>
                            <?php if( !empty( $_POST["paleta"] ) ): ?>
                                <tr>
                                    <td>¿A color&quest;</td>
                                    <td><?php if( $_POST["paleta"] ) echo "Sí"; else echo "No"; ?></td>
                                    <td><?php $precioColor = calcularPrecioColor( $_POST["paleta"] , $nFotos );
                                              echo number_format( (float)$precioColor , 2 , "," , "." ); ?>&euro;</td>
                                </tr>
                            <?php endif; ?>
                            <?php if( !empty( $_POST["copias"] ) ): ?>
                                <tr>
                                    <td>Copias</td>
                                    <td><?php echo $_POST["copias"]; ?></td>
                                    <td>&times;<?php echo $_POST["copias"]; ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <footer class="precios">
                        <div class="total">
                            <span>Subotal</span>
                            <span><?php $precioTotal = ( $precioPaginas + $precioColor + $precioDPI ) * intval( $_POST["copias"] );
                                        echo number_format( (float)$precioTotal , 2 , "," , "." ); ?>&euro;</span>
                        </div>
                        <div class="totalExtra">
                            <span>Envío</span>
                            <span>2,50&euro;</span>
                        </div>
                        <div class="total">
                            <span>Total</span>
                            <span><?php $precioTotal = $precioTotal + 2.5;
                                        echo number_format( (float)$precioTotal , 2 , "," , "." ); ?>&euro;</span>
                        </div>
                    </footer>
                    <!-- REENVIAR LOS DATOS POST AL CONTROLADOR MEDIANTE LA SESIÓN -->
                    <form id="relay" method="POST" action="php/controlSolicitud.php" style="display:none;">
                        <input type='hidden' name='datos' value="<?php echo htmlentities( serialize( $_POST ) ); ?>">
                    </form>
                    <button class="boton" form="relay">Confirmar los datos</button>
                </section>
            </main>
        </div>
        <footer><?php require_once("php/pie.php"); ?></footer>
    </body>
</html>
