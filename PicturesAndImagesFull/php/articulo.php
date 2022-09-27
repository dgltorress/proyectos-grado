<?php
    function nuevoArticulo( $datos , $autenticado ){
        if( is_array( $datos ) && count( $datos ) == 9 ){
            echo
            '<article class="fotoReciente">
                <header>
                    <a href="'.($autenticado ? 'foto.php?foto='.$datos['idFoto'] : "index.php?msg=5").'">
                        <h2 title="'.$datos['titulo'].'">'.$datos['titulo'].'</h2>
                    </a>
                    <span class="autoria">Por&nbsp;
                        <a href="usuario.php?id='.$datos['idAutor'].'">
                            <img class="miniaturaPerfil" src="'.$datos['pfp'].'" alt="Foto de perfil de '.$datos['autor'].'">&nbsp;'.$datos['autor'].'
                        </a>
                        &nbsp;el&nbsp;<time title="'.parsearFechaCompleta( $datos['fRegistro'] ).'"
                            datetime="'.$datos['fRegistro'].'">'.parsearFechaCompleta( $datos['fRegistro'] ).'</time>
                    </span>
                </header>
                <a href="'.($autenticado ? 'foto.php?foto='.$datos['idFoto'] : "index.php?msg=5").'" class="figura">
                    <figure>
                        <img src="'.$datos['url'].'" alt="'.$datos['alternativo'].'">
                    </figure>
                </a>
                <footer>
                    <span><span class="icon icon-location"></span>'.$datos['pais'].'</span>
                    <button class="boton" type="button"><span class="icon icon-export"></span></button>
                </footer>
            </article>';
        }
        else echo '<article class="fotoReciente">ARTÍCULO ERRÓNEO</article>';
    }

    function nuevoArticuloSimple( $datos ){
        if( is_array( $datos ) && count( $datos ) == 6 ){
            echo
            '<article class="fotoReciente">
                <header>
                    <a href="foto.php?foto='.$datos['id'].'">
                        <h2 title="'.$datos['titulo'].'">'.$datos['titulo'].'</h2>
                    </a>
                    <span class="autoria">
                        <time title="'.parsearFechaCompleta( $datos['fRegistro'] ).'"
                              datetime="'.$datos['fRegistro'].'">'.parsearFechaCompleta( $datos['fRegistro'] ).'</time>
                    </span>
                </header>
                <a href="foto.php?foto='.$datos['id'].'" class="figura">
                    <figure>
                        <img src="'.$datos['url'].'" alt="'.$datos['alternativo'].'">
                    </figure>
                </a>
                <footer>
                    <span><span class="icon icon-location"></span>'.$datos['pais'].'</span>
                    <button class="boton" type="button"><span class="icon icon-export"></span></button>
                </footer>
            </article>';
        }
        else echo '<article class="fotoReciente">ARTÍCULO SIMPLE ERRÓNEO</article>';
    }

    function nuevoArticuloSimpleAlbum( $datos ){
        if( is_array( $datos ) && count( $datos ) == 7 ){
            echo
            '<article class="fotoReciente">
                <header>
                    <a href="foto.php?foto='.$datos['idFoto'].'">
                        <h2 title="'.$datos['tituloFoto'].'">'.$datos['tituloFoto'].'</h2>
                    </a>
                    <span class="autoria">
                        Álbum:<a href="album.php?id='.$datos['idAlbum'].'">'.$datos['tituloAlbum'].'</a>
                    </span>
                </header>
                <a href="foto.php?foto='.$datos['idFoto'].'" class="figura">
                    <figure>
                        <img src="'.$datos['url'].'" alt="'.$datos['alternativo'].'">
                    </figure>
                </a>
                <footer>
                    <span><span class="icon icon-location"></span>'.$datos['pais'].'</span>
                    <button class="boton" type="button"><span class="icon icon-export"></span></button>
                </footer>
            </article>';
        }
        else echo '<article class="fotoReciente">ARTÍCULO SIMPLE ERRÓNEO</article>';
    }

    function nuevoArticuloFoto( $datos ){
        if( is_array( $datos ) && count( $datos ) == 12 ){
            echo
            '<article id="ArtFoto">
            <header>
                <h2 title="'.$datos['tituloFoto'].'">'.$datos['tituloFoto'].'</h2>
            </header>
            <figure>
                <img src="'.$datos['url'].'" alt="'.$datos['alternativo'].'">
            </figure>
            <p class="ForzarP_Medio">
                <time datetime="'.$datos['fechaFoto'].'">'.parsearFechaCompleta( $datos['fechaFoto'] ).'</time>
                <span>'.$datos['pais'].'</span>
            </p>
            <h3>Descripción</h3>
                <p>'.$datos['descripcionFoto'].'</p>
            <h3>Álbumes</h3>
                <p><a href="album.php?id='.$datos['idAlbum'].'">Álbum: '.$datos['tituloAlbum'].'</a></p>
            <h3>Autor</h3>
                <p>
                    <span class="autoria">Foto subida por&nbsp;
                        <a href="usuario.php?id='.$datos['idAutor'].'">
                            <img class="miniaturaPerfil" src="'.$datos['pfp'].'" alt="Foto de perfil de '.$datos['Autor'].'">&nbsp;'.$datos['Autor'].'
                        </a>
                    </span>
                </p>
            <footer>
            </footer>
        </article>';
        }
        else echo '<article class="fotoReciente">ARTÍCULO SIMPLE ERRÓNEO</article>';
    }

    function nuevoEstilo( $datos ){
        if( is_array( $datos ) && count( $datos ) == 4 ){
            echo
            '<dt >'.$datos['nombre'].'</dt>
            <dd>Descripcion: <a href =php/controlEstilo.php?estilo='.$datos['id'].' >'.$datos['descripcion'].'</a></dd>';
        }
        else echo '<article class="fotoReciente">ESTILO ERRÓNEO</article>';
    }

    function listaAlbums($datos){
        if( is_array( $datos ) && count( $datos ) == 4 ){
            echo
            '<a href="album.php?id='.$datos['id'].'">'.$datos['titulo'].'</a>';
        }
        else echo '<article class="fotoReciente">ESTILO ERRÓNEO</article>';
    }
?>