<?php
    // LAS VALIDACIONES POR PARÁMETRO DEVUELVEN 0 o 1, NO true O false

    // ======== Validaciones por parámetro ========

    function validarNombreUsuario( $nombre ){
        // ( 3-15 caracteres ASCII y números, no puede comenzar con un número )
        if( !empty( $nombre ) ) return preg_match( '/^[a-zA-Z]{1}[a-zA-Z0-9]{2,14}$/' , $nombre );
        return 0;
    }

    function validarContrasena( $contrasena ){
        // ( 6-15 caracteres ASCII y números, guión y barra baja, debe contener 1 minúscula, 1 mayúscula y 1 número )
        if( !empty( $contrasena ) ) return preg_match( '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9_-]{6,15}$/' , $contrasena );
        return 0;
    }

    function validarEmail( $email ){
        // ( 1-64 caracteres ASCII, números y símbolos -> 1 @ -> 1-255 caracteres ASCII, números, punto y guión )
        if( preg_match( '/^[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~.-]{1,64}[@]{1}[a-zA-Z0-9.-]{1,255}$/' , $email ) === 1 ){
            $emailFiltrado = explode( '@' , $email );

            $parteLocal = $emailFiltrado[0];
            $dominio = $emailFiltrado[1];

            // Comprueba la distribución del punto en parte-local (ni al principio, ni al final, ni dos o más seguidos)
            if ( $parteLocal[0] != '.' && $parteLocal[ strlen( $parteLocal ) - 1 ] != '.' && preg_match( '/[.]{2,}/' , $parteLocal ) !== 1 ) {
                // Separa los subdominios y comprueba que ninguno supere los 63 caracteres
                $subdominios = explode( '.' , $dominio );
                
                for ( $i = 0 ; $i < count( $subdominios ) ; $i++ ) {
                    if ( strlen( $subdominios[$i] ) < 1 || strlen( $subdominios[$i] ) > 63 ) return 0;
                }

                return 1;
            }
        }
        return 0;
    }

    function validarColorHex( $color ){
        if( !empty( $color ) ) return preg_match( '/^[#]{1}[a-fA-F0-9]{6}$/' , $color );
        return 0;
    }

    // Valida una fecha a partir de un string con formato "YYYY-MM-DD"
    function validarFecha( $datos ){
        if( !empty( $datos ) ){
            $fecha = explode( '-' , $datos );

            if( count( $fecha ) === 3 ){
                if( checkdate( $fecha[1] , $fecha[2] , $fecha[0] ) ) return 1;
                else                                                 return 0;
            }
        return 0;
        }
    }

        // Valida una fecha a partir de un string con formato "DD/MM/YYYY"
        function validarFecha2( $datos ){
            if( !empty( $datos ) ){
                $fecha = explode( '/' , $datos );
                
                if( count( $fecha ) === 3 ){
                    if( checkdate( $fecha[0] , $fecha[1] , $fecha[2] ) ) return 1;
                    else                                                 return 0;
                }
            return 0;
            }
        }

    // Valida una hora a partir de un string con formato "HH:mm(:ss)"
    function validarHora( $datos ){
        if( !empty( $datos ) ){
            $hora = explode( ':' , $datos );

            if( count( $hora ) >= 2 ){
                $segundos = true;
                if( count( $hora ) === 3 ){
                    $nSegundos = intval( $hora[2] );
                    $segundos = ( $nSegundos >= 0 && $nSegundos < 60 );
                }

                $nHoras = intval( $hora[0] );
                $nMinutos = intval( $hora[1] );
                if( $nHoras >= 0 && $nHoras < 24 &&
                    $nMinutos >= 0 && $nMinutos < 60 &&
                    $segundos ) return 1;
                else            return 0;
            }
        return 0;
        }
    }

    // Valida una fecha y hora a partir de un string con formato "YYYY-MM-DD HH:mm(:ss)"
    function validarFechaCompleta( $datos ){
        if( !empty( $datos ) ){
            // Cambia el separador T por espacio en caso de que la fecha venga en ese formato
            $datos = str_replace( "T" , " " , $datos );

            $fechaYHora = explode( ' ' , $datos );

            if( count( $fechaYHora ) === 2 &&
                validarFecha( $fechaYHora[0] ) === 1 &&
                validarHora(  $fechaYHora[1] ) === 1 ) return 1;
            else                                       return 0;
        }
        return 0;
    }

    // ============================================

    // ======== Validaciones de existencia en la base de datos ========

    // Comprueba que un elemento con un id en una tabla existe
    function validarExistencia( $id , $tabla , $conn ){
        if( mysqli_num_rows( mysqli_query( $conn ,
         "SELECT id FROM $tabla WHERE id = " . intval( $id ) ) ) === 1 ) return true;
        else                                                             return false;
    }
    //comprueba que si existe un elemento en funcion d un valor, si existe devuelve 0 ya q se busca q no exista
    function validarExistenciaComplejo(  $valor , $tabla , $columna , $conn ){
        echo 'entre';
        if( !empty( $valor ) ){
            // Sentencia preparada al ser cadenas
            $sentencia = "SELECT  $columna FROM $tabla WHERE $columna = ?";
            $seleccion = @mysqli_prepare( $conn , $sentencia ) OR die( "No se ha podido preparar la selección." );
            @mysqli_stmt_bind_param( $seleccion , "s" , $valor );

            @mysqli_execute( $seleccion );
            $respuesta = @mysqli_stmt_get_result( $seleccion );
            
            if( mysqli_num_rows( $respuesta ) === 1 ) return 0;
            else                                       return 1;
        }
        return 0;
    }

    // Comprueba que un elemento con un valor en una columna de una tabla existe
    // y devuelve el ID asociado
    function validarExistenciaPorValor( $valor , $tabla , $columna , $conn ){
        if( !empty( $valor ) ){
            // Sentencia preparada al ser cadenas
            $sentencia = "SELECT id, $columna FROM $tabla WHERE $columna = ?";
            $seleccion = @mysqli_prepare( $conn , $sentencia ) OR die( "No se ha podido preparar la selección." );
            @mysqli_stmt_bind_param( $seleccion , "s" , $valor );

            @mysqli_execute( $seleccion );
            $respuesta = @mysqli_stmt_get_result( $seleccion );
            
            if( mysqli_num_rows( $respuesta ) === 1 ) return mysqli_fetch_row( $respuesta )[0];
        }
        return 0;
    }

    // ================================================================
?>