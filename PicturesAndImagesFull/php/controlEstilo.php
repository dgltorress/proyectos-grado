<?php
    // Conectar con BD
    require_once( "conexionBD.php");
    // Importar validaciones
    require_once( "validaciones.php");


    if(!empty($_GET['estilo']) ){
        // Almacenar datos recibidos
        $pEstilo  = $_GET['estilo'];

        if(isset($_COOKIE["credenciales"])){
            echo $_COOKIE["credenciales"];
            $datosUser= json_decode($_COOKIE["credenciales"],true);
            $pUsuario = $datosUser[0];
            echo $pUsuario;

            // Inserción preparada para evitar inyección SQL
            $sentencia = 'UPDATE usuarios SET estilo=? WHERE nombre= ?';
            $insercion = @mysqli_prepare( $conexion , $sentencia ) OR die( "No se ha podido preparar la inserción." );
            
            // Se sutituyen los parámetros y se ejecuta la sentencia
            @mysqli_stmt_bind_param( $insercion , "is" , $pEstilo , $pUsuario);
            
            if( @mysqli_execute( $insercion ) ) 
            {
                echo $datosUser[0];
                $datosUser[2]=$_GET['estilo'];
                setcookie( "credenciales" , json_encode( $datosUser ) , time()+7776000 , "/" );
                header( "Location: ../index.php?msg=10" );
            }
            else header( "Location: ../index.php?msg=11" );
        }
        else{
            session_start();
            echo $_SESSION["credenciales"];
            $datosUser= json_decode($_SESSION["credenciales"],true);
            $pUsuario = $datosUser[0];
            echo $pUsuario;

            // Inserción preparada para evitar inyección SQL
            $sentencia = 'UPDATE usuarios SET estilo=? WHERE nombre= ?';
            $insercion = @mysqli_prepare( $conexion , $sentencia ) OR die( "No se ha podido preparar la inserción." );
            
            // Se sutituyen los parámetros y se ejecuta la sentencia
            @mysqli_stmt_bind_param( $insercion , "is" , $pEstilo , $pUsuario);
            
            if( @mysqli_execute( $insercion ) ) 
            {
                $datosUser[2]=$_GET['estilo'];
                echo $datosUser[2];
                $_SESSION["credenciales"]= json_encode($datosUser);
                echo $_SESSION["credenciales"];
                header( "Location: ../index.php?msg=10" );
            }
            else header( "Location: ../index.php?msg=11" );
        }
    }
    else header( "Location: ../index.php?msg=11");
?>