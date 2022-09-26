'use strict';

// === CONSTANTES GLOBALES ===

const CLASE_ACTIVA        = 'activo';
const CLASE_INTERACTUABLE = 'interactuable';
const CLASE_PERMANENTE    = 'permanente';

const DIFERENCIA_TEMP_INTERNA  = 24;
const DIFERENCIA_TEMP_ALIMENTO = 6;
const MEDIA_CONSUMO            = 1.1;

// === VARIABLES GLOBALES ===

let estadoGlobal = 0; // 0: APAGADO , 1: ENCENDIDO , 2: HORNEANDO.
let horaActual   = new Date(); // Date.

let temporizadorMarcadoresID = 0;
let temporizadorCronometroID = 0;

// * Panel izquierdo *

let sensorTempInterna  = DIFERENCIA_TEMP_INTERNA;                            // Grados centígrados.
let sensorTempAlimento = DIFERENCIA_TEMP_INTERNA + DIFERENCIA_TEMP_ALIMENTO; // Grados centígrados.
let sensorEnergia      = 0;                                                  // Kilovatios.

let sensorProximidad    = false;
let sensorPuertaAbierta = false;


// * Panel central *

let temperatura            = 0;  // Grados centígrados.
let cifraActivaTemperatura = -1; // 0 ( CENTENAS ) , 1 ( DECENAS ) , 2 ( UNIDADES ).

let cronometro             = [ 0 , 0 ]; // Minutos y segundos.
let cifraActivaCronometro  = -1;        // 0 [ DECENAS (m) ] , 1 [ UNIDADES (m) ] , 3 [ DECENAS (s) ] , 4 [ UNIDADES (s) ].


let funcionesModernas = [ false , false , false ]; // 0 ( WIFI ) , 1 ( ALTAVOZ ) , 2 ( MICRO ).

let horaReloj = [ horaActual.getHours() , horaActual.getMinutes() ];


// * Panel derecho *

let modoVentilador = false;

let modoGratinar = false;

let modoResistenciaSuperior = false;
let modoResistenciaInferior = false;
let modoResistenciaAmbas    = false;

let otrosModos = [ false , false , false ]; // 0 ( LUZ INTERNA ) , 1 ( DESCONGELACIÓN ) , 2 ( PIZZA ).



// === MÉTODOS ===


// == PANEL IZQUIERDO ==

// Cambia el valor interno y visual de una función moderna.
function toggleFuncionModerna( nFuncion ){
	// Hay tres funciones modernas: 0 ( WIFI ) , 1 ( ALTAVOZ ) , 2 ( MICRO ).
	if( !isNaN( nFuncion ) && nFuncion >= 0 && nFuncion <= 2 ){
		// Se invierte el booleano guardado internamente.
		funcionesModernas[ nFuncion ] = !funcionesModernas[ nFuncion ];
		
		// Se obtiene el elemento de la página a cambiar visualmente.
		let elemento = document.getElementById( 'funcionesModernas' ).children[ nFuncion ];
		
		// Si el último valor del elemento es true, se marca visualmente. Si no, se desmarca.
		elemento.classList.toggle( CLASE_ACTIVA , funcionesModernas[ nFuncion ] === true );
	}
}

