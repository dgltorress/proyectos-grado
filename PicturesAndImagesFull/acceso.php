<?php

    require_once("php/funciones.php");
    // Impide acceder como usuario autenticado
    if( estaAutenticado() ) header( "Location: index.php?msg=6" );
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <?php
            $titulo = "Iniciar sesión";
            $descripcion = "Formulario de acceso.";

            require_once("php/head.php");
        ?>
        <style>
            form{
                width:40% !important;
            }
        </style> 
    </head>
    <body>
        <header><?php require_once("php/cabecera.php"); ?></header>
        <div>
            <main>
                <section> <!--Formulario de acceso-->
                    <h1>Inciar sesión</h1>
                    <span>¿No tienes una cuenta? <a href="registro.php">Regístrate</a></span>
                    <form method="POST" onsubmit="return comprobarAcceso( this );" action="php/controlAcceso.php">
                        <label for="usr">Nombre de usuario</label>
                        <input id="usr" name="usuario" type="text" autofocus>

                        <label for="pwd">Contraseña</label>
                        <input id="pwd" name="password" type="password">

                        <label class="casilla">
                            <span>Recordar mis datos</span>
                            <input id="rmb" name="recordar" type="checkbox">
                        </label>

                        <button class="boton" type="submit">Inicia sesión</button>
                    </form>
                </section>
            </main>
        </div>
        <footer><?php require_once("php/pie.php"); ?></footer>
    </body>
</html>