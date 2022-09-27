<?php
    $dominio    = "localhost:3306";
    $usuario    = "piadmin";
    $contrasena = "clave";
    $base       = "pibd";

    $conexion = @mysqli_connect( $dominio , $usuario , $contrasena , $base ); // "@" desactiva warnings
    if( !$conexion ) die( "No se ha podido conectar con la base de datos." );

    // Crean un select ordenado con los países disponibles
    function obtenerPaises( $conn ){
        $paises = @mysqli_query( $conn , "SELECT id,nombre FROM paises WHERE id > 1 ORDER BY nombre ASC" ) OR die( "No se ha podido realizar la consulta." );
        return $paises;
    }

    function crearListaPaises( $conn ){
        $paises = obtenerPaises( $conn );
        while( $pais = @mysqli_fetch_row( $paises ) ) echo '<option value="'.$pais[0].'">'.$pais[1].'</option>';
    }

    // Devuelve el título de un álbum
    function tituloAlbum( $identificador , $conn ){
        $id = intval( $identificador );

        if( $id > 0 ){
            $sentencia = "SELECT titulo FROM albumes WHERE albumes.id = $id";
            
            if( $album = @mysqli_fetch_row( @mysqli_query( $conn , $sentencia ) ) ) return $album[0];
            else                                                                    return -1;
        }
    }

    // Devuelve la cantidad de fotos en un álbum
    function fotosEnAlbum( $identificador , $conn ){
        $id = intval( $identificador );

        if( $id > 0 ){
            $sentencia = "SELECT COUNT(f.id) FROM albumes AS a
                          LEFT JOIN fotos AS f ON a.id = f.album
                          JOIN usuarios AS u ON u.id = a.usuario
                          WHERE a.id = $id
                          GROUP BY a.id";
            
            if( $pais = @mysqli_fetch_row( @mysqli_query( $conn , $sentencia ) ) ) return $pais[0];
            else                                                                   return -1;
        }
    }

    
    function cerrarConexion(){
        mysqli_close($conexion);
    }
?>