function actualizarL(){
	let sensores = document.getElementById( 'interfaz' ).firstElementChild.children ,
		cifrasSensores ,
		valorCifra ,
		cifraSignificativaEncontrada = false ;
	
	switch( estadoGlobal ){
		case 1:
		case 2:
			// Alterar valores.
			let tempEstable = sensorTempInterna - DIFERENCIA_TEMP_INTERNA;
			if     ( tempEstable > temperatura ) sensorTempInterna--;
			else if( tempEstable < temperatura ) sensorTempInterna++;
			else                                 sensorTempInterna = enteroAleatorioInclusive( DIFERENCIA_TEMP_INTERNA-2 , DIFERENCIA_TEMP_INTERNA+2 );
			
			if     ( sensorTempInterna < 0   ) sensorTempInterna = 0;
			else if( sensorTempInterna > 999 ) sensorTempInterna = 999;
			
			
			tempEstable = sensorTempAlimento - DIFERENCIA_TEMP_INTERNA - DIFERENCIA_TEMP_ALIMENTO;
			if     ( tempEstable > temperatura ) sensorTempAlimento--;
			else if( tempEstable < temperatura ) sensorTempAlimento++;
			else                                 sensorTempAlimento = enteroAleatorioInclusive( DIFERENCIA_TEMP_INTERNA+DIFERENCIA_TEMP_ALIMENTO-2 , DIFERENCIA_TEMP_INTERNA+DIFERENCIA_TEMP_ALIMENTO+2 );
			
			if     ( sensorTempAlimento < 0   ) sensorTempAlimento = 0;
			else if( sensorTempAlimento > 999 ) sensorTempAlimento = 999;
			
			
			if( estadoGlobal === 2 ){
				sensorEnergia = flotanteAleatorioInclusive( MEDIA_CONSUMO-0.6 , MEDIA_CONSUMO );
				
				if     ( sensorEnergia < 0   ) sensorEnergia = 0;
				else if( sensorEnergia > 999 ) sensorEnergia = 999;
			}
			else sensorEnergia = 0;
			
			// Obtener valores como String.
			let tempInternaString  = sensorTempInterna.toString()  ,
				tempAlimentoString = sensorTempAlimento.toString() ,
				energiaString      = sensorEnergia.toString() ;
				
			while( tempInternaString.length  < 3 ) tempInternaString  = '0' + tempInternaString;
			while( tempAlimentoString.length < 3 ) tempAlimentoString = '0' + tempAlimentoString;
			while( energiaString.length      < 3 ) energiaString      = '0' + energiaString;
				
			for( let i = 0 ; i < sensores.length-1 ; i++ ){
				cifrasSensores       = sensores[ i ].children;
				
				let cifrasSensoresString = [ tempInternaString , tempAlimentoString , energiaString ];
				
				sensores[ i ].firstElementChild.classList.add( CLASE_PERMANENTE );
				
				for( let j = 1 ; j < cifrasSensores.length-1 ; j++ ){
					if( !( i === 2 && j === 2 ) ){
						let valorCifra = parseInt( cifrasSensoresString[ i ][ j-1 ] );
						
						if( !isNaN( valorCifra ) ) cifrasSensores[ j ].innerText = valorCifra;
						else                       cifrasSensores[ j ].innerText = 0;
						
						if( ( valorCifra > 0 ) || ( cifraSignificativaEncontrada === true ) || ( j === 3 ) ){
							cifraSignificativaEncontrada = true;
							cifrasSensores[ j ].classList.add( CLASE_ACTIVA );
						}
						else cifrasSensores[ j ].classList.remove( CLASE_ACTIVA );
					}
					else cifrasSensores[ j ].classList.add( CLASE_ACTIVA );
				}
				
				sensores[ i ].lastElementChild.classList.add( CLASE_PERMANENTE );
				
				cifraSignificativaEncontrada = false;
			}
		break;
		
		default:
			for( let i = 0 ; i < sensores.length-1 ; i++ ){
				sensores[ i ].firstElementChild.classList.remove( CLASE_PERMANENTE );
				sensores[ i ].lastElementChild.classList.remove( CLASE_PERMANENTE );
			}
		break;
	}
}


// == PANEL CENTRAL ==

// Cambia el valor de una cifra de la temperatura o el cronómetro.
function setCifraActiva( nCifra , isTemperatura = false ){
	if( !isNaN( nCifra ) && nCifra >= 0 && nCifra <= 4 ){
		let contenedorCifra;
		
		// Control de temperatura.
		if( isTemperatura === true && nCifra <= 2 ){
			// Obtiene el contenedor de la temperatura.
			contenedorCifra = document.getElementById( 'indicadores' ).firstElementChild;
			
			// Si se selecciona la misma cifra dos veces,
			if( cifraActivaTemperatura === nCifra ){
				// Se deselecciona.
				cifraActivaTemperatura = -1;
				
				// Se desmarca.
				contenedorCifra.children[ nCifra ].classList.remove( CLASE_ACTIVA );
				
				// Se desmarcan los últimos 3 controles de tiempo y temperatura.
				toggleControlesTiempo( false , 1 );
			}
			// Si se selecciona uno distinto al que había,
			else{
				// Desmarca la cifra anterior, si la había.
				if( cifraActivaTemperatura >= 0 ){
					document.getElementById( 'indicadores' ).firstElementChild.children[ cifraActivaTemperatura ].classList.remove( CLASE_ACTIVA );
				}
				else if( cifraActivaCronometro >= 0 ){
					document.getElementById( 'indicadores' ).lastElementChild.children[ cifraActivaCronometro ].classList.remove( CLASE_ACTIVA );
				}
				
				// Almacena la cifra activada.
				cifraActivaTemperatura = nCifra;
			
				// Marca visualmente la cifra activada.
				contenedorCifra.children[ nCifra ].classList.add( CLASE_ACTIVA );
				
				// Marca los últimos 3 controles de tiempo y temperatura.
				toggleControlesTiempo( true , 1 );
			
				// Desmarca el otro indicador.
				cifraActivaCronometro = -1;
			}
		}
		// Control de tiempo.
		else{
			// Obtiene el contenedor de la temperatura.
			contenedorCifra = document.getElementById( 'indicadores' ).lastElementChild;
			
			// Si se selecciona la misma cifra dos veces,
			if( cifraActivaCronometro === nCifra ){
				// Se deselecciona.
				cifraActivaCronometro = -1;
				
				// Se desmarca.
				contenedorCifra.children[ nCifra ].classList.remove( CLASE_ACTIVA );
				
				// Se desmarcan los últimos 3 controles de tiempo y temperatura.
				toggleControlesTiempo( false , 1 );
			}
			// Si se selecciona uno distinto al que había,
			else{
				// Desmarca la cifra anterior, si la había.
				if( cifraActivaTemperatura >= 0 ){
					document.getElementById( 'indicadores' ).firstElementChild.children[ cifraActivaTemperatura ].classList.remove( CLASE_ACTIVA );
				}
				else if( cifraActivaCronometro >= 0 ){
					document.getElementById( 'indicadores' ).lastElementChild.children[ cifraActivaCronometro ].classList.remove( CLASE_ACTIVA );
				}
				
				// Almacena la cifra activada.
				cifraActivaCronometro = nCifra;
			
				// Marca visualmente la cifra activada.
				contenedorCifra.children[ nCifra ].classList.add( CLASE_ACTIVA );
				
				// Marca los últimos 3 controles de tiempo y temperatura.
				toggleControlesTiempo( true , 1 );
			
				// Desmarca el otro indicador.
				cifraActivaTemperatura = -1;
			}
		}
	}
}

