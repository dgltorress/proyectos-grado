
import java.net.ServerSocket;
import java.net.Socket;
import java.util.Random;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Scanner;
import java.io.*;
import java.net.*;

public class AA_Nucleo_Engine
{
    private static ArrayList<Socket> clientes = new ArrayList<>();
    private static ArrayList<Hilo> hilos = new ArrayList<>();

    private static byte id = 1;

    private static String[] IDciudades = new String[4];
    private static String[] ciudades = new String[4];
    private static short[] clima = new short[4];

    private static int fase = 0;

    private static boolean partidaAcabada = false;
    private static boolean cuentaAtras = false;

    private static final String APIKEY = ""; // <----------------------------------------------------------
	/* SE REQUIERE UNA API KEY VÁLIDA PARA VER LA TEMPERATURA
	 * DESDE OPENWEATHERMAP PARA PODER INICIAR UNA PARTIDA (https://openweathermap.org/api/#current)
	 */
	 
    public static void main( String[] args )
    {
        int puertoAbierto;
        int maxPlayers;

        Arrays.fill( IDciudades , "" );
        Arrays.fill( ciudades , "" );
        Arrays.fill( clima , (short)0 );

        CuentaAtras cA = null;

        if( args.length < 2 )
        {
            System.out.println("Introduce el puerto a abrir y el max. de jugadores");
            System.out.println("Ejemplo: java AA_Nucleo_Engine 1111 30");
            System.exit(1);
        }

        try
        {
            puertoAbierto = Integer.parseInt(args[0]);
            maxPlayers = Integer.parseInt(args[1]);

            ServerSocket ss = new ServerSocket( puertoAbierto ); // Se abre el socket
            System.out.println( " -- A LA ESCUCHA EN: " + puertoAbierto + " -- " );

            new PrintWriter("mapa.txt").close();

            while( !partidaAcabada )
            {
                switch( fase ){
                    case 2: // EN PARTIDA

                        if( Tablero.getNumeroJugadores() < 2 ){
                            partidaAcabada = true;
                            Tablero.detener();
                            mandarATodos( " --- FIN DE LA PARTIDA ---\n" );
                            if( Tablero.getNumeroJugadores() == 1 ){
                                mandarATodos(" \\\\\\\\\\\\  Ha ganado " + Tablero.getJugadores().get(0).getAlias() + ", felicidades!  ////// " );
                            }
                            mandarATodos( "FIN" );
                            for( int i = 0 ; i < hilos.size() ; ++i ){ hilos.get(i).setFase(-1); }
                        }
                        Thread.sleep(5000);

                    break;

                    case 0: // REUNIENDO JUGADORES

                        Socket sc = ss.accept();
                        System.out.println("Jugador conectado: " + sc.getInetAddress() + ":" + sc.getPort());

                        String infoJugador = leeSocket( sc , "" );

                        if( !infoJugador.equals("") ){
                            clientes.add(sc); // solo se almacena a los jugadores

                            Hilo t = new Hilo( sc , id , infoJugador );
                            t.start();
                            hilos.add(t);

                            String anuncioJugadores = "Jugadores en la sala: " + id + " de " + maxPlayers;
                            System.out.println( anuncioJugadores );
                            mandarATodos( anuncioJugadores );

                            ++id;
                        }
                        else{
                            sc.close(); // corta la conexion con el temporizador
                            fase = 1;
                        }

                        if( !cuentaAtras && id > 2 ){ // Inicia la cuenta atras cuando hay al menos dos jugadores
                            if( maxPlayers != 2 )
                            {
                                cA = new CuentaAtras( 20 , 2 , puertoAbierto ); // 30s, actualiza cada 2s
                                cA.start();
                                cuentaAtras = true;
                            }
                            else fase = 1;
                        }

                        if( id > maxPlayers && cA != null ){ cA.interrupt(); fase = 1;} // MAX JUGADORES O TIMEOUT

                    break;

                    case 1: // PREPARACION DE LA PARTIDA

                        mandarATodos("PARTIDA COMENZANDO...\n\n");

                        // Obtiene una lista con IDs de ciudades
                        IDciudades = obtenerIDCiudadesAleatorias();

                        // Pide climas y nombres de ciudades a OpenWeatherMap
                        System.out.println("Obteniendo climas de OpenWeatherMap...");
                        for( int i = 0 ; i < 4 ; ++i )
                        {
                            String[] nombreyClima = obtenerNombreyClima( pedirClima( IDciudades[i] ) );
                            
                            ciudades[i] = nombreyClima[0];
                            clima[i] = Short.parseShort( nombreyClima[1] );
                        }

                        // Recopila y muestra a todos la informacion de las ciudades
                        StringBuilder infoClimas = new StringBuilder();
                        infoClimas.append("******** CIUDADES ********\n\n");
                        infoClimas.append("  " + ciudades[0] + " - " + clima[0] + "C\t");
                        infoClimas.append("||");
                        infoClimas.append("\t" + ciudades[1] + " - " + clima[1] + "C\t");
                        infoClimas.append("\n");
                        infoClimas.append("  " + ciudades[2] + " - " + clima[2] + "C\t");
                        infoClimas.append("||");
                        infoClimas.append("\t" + ciudades[3] + " - " + clima[3] + "C\t\n\n");

                        String infoClimasString = infoClimas.toString();
                        mandarATodos( infoClimasString );

                        // Registra las ciudades y climas en la BD
                        registrarCiudades();

                        // Prepara el tablero con la informacion necesaria
                        Tablero.preparar( ciudades , clima );
                        System.out.println( Tablero.aString() );

                        System.out.print( infoClimasString );

                        // Manda la lista de jugadores
                        ArrayList<Jugador> jugadores = Tablero.getJugadores();
                        StringBuilder listaJugadores = new StringBuilder();
                        listaJugadores.append("******** JUGADORES EN ESTA PARTIDA [" + (id - 1) + "/" + maxPlayers + "] ********\n\n");
                        for( int i = 0 ; i < jugadores.size() ; ++i ){
                            Jugador j = jugadores.get(i);
                            listaJugadores.append("[" + j.getId() + "] - " + j.getAlias() + " | Posicion: " + j.getPos() +  " | Efectos: Calor (" + j.getBufoCalor() + "Nv) , Frio (" + j.getBufoFrio() + "Nv)\n" );
                        } listaJugadores.append("\n\nPulsa ENTER para comenzar a usar el teclado numerico una vez en la partida.\n\n");

                        String listaJugadoresString = listaJugadores.toString();
                        mandarATodos( listaJugadoresString );
                        System.out.print( listaJugadoresString );

                        for( int i = 0 ; i < hilos.size() ; ++i ){ hilos.get(i).setFase(1); }

                        // registra el tablero inicial
                        Tablero.registrarMapa();

                        // pausa durante 10 segundos con la informacion de la partida
                        Thread.sleep(10000);

                        // inicia el logger
                        Tablero.comenzar();

                        // manda el tablero inicial
                        mandarATodos(Tablero.aString());

                        fase = 2;

                    break;

                    default: System.exit(-1);
                }
            }

            ss.close();
        }
        catch ( Exception e ){ System.out.println( "Parece que ha habido un problema." ); }
    }

