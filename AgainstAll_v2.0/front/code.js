'use strict';

let mapa = new Array(400), // almacena referencias a la tabla para acceso constante
    ciudades = new Array(4), // almacena referencias a la cabeza de las tablas que muestran la ciudad
    listaJugadores = undefined, // almacena una referencia a la lista ordenada de jugadores
    notificado = false;

mapa.fill(undefined); // se llena el array de valores sin definir y se sella
Object.seal(mapa);

ciudades.fill(undefined);
Object.seal(ciudades);

const gC = '&#186;C';

// pone el anyo actual en el pie
function anyo()
{
  let elemento = document.getElementById("curYear"),
      nuevoAnyo = new Date().getFullYear();

    elemento.innerHTML = nuevoAnyo;
    elemento.setAttribute( 'datetime' , nuevoAnyo );
}

// busca si hay una partida que coincida con la IP y puerto dados
// e invoca el metodo correspondiente
function buscarPartida( formulario )
{
  quitarNotFound();

  let fd = new FormData( formulario ),
      ip = fd.get('IP'),
      puerto = fd.get('puerto');
  
  let xhr = new XMLHttpRequest(),
      url = `http://${ip}:${puerto}`;console.log(url);

  xhr.open( 'GET' , url , true );console.log(url);

  xhr.onload = function()
  {
    if( xhr.responseText === 'hola' ) mostrarTablero( ip , puerto );
    else                              partidaNoEncontrada();
  }

  xhr.onerror = function(){ partidaNoEncontrada(); }

  xhr.send();
}

// genera el infograma de la partida
// almacena las referencias
// y comienza el proceso de peticiones
function mostrarTablero( ip , puerto )
{
  // Borra la pagina de inicio dinamicamente
  document.getElementById('paginaPrincipal').remove();

  // CREA LOS CONTENEDORES
  // partida
  let contenedorPartida = document.createElement('div');
  contenedorPartida.classList.add('contenidoPrincipal');
  contenedorPartida.setAttribute('id','partida');

  let cabeceraPartida = document.createElement('h1');
  cabeceraPartida.classList.add('finPartida');
  cabeceraPartida.innerHTML = `Comenzando partida en ${ip}:${puerto}`;

  // bandeja
  let bandejaPartida = document.createElement('div');
  bandejaPartida.classList.add('bandeja');

  // jugadores y tablero
  let jugadoresPartida = document.createElement('div'),
      tableroPartida   = document.createElement('div');
  jugadoresPartida.classList.add('contenido'); jugadoresPartida.setAttribute('id','listaJugadores');
    tableroPartida.classList.add('contenido'); tableroPartida.setAttribute('id','tablero');

  let cabeceraLista = document.createElement('h2');
  cabeceraLista.innerHTML = 'Jugadores en partida';

  // lista de jugadores (SE GUARDA UNA REFERENCIA)
  let listaJugadoresPartida = document.createElement('ol');
  listaJugadores = listaJugadoresPartida;

  // decoraciones del tablero
  let decoracionNorte = document.createElement('p'),
      decoracionSur   = document.createElement('p');
    decoracionNorte.classList.add('decoracion');
      decoracionSur.classList.add('decoracion');
    decoracionNorte.innerHTML = '/==================/-====/-===/-=//-- ( N ) --\\\\=-\\===-\\====-\\==================\\';
      decoracionSur.innerHTML = '\\==================\\-====\\-===\\-=\\\\-- ( S ) --//=-/===-/====-/==================/';

  // separadores de tablas
  let separador1 = document.createElement('div'),
      separador2 = document.createElement('div');
  separador1.classList.add('separaTablas');
  separador2.classList.add('separaTablas');

  // tablas
  for( let i = 0 ; i < 4 ; ++i )
  {
    let tabla = document.createElement('table'),
        colum = document.createElement('colgroup'),
        cabec = document.createElement('th');

    colum.setAttribute('span','10');
    cabec.setAttribute('id',`ciudad[${i+1}]`); cabec.setAttribute('colspan','10');
    ciudades[i] = cabec;

    tabla.appendChild(colum); tabla.appendChild(cabec);

    for( let j = 0 ; j < 10 ; ++j )
    {
      let fila = document.createElement('tr');

      for( let k = 0 ; k < 10 ; ++k )
      {
        let celda = document.createElement('td');
        fila.appendChild(celda);

        // ALMACENA UNA REFERENCIA A CADA CELDA
        switch(i) // dependiendo de la tabla, cambia el algoritmo
        {
          case 0: mapa[k+(j*20)] = celda; break;
          case 1: mapa[k+((j*20)+10)] = celda; break;
          case 2: mapa[k+((j*20)+200)] = celda; break;
          case 3: mapa[k+(((j*20)+10)+200)] = celda; break;
        }
      }

      tabla.appendChild(fila);
    }

    if( i < 2 ) separador1.appendChild(tabla);
    else        separador2.appendChild(tabla);
  }

  // se pegan todos los contenedores en orden
  document.body.insertBefore(contenedorPartida, document.body.firstChild);

  contenedorPartida.appendChild(cabeceraPartida);
  contenedorPartida.appendChild(bandejaPartida);

  bandejaPartida.appendChild(jugadoresPartida);
  bandejaPartida.appendChild(tableroPartida);

  jugadoresPartida.appendChild(cabeceraLista);
  jugadoresPartida.appendChild(listaJugadoresPartida);

  tableroPartida.appendChild(decoracionNorte);
  tableroPartida.appendChild(separador1);
  tableroPartida.appendChild(separador2);
  tableroPartida.appendChild(decoracionSur);

  actualizarTablero( cabeceraPartida , ip , puerto );
}

