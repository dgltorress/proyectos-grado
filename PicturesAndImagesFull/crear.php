<?php
    require_once("php/funciones.php");
    // Impide acceder como invitado
    if( !estaAutenticado() ) header( "Location: index.php?msg=5" );
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <?php
            $titulo = "Crear álbum";
            $descripcion = "Crea tu propio álbum.";

            require_once("php/head.php");
        ?>
        <style>
            section>form{
                width:50% !important;
                margin-top:2em !important;
            }
            input,textarea{
                font-size:16px !important;
            }
        </style>
    </head>
    <body>
        <header><?php require_once("php/cabecera_registro.php"); ?></header>
        <div>
            <main>
                <section> <!--Formulario de acceso-->
                    <h1>Crear álbum</h1>
                    <span>Expande aún más tu <a href="albumes.php?usr=<?= $_GET['usr'] ?>">colección de álbumes</a>.</span>
                    <form method="POST" action="php/controlCrear.php">
                        <label for="ttl">Título *</label>
                        <input id="ttl" name="titulo" type="text" placeholder="Un álbum..." autofocus> <!-- required -->

                        <label for="desc">Descripción</label>
                        <textarea id="desc" name="descripcion" placeholder="Describe tu álbum..." style="font-size:13px;" rows="5"></textarea>

                        <input type="hidden" name="usuario" value="<?= $_GET['usr'] ?>">

                        <button class="boton" type="submit">Crear álbum</button>
                        <button class="boton" type="reset">Limpiar</button>
                    </form>
                </section>
            </main>
        </div>
        <footer><?php require_once("php/pie.php"); ?></footer>
    </body>
</html>