<?php
    require_once("php/funciones.php");
    $autenticado = estaAutenticado();
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <?php
            $titulo = "Declaración de accesibilidad";
            $descripcion = "Declaración de accesibilidad del sitio.";

            require_once("php/head.php");
        ?>
    </head>
    <body>
        <header><?php elegirCabecera( $autenticado ); ?></header>
        <div>
            <main>
                <section class="accesibilidad"> <!--accsibilidad-->
                    <h1 >Declaración de Accesibilidad</h1>
                        <h2 >Introducción a la accesibilidad digital</h2>
                            <p>La accesibilidad es el grado en el que todas las personas pueden percibir, comprender y navegar por la información contenida en un documento digital independientemente de sus capacidades técnicas, cognitivas o físicas.</p>
                        <h2 >Declaración de accesibilidad de PI - Pictures &amp; Images</h2>
                            <p>PI se ha comprometido a hacer accesible su sitio web en la mayor medida que sea posible. La presente declaración de accesibilidad se aplica a las páginas web del dominio https://testhsdufhwef34.000webhostapp.com.</p>
                        <h2>Situación de Cumplimiento</h2>
                            <h3>Colores</h3>
                                <p>Por defecto, la página obtiene un a puntuación de 20.47, por lo que superamos con creces el requisito AAA(enhanced) en el área de contraste. Por ende, hemos añadido como alternativa un estilo de bajo contraste para aquellos a quienes pueda mejorar la accesibilidad de la página.</p>
                            <h3>Etiquetado semántico</h3>
                                <p>Se ha realizado uso de las siguientes etiquetas con significado semántico en toda ocasión donde se ha visto necesario:
                                    <ul style="list-style:square;margin-left:3em;">
                                        <li>Una etiqueta &lt;header&gt; para contener el menú de navegación y la barra de búsqueda.</li>
                                        <li>&lt;main&gt; para la sección principal de la página, &lt;aside&gt; para la barra lateral.</li>
                                        <li>&lt;section&gt; y &lt;article&gt; para estructurar el contenido de la página.</li>
                                        <li>Un &lt;footer&gt; que contiene datos de los creadores a sus redes sociales y a esta página.</li>
                                    </ul>
                                </p>
                                <p>Además, usamos la etiqueta &lt;time&gt; para marcar fechas con el atributo <em>datetime</em>.</p>
                            <h3>Texto alternativo de imágenes</h3>
                                <p>Todas las imágenes que se han incluido en la página son indispensables para su correcto funcionamiento. Además, en todas las etiquetas &lt;img&gt; usadas se ha utilizado el atributo <em>alt</em> para definir una pequeña descripción en caso de que no se cargasen a la hora de visualizar la página o para lectores de pantalla.</p>
                            <h3>Cómo activar hoja de estilo accesible</h3>
                                <p>La página consta de 3 estilos alternativos generales: de bajo contraste, tamaño de fuente grande y tamaño de fuente muy grande. De estos se derivan otras dos combinaciones de cada estilo que modifica la fuente con el estilo de bajo contraste. Para activarlo en Firefox sigue los siguientes pasos:</p>
                                    <p style="font-size: 1.2em;"><strong>Tecla F10 &gt;&gt; Ver &gt;&gt; Estilo de página &gt;&gt; [Nombre del estilo]</strong></p>
                </section>
            </main>
        </div>
    <footer><?php require_once("php/pie.php"); ?></footer>
    </body>
</html>