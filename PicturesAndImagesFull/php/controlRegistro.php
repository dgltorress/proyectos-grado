<?php
    // Conectar con BD
    require_once( "conexionBD.php");
    require_once("controlArchivos.php");
    $datos = unserialize( $_POST['datos'] );
    echo $_FILES['pfp']['name'];
    if( !empty( $datos ) ){
        echo 'hola';
        // Importar validaciones
        require_once( "validaciones.php" );
        // Importar funciones
        require_once( "funciones.php" );

        // Comprobación de existencia y validez de datos obligatorios
        /// USUARIO: Existir en la base de datos
        $hayUsuario = (validarExistenciaComplejo( $datos['usuario'] , "usuarios" ,'nombre', $conexion ));
        /// PASSWORD: Validar Contraseña
        $hayPass    = ( !empty( $datos['password'])  && validarContrasena($datos['password']) === 1);
        /// PASSWORD: la password repetida es identica a la password
        $hayPassRepetida = ( !empty($datos['password2']) &&  $datos['password2'] === $datos['password'] );
        /// TÍTULO: Longitud máxima de 200 caracteres
        $haySexo    = ( !empty( $datos['sexo'] ));
        /// EMAIL: Email válido y longitud máxima de 250 caracteres (Un email válido puede ocupar más)
        $hayEmail     = ( validarEmail( $datos['email'] ) === 1 && strlen( $datos['email'] ) <= 250 );
        /// Fecha: fecha de nacimiento valida.
        $hayFecha = validarFecha2( $datos['fechaNac'] ); 



        /*echo $hayFecha;
        echo $hayEmail;
        echo $hayPass;
        echo $haySexo;
        echo $hayPassRepetida;
        echo $hayUsuario;*/

        // Datos obligatorios válidos
        if( $hayFecha && $hayUsuario && $hayPass && $hayPassRepetida && $haySexo && $hayEmail ){
           echo 'todo gucci';
            // Almacenar datos recibidos 
            
            $pUsuario     = $datos['usuario'];

            // hasheo la password
            $pPass    = password_hash($datos['password'], PASSWORD_DEFAULT); 

            $pSexo= $datos['sexo'];

            $aux= explode('/',$datos['fechaNac']);
            $pFecha = $aux[2].'-'.$aux[1].'-'.$aux[0];
            
            $pEmail  = $datos['email'];
            $pCiudad = ( !empty( $datos['ciudad']) );
            if(validarExistencia( $datos['pais'] , "paises" , $conexion )) $pPais = $datos['pais'];
            $pEstilo=1;
            $pFechaR = date( "Y-m-d H:i:s" );

            $pFoto="img/perfil.jpg";
            if(strcmp($_FILES['pfp']['name'],"") !== 0) $result=subir("img/pfp/");
            if($result != null) $pFoto= $result;
            else $hayFoto = 0;

            // Inserción preparada para evitar inyección SQL
            $sentencia = 'INSERT INTO usuarios ( nombre , clave, email , sexo ,
                        fNacimiento , ciudad , pais , foto , fRegistro , estilo ) VALUES
                        ( ? , ? , ? , ? , STR_TO_DATE( ? , "%Y-%m-%d %H:%i:%s" ) , ? , ?, ?,
                        STR_TO_DATE( ? , "%Y-%m-%d %H:%i:%s" ) , ? );';

            $insercion = @mysqli_prepare( $conexion , $sentencia ) OR die( "No se ha podido preparar la inserción." );

            // Se sutituyen los parámetros y se ejecuta la sentencia
            @mysqli_stmt_bind_param( $insercion , "sssississi" ,
            $pUsuario , $pPass , $pEmail , $pSexo ,
            $pFecha , $pCiudad , $pPais , $pFoto , $pFechaR , $pEstilo );

            $exito = @mysqli_execute( $insercion );
            unset( $_SESSION['datosSolicitud'] );

            if( $exito ) header( "Location: ../index.php?msg=8" );
            else header( "Location: ../index.php?msg=13" );
            
        }
        else{
            //unset( $_SESSION['datosSolicitud'] );
            echo header( "Location: ../index.php?msg=13" );
        }
    }
    else{
        unset( $_SESSION['datosSolicitud'] );
        echo header( "Location: ../index.php?msg=13" );
    }
?>