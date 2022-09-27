<?php
    require_once("php/conexionBD.php");
    require_once("php/funciones.php");
    // Impide acceder como usuario autenticado
    if( estaAutenticado() ) header( "Location: index.php?msg=6" );
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <?php
            $titulo = "Registro";
            $descripcion = "Crea una cuenta nueva.";

            require_once("php/head.php");
        ?>
    </head>
    <body>
        <header><?php require_once("php/cabecera.php"); ?></header>
        <div>
            <main>
                <!--onchange="comprobarCampoRegistro(this);"-->
                <section> <!--Formulario de registro-->
                    <h1>Registro</h1>
                    <span>¿Ya tienes una cuenta? <a href="acceso.php">Inicia sesión</a></span>
                    <header>
                        Estar registrado te permite <strong>subir</strong> y <strong>visualizar</strong> fotos en detalle.
                    </header>
                    <form method="POST"   action="registroDatos.php" enctype="multipart/form-data">
                        <label for="usr">Nombre de usuario *</label>
                        <input id="usr" name="usuario" type="text"  autofocus>

                        <label for="pwd">Contraseña *</label>
                        <input id="pwd" name="password" type="password" >

                        <label for="pwd2">Repite contraseña *</label>
                        <input id="pwd2" name="password2" type="password" >

                        <label for="eml">Correo electrónico *</label>
                        <input id="eml" name="email" type="text" >

                        <label for="sexo">Sexo *</label>
                        <select id="sex" name="sexo">
                            <option value="">---</option>
                            <option value="H">Hombre</option>
                            <option value="M">Mujer</option>
                            <option value="X">Otro/No especifica</option>
                        </select>

                        <label for="bdt">Fecha de nacimiento (DD/MM/YYYY) *</label>
                        <input id="bdt" name="fechaNac" type="text" >

                        <fieldset>
                            <legend>Lugar de residencia</legend>
                            <label for="cdd">Ciudad</label>
                            <input id="cdd" name="ciudad" type="text" placeholder="Ciudad">
                            <label for="cnt">País</label>
                            <select id="cnt" name="pais">
                                <option value="">---</option>
                                <?php crearListaPaises( $conexion ); ?>
                            </select>
                        </fieldset>

                        <figure style="text-align:center;">
                            <label for="pfp" id="pfpreview"><img src="img/perfil.jpg" alt="Foto de perfil"></label>
                            <input id="pfp" name="pfp" type="file"  accept="image/*">
                        </figure>
                        
                        <span class="requerido">* Campo requerido</span>

                        <button class="boton" type="submit">Regístrate</button>
                        <button class="boton" type="reset">Limpiar</button>
                    </form>
                </section>
            </main>
        </div>
        <footer><?php require_once("php/pie.php"); ?></footer>
    </body>
</html>