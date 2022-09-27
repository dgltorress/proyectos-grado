<?php
    function nuevoAlbumListado( $datos ){
        if( is_array( $datos ) && count( $datos ) == 4 ){
            $enlace = '<a href="album.php?id='.$datos['id'].'">';
            echo
            '<tr>
                <td>'.$enlace.$datos['titulo'].'</a></td>
                <td>'.$enlace.$datos['descripcion'].'</a></td>
                <td>'.$enlace.$datos['nFotos'].'</a></td>
            </tr>';
        }
        else echo '<tr>
                    <td></td>
                    <td>DATOS ERRÓNEOS</td>
                    <td></td>
                   </tr>';
    }
    function Total($datos){
        if( is_array( $datos ) && count( $datos ) == 1 ){
            
            echo $datos['Total'] ;
        }
        else echo 'DATOS ERRÓNEOS';
    }
?>