// Comenzar/Detener cocción.
function toggleCoccion(){
	// Comenzar cocción.
	if( estadoGlobal === 1 ){
		actualizarControles( 2 );
		
		// Inicia el cronómetro.
		bucleCronometro( 1000 );
	}
	// Detener cocción.
	else if( estadoGlobal === 2 ){
		// Resetea los indicadores centrales sin borrar el contenido.
		resetearIndicadores( true );
		
		// Detiene el cronómetro.
		clearInterval( temporizadorCronometroID );
		temporizadorCronometroID = 0;
		
		// Detiene el cambio de temperatura.
		temperatura = 0;
		
		actualizarControles( 1 );
	}
}

// Resetea los valores del indicador que tenga una cifra seleccionada.
function resetearIndicadores( mantenerContenido = false ){
	let cifras;
	
	// El indicador de temperatura está seleccionado.
	if( cifraActivaTemperatura >= 0 ){
		cifras = document.getElementById( 'indicadores' ).firstElementChild.children;
		
		// Desmarca la cifra interna y visualmente.
		cifras[ cifraActivaTemperatura ].classList.remove( CLASE_ACTIVA );
		cifraActivaTemperatura = -1;
		
		// Reemplaza los valores de cada campo por 0.
		if( mantenerContenido === false ) for( let i = 0 ; i < cifras.length ; i++ ) if( i !== 3 ) cifras[ i ].innerText = 0;
		
		// Quita funcionalidad y clases.
		toggleControlesTiempo( false , 1 );
	}
	// El indicador de tiempo está seleccionado.
	if( cifraActivaCronometro >= 0 ){
		cifras = document.getElementById( 'indicadores' ).lastElementChild.children;
		
		// Desmarca la cifra interna y visualmente.
		cifras[ cifraActivaCronometro ].classList.remove( CLASE_ACTIVA );
		cifraActivaCronometro = -1;
		
		// Reemplaza los valores de cada campo por 0.
		if( mantenerContenido === false ) for( let i = 0 ; i < cifras.length ; i++ ) if( i !== 2 ) cifras[ i ].innerText = 0;
		
		// Quita funcionalidad y clases.
		toggleControlesTiempo( false , 1 );
	}
}

// Incrementa o decrementa el valor de la cifra seleccionada.
function actualizarCifraActiva( decrementar = false ){
	let contenedor , valor;
	// El indicador de temperatura está seleccionado.
	if( cifraActivaTemperatura >= 0 ){
		// Obtiene el contenedor de la cifra y su valor entero.
		contenedor = document.getElementById( 'indicadores' ).firstElementChild.children[ cifraActivaTemperatura ];
		valor      = parseInt( contenedor.innerText );
		
		if( !isNaN( valor ) ){
			let valorMax = 9;
			
			// Incrementa o decrementa el valor en 1.
			if( decrementar === true ){
				valor--;
				if( valor < 0 ) valor = valorMax; 
			}
			else{
				valor++;
				if( valor > valorMax ) valor = 0; 
			}
			
			// Lo actualiza en el contenedor.
			contenedor.innerText = valor;
		}
	}
	// El indicador de tiempo está seleccionado.
	else if( cifraActivaCronometro >= 0 ){
		// Obtiene el contenedor de la cifra y su valor entero.
		contenedor = document.getElementById( 'indicadores' ).lastElementChild.children[ cifraActivaCronometro ];
		valor      = parseInt( contenedor.innerText );
		
		if( !isNaN( valor ) ){
			let valorMax;
			
			// Las decenas de minutos no deben superar 6.
			valorMax = ( cifraActivaCronometro === 3 ) ? 5 : 9 ;
			
			// Incrementa o decrementa el valor en 1.
			if( decrementar === true ){
				valor--;
				if( valor < 0 ) valor = valorMax; 
			}
			else{
				valor++;
				if( valor > valorMax ) valor = 0; 
			}
			
			// Lo actualiza en el contenedor.
			contenedor.innerText = valor;
		}
	}
}

