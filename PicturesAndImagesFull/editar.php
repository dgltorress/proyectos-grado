<?php
    require_once("php/funciones.php");
    if( !estaAutenticado() ) header( "Location: index.php?msg=5" );

    require_once("php/conexionBD.php");
    require_once("php/articulo.php");


    $usuarios = @mysqli_query( $conexion ,
    "SELECT * FROM usuarios
    WHERE nombre = '".$_GET['usr']."'" ) OR die( "No se ha podido realizar la consulta." );
    $usuario = @mysqli_fetch_assoc( $usuarios );
    @mysqli_free_result( $usuarios );

?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <?php
        
            $titulo = "Mis datos";
            $descripcion = "Datos de tu cuenta.";

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
        <header><?php require_once("php/cabecera_registro.php"); ?></header>
        <div>
        <main>
                <!--onchange="comprobarCampoRegistro(this);"-->
                <section> <!--Formulario de registro-->
                    <h1>Registro</h1>
                    <span>¿Ya tienes una cuenta? <a href="acceso.php">Inicia sesión</a></span>
                    <header>
                        Estar registrado te permite <strong>subir</strong> y <strong>visualizar</strong> fotos en detalle.
                    </header>
                    <form method="POST"   action="editarDatos.php" enctype="multipart/form-data">
                        <label for="usr">Nombre de usuario *</label>
                        <input id="usr" name="usuario" type="text" value="<?php echo $usuario['nombre']?>" autofocus>

                        <input id="usrOG" name="usrOG" type ='hidden' value="<?php echo $usuario['nombre']?>">

                        <label for="pwd">Nueva contraseña</label>
                        <input id="pwd" name="password" type="password" value="">

                        <label for="pwd2">Repite nueva contraseña</label>
                        <input id="pwd2" name="password2" type="password" value="">

                        <label for="eml">Correo electrónico *</label>
                        <input id="eml" name="email" type="text" value="<?php echo $usuario['email']?>">

                        <label for="sexo">Sexo *</label>
                        <select id="sex" name="sexo" >
                            <option value="">---</option>
                            <option value="H" <?php if ($usuario['sexo'] == '0') echo ' selected="selected"'; ?>>Hombre</option>
                            <option value="M" <?php if ($usuario['sexo'] == '1') echo ' selected="selected"'; ?>>Mujer</option>
                            <option value="X" <?php if ($usuario['sexo'] == '2') echo ' selected="selected"'; ?>>Otro/No especifica</option>
                        </select>

                        <label for="bdt">Fecha de nacimiento (YYYY-MM-DD) *</label>
                        <input id="bdt" name="fechaNac" type="text" value="<?php echo $usuario['fNacimiento']?>" >

                        <fieldset>
                            <legend>Lugar de residencia</legend>
                            <label for="cdd">Ciudad</label>
                            <input id="cdd" name="ciudad" type="text" placeholder="Ciudad" value="<?php echo $usuario['ciudad']?>">
                            <label for="cnt">País</label>
                            <select id="cnt" name="pais">
                                <option value="">---</option>
                                <?php
                                    $paises = obtenerPaises( $conexion );
                                    while( $pais = @mysqli_fetch_row( $paises ) ){
                                        $opcion='<option value="'.$pais[0].'" ';
                                        if ($usuario['pais'] == $pais[0]){
                                            $opcion= $opcion .' selected="selected"';
                                        }
                                        $opcion= $opcion .'>'.$pais[1].'</option>';
                                        echo $opcion;
                                    } 
                                ?>
                            </select>
                        </fieldset>

                        <figure style="text-align:center;">
                            <label for="pfp" id="pfpreview"><img src="<?php echo $usuario['foto']?>" alt="Foto de perfil"></label>
                            <input id="pfp" name="foto" type="file" accept="image/*" >
                        </figure>

                        <label for="del">Borrar foto de perfil</label>
                        <input id="del" name="delete" type="checkbox" >
                        
                        <span class="requerido">* Campo requerido</span>

                        <button class="boton" type="submit">Confirmar</button>
                        <button class="boton" type="reset">Limpiar</button>
                    </form>
                </section>
            </main>
        </div>
        <footer><?php require_once("php/pie.php"); ?></footer>
    </body>
</html>