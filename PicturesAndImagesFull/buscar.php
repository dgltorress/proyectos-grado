<?php
    require_once("php/conexionBD.php");
    require_once("php/funciones.php");

    $autenticado = estaAutenticado();
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <?php
            $titulo = "Buscar";
            $descripcion = "Formulario de búsqueda.";

            require_once("php/head.php");
        ?>
    </head>
    <body>
        <header><?php elegirCabecera( $autenticado ); ?></header>
        <div>
            <main>
                <section> <!--Filtros-->
                    <h1>Búsqueda avanzada</h1>
                    <form id="formBuscar" method="GET" action="resultado.php">    
                        <fieldset >
                            <legend>Filtrar</legend>
                            <label for="ttl">Título</label>
                            <input type="text" id="ttl" name="titulo">
                            <label for="day">Fecha</label>
                            <input type="date" id="day" name="fecha">
                            <label for="cnt">País</label>
                            <select id="cnt" name="pais">
                                <option value="">---</option>
                                <?php crearListaPaises( $conexion ); ?>
                            </select>
                        </fieldset>
                        <button class="boton" type="submit">Buscar</button>
                        <button class="boton" type="reset">Limpiar</button>
                    </form>
                    <a href="no_resultado.php">Buscar error</a>
                </section>
            </main>
        </div>
        <footer><?php require_once("php/pie.php"); ?></footer>
    </body>
</html>