// modifica el contenido del tablero durante la partida
function actualizarTablero( cabeceraPartida , ip , puerto )
{
  // se almacena el estado de la partida (0: sin empezar, 1: en curso, 2: terminada)
  let estado = 0,
      enlace = `http://${ip}:${puerto}/`,
      ciudadesPedidas = false,
      apiCaida = false;

  // COMIENZA EL LOOP DE PETICIONES
  let idInterval = setInterval( function()
    {
      // pide el mapa
      let xhrMapa = new XMLHttpRequest();

      xhrMapa.open( 'GET' , `${enlace}mapa` , true );
      xhrMapa.onload = function()
      {
        if( apiCaida ){ apiCaida = false; estado = 0; notificarApiCaida( apiCaida , cabeceraPartida ); }
        let casillas = xhrMapa.responseText.split(',');

        // si el mapa no tiene el formato correcto,
        // se asume que la partida no ha empezado
        // o que acaba de terminar
        if( casillas.length != 400 )
        {
          switch( estado )
          {
            case 0: break;
            case 1:
              estado = 2;
              cabeceraPartida.classList.replace('viendoPartida','finPartida');
              cabeceraPartida.innerHTML = `Partida de ${ip}:${puerto} terminada`;
            break;
            default: break;
          }
        }
        // si lo tiene, se ejecutan el resto de instrucciones
        else
        {
          if( estado === 0 )
          {
            estado = 1;
            cabeceraPartida.classList.replace('finPartida','viendoPartida');
            cabeceraPartida.innerHTML = `Viendo partida de ${ip}:${puerto}`;
          }

          if( !ciudadesPedidas )
          {
            // pide las ciudades una vez
            let xhrCity = new XMLHttpRequest();

            xhrCity.open( 'GET' , `${enlace}city` , true );
            xhrCity.onload = function()
            {
              let respuestaCiudades = xhrCity.responseText.split(':'),
                  numeroCiudad = 0;
              for( let respuestaCiudad of respuestaCiudades )
              {
                let grupoCC = respuestaCiudad.split(';');
                ciudades[numeroCiudad].innerHTML = `${grupoCC[0]} (${grupoCC[1]}${gC})`;
                numeroCiudad++;
              }
              ciudadesPedidas = true;
            }
            xhrCity.onerror = function(){ console.log('Error al obtener las ciudades'); clearInterval( idInterval ); }
            xhrCity.send();
          }

          let numeroCasilla = 0;
          // actualiza las casillas con los objetos
          for( let casilla of casillas )
          {
            if( casilla !== '32' ) // no es espacio vacio
            {
              if( casilla.charAt(0) !== '-' ) // no es un jugador
              {
                if     ( casilla === '46' )  mapa[numeroCasilla].innerHTML = '.'; // alimento
                else if( casilla === '120' ) mapa[numeroCasilla].innerHTML = 'X'; // mina
              }
            }
            else mapa[numeroCasilla].innerHTML = '';
            numeroCasilla++;
          }

          // pide la lista de jugadores
          let xhrPlayer = new XMLHttpRequest();
            
          xhrPlayer.open( 'GET' , `${enlace}player` , true );
          xhrPlayer.onload = function()
          {
            let jugadores = xhrPlayer.responseText.split(':');

            listaJugadores.innerHTML = '';
            for( let jugadore of jugadores )
            {
              let jugador = jugadore.split(';'),
                  elementoLista = document.createElement('li'),
                  posicion = parseInt(jugador[0]),
                  nivel = jugador[2].replace('-','');
              
              elementoLista.innerHTML = `${jugador[1]} | Nv.${jugador[2].replace('-','')}`;
              listaJugadores.appendChild(elementoLista);
            
              mapa[posicion].innerHTML = `${jugador[1]}<br>Nv.${nivel}`;
            }
          }
          xhrPlayer.onerror = function(){ console.log('Error al obtener la lista de jugadores'); clearInterval( idInterval ); }
          xhrPlayer.send();
        }
      }
      xhrMapa.onerror = function(){ apiCaida = true; notificarApiCaida( apiCaida , cabeceraPartida ); }
      xhrMapa.send();

      // sale del loop cuando la partida haya acabado
      if( estado === 2 ) clearInterval( idInterval );
    },750); // actualiza cada 750ms
}

// notifica con un mensaje bajo el formulario
// de que no se ha encontrado una partida
function partidaNoEncontrada()
{
  if( !document.getElementById('notFound') )
  {
    let paginaPrincipal = document.getElementById('paginaPrincipal');

    if( paginaPrincipal )
    {
      let mensaje = document.createElement('p');
      mensaje.setAttribute( 'id' , 'notFound' );
      mensaje.innerHTML = 'No se ha podido encontrar esa partida';

      paginaPrincipal.appendChild( mensaje );
    }
  }
}

function quitarNotFound()
{
  let mensaje = document.getElementById('notFound');
  if( mensaje ) mensaje.remove();
}

// notifica con un mensaje bajo el formulario
// de que no se ha encontrado una partida
function notificarApiCaida( estaCaida , cabeceraPartida )
{
  if( cabeceraPartida )
  {
    if( !notificado && estaCaida )
    {
      cabeceraPartida.classList.add('partidaCaida')
      cabeceraPartida.innerHTML = 'API no disponible';

      notificado = true;
    }
    else if( notificado && !estaCaida )
    {
      cabeceraPartida.classList.remove('partidaCaida')
      cabeceraPartida.innerHTML = 'Reconectando a la partida...';

      notificado = false;
    }
  }
}