    public static String leeSocket (Socket sc, String datos)
	{
		try
		{
			InputStream aux = sc.getInputStream();
			DataInputStream flujo = new DataInputStream( aux );

			datos = new String();
			datos = flujo.readUTF();
		}
		catch ( Exception e ){ System.out.println( "Parece que ha habido un problema." ); }

      return datos;
	}

    public static void escribeSocket (Socket sc, String datos)
	{
		try
		{
			OutputStream aux = sc.getOutputStream();
			DataOutputStream flujo= new DataOutputStream( aux );

			flujo.writeUTF(datos);      
		}
		catch ( Exception e ){
            System.out.println( "ErrorEscribeEngine: " + e.toString() );
            System.out.println( "Socket: " + sc.getInetAddress() + ":" + sc.getPort() );
            System.out.println( "Contenido del mensaje a enviar: " + datos );
        }
		return;
	}

    public static void mandarATodos( String s ){
        for( int i = 0 ; i < clientes.size() ; ++i ){ escribeSocket( clientes.get(i) , s ); }
        // System.out.println( "Enviado a todos: " + s );
    }

    public static void limpiarConsola(){
        try  
        {  
            final String os = System.getProperty("os.name"); 

            if (os.contains("Windows")){
                new ProcessBuilder("cmd", "/c", "cls").inheritIO().start().waitFor();
            }  
            else{ Runtime.getRuntime().exec("clear"); }
        }  
        catch (final Exception e){ e.printStackTrace(); } 
    }