// Enciende o apaga la funcionalidad y aspecto de los controles de temperatura y tiempo.
function toggleControlesTiempo( encender = null , posInicial = 0 ){
	if( !isNaN( posInicial ) && encender !== null && posInicial >= 0 ){
		let controlesTiempo = document.getElementById( 'controlesTiempo' ).children;
		
		if( encender === true ){
			for( let i = posInicial ; i < controlesTiempo.length ; i++ ){
				let funcion;
				switch( i ){
					case 0: funcion = 'toggleCoccion();';               break;
					case 1: funcion = 'resetearIndicadores( false );';         break;
					case 2: funcion = 'actualizarCifraActiva( true );'; break;
					case 3: funcion = 'actualizarCifraActiva();';       break;
					
					default: funcion = ''; break;
				}
				controlesTiempo[ i ].setAttribute( 'onclick' , funcion );
				controlesTiempo[ i ].classList.add( CLASE_ACTIVA );
				controlesTiempo[ i ].classList.add( CLASE_INTERACTUABLE );
			}
		}
		else{
			for( let i = posInicial ; i < controlesTiempo.length ; i++ ){
				controlesTiempo[ i ].removeAttribute( 'onclick' );
				controlesTiempo[ i ].classList.remove( CLASE_ACTIVA );
				controlesTiempo[ i ].classList.remove( CLASE_INTERACTUABLE );
			}
		}
	}
}

// Actualiza la hora.
function actualizarHora( nuevaHoraManual = null ){
	let etiquetaHora  = document.getElementById( 'reloj' ).firstElementChild;
	
	if( nuevaHoraManual === null ){
		horaActual = new Date();
		horaReloj = [ horaActual.getHours() , horaActual.getMinutes() ];
		
		let contenidoHora = ( ( horaReloj[0] > 9 ) ? horaReloj[0] : '0' + horaReloj[0] ) +
							':' +
							( ( horaReloj[1] > 9 ) ? horaReloj[1] : '0' + horaReloj[1] ) ;
		
		etiquetaHora.innerText = contenidoHora;
		etiquetaHora.setAttribute( 'DATETIME' , contenidoHora );
	}
	else{
		etiquetaHora.innerText = nuevaHoraManual;
		etiquetaHora.setAttribute( 'DATETIME' , nuevaHoraManual );
	}
}

// Actualiza el tiempo del cronómetro.
function actualizarCronometro(){ // console.log('CRONÓMETRO ACTUALIZADO');
	// Cuenta 1 segundo hacia atrás.
	if( cronometro[ 1 ] > 0 ) cronometro[ 1 ]--;
	else{
		if( cronometro[ 0 ] > 0 ){
			cronometro[ 0 ]--;
			cronometro[ 1 ] = 59;
		}
		else{
			cronometro[ 0 ] = 0;
			cronometro[ 1 ] = 0;
		}
	}
	
	// Obtiene el contenedores y la hora como String.
	let cifrasCronometro = document.getElementById( 'indicadores' ).lastElementChild.children ,
		horaString = ( ( cronometro[ 0 ] > 9 ) ? cronometro[ 0 ].toString() : '0' + cronometro[ 0 ].toString() ) +
					 ':' +
					 ( ( cronometro[ 1 ] > 9 ) ? cronometro[ 1 ].toString() : '0' + cronometro[ 1 ].toString() ) ;
	
	// Actualiza los valores visualmente.
	for( let i = 0 ; i < cifrasCronometro.length ; i++ ){
		cifrasCronometro[ i ].innerText = horaString[ i ];
		cifrasCronometro[ i ].classList.add( CLASE_ACTIVA );
	}
	cifrasCronometro[ 0 ].classList.toggle( CLASE_ACTIVA , cronometro[ 0 ] > 9 );
	
	if( ( cronometro[ 0 ] === 0 ) && ( cronometro[ 1 ] === 0 ) ){
		clearInterval( temporizadorCronometroID );
		temporizadorCronometroID = 0;
	}
}

// Llama a actualizarCronometro() cada X milisegundos.
function bucleCronometro( x ){
	// Obtiene un valor numérico para el cronómetro.
	
	cronometro = [ 0 , 0 ];
	
	let cifrasCronometro = document.getElementById( 'indicadores' ).lastElementChild.children ,
		valorCifraCronometro ;
	
	for( let i = 0 ; i < cifrasCronometro.length ; i++ ){
		if( i === 2 ) continue; // Separador.
		
		valorCifraCronometro = parseInt( cifrasCronometro[ i ].innerText );
		
		if( isNaN( valorCifraCronometro ) || ( valorCifraCronometro < 0 ) || ( valorCifraCronometro > 9 ) ) valorCifraCronometro = 0;
		if( ( i === 3 ) && ( valorCifraCronometro > 5 ) ) valorCifraCronometro = 5;
		
		switch( i ){
			case 0: cronometro[ 0 ] += valorCifraCronometro * 10; break; // Decenas  (m).
			case 1: cronometro[ 0 ] += valorCifraCronometro;      break; // Unidades (m).
			case 3:	cronometro[ 1 ] += valorCifraCronometro * 10; break; // Decenas  (s).
			case 4: cronometro[ 1 ] += valorCifraCronometro+1;    break; // Unidades (s).
			
			default: break;
		}
	}
	
	// Activa el cronómetro.
	
	x = parseInt( x );
	
	actualizarCronometro(); // PROVISIONAL
	
	if( !isNaN( x ) && ( x > 100 ) ) temporizadorCronometroID = setInterval( actualizarCronometro , x    );
	else                             temporizadorCronometroID = setInterval( actualizarCronometro , 1000 );
}

