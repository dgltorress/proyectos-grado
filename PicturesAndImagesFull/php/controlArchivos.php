<?php

function subir($path_incomp){
    $path='../'.$path_incomp;
    //usar path aqui cuando funcione
    $target_dir = $path;
    $target_file = $target_dir . basename($_FILES["pfp"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    //comprobar q existe q existe la imagen
    if(strcmp($_FILES['pfp']['name'],"") !== 0) {
            // Comprobar que el archivo es una imagen
        $check = getimagesize($_FILES["pfp"]["tmp_name"]);
        if($check !== false && $_FILES['pfp']['size'] < 2097152) {
            $temp = explode(".", $_FILES["pfp"]["name"]);
            $fileName;
            $temp[0] = round(microtime(true)); //nombre aleatorio en base al tiempo
            $fileName = $temp[0] . "." . $temp[1];
    
            if (file_exists($path . $fileName)) {
                echo $fileName . " ya existe, se procede a renombrar. ";
            } else {
                move_uploaded_file($_FILES["pfp"]["tmp_name"], $path . $fileName);
                echo "Guardado como: " . $path . $fileName;
                $uploadOk = 1;
                return $path_incomp . $fileName;
            }
        } else {
            echo "El archivo no es una imagen.";
            $uploadOk = 0;
            return null;
        }
    }
    else{
        echo "No se ha subido ningun archivo.";
        $uploadOk = 0;
        return null; 
    }
    return null;
}

function borrar($path){
    echo $path;
    if ( unlink( realpath( ( '../' . $path ) ) ) ) return true;
    else                                           return false;
}

?>