    // obtiene el clima de OpenWeatherMap
    public static String pedirClima( String id )
    {
        String respuesta = "";

        StringBuilder enlace = new StringBuilder();
        enlace.append( "http://api.openweathermap.org/data/2.5/weather?id=" );
        enlace.append( id ); enlace.append( "&appid=" ); enlace.append( APIKEY );
        enlace.append( "&units=metric&lang=es" );

        try{
        URL url = new URL( enlace.toString() );

        HttpURLConnection conexion = (HttpURLConnection)url.openConnection();
        conexion.setRequestProperty("accept", "application/json");

        InputStream respuestaBruto = conexion.getInputStream();
        respuesta = inputStreamaString( respuestaBruto );

        } catch( Exception e ){ System.out.println("Ha habido un problema al obtener los datos del clima. Abortando..."); }

        return respuesta;
    }

    // convierte la respuesta en un String
    public static String inputStreamaString( InputStream iS )
    {
        Scanner s = new Scanner( iS ).useDelimiter("\\A");
        String resultado = s.hasNext() ? s.next() : "";
        s.close();

        return resultado;
    }

    // procesa el JSON de respuesta y devuelve el nombre y la temperatura
    public static String[] obtenerNombreyClima( String respuesta )
    {
        String[] nombreyClima = new String[2];

        String[] procesado = respuesta.split(",");
        String procesadoNom = "", procesadoClim = "";

        for( int i = procesado.length-1 ; i >= 0 ; --i ) // busca el campo del nombre
        {
            if( procesado[i].charAt(1) == 'n' )
            {
                procesadoNom = procesado[i];
                break;
            }
        }

        for( int i = 0 ; i < procesado.length ; ++i ) // busca el campo de temperatura
        {
            if( procesado[i].charAt(9) == 't' && procesado[i].charAt(10) == 'e' )
            {
                procesadoClim = procesado[i];
                break;
            }
        }

        // extrae la informacion de los strings
        nombreyClima[0] = procesadoNom.split(":")[1].replace("\"", "");
        try{ nombreyClima[1] = Integer.toString( Math.round( Float.parseFloat( procesadoClim.split(":")[2] ) ) ); }
        catch( NumberFormatException e ){ System.out.println( "Temperatura obtenida con formato incorrecto:" ); }

        return nombreyClima;
    }

