<?php
    // Conectar con BD
    require_once( "conexionBD.php");
   
    require_once("controlArchivos.php");
    echo $_FILES['pfp']['name'];
    $datos = unserialize( $_POST['datos'] );

    if(!empty($datos) ){
        echo $_POST['datos'];
        // Importar validaciones
        require_once( "validaciones.php" );
        // Importar funciones
        require_once( "funciones.php" );

        $usuarios = @mysqli_query( $conexion ,"SELECT * FROM usuarios
        WHERE nombre = '".$datos['usrOG']."'" ) OR die( "No se ha podido realizar la consulta." );
        $usuario = @mysqli_fetch_assoc( $usuarios );
        @mysqli_free_result( $usuarios );

        $hayUsuario = (validarExistenciaComplejo( $datos['usuario'] , "usuarios" ,'nombre', $conexion ));
        if(strcmp($datos["usuario"],$usuario["nombre"])!=0 && $hayUsuario)  $pUsuario=$datos["usuario"];
        else $pUsuario=$usuario["nombre"];
        $checked=0;
        $pPassAux= -1;
        $hayPass  = ( !empty( $datos['password'])  && validarContrasena($datos['password']) === 1);
        echo $hayPass;
        if($hayPass) $checked = password_verify($datos['password'], $Usuario['clave']);
        if($checked!=true) $pPassAux= password_hash($datos['password'], PASSWORD_DEFAULT);
        if($pPassAux!=-1)$hayPassRepetida = ( !empty($datos['password2']) &&  $datos['password2'] === $datos['password'] );
        if($hayPassRepetida) $pPass = $pPassAux;
        else $pPass=$usuario["clave"];

        $haySexo = ( !empty( $datos['sexo'] ));
        if($haySexo && $datos['sexo']!=$usuario["sexo"] )  $pSexo= $datos['sexo'];
        else $pSexo = $usuario["sexo"] ;

        $hayEmail= ( validarEmail( $datos['email'] ) === 1 && strlen( $datos['email'] ) <= 250 );
        if($hayEmail && strcmp($datos["email"],$usuario["email"])!=0) $pEmail  = $datos['email'];
        else $pEmail  = $usuario['email'];

        $hayFecha = validarFecha( $datos['fechaNac'] ); 
        $aux= explode( '-' , $datos['fechaNac'] );
        echo $datos['fechaNac'];
        $pFechaAux = $aux[0].'-'.$aux[1].'-'.$aux[2];
        if($hayFecha && strcmp($pFechaAux,$usuario["fNacimiento"])!=0) $pFecha=$pFechaAux;
        else  $pFecha=$usuario["fNacimiento"];
        echo $pFecha;
        if(strcmp($datos["ciudad"],$usuario["ciudad"])!=0)  $pCiudad=$datos["ciudad"];
        else   $pCiudad=$usuario["ciudad"];

        if(strcmp($datos["pais"],$usuario["pais"])!=0)  $pPais=$datos["pais"];
        else   $pPais=$usuario["pais"];

        $pFoto="img/perfil.jpg";
        if(isset($datos['delete']) && strcmp($_FILES['pfp']['name'],"") !== 0 && strcomp($usuario['foto'],"img/perfil.jpg") != 0){
            Borrar($usuario['foto']);
        }
        else{
            $result=subir("img/pfp/");
            if($result != null) {
                $pFoto= $result;
                if(strcmp($usuario['foto'],"img/perfil.jpg") != 0) Borrar($usuario['foto']);
            }
        }
        // Inserción preparada para evitar inyección SQL
        $sentencia = 'UPDATE usuarios SET nombre= ?, clave= ?,email=?,sexo=?, fNacimiento= CAST( ? AS DATE),ciudad=?,pais=?, foto=? WHERE id= ?';
        $insercion = @mysqli_prepare( $conexion , $sentencia ) OR die( "No se ha podido preparar la inserción." );
            
        // Se sutituyen los parámetros y se ejecuta la sentencia
         @mysqli_stmt_bind_param( $insercion , "sssissisi" ,$pUsuario , $pPass , $pEmail , $pSexo ,$pFecha , $pCiudad , $pPais,$pFoto,$usuario['id']);
            
        if( @mysqli_execute( $insercion ) ) 
        {
            header( "Location: ../index.php?msg=12" );
        }
        else header( "Location: ../index.php?msg=13" );
        
    }
   else header( "Location: ../index.php?msg=13");
?>