function actualizarC(){
	// = Actualizar indicadores de temperatura y cronómetro =
	
	let indicadores = document.getElementById( 'indicadores' ).children ,
		cifrasTemp = indicadores[ 0 ].children ,
		cifrasCron = indicadores[ 1 ].children ;
	
	// Añade o quita las clases y su funcionalidad.
	switch( estadoGlobal ){
		case 1:
			for( let i = 0 ; i < cifrasTemp.length-1 ; i++ ){
				cifrasTemp[ i ].setAttribute( 'onclick' , 'setCifraActiva( ' + i + ' , true );' );
				cifrasTemp[ i ].classList.add( CLASE_INTERACTUABLE );
				cifrasTemp[ i ].classList.remove( CLASE_ACTIVA );
			}
			cifrasTemp[ cifrasTemp.length-1 ].classList.add( CLASE_ACTIVA );
			
			for( let i = 0 ; i < cifrasCron.length ; i++ ){
				if( i === 2 ) continue;
				cifrasCron[ i ].setAttribute( 'onclick' , 'setCifraActiva( ' + i + ' );' );
				cifrasCron[ i ].classList.add( CLASE_INTERACTUABLE );
				cifrasCron[ i ].classList.remove( CLASE_ACTIVA );
			}
			cifrasCron[ 2 ].classList.add( CLASE_ACTIVA );
		break;
		case 2:
			let cifraTempSignificativaEncontrada = false ,
				valorCifraTemp , 
				indiceCifra ;
				
			temperatura = 0; 
			
			for( let i = 0 ; i < cifrasTemp.length-1 ; i++ ){
				valorCifraTemp = parseInt( cifrasTemp[ i ].innerText );
						
				if( !isNaN( valorCifraTemp ) ) cifrasTemp[ i ].innerText = valorCifraTemp;
				else                           cifrasTemp[ i ].innerText = 0;
				
				if( ( valorCifraTemp > 0 ) || ( cifraTempSignificativaEncontrada === true ) || ( i === 2 ) ){
					cifraTempSignificativaEncontrada = true;
					cifrasTemp[ i ].classList.add( CLASE_ACTIVA );
				}
				else cifrasTemp[ i ].classList.remove( CLASE_ACTIVA );
						
				cifrasTemp[ i ].removeAttribute( 'onclick' );
				cifrasTemp[ i ].classList.remove( CLASE_INTERACTUABLE );
				
				indiceCifra = i; if( indiceCifra === 2 ) indiceCifra = 0; else if( indiceCifra === 0 ) indiceCifra = 2; 
				temperatura += valorCifraTemp * Math.pow( 10 , indiceCifra );
			}
			cifrasTemp[ cifrasTemp.length-1 ].classList.add( CLASE_ACTIVA );
			
			for( let i = 0 ; i < cifrasCron.length ; i++ ){
				if( i === 2 ) continue;
				cifrasCron[ i ].removeAttribute( 'onclick' );
				cifrasCron[ i ].classList.remove( CLASE_INTERACTUABLE );
			}
			cifrasCron[ 2 ].classList.add( CLASE_ACTIVA );
		break;
		
		default:
			for( let i = 0 ; i < cifrasTemp.length-1 ; i++ ){
				cifrasTemp[ i ].removeAttribute( 'onclick' );
				cifrasTemp[ i ].classList.remove( CLASE_INTERACTUABLE );
				cifrasTemp[ i ].classList.remove( CLASE_ACTIVA );
			}
			cifrasTemp[ cifrasTemp.length-1 ].classList.remove( CLASE_ACTIVA );
			
			for( let i = 0 ; i < cifrasCron.length ; i++ ){
				if( i === 2 ) continue;
				cifrasCron[ i ].removeAttribute( 'onclick' );
				cifrasCron[ i ].classList.remove( CLASE_INTERACTUABLE );
				cifrasCron[ i ].classList.remove( CLASE_ACTIVA );
			}
			cifrasCron[ 2 ].classList.remove( CLASE_ACTIVA );
		break;
	}
	
	// = Actualizar controles de indicadores =
	let controlesTiempo = document.getElementById( 'controlesTiempo' ).children;
	
	// Añade o quita las clases y su funcionalidad.
	switch( estadoGlobal ){
		case 1:
			toggleControlesTiempo( true );
		case 2:
			toggleControlesTiempo( false , 1 );
		break;
		
		default: toggleControlesTiempo( false ); break;
	}
	
	// = Actualizar funciones modernas =
	let funcionesModernas = document.getElementById( 'funcionesModernas' ).children;
	
	switch( estadoGlobal ){
		case 1:
		case 2:
			for( let i = 0 ; i < funcionesModernas.length ; i++ ){
				funcionesModernas[ i ].setAttribute( 'onclick' , 'toggleFuncionModerna( ' + i + ' );' );
				funcionesModernas[ i ].classList.add( CLASE_INTERACTUABLE );
			}
		break;
		
		default:
			for( let i = 0 ; i < funcionesModernas.length ; i++ ){
				funcionesModernas[ i ].removeAttribute( 'onclick' );
				funcionesModernas[ i ].classList.remove( CLASE_INTERACTUABLE );
			}
		break;
	}
	
	// = Actualizar reloj =
	let etiquetaHora = document.getElementById( 'reloj' ).firstElementChild;
	
	switch( estadoGlobal ){
		case 1:
		case 2:
			etiquetaHora.classList.add( CLASE_PERMANENTE );
		break;
		
		default:
			etiquetaHora.classList.remove( CLASE_PERMANENTE );
		break;
	}
}


