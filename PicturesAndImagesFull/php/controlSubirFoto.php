<?php
    // Conectar con BD
    require_once( "conexionBD.php");
    // Importar validaciones
    require_once( "validaciones.php" );
    require_once( "controlArchivos.php" );

    // Comprobación de existencia y validez de datos obligatorios
    // $hayFoto = !empty( $_POST['foto'] );
    /// Álbum:       Existir en la base de datos
    $hayAlbum       = validarExistencia( $_POST['album'] , "albumes" , $conexion );
    /// Título:      Longitud máxima de 50 caracteres
    $hayTitulo      = ( !empty( $_POST['titulo'] ) && strlen( $_POST['alternativo'] ) <= 50 );
    /// Alternativo: Longitud mínima de 10 caracteres, máxima de 255 y no puede empezar por "Foto" o "Imagen"
    $hayAlternativo = ( !empty( $_POST['alternativo'] )                                  &&
                        strlen( $_POST['alternativo'] ) >= 10                            &&
                        strlen( $_POST['alternativo'] ) <= 255                           &&
                        strtoupper( substr( $_POST['alternativo'] , 0 , 4 ) ) !== "FOTO" &&
                        strtoupper( substr( $_POST['alternativo'] , 0 , 6 ) ) !== "IMAGEN" );
    $pFichero;
    if( strcmp( $_FILES['pfp']['name'] , "" ) !== 0 ){
        $resultado = subir( "img/pic/" );
        if( $resultado != null ) $pFichero = $resultado;
    }

    if( $hayAlbum && $hayTitulo && $hayAlternativo && isset( $pFichero ) ){ // && $hayFoto
        // Almacenar datos recibidos
        // $pFoto = $hayFoto ? : ;
        $pAlbum       = $_POST['album'];
        $pTitulo      = $_POST['titulo'];
        $pAlternativo = $_POST['alternativo'];

        $pDescripcion = ( !empty( $_POST['descripcion'] ) &&  strlen( $_POST['alternativo'] ) <= 254 ) ?
                        $_POST['descripcion'] : "Sin descripción" ;
        $pFecha       = ( validarFechaCompleta( $_POST['fecha'] ) === 1 ) ?
                        str_replace( "T" , " " , $_POST['fecha'] ) : date( "Y-m-d H:i:s" ) ;
        $pPais        = validarExistencia( $_POST['pais'] , "paises" , $conexion ) ? intval( $_POST['pais'] ) : 1 ;

        // Inserción preparada para evitar inyección SQL
        $sentencia = 'INSERT INTO fotos ( album , titulo , alternativo , descripcion ,
                      fecha , pais , fichero ,
                      fRegistro ) VALUES
                      ( ? , ? , ? , ? ,
                      STR_TO_DATE( ? , "%Y-%m-%d %H:%i:%s" ) , ? , ? ,
                      STR_TO_DATE( "'.date( "Y-m-d H:i:s" ).'" , "%Y-%m-%d %H:%i:%s" ) )';

        $insercion = @mysqli_prepare( $conexion , $sentencia ) OR die( "No se ha podido preparar la inserción." );
        
        // Se sutituyen los parámetros y se ejecuta la sentencia
        @mysqli_stmt_bind_param( $insercion , "issssis" , $pAlbum , $pTitulo , $pAlternativo , $pDescripcion , $pFecha , $pPais , $pFichero );
        
        if( @mysqli_execute( $insercion ) ) header( "Location: ../foto.php?foto=" . @mysqli_insert_id( $conexion ) );
        else                                header( "Location: ../index.php?msg=7" );
    }
    else header( "Location: ../index.php?msg=7" ); // <- Se asume que aquí un usuario ha intentado
                                                   // enviar datos erróneos desconectando las
                                                   // validaciones HTML y JS
?>