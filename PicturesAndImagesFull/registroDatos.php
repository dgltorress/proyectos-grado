<?php
    require_once("php/funciones.php");
    // Impide acceder como usuario autenticado
    if( estaAutenticado() ) header( "Location: index.php?msg=6" );
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <?php
        
            $titulo = "RegistroDatos";
            $descripcion = "Datos de una cuenta nueva.";

            require_once("php/head.php");


        ?>
        <style>
            table{
                width:50%;
                margin:1em auto 1em auto;
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
        </style> 
    </head>
    <body>
        <?php
                    $MENSAJEERRORREPITE = 'Debe coincidir con la contraseña.';

                    // FORMULARIO DE REGISTRO
        
                        // Obtiene los campos
                        //var_dump($_POST);
                        // Comprueba que todos los $campos requeridos están rellenados
                        if (!empty( $_POST['usuario'] ) && !empty( $_POST['password'] && !empty( $_POST['password2'] ))) {
                            // Analiza el contenido de cada campo y muestra un mensaje modal si no es válido
                            if ( strcmp( $_POST['password'] , $_POST['password2'] ) == 0 ) {
                                
                            }
                            else {
                                header( "Location: ./registro.php?msg=4" );
                            }
                        }
                        else {
                            header( "Location: ./registro.php?msg=3" );
                        }
        
        
                    /* --------------------------------------------------------------- */
        ?>
        <header><?php require_once("php/cabecera.php"); ?></header>
        <div>
            <main> <!--Formulario-->
                <section>
                    <h1>Confirma los datos</h1>
                    <p>Revisa los datos introducidos.</p>
					<p class="campoErroneo">Las validaciones se realizan con PHP; no se muestra ayuda en JS. Para ver las restricciones de cada campo, accede a <code style="font-family:Courier new;">./php/validaciones.php</code>.</p>
                    <table>
                        <caption>Información personal</caption>
                        <thead>
                            <tr>
                                <th scope="col">Concepto</th>
                                <th scope="col">Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if( !empty( $_POST["usuario"] ) ): ?>
                                <tr>
                                    <td>Nombre</td>
                                    <td><?php echo $_POST["usuario"]; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if( !empty( $_POST["email"] ) ): ?>
                                <tr>
                                    <td>Email</td>
                                    <td><?php echo  $_POST["email"] ; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if( !empty( $_POST["sexo"] ) ): ?>
                                <tr>
                                    <td>Sexo</td>
                                    <td><?php echo $_POST["sexo"] ; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if( !empty( $_POST["fechaNac"] ) ): ?>
                                <tr>
                                    <td>Fecha de nacimiento</td>
                                    <td><?php echo $_POST["fechaNac"] ; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if( !empty( $_POST["ciudad"] ) ): ?>
                                <tr>
                                    <td>Ciudad</td>
                                    <td><?php echo  $_POST["ciudad"] ; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if( !empty( $_POST["pais"] ) ): ?>
                                <tr>
                                    <td>País</td>
                                    <td><?php echo  $_POST["pais"] ; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if( !empty( $_POST["pfp"] ) ): ?>
                                <tr>
                                    <td>País</td>
                                    <td><?php echo  $_POST["pfp"] ; $_FILES["pfp"]["name"];?></td>
                                </tr>
                            <?php endif; ?>
                        <tbody>
                    </table>
                    <!-- REENVIAR LOS DATOS POST AL CONTROLADOR MEDIANTE LA SESIÓN -->
                    <form id="relay" method="POST" action="php/controlRegistro.php" enctype="multipart/form-data">
						<input type='hidden' name='datos' value="<?php echo htmlentities( serialize( $_POST ) ); ?>">
						<input id="pfp" name="pfp" type="file" accept="image/*">
                    </form>
                    <button class="boton" form="relay">Confirmar los datos</button>
                </section>
            </main>
        </div>
        <footer><?php require_once("php/pie.php"); ?></footer>
    </body>
</html>