// == PANEL DERECHO ==

// Cambia el valor interno y visual del aire circulante.
function toggleVentilador(){
	// Se invierte el booleano guardado internamente.
	modoVentilador = !modoVentilador;
		
	// Se obtiene el elemento de la página a cambiar visualmente.
	let elemento = document.getElementById( 'modoVentilador' ).firstElementChild;
		
	// Si el último valor del elemento es true, se marca visualmente. Si no, se desmarca.
	elemento.classList.toggle( CLASE_ACTIVA , modoVentilador === true );
}

// Cambia el valor interno y visual de la resistencia activada.
function toggleResistencia( nResistencia ){
	// Hay cuatro botones de resistencia: 0 ( SUPERIOR ) , 1 ( INFERIOR ) , 2 ( GRATINADOR ) , 3 ( AMBAS ).
	if( !isNaN( nResistencia ) && nResistencia >= 0 && nResistencia <= 3 ){
		// Se obtiene el elemento de la página a cambiar visualmente.
		let nCaja         = ( nResistencia < 2 ) ? 1 : 2 ,
			nElemento     = ( nResistencia < 2 ) ? nResistencia : nResistencia-2 ,
			cajaPrincipal = document.getElementById( 'modoResistencias' ) ,
			elemento      = cajaPrincipal.children[ nCaja ].children[ nElemento ];
		
		// Si es el gratinador,
		if( nResistencia === 2 ){
			modoGratinar = !modoGratinar;
			elemento.classList.toggle( CLASE_ACTIVA , modoGratinar === true );
		}
		// Si es una resistencia normal,
		else{
			// Obtiene los elementos que es posible que se deba cambiar visualmente.
			let lineas        = cajaPrincipal.firstElementChild ,
				elementoAmbas = ( nResistencia === 3 ) ? elemento : cajaPrincipal.children[ 2 ].lastElementChild;
				
				
			switch( nResistencia ){
				case 0: // Superior
					modoResistenciaSuperior = !modoResistenciaSuperior;
					elemento.classList.toggle( CLASE_ACTIVA , modoResistenciaSuperior === true );
					
				break;
				case 1: // Inferior
					modoResistenciaInferior = !modoResistenciaInferior;
					elemento.classList.toggle( CLASE_ACTIVA , modoResistenciaInferior === true );
					
				break;
				
				case 3: // Ambas
					// Altera interna y visualmente las resistencias independientes.
					let resistenciasIndependientes = cajaPrincipal.children[ 1 ];
					
					modoResistenciaAmbas = !modoResistenciaAmbas;
					// Pone ambas a true.
					if( modoResistenciaAmbas === true ){
						// Superior
						modoResistenciaSuperior = true;
						resistenciasIndependientes.firstElementChild.classList.add( CLASE_ACTIVA );
						
						// Inferior
						modoResistenciaInferior = true;
						resistenciasIndependientes.lastElementChild.classList.add( CLASE_ACTIVA );
					}
					// Pone ambas a false.
					else{
						// Superior
						modoResistenciaSuperior = false;
						resistenciasIndependientes.firstElementChild.classList.remove( CLASE_ACTIVA );
						
						// Inferior
						modoResistenciaInferior = false;
						resistenciasIndependientes.lastElementChild.classList.remove( CLASE_ACTIVA );
					}
					
				break;
				
				default: break;
			}
			
			// Según los valores internos de las resistencias independientes, se altera el valor y el icono de ambas.
			if( ( modoResistenciaSuperior === true  ) && ( modoResistenciaInferior === true  ) ){
				modoResistenciaAmbas = true;
				lineas.classList.add( CLASE_ACTIVA ); elementoAmbas.classList.add( CLASE_ACTIVA );
			}
			else{
				modoResistenciaAmbas = false;
				lineas.classList.remove( CLASE_ACTIVA ); elementoAmbas.classList.remove( CLASE_ACTIVA );
			}
		}
	}
}

// Cambia el valor interno y visual de un modo de cocción misceláneo.
function toggleOtroModo( nModo ){
	// Hay tres modos alternativos: 0 ( LUZ INTERNA ) , 1 ( DESCONGELACIÓN ) , 2 ( PIZZA ).
	if( !isNaN( nModo ) && nModo >= 0 && nModo <= 2 ){
		// Se invierte el booleano guardado internamente.
		otrosModos[ nModo ] = !otrosModos[ nModo ];
		
		// Se obtiene el elemento de la página a cambiar visualmente.
		let elemento = document.getElementById( 'modoOtros' ).children[ nModo ];
		
		// Si el último valor del elemento es true, se marca visualmente. Si no, se desmarca.
		elemento.classList.toggle( CLASE_ACTIVA , otrosModos[ nModo ] === true );
	}
}

