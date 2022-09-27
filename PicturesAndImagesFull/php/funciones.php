<?php

session_start(); // NECESARIO PARA ACCEDER A LA VARIABLE _SESSION

// PARSEADORES DE DATOS

function parsearTelefono( $telefono ){
    $tel = str_replace(" ","",$telefono);

    if( strlen( $tel ) == 9 ){
        $tel = substr_replace( $tel , " " , 3  , 0 );
        $tel = substr_replace( $tel , " " , 6  , 0 );
        $tel = substr_replace( $tel , " " , 9  , 0 );
        $tel = substr_replace( $tel , " " , 10 , 0 );

        return $tel;
    }

    else return "FORMATO INCORRECTO";
}

function parsearFecha( $fecha ){
    if( $fecha != null && is_string( $fecha ) ){
        $meses = ["enero" , "febrero" , "marzo" ,
          "abril" , "mayo" , "junio" , "julio" ,
          "agosto" , "septiembre" , "octubre" ,
          "noviembre" , "diciembre"];
    
        $datos = explode( "-" , $fecha );

        if( count( $datos ) == 3 ){
            return ltrim( $datos[2] , "0" ) . " de " . $meses[$datos[1]-1] . " de " . $datos[0];
        }
    }

    return $fecha;
}

function parsearFechaCompleta( $fechaCompleta ){
    if( $fechaCompleta != null && is_string( $fechaCompleta ) ){
        $datos = explode( " " , $fechaCompleta );

        if( count( $datos ) == 2 ){
            return substr_replace( ( parsearFecha( $datos[0] ) . " a las " . $datos[1] ) , "" , -3 );
        }
    }
    return $fechaCompleta;
}

function tiempoLegible( $tiempo ){
    if( !empty( $tiempo ) ){
        $cantidades = explode( ":" , $tiempo );

        if( count( $cantidades ) == 3 ){
            $horas = ""; $minutos = ""; $segundos = "";
            if( intval( $cantidades[0] > 0 ) ) $horas = $cantidades[0] . ' horas ';
            if( intval( $cantidades[1] > 0 ) ) $minutos = $cantidades[1] . ' minutos ';
            if( intval( $cantidades[2] > 0 ) ) $segundos = $cantidades[2] . ' segundos';

            return ltrim( $horas , "0" ) . ltrim( $minutos , "0" ) . ltrim( $segundos , "0" );
        }
    }
    return $tiempo;
}

//----------------------------------------------

// CALCULADORAS DE PRECIOS

function calcularPrecioPaginas( $paginas ){
    if( $paginas >= 0 ){
        $precio = 0;

        for ( $i = 0 ; $i < $paginas ; $i++ ) {
            if      ( $i < 5 )            $precio += 0.10;
            else if ( $i > 5 && $i < 11 ) $precio += 0.08;
            else                          $precio += 0.07;
        }
        return $precio;
    }
    return 0;
}

function calcularPrecioColor( $aColor , $fotos ){
    if( $aColor ){
        if( $fotos >= 0 ) return ( 0.05 * $fotos );
        else              return -1;
    }
    else return 0;
}

function calcularPrecioResolucion( $dpi , $fotos ){
    if( $dpi > 300 ){
        if( $fotos >= 0 ) return ( 0.02 * $fotos );
        else              return -1;
    }
    else return 0;
}

function calcularPrecio( $paginas , $fotos , $aColor , $dpi , $copias , $extra ){
    if( !empty( $paginas ) && !empty( $aColor ) && !empty( $dpi ) ){
        $precio = calcularPrecioPaginas( $paginas );

        if( $aColor )    $precio += ( 0.05 * $fotos );
        if( $dpi > 300 ) $precio += ( 0.02 * $fotos );

        return $precio * intval( $copias ) + floatval( $extra );
    }

    return -1;
}

//----------------------------------------------

// COMPROBACIONES DE INICIO DE SESIÓN

