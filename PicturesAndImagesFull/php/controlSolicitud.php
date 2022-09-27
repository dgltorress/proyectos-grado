<?php
    // Conectar con BD
    require_once( "conexionBD.php");

    $datos = unserialize( $_POST['datos'] );

    if( !empty( $datos ) ){
        // Importar validaciones
        require_once( "validaciones.php" );
        // Importar funciones
        require_once( "funciones.php" );

        // Comprobación de existencia y validez de datos obligatorios
        /// ÁLBUM: Existir en la base de datos
        $hayAlbum     = validarExistencia( $datos['album'] , "albumes" , $conexion );
        /// NOMBRE: Longitud máxima de 75 caracteres
        $hayNombre    = ( !empty( $datos['nombre'] ) && strlen( $datos['nombre'] ) <= 75 );
        /// APELLIDOS: Longitud máxima de 125 caracteres
        $hayApellidos = ( !empty( $datos['apellidos'] ) && strlen( $datos['apellidos'] ) <= 125 );
        /// TÍTULO: Longitud máxima de 200 caracteres
        $hayTitulo    = ( !empty( $datos['titulo'] ) && strlen( $datos['titulo'] ) <= 200 );
        /// EMAIL: Email válido y longitud máxima de 200 caracteres (Un email válido puede ocupar más)
        $hayEmail     = ( validarEmail( $datos['correo'] ) === 1 && strlen( $datos['correo'] ) <= 200 );
        /// DIRECCIÓN: Longitud máxima de 200 caracteres,
        $hayDireccion = ( validarExistencia( $datos['pais'] , "paises" , $conexion ) && // País en la base de datos
                          intval( $datos['pais'] ) > 1 && // id de País superior a 1
                          !empty( $datos['provincia'] ) && !empty( $datos['localidad'] ) && // Provincia y localidad tienen valor
                          !empty( $datos['postal'] ) && strlen( $datos['postal'] ) <= 10 && // Código postal existe y tiene hasta 10 caracteres
                          !empty( $datos['calle'] ) && // Calle existe
                          intval( $datos['numero'] ) > 0 && intval( $datos['numero'] ) < 10000 && // Número existe y tiene entre 1 y 4 dígitos
                          intval( $datos['piso'] ) > 0 && intval( $datos['piso'] ) < 1000 && // Piso existe y tiene entre 1 y 3 dígitos
                          !empty( $datos['puerta'] ) && strlen( $datos['puerta'] ) <= 10 ); // Puerta existe y tiene 10 o menos caracteres

        // Se concatenan los datos de dirección separados por punto y coma (;) y se comprueba la longitud
        $sprtr = ";";
        $pDireccion = intval( $datos['pais'] ) . $sprtr .
                      $datos['provincia'] . $sprtr .
                      $datos['localidad'] . $sprtr .
                      $datos['postal'] . $sprtr .
                      $datos['calle'] . $sprtr .
                      intval( $datos['numero'] ) . $sprtr .
                      intval( $datos['piso'] ) . $sprtr .
                      $datos['puerta'];
        if( strlen( $pDireccion ) > 200 ) $hayDireccion = false;

        // Datos obligatorios válidos
        if( $hayDireccion && $hayAlbum && $hayNombre && $hayApellidos && $hayTitulo && $hayEmail ){
            // Almacenar datos recibidos (excepto dirección)
            $pAlbum     = $datos['album'];
            $pNombre    = $datos['nombre'];
            $pApellidos = $datos['apellidos'];
            $pTitulo    = $datos['titulo'];
            $pEmail     = $datos['correo'];

            $pDescripcion = ( !empty( $datos['descripcion'] ) && strlen( $datos['descripcion'] ) <= 4000 ) ?
                            $datos['descripcion'] : "";
            $pColor       = validarColorHex( $datos['color'] ) ? $datos['color'] : "#000000" ;
            $pCopias      = ( intval( $datos['copias'] ) > 0 ) ? intval( $datos['copias'] ) : 1 ;
            $pResolucion  = in_array( $datos['resolucion'] , ["150","300","450","600","750","900"] , true ) ?
                            intval( $datos['resolucion'] ) : 150 ;
            $pFecha       = ( validarFecha( $datos['fecha'] ) === 1 ) ?
                            $datos['fecha'] : date( "Y-m-d H:i:s" ) ;
            $pIColor      = isset( $datos['paleta'] ) ? 1 : 0 ;

            $nFotos   = fotosEnAlbum( $pAlbum , $conexion );
            $nPaginas = intdiv( $nFotos - 1 , 3 ) + 1;
            $pCoste   = calcularPrecio( $nPaginas , $nFotos , ( $pIColor === 1 ) , $pResolucion , $pCopias , 2.5 );

            // Inserción preparada para evitar inyección SQL
            $sentencia = 'INSERT INTO solicitudes ( album , nombre , apellidos , titulo , email , descripcion ,
                        direccion , color , copias , resolucion , fecha , iColor ,
                        fRegistro , coste ) VALUES
                        ( ? , ? , ? , ? , ? , ? ,
                        ? , ? , ? , ? , STR_TO_DATE( ? , "%Y-%m-%d %H:%i:%s" ) , ? ,
                        STR_TO_DATE( "'.date( "Y-m-d H:i:s" ).'" , "%Y-%m-%d %H:%i:%s" ) , ? );';

            $insercion = @mysqli_prepare( $conexion , $sentencia ) OR die( "No se ha podido preparar la inserción." );

            // Se sutituyen los parámetros y se ejecuta la sentencia
            @mysqli_stmt_bind_param( $insercion , "isssssssiisid" ,
            $pAlbum , $pNombre , $pApellidos , $pTitulo , $pEmail , $pDescripcion ,
            $pDireccion , $pColor , $pCopias , $pResolucion , $pFecha , $pIColor , $pCoste );

            $exito = @mysqli_execute( $insercion );
            unset( $_SESSION['datosSolicitud'] );

            if( $exito ) header( "Location: ../index.php?msg=8" );
            else         header( "Location: ../index.php?msg=7" );
        }
        else{
            unset( $_SESSION['datosSolicitud'] );
            header( "Location: ../index.php?msg=7" );
        }
    }
    else{
        unset( $_SESSION['datosSolicitud'] );
        header( "Location: ../index.php?msg=7" );
    }
?>