function actualizarR(){
	// = Actualizar aire circulante =
	let modoVentilador = document.getElementById( 'modoVentilador' ).firstElementChild;
	
	switch( estadoGlobal ){
		case 1:
			modoVentilador.setAttribute( 'onclick' , 'toggleVentilador();' );
			modoVentilador.classList.add( CLASE_INTERACTUABLE );
		break;
		
		case 2:
		default:
			modoVentilador.removeAttribute( 'onclick' );
			modoVentilador.classList.remove( CLASE_INTERACTUABLE );
		break;
	}
	
	// = Actualizar resistencias =
	let modoResistencias = document.getElementById( 'modoResistencias' ).children;
	
	switch( estadoGlobal ){
		case 1:
			let nResistencia = 0;
			for( let i = 1 ; i < modoResistencias.length ; i++ ){
				for( let j = 0 ; j < modoResistencias[ i ].children.length ; j++ ){
					modoResistencias[ i ].children[ j ].setAttribute( 'onclick' , 'toggleResistencia( ' + nResistencia++ + ' );' );
					modoResistencias[ i ].children[ j ].classList.add( CLASE_INTERACTUABLE );
				}
			}
		break;
		
		case 2:
		default:
			for( let i = 1 ; i < modoResistencias.length ; i++ ){
				for( let j = 0 ; j < modoResistencias[ i ].children.length ; j++ ){
					modoResistencias[ i ].children[ j ].removeAttribute( 'onclick' );
					modoResistencias[ i ].children[ j ].classList.remove( CLASE_INTERACTUABLE );
				}
			}
		break;
	}
	
	// = Actualizar modos misceláneos =
	let modoOtros = document.getElementById( 'modoOtros' ).children;
	
	switch( estadoGlobal ){
		case 1:
			for( let i = 0 ; i < modoOtros.length ; i++ ){
				modoOtros[ i ].setAttribute( 'onclick' , 'toggleOtroModo( ' + i + ' );' );
				modoOtros[ i ].classList.add( CLASE_INTERACTUABLE );
			}
		break;
		
		case 2:
		default:
			for( let i = 0 ; i < modoOtros.length ; i++ ){
				modoOtros[ i ].removeAttribute( 'onclick' );
				modoOtros[ i ].classList.remove( CLASE_INTERACTUABLE );
			}
		break;
	}
}


// * Generales *

// Cambia el estado del horno y actualiza las funciones onclick y clases de los controles.
function actualizarControles( nuevoEstado = 1 ){
	if( !isNaN( nuevoEstado ) && nuevoEstado >= 0 && nuevoEstado <= 2 ){ // 0: APAGADO , 1: ENCENDIDO , 2: HORNEANDO.
		estadoGlobal = nuevoEstado;
	
		actualizarL();
		actualizarC();
		actualizarR();
		
		actualizarProximidad   ( document.getElementById( 'proximidad' ).value );
		actualizarPuertaAbierta( document.getElementById( 'abrirPuerta' ).checked );
	}
}

// Actualiza los contenedores con números cuando sea conveniente.
function actualizarMarcadores(){ // console.log('MARCADORES (HORA Y SENSORES) ACTUALIZADOS');
	actualizarL();
	actualizarHora();
}

// Llama a actualizarMarcadores() cada X milisegundos.
function bucleMarcadores( x ){
	x = parseInt( x );
	
	if( !isNaN( x ) && ( x > 100 ) ) temporizadorMarcadoresID = setInterval( actualizarMarcadores , x    );
	else                             temporizadorMarcadoresID = setInterval( actualizarMarcadores , 1000 );
}

// Actualiza el sensor de proximidad.
function actualizarProximidad( valorCampo ){
	valorCampo = parseFloat( valorCampo ).toFixed( 1 );
	
	let indicadorDistancia = document.getElementById( 'proximidad' ).nextElementSibling ;
	
	if( !isNaN( valorCampo ) ){
		indicadorDistancia.innerText = valorCampo + 'm';
		
		sensorProximidad = ( valorCampo <= 1.5 ) ? true : false ;
		
		if( estadoGlobal > 0 ){
			document.getElementById( 'interfaz' ).firstElementChild.lastElementChild.firstElementChild.classList.toggle( 'activo' , sensorProximidad );
		}
	}
	else indicadorDistancia.innerText = '?m';
}

// Actualiza el sensor de proximidad.
function actualizarPuertaAbierta( valorCampo ){
	sensorPuertaAbierta = Boolean( valorCampo );
		
	if( estadoGlobal > 0 ){
		document.getElementById( 'interfaz' ).firstElementChild.lastElementChild.lastElementChild.classList.toggle( 'activo' , sensorPuertaAbierta );
	}
}

// Establece las propiedades del botón de apagar.
function setBotonApagar(){
	let botonApagar = document.getElementById( 'botonApagar' );
	
	botonApagar.setAttribute( 'onclick' , 'toggleHorno();' );
	botonApagar.classList.add( CLASE_PERMANENTE );
	botonApagar.firstElementChild.classList.add( CLASE_PERMANENTE );
	botonApagar.classList.add( CLASE_INTERACTUABLE );
}