function estaAutenticado(){

    $datos;
    $dominio    = "localhost:3306";
    $usuario    = "piadmin";
    $contrasena = "clave";
    $base       = "pibd";

    $conexion = @mysqli_connect( $dominio , $usuario , $contrasena , $base ); // "@" desactiva warnings
    if( !$conexion ) die( "No se ha podido conectar con la base de datos." );

    // Primero comprueba si hay datos en la sesión y si coinciden con la registrada
    if( !empty( $_SESSION ) && $_COOKIE[ session_name() ] == session_id() ) $datos = json_decode( $_SESSION["credenciales"] ,true);
    // Si falla, se comprueba la cookie
    else if( !empty( $_COOKIE["credenciales"] ) )                           $datos = json_decode( $_COOKIE["credenciales"] ,true );

    if( !empty( $datos ) ){
        $datosUsuarios = @mysqli_query( $conexion ,'SELECT u.nombre,u.clave,u.estilo from usuarios as u where UPPER(u.nombre)= UPPER("'.$datos[0].'") and UPPER(u.clave) = UPPER("'.$datos[1].'") ') 
        OR die( "Ha habido un problema al comprobar usuarios existentes" );
        $display = !empty($datosUsuarios);
        if($display) {
            $Usuario = mysqli_fetch_assoc( $datosUsuarios );
            if(!empty($Usuario)){
                mysqli_free_result( $datosUsuarios );
                mysqli_close($conexion); 
                return true;
            }
            else{
                mysqli_free_result( $datosUsuarios );  

                // Destruye la cookie y sesión si no es válida
                setcookie( "credenciales" , "" , time() - 3600 , "/" );

                $_SESSION = array();
                if( isset( $_COOKIE[ session_name() ] ) ) setcookie( session_name() , '' , time() - 3600 , '/' );
                session_destroy();
                mysqli_close($conexion);
                return false;
            }     
        }
    }

    // Destruye la cookie y sesión si no es válida
    setcookie( "credenciales" , "" , time() - 3600 , "/" );

    $_SESSION = array();
    if( isset( $_COOKIE[ session_name() ] ) ) setcookie( session_name() , '' , time() - 3600 , '/' );
    session_destroy();

    return false;
}

function elegirCabecera( $autenticado ){
    require_once( $autenticado ? "php/cabecera_registro.php" : "php/cabecera.php" );
}

function verNombreUsuario(){
    $datos;
    // Coge los datos de la sesión o de la cookie
    if( !empty( $_SESSION ) && $_COOKIE[ session_name() ] == session_id() ) $datos = json_decode( $_SESSION["credenciales"] );
    else if( !empty( $_COOKIE["credenciales"] ) )                           $datos = json_decode( $_COOKIE["credenciales"] );
    
    // Imprime el primer dato
    if( $datos != null && is_array( $datos ) && count( $datos ) > 0 ){
        return $datos[0];
    }
    
    echo "ERROR_USUARIO_INEXISTENTE";
    return "ERROR_USUARIO_INEXISTENTE";
}



function verUltimoAcceso(){
    echo isset( $_COOKIE["ultimoAcceso"] ) ? parsearFechaCompleta( $_COOKIE["ultimoAcceso"] ) : "Nunca" ;
}

function cambiarEstilo(){
    $datos;
    if( !empty( $_SESSION ) && $_COOKIE[ session_name() ] == session_id() ) $datos = json_decode( $_SESSION["credenciales"] );
    else if( !empty( $_COOKIE["credenciales"] ) )                           $datos = json_decode( $_COOKIE["credenciales"] );
    
    if( $datos != null && is_array( $datos ) && count( $datos ) > 0 ) return $datos[2];
}

// Lee el fichero con las posibles foto del día, escoge una aleatoria y devuelve los datos
function obtenerFotoDelDia(){
    $datosFoto     = [];
    $lineasValidas = [];

    $fichero = fopen( "json/fotoDelDia.txt" , "r" );
    if ( $fichero ) {
        // Lee el fichero línea por línea
        while( ( $linea = fgets( $fichero ) ) ) {
            if( !empty( $linea ) ){
                $temp = explode( ":" , $linea );
                if( count( $temp ) == 3 && intval( $temp[0] ) > 0 ){
                    // Almacena las líneas correctas
                    array_push( $lineasValidas , $linea );
                }
            }
        }

        if( !empty( $lineasValidas ) ){
            // Elige una línea aleatoria
            $rndIdx = random_int( 0 , count( $lineasValidas ) - 1 );

            // Se asegura de coger uno distinto
			if( !isset( $_COOKIE["ultimaFotoDia"] ) ){
				setcookie( "ultimaFotoDia" , random_int( 0 , count( $lineasValidas ) - 1 ) , 0 , "/" );
			}
			else{
				while( intval( $_COOKIE["ultimaFotoDia"] ) == $rndIdx ) $rndIdx = random_int( 0 , count( $lineasValidas ) - 1 );
			}

            // Almacena el valor elegido
            setcookie( "ultimaFotoDia" , $rndIdx , 0 , "/" );

            // Almacena y devuelve los datos
            $datosFoto = explode( ":" , $lineasValidas[ $rndIdx ] );
            $datosFoto[0] = intval( $datosFoto[0] );
            return $datosFoto;
        }
    }
    
    $datosFoto[0] = 0;
    $datosFoto[1] = "";
    $datosFoto[2] = "FOTO VÁLIDA NO ENCONTRADA";

    fclose($fichero);

    return $datosFoto;
}

