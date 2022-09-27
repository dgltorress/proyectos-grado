<?php
/*
ARCHIVO: controlAcceso.php
Código PHP que controla el inicio de sesión.

AUTORES: Daniel Giménez el 31/10/2021

REVISIONES:
*/
    // Declara usuarios y contrasenas de ejemplo
    require_once("conexionBD.php");

            // Si los datos recibidos existen, itera
    if( !empty( $_POST["usuario"] ) && !empty( $_POST["password"] ) ){
        // Si ambos coinciden con algun par de datos, redirige al indice autenticado

        $datosUsuarios = @mysqli_query( $conexion ,'SELECT u.nombre,u.clave,u.estilo from usuarios as u where UPPER(u.nombre)= UPPER("'.$_POST["usuario"].'") ') 
        OR die( "Ha habido un problema al comprobar usuarios existentes" );
        $display = !empty($datosUsuarios);
        if($display) {
            echo 'primero';
            $Usuario = mysqli_fetch_assoc( $datosUsuarios );
            if(!empty($Usuario)){
                echo 'entro';
                $checked = password_verify($_POST['password'], $Usuario['clave']);
                if($checked){
                    // Controla que la cookie de logueo no se renueve con cada inicio de sesión
                    if( !isset( $_COOKIE["credenciales"] ) ){
                    //esta seccion se haria en el control de esta autenticado
                    // $hash would be the $hash (above) stored in your database for this user
    
                        $datos = [ $_POST["usuario"] , $Usuario['clave'], $Usuario['estilo'] ];
                        // Si el usuario decide que se recuerden sus datos, se almacenan --90 días-- en su navegador
                        if( $_POST["recordar"] ) setcookie( "credenciales" , json_encode( $datos ) , time()+7776000 , "/" );
                        // /\/\/\ MUY IMPORTANTE LA RUTA AL FINAL /\/\/\
                        // Si no, los datos sólo se mantienen durante la sesión
                        else{
                            session_start();
                            $_SESSION["credenciales"] = json_encode( $datos );
                        }
                    }
    
                    // Almacena la última fecha de acceso durante los siguientes --10 años--
                    date_default_timezone_set('CET');
                    setcookie( "ultimoAcceso" , date( "Y-m-d H:i:s" ) , time()+315569520 , "/" );
                                
                    header( "Location: ../perfil.php" );
                    exit;
    
                    // Guarda el NOMBRE DE USUARIO y UN TOKEN ( no la contrasena )
                    // $datos = [ $_POST["usuario"] ];
                    // array_push( $datos , bin2hex( openssl_random_pseudo_bytes( 64 , true ) ) ) ;
                    // setcookie( "credenciales" , json_encode( $datos ) , time()+90*24*60*60);
                }
                else{
                    echo 'adios1';
                    // Si no coinciden, borra la cookie si existe
                    // y redirige al indice de invitado con informacion de mensaje de error
                    if( isset( $_COOKIE["credenciales"] ) ) setcookie( "credenciales" , "" , time() - 3600 , "/" );
                    header( "Location: ../index.php?msg=1" );
                    exit;  
                }
            }
            else{
                echo 'adios1';
                // Si no coinciden, borra la cookie si existe
                // y redirige al indice de invitado con informacion de mensaje de error
                if( isset( $_COOKIE["credenciales"] ) ) setcookie( "credenciales" , "" , time() - 3600 , "/" );
                header( "Location: ../index.php?msg=1" );
                exit;   
            }
            mysqli_free_result( $datosUsuarios );    
        }
    }
    else{
        echo 'adios2';
        // Si se envia un campo vacio
        // y redirige al indice de invitado con informacion de mensaje de error
        if( isset( $_COOKIE["credenciales"] ) ) setcookie( "credenciales" , "" , time() - 3600 , "/" );
        header( "Location: ../index.php?msg=1" );
        exit;
    }
    exit;
    //    var_dump($credenciales[0][2]);
?>