// Enciende o apaga el horno.
function toggleHorno(){
	// Si está apagado, encender.
	if( estadoGlobal === 0 ){
		bucleMarcadores( 800 );
		actualizarHora();
		actualizarControles( 1 );
	}
	// Si está encendido o en cocción, resetear variables internas y contenido visual y apagar.
	else{
		// Detiene los temporizadores, si están activados.
		clearInterval( temporizadorMarcadoresID );
		temporizadorMarcadoresID = 0;
		
		clearInterval( temporizadorCronometroID );
		temporizadorCronometroID = 0;
		
		// Resetea las variables y el contenido visual.
		resetearVariablesInternas();
		resetearContenidoVisual();
		actualizarControles( 0 );
	}
}

// Resetea las variables globales internas.
function resetearVariablesInternas(){
	estadoGlobal = 0; // 0: APAGADO , 1: ENCENDIDO , 2: HORNEANDO.
	horaActual   = new Date(); // Date.
	
	// * Panel izquierdo *
	
	sensorTempInterna  = DIFERENCIA_TEMP_INTERNA;                            // Grados centígrados.
	sensorTempAlimento = DIFERENCIA_TEMP_INTERNA + DIFERENCIA_TEMP_ALIMENTO; // Grados centígrados.
	sensorEnergia      = 0;                                                  // Kilovatios.
	
	// sensorProximidad    = false; ESTAS VARIABLES NO SE REINICIAN PORQUE PERTENECEN AL ENTORNO.
	// sensorPuertaAbierta = false;
	
	
	// * Panel central *
	
	temperatura            = 0;  // Grados centígrados.
	cifraActivaTemperatura = -1; // 0 ( CENTENAS ) , 1 ( DECENAS ) , 2 ( UNIDADES ).
	
	cronometro             = [ 0 , 0 ]; // Minutos y segundos.
	cifraActivaCronometro  = -1;        // 0 [ DECENAS (m) ] , 1 [ UNIDADES (m) ] , 3 [ DECENAS (s) ] , 4 [ UNIDADES (s) ].
	
	
	funcionesModernas = [ false , false , false ]; // 0 ( WIFI ) , 1 ( ALTAVOZ ) , 2 ( MICRO ).
	
	horaReloj = [ horaActual.getHours() , horaActual.getMinutes() ];
	
	
	// * Panel derecho *
	
	modoVentilador = false;
	
	modoGratinar = false;
	
	modoResistenciaSuperior = false;
	modoResistenciaInferior = false;
	modoResistenciaAmbas    = false;
	
	otrosModos = [ false , false , false ];
}

// Resetea el contenido visual y las clases.
function resetearContenidoVisual(){
	// Resetea los sensores.
	let sensores = document.getElementById( 'interfaz' ).firstElementChild.children ,
		cifrasSensor;
	
	for( let i = 0 ; i < sensores.length ; i++ ){
		cifrasSensor = sensores[ i ].getElementsByTagName( 'span' );
		
		for( let j = 0 ; j < cifrasSensor.length-1 ; j++ ){
			if( !( i === 2 && j === 1 ) ){
				cifrasSensor[ j ].innerText = '0';
			}
		}
	}
	
	// Resetea los indicadores centrales.
	cifraActivaTemperatura = 1;
	cifraActivaCronometro = 1;
	resetearIndicadores( false );
	
	// Pone la hora a 00:00
	actualizarHora( '00:00' );
	
	// Elimina todas las clases y reestablece el botón de apagar.
	// Es necesario copiar las HTMLcollections dinámicas en arrays estáticos.
	let elementosActivos        = Array.prototype.slice.call( document.getElementById( 'interfaz' ).getElementsByClassName( CLASE_ACTIVA        ) ) ,
		elementosInteractuables = Array.prototype.slice.call( document.getElementById( 'interfaz' ).getElementsByClassName( CLASE_INTERACTUABLE ) ) ,
		elementosPermanentes    = Array.prototype.slice.call( document.getElementById( 'interfaz' ).getElementsByClassName( CLASE_PERMANENTE    ) ) ;
		
	for( let i = 0 ; i < elementosActivos.length        ; i++ )        elementosActivos[ i ].classList.remove( CLASE_ACTIVA        );
	for( let i = 0 ; i < elementosInteractuables.length ; i++ ) elementosInteractuables[ i ].classList.remove( CLASE_INTERACTUABLE );
	for( let i = 0 ; i < elementosPermanentes.length    ; i++ )    elementosPermanentes[ i ].classList.remove( CLASE_PERMANENTE    );
	
	setBotonApagar();
}

function enteroAleatorioInclusive( min , max ){
	min =  Math.ceil( min );
	max = Math.floor( max );
	return ( Math.floor ( Math.random() * ( max - min + 1 ) + min ) );
}

function flotanteAleatorioInclusive( min , max ){
	return ( ( Math.random() * ( max - min + 1 ) + min ).toFixed( 1 ) );
}