// Lee el JSON con los consejos y devuelve uno aleatorio
function obtenerConsejo(){
    $consejosValidos = [];

    // Parsea el JSON
    $consejosJSON = json_decode( file_get_contents( "json/consejos.json" ) );

    // Los datos son correctos
    if( !empty( $consejosJSON ) ){
        // Obtiene el verdadero objeto con los consejos
        $consejos = $consejosJSON->{'consejos'};

        // Almacena las entradas válidas
        foreach( $consejos as $consejo ){
            if( !empty( $consejo->{'id'} ) &&
                !empty( $consejo->{'categoria'} ) &&
                !empty( $consejo->{'dificultad'} ) &&
                !empty( $consejo->{'consejo'} ) )
                array_push( $consejosValidos , $consejo );
        }

        // Hay algún dato válido
        if( !empty( $consejosValidos ) ){
            // Elige una línea aleatoria
            $rndIdx = random_int( 0 , count( $consejosValidos ) - 1 );

            // Se asegura de coger uno distinto
			if( !isset( $_COOKIE["ultimoConsejo"] ) ){
				setcookie( "ultimoConsejo" , random_int( 0 , count( $consejosValidos ) - 1 ) , 0 , "/" );
			}
			else{
				while( intval( $_COOKIE["ultimoConsejo"] ) == $rndIdx ) $rndIdx = random_int( 0 , count( $consejosValidos ) - 1 );
			}

            // Almacena el valor elegido
            setcookie( "ultimoConsejo" , $rndIdx , 0 , "/" );

            $consejo = [];
            array_push( $consejo , $consejosValidos[$rndIdx]->{'categoria'} );
            array_push( $consejo , $consejosValidos[$rndIdx]->{'dificultad'} );
            array_push( $consejo , $consejosValidos[$rndIdx]->{'consejo'} );

            return $consejo;
        }
    }
    else{
        $consejo = [];
        array_push( $consejo , "" );
        array_push( $consejo , "" );
        array_push( $consejo , "CONSEJO NO RECONOCIDO" );

        return $consejo;
    }
}

/*
    function obtenerConsejo(){
    $consejosValidos = [];

    // Parsea el JSON
    $consejosJSON = json_decode( file_get_contents( "json/consejos.json" ) );

    // Los datos son correctos
    if( !empty( $consejosJSON ) ){
        // Obtiene el verdadero objeto con los consejos
        $consejos = $consejosJSON->{'consejos'};

        // Almacena las entradas válidas
        foreach( $consejos as $consejo ){
            if( !empty( $consejo->{'id'} ) &&
                !empty( $consejo->{'categoria'} ) &&
                !empty( $consejo->{'dificultad'} ) &&
                !empty( $consejo->{'consejo'} ) )
                array_push( $consejosValidos , $consejo );
        }

        // Hay algún dato válido
        if( !empty( $consejosValidos ) ){
            $rndIdx = random_int( 1 , count( $consejosValidos ) ) - 1;

            $consejo = [];
            array_push( $consejo , $consejosValidos[$rndIdx]->{'categoria'} );
            array_push( $consejo , $consejosValidos[$rndIdx]->{'dificultad'} );
            array_push( $consejo , $consejosValidos[$rndIdx]->{'consejo'} );

            return $consejo;
        }
    }
    else{
        $consejo = [];
        array_push( $consejo , "" );
        array_push( $consejo , "" );
        array_push( $consejo , "CONSEJO NO RECONOCIDO" );

        return $consejo;
    }
}
*/

//----------------------------------------------

?>