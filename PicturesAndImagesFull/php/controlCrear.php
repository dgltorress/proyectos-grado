<?php
    // Conectar con BD
    require_once( "conexionBD.php");
    // Importar validaciones
    require_once( "validaciones.php");

    // Comprobación de existencia y validez de datos obligatorios
    /// Título: Longitud máxima de 50 caracteres
    $hayTitulo = ( !empty( $_POST['titulo'] ) && strlen( $_POST['alternativo'] ) <= 50 );
    /// Usuario: Existe en la base de datos
    $pUsuario = validarExistenciaPorValor( $_POST['usuario'] , "usuarios" , "nombre" , $conexion );
    $hayUsuario = ( $pUsuario > 0 );

    if( $hayTitulo && $hayUsuario ){
        // Almacenar datos recibidos
        $pTitulo  = $_POST['titulo'];

        $pDescripcion = ( !empty( $_POST['descripcion'] ) &&  strlen( $_POST['alternativo'] ) <= 254 ) ?
                        $_POST['descripcion'] : "Sin descripción" ;

        // Inserción preparada para evitar inyección SQL
        $sentencia = 'INSERT INTO albumes ( titulo , descripcion , usuario ) VALUES ( ? , ? , ? )';
        $insercion = @mysqli_prepare( $conexion , $sentencia ) OR die( "No se ha podido preparar la inserción." );
        
        // Se sutituyen los parámetros y se ejecuta la sentencia
        @mysqli_stmt_bind_param( $insercion , "ssi" , $pTitulo , $pDescripcion , $pUsuario );
        
        if( @mysqli_execute( $insercion ) ) header( "Location: ../album.php?id=" . @mysqli_insert_id( $conexion ) . "&msg=9" );
        else                                header( "Location: ../index.php?msg=7" );
    }
    else header( "Location: ../index.php?msg=7" . $pUsuario . $_POST['usuario'] );
?>