    // Selecciona 4 IDs de ciudad aleatorios no repetidos
    public static String[] obtenerIDCiudadesAleatorias()
    {
        // Lee la lista de ciudades
        ArrayList<String> IDs = new ArrayList<>();
        Random rand = new Random();

        try{
        FileReader fichero = new FileReader( "ciudades.txt" );
		BufferedReader lectura = new BufferedReader( fichero );

		String linea = lectura.readLine();
        
        // Obtiene los IDs de las ciudades
		while( linea != null ) {
			String[] ciudadYClima = linea.split(";");

			if( ciudadYClima.length != 2){
				System.out.println("Formato incorrecto. Los elementos deben almacenarse con el siguiente formato:");
				System.out.println("[ID 1];[NOMBRE CIUDAD 1]");
				System.out.println("[ID 2];[NOMBRE CIUDAD 2]");
				System.out.println("          ....          ");
				System.out.println("[ID n];[NOMBRE CIUDAD n]");
                System.out.println();
                System.out.println("Abortando...");
				System.exit(1);
			}

			IDs.add( ciudadYClima[0] );
			linea = lectura.readLine();
		}
				
		if( fichero != null ) fichero.close();
		if( lectura != null ) lectura.close();
        } catch( IOException e ){ System.out.println( "Ha habido un error al leer la lista de ciudades." ); System.exit(1); }

        String[] ciudadesAleatorias = new String[4];
        Arrays.fill( ciudadesAleatorias , "" );

        // Consigue cuatro IDs aleatorios diferentes en caso de que haya suficientes
        if( IDs.size() > ciudadesAleatorias.length )
        {
            for( int i = 0 ; i < ciudadesAleatorias.length ; ++i )
            {
                boolean existe;

                do{
                    int aleatorio = rand.nextInt( IDs.size() ); // 0 - IDs.size()-1;
                    existe = false;

                    for( int j = 0 ; j < ciudadesAleatorias.length ; ++j )
                    {
                        if( ciudadesAleatorias[j].equals( IDs.get(aleatorio) ) ) existe = true;
                    
                        if( j == 3 && !existe ){
                            ciudadesAleatorias[i] = IDs.get(aleatorio);
                            existe = false;
                        }
                    }
                } while( existe );
            }
        }

        else{
            for( int i = 0 ; i < ciudadesAleatorias.length ; ++i )
            {
                int aleatorio = rand.nextInt( IDs.size() ); // 0 - IDs.size()-1;
                ciudadesAleatorias[i] = IDs.get(aleatorio);
            }
        }

        return ciudadesAleatorias;
    }

    public static void registrarCiudades()
    {
        try
        {
            Writer fichero = new BufferedWriter(new FileWriter("ciudadesElegidas.txt"));

            for( byte i = 0 ; i < ciudades.length-1 ; ++i ) fichero.append( ciudades[i] + ";" + clima[i] + ":" );
            fichero.append( ciudades[ciudades.length-1] + ";" + clima[ciudades.length-1] );

            if( fichero != null ) fichero.close();
        } catch( IOException e ){ System.out.println("Error al registrar las ciudades obtenidas. Abortando..."); System.exit(1); }
    }
}

class CuentaAtras extends Thread // HILO PARA INICIAR LA CUENTA ATRAS
{
	String mensaje;
	
    private int temporizador;
    private int velocidadContador;

    private int puertoLocal;
	
	CuentaAtras( int total , int pace , int puertoAbierto )
	{
	    super("Cuenta atras para empezar la partida.");
		mensaje = "";

        temporizador = total;
        velocidadContador = pace;

        puertoLocal = puertoAbierto;
	}

	@Override
	public void run()
	{
	    try
	    {
            // Crea una cuenta atras en base a los parametros y notifica a todos
	        while( temporizador > 0 ){
                System.out.println( "Tiempo restante: " + temporizador + "s" );
                AA_Nucleo_Engine.mandarATodos( "Tiempo restante: " + temporizador + "s" );

				temporizador -= velocidadContador;
                Thread.sleep( velocidadContador * 1000 );
			}

            // Espabila al engine con un mensaje clave cuando se acaba la cuenta atras
            Socket fake = new Socket( "127.0.0.1" , puertoLocal );
            try
		    {
			    OutputStream aux = fake.getOutputStream();
			    DataOutputStream flujo = new DataOutputStream( aux );

			    flujo.writeUTF("");
		    }
		    catch ( Exception e ){}
            fake.close();
	    }
	    catch(Exception e){}
	}
}
