'use strict';

/*
ARCHIVO: codigo.js
JavaScript genérico para la página.

AUTORES: Ian Bucu y Daniel Giménez el 22/10/2021

REVISIONES:
23/10/2021 Validación de formularios (excepto email)
25/10/2021 Comprobación de email
26/10/2021 Tabla de precios
*/

// CONSTANTES GLOBALES
// === Expresiones regulares ===
// ( 3-15 caracteres ASCII y números, no puede comenzar con un número )
const REGEXUSUARIO = /^[a-zA-Z]{1}[a-zA-Z0-9]{2,14}$/;
// ( 6-15 caracteres ASCII y números, guión y barra baja, debe contener 1 minúscula, 1 mayúscula y 1 número )
const REGEXCONTRASENA = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9_-]{6,15}$/;
// ( 1-64 caracteres ASCII, números y símbolos -> 1 @ -> 1-255 caracteres ASCII, números, punto y guión )
const REGEXEMAIL = /^[a-zA-Z0-9!#$%&'*+/=?^_`{|}~.-]{1,64}[@]{1}[a-zA-Z0-9.-]{1,255}$/;
// ( 2 o más puntos seguidos )
const REGEXPARTELOCAL = /[.]{2,}/;

// === Mensajes de error ===
const MENSAJEERRORUSUARIO = 'Debe tener entre 3 y 15 caracteres, no puede empezar con un número y sólo puede contener caracteres del alfabeto inglés.';
const MENSAJEERRORCONTRASENA = 'Debe tener entre 6 y 15 caracteres y contener al menos un número, un carácter en minúsucula y un carácter en mayúscula.';
const MENSAJEERRORREPITE = 'Debe coincidir con la contraseña.';
const MENSAJEERROREMAIL = 'Debe ser una dirección de email válida.';
const MENSAJEERRORSEXO = 'Debe elegirse un sexo.';
const MENSAJEERRORFECHA = 'Formato de fecha no válido.';

/* Comprobación de formularios */
/* ############################################################### */

// FORMULARIO DE ACCESO
function comprobarAcceso(formulario) {
    // Obtiene los campos
    let campos = formulario.getElementsByTagName('INPUT');

    // Almacena los campos
    let nombreUsuario = campos[0].value,
        contrasena = campos[1].value;

    // Comprueba que ambos campos están rellenados
    if (nombreUsuario.length > 0 && contrasena.length > 0) {
        // Comprueba que no haya EXCLUSIVAMENTE espacios o tabulaciones
        let cont = 0,
            mensaje = 'Debe haber un caracter distinto a espacio o tabulación en todos los campos.';

        // Campo de nombre de usuario
        for (let i = 0; i < nombreUsuario.length; ++i) {
            if (nombreUsuario[i] == ' ' || nombreUsuario[i] == '\t') ++cont;
        }

        if (cont == nombreUsuario.length) {
            alerta( 'Error' , mensaje , 0 );
            return false;
        }

        // Si es válido, reinicia el contador
        cont = 0;

        // Campo de nombre de contraseña
        for (let i = 0; i < contrasena.length; ++i) {
            if (contrasena[i] == ' ' || contrasena[i] == '\t') ++cont;
        }

        if (cont == contrasena.length) {
            alerta( 'Error' , mensaje , 0 );
            return false;
        }

        return true;
    }

    // Si los campos no están rellenados, se indica al usuario
    else {
        alerta( 'Error' , 'Rellena ambos campos.' , 0 );
    }

    return false;
}

// CAMPO DE FORMULARIO DE REGISTRO
function comprobarCampoRegistro(campo) {
    // Comprueba una condición y asigna un mensaje de error en función del campo
    let mensaje, valido;

    switch (campo.name) {
        case 'usuario':
            valido = REGEXUSUARIO.test(campo.value);
            mensaje = MENSAJEERRORUSUARIO;
            break;

        case 'password':
            valido = REGEXCONTRASENA.test(campo.value);
            mensaje = MENSAJEERRORCONTRASENA;
            break;

        case 'password2':
            valido = (campo.value == document.getElementById('pwd').value);
            mensaje = MENSAJEERRORREPITE;
            break;

        case 'email':
            valido = (comprobarEmail(campo.value));
            mensaje = MENSAJEERROREMAIL;
            break;

        case 'sexo':
            valido = (campo.value != '');
            mensaje = MENSAJEERRORSEXO;
            break;

        case 'fechaNac':
            valido = comprobarFecha(campo.value);
            mensaje = MENSAJEERRORFECHA;
            break;

        default:
            console.error('SE HA UTILIZADO LA FUNCIÓN DE COMPROBAR CAMPO EN UN CAMPO DE REGISTRO NO DECLARADO');
            return false;
    }

    // Comprueba siempre si es necesario quitar o añadir el mensaje de error
    tratarCampoMensajeError(campo, mensaje, valido);

    return false;
}

// FORMULARIO DE REGISTRO
function comprobarRegistro(formulario) {
    // Obtiene los campos
    let campos = formulario.getElementsByTagName('INPUT'),
        desplegables = formulario.getElementsByTagName('SELECT');

    // Comprueba que todos los campos requeridos están rellenados
    if (campos[0].value.length > 0 &&
        campos[1].value.length > 0 &&
        campos[2].value.length > 0 &&
        campos[3].value.length > 0 &&
        desplegables[0].value.length > 0 &&
        campos[4].value.length > 0) {
        // Analiza el contenido de cada campo y muestra un mensaje modal si no es válido
        if (REGEXUSUARIO.test(campos[0].value)) {
            if (REGEXCONTRASENA.test(campos[1].value)) {
                if (campos[1].value == campos[2].value) {
                    if (comprobarEmail(campos[3].value)) {
                        if (comprobarFecha(campos[4].value)) {
                            window.location.href = 'index.php';
                        }
                        else {
                            alerta('Error en campo de fecha', MENSAJEERRORFECHA, 0);
                        }
                    }
                    else {
                        alerta('Error en campo de email', MENSAJEERROREMAIL, 0);
                    }
                }
                else {
                    alerta('Error en campo de repite contraseña', MENSAJEERRORREPITE, 0);
                }
            }
            else {
                alerta('Error en campo de contraseña', MENSAJEERRORCONTRASENA, 0);
            }
        }
        else {
            alerta('Nombre de usuario no válido', MENSAJEERRORUSUARIO, 0);
        }
    }

    else {
        alerta('Formulario incompleto', 'Rellena todos los campos requeridos.', 0);
    }

    return false;
}
/* --------------------------------------------------------------- */



/* Tratamiento de errores en formularios */
/* ############################################################### */

// AÑADIR O QUITAR MENSAJE DE ERROR EN UN CAMPO
function tratarCampoMensajeError(campo, mensaje, modo) {
    // Almacena el elemento siguiente, comprueba si existe y determina su tipo
    let hermano = campo.nextElementSibling,
        hayMensaje = (hermano != null && hermano.tagName.toLowerCase() == 'p');

    // AÑADIR EL MENSAJE (SI NO EXISTE)
    if (modo == 0 && !hayMensaje) {
        // Aplica estilo al campo
        campo.classList.add('campoErroneo');

        // Crea la etiqueta, aplica el estilo y añade el mensaje
        let mensajeError = document.createElement('p');
        mensajeError.classList.add('mensajeError');
        mensajeError.innerHTML = mensaje;

        // La anexa al campo
        campo.parentNode.insertBefore(mensajeError, campo.nextSibling);

        return true;
    }

    // QUITAR EL MENSAJE (SI EXISTE)
    else if (modo == 1 && hayMensaje) {
        // Quita el estilo al campo
        campo.classList.remove('campoErroneo');

        // Elimina la etiqueta
        hermano.remove();

        return true;
    }

    else if (modo < 0 || modo > 1) console.error('ACCIÓN NO RECONOCIDA: 0 PARA QUITAR MENSAJE DE ERROR, 1 PARA AÑADIR')

    return false;
}


// MENSAJE MODAL
function alerta(titulo, mensaje, exito) {
    // Comprueba que no existe ya un mensaje modal
    if (!document.getElementById('mensajeModal')) {
        // Crea el mensaje modal
        let contenedorMensaje = document.createElement('DIV'),

            mensajeModal = document.createElement('ARTICLE'),

            cabecera = document.createElement('HEADER'),
            color = (exito) ? '#050' : '#600',

            cuerpo = document.createElement('P'),

            pie = document.createElement('FOOTER'),
            botonCerrar = document.createElement('BUTTON');

        // Asigna sus propiedades
        contenedorMensaje.id = 'mensajeModal';

        cabecera.style.color = color;
        cabecera.innerHTML = titulo;

        cuerpo.innerHTML = mensaje;

        botonCerrar.innerHTML = 'Cerrar';
        botonCerrar.classList.add('boton');
        botonCerrar.onclick = function () { contenedorMensaje.remove(); }

        // Junta sus partes
        pie.appendChild(botonCerrar);

        mensajeModal.appendChild(cabecera);
        mensajeModal.appendChild(cuerpo);
        mensajeModal.appendChild(pie);

        contenedorMensaje.appendChild(mensajeModal);

        // Muestra el mensaje
        document.body.appendChild(contenedorMensaje);
        botonCerrar.focus();
    }
}
/* --------------------------------------------------------------- */


// COMPROBAR DIRECCION DE CORREO
function comprobarEmail(emailCadena) {
    // Comprueba que se cumple la estructura básica y no supera los 254 caracteres
    if (REGEXEMAIL.test(emailCadena) && emailCadena.length < 255) {
        // Divide el email en parte-local y dominio
        let email = emailCadena.split('@'),
            parteLocal = email[0],
            dominio = email[1];

        // Comprueba la distribución del punto en parte-local (ni al principio, ni al final, ni dos o más seguidos)
        if (parteLocal[0] != '.' && parteLocal[parteLocal.length - 1] != '.' &&
            !REGEXPARTELOCAL.test(parteLocal)) {
            // Separa los subdominios y comprueba que ninguno supere los 63 caracteres
            let subdominios = dominio.split('.');

            for (let i = 0; i < subdominios.length; ++i) {
                if (subdominios[i].length < 1 || subdominios[i].length > 63) return false;
            }

            return true;
        }
    }

    return false;
}
/* --------------------------------------------------------------- */

// COMPROBAR DIRECCION DE CORREO
function comprobarEmail(emailCadena) {
    // Comprueba que se cumple la estructura básica y no supera los 254 caracteres
    if (REGEXEMAIL.test(emailCadena) && emailCadena.length < 255) {
        // Divide el email en parte-local y dominio
        let email = emailCadena.split('@'),
            parteLocal = email[0],
            dominio = email[1];

        // Comprueba la distribución del punto en parte-local (ni al principio, ni al final, ni dos o más seguidos)
        if (parteLocal[0] != '.' && parteLocal[parteLocal.length - 1] != '.' &&
            !REGEXPARTELOCAL.test(parteLocal)) {
            // Separa los subdominios y comprueba que ninguno supere los 63 caracteres
            let subdominios = dominio.split('.');

            for (let i = 0; i < subdominios.length; ++i) {
                if (subdominios[i].length < 1 || subdominios[i].length > 63) return false;
            }

            return true;
        }
    }

    return false;
}
/* --------------------------------------------------------------- */

// COMPROBAR FECHA
function comprobarFecha(fechaCadena) {
    if (fechaCadena != null) {
        let fecha = fechaCadena.split('/'),
            dias = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

        if (fecha.length == 3 && fecha[2] > 0 && (fecha[1] - 1) >= 0 && (fecha[1] - 1) <= 11 && fecha[0] > 0 && fecha[0] <= dias[fecha[1] - 1]) {
            return !isNaN(new Date(fecha[2], fecha[1] - 1, fecha[0]));
        }
    }

    return false;
}
/* --------------------------------------------------------------- */

// COMPROBOBAR PARAMETROS DE URL
function comprobarURL() {
    let parametros = new URL( window.location.href ).searchParams,
        valorMensaje = parseInt( parametros.get('msg') );
    
    // Tira un mensaje
    if( valorMensaje ){
        switch( valorMensaje ){
            case 1: alerta( 'Error al iniciar sesión', 'Credenciales incorrectas' , 0 ); break;
            case 2: alerta( 'Error al abrir foto', 'No existe la foto' , 0 ); break;
            case 3: alerta( 'Error al registrarse', 'Debes rellenar el nombre de usuario, la contraseña y repetir contraseña' , 0 ); break;
            case 4: alerta( 'Error al registrarse', 'Las contraseñas no coinciden' , 0 ); break;
            case 5: alerta( 'Error al acceder al recurso', 'Regístrate para visualizar este contenido' , 0 ); break;
            case 6: alerta( 'Error al acceder al recurso', 'Un usuario registrado no puede visualizar este contenido' , 0 ); break;
            case 7: alerta( 'Error al crear la foto', 'Datos erróneos' , 0 ); break;
            case 8: alerta( 'Creación correcta', 'Recurso creado con éxito' , 1 ); break;
            case 9: alerta( '¡Álbum creado con éxito!', 'Prueba a subir tu primera foto' , 1 ); break;
            case 10: alerta( 'Nuevo estilo', 'De ahora en adelante, cuando accedas tendrás este estilo por defecto' , 1 ); break;
            case 11: alerta( 'Error al acceder a recurso', 'Ha habido un error al realizar una consulta. Inténtalo de nuevo en un minuto' , 0 ); break;
            case 12: alerta( 'Edición correcta', 'Usuario editado con éxito' , 1 ); break;
            case 13: alerta( 'Error al actualizar el usuario', 'Datos erróneos' , 0 ); break;
            case 14: alerta( 'Usuario eliminado', 'Esperamos que vuelvas pronto' , 1 ); break;
            case 15: alerta( 'Error al borrar el usuario', 'Datos erróneos' , 0 ); break;
            default: alerta( 'Error', 'Se ha producido un error' , 0 );
        }
    }

    return false;
}
/* --------------------------------------------------------------- */
/*
 Calcula los valores de las celdas en funcion del número de celda
function calcularCelda( celda , pags , fotos ) {
    let valor = 0;

    for ( let i = 0 ; i < pags ; i++ ) {
        if      (i < 5)           valor += 0.10;
        else if (i > 5 && i < 11) valor += 0.08;
        else                      valor += 0.07;
    }

    switch( celda ) {
        case 2:                                           break;
        case 3: valor += (0.02 * fotos);                  break;
        case 4: valor += (0.05 * fotos);                  break;
        case 5: valor += (0.05 * fotos) + (0.02 * fotos); break;
        default:
            console.error('HA HABIDO UN ERROR AL CALCULAR EL PRECIO');
            return false;
    }

    valor = ( Math.round( ( valor + Number.EPSILON ) * 100 ) / 100 ).toFixed( 2 );
    return valor;
}
 --------------------------------------------------------------- 

//CREA UN ELEMENTO TBODY QUE SERA RELLENADO MEDIANTE INSERTCELL Y INSERTROW
function crearTabla() {
    var tabla = document.createElement("tbody");
    console.log(tabla);
    let pags = -1;
    let fotos = -1;
    for (let f = 0; f < 15; f++) {
        var fila = tabla.insertRow();
        for (let c = 0; c < 6; c++) {
            if (c == 0) {
                var celda = fila.insertCell();
                celda.textContent = f + 1;
                pags = f + 1;
            }
            else if (c == 1) {
                var celda = fila.insertCell();
                celda.textContent = 3 * (f + 1);
                fotos = 3 * (f + 1);
            }
            else {
                var celda = fila.insertCell();
                celda.textContent = calcularCelda(c, pags, fotos);
            }
        }
    }
    document.getElementById("TabPrecios").appendChild(tabla);
}
 --------------------------------------------------------------- */