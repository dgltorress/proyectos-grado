
import java.net.Socket;
import java.io.*;

public class AA_Player {

	public static void main( String[] args )
	{
		AA_Player jugador = new AA_Player();

		String mensaje = "";
		
		String hostEngine;
		String puertoEngine;
		String hostRegistry;
		String puertoRegistry;

		String infoJugador = null;

		int fase = 0;

		if (args.length < 4) {
			System.out.println ("Debe indicar la direccion del servidor y el puerto");
			System.out.println ("Ejemplo: java AA_Player 127.0.0.1 1111 127.0.0.1 3333");
			System.exit(1);
		}

		hostEngine = args[0];
		puertoEngine = args[1];
		hostRegistry = args[2];
		puertoRegistry = args[3];

		try
		{
			Socket s = null;

			InputStreamReader isr = new InputStreamReader( System.in );
			BufferedReader br = new BufferedReader( isr );

			while( !mensaje.equals( "FIN" ) )
			{
				/*
					FASE 0: menú
					FASE 1: crear perfil
					FASE 2: editar perfil
					FASE 3: iniciar sesion
					FASE 4: salir
					(5) en partida
				*/
				switch( fase ){
					case 5: // PARTIDA (traduce los inputs del usuario a coordenadas)

							mensaje = br.readLine();
							System.out.println();
	
							if(mensaje.length() > 0){
								switch( mensaje.charAt(0) ){
									case 56: mensaje = "N "; break;
									case 52: mensaje = " W"; break;
									case 50: mensaje = "S "; break;
									case 54: mensaje = " E"; break;
									case 55: mensaje = "NW"; break;
									case 57: mensaje = "NE"; break;
									case 49: mensaje = "SW"; break;
									case 51: mensaje = "SE"; break;
	
									default: mensaje = "zz";
								}
							}
							else mensaje = "zz";
	
							jugador.escribeSocket( s , mensaje );

					break;

					case 0: // MENU

						System.out.println("                     _           _              _ _ ");
						System.out.println("    /\\              (_)         | |       /\\   | | |");
						System.out.println("   /  \\   __ _  __ _ _ _ __  ___| |_     /  \\  | | |");
						System.out.println("  / /\\ \\ / _` |/ _` | | '_ \\/ __| __|   / /\\ \\ | | |");
						System.out.println(" / ____ \\ (_| | (_| | | | | \\__ \\ |_   / ____ \\| | |");
						System.out.println("/_/    \\_\\__, |\\__,_|_|_| |_|___/\\__| /_/    \\_\\_|_|   Release 2");
						System.out.println("          __/ |");
						System.out.println("         |___/ ");
						System.out.println();
						System.out.println("[1] Crear perfil");
						System.out.println("[2] Editar perfil");
						System.out.println("[3] Unirse a partida");
						System.out.println("[4] Salir");
						System.out.println();

						System.out.print( "Escoge una opcion: " );
						mensaje = br.readLine();

						fase = jugador.escogerOpcion( mensaje );
						
					break;

					case 3: // INICIAR SESION
					
						s = new Socket( hostRegistry , Integer.parseInt( puertoRegistry ) );

						jugador.escribeSocket( s , "L" ); // Indica al servidor que quiere iniciar sesion (login)

						System.out.print( "Introduce tu nombre de usuario: " );
						mensaje = br.readLine();
						jugador.escribeSocket( s , mensaje );

						System.out.print( "Introduce tu contrasena: " );
						mensaje = br.readLine();
						jugador.escribeSocket( s , mensaje );

						mensaje = ( jugador.leeSocket( s , mensaje ) );

						/*
						INICIO DE SESION CORRECTO
						- Recibe su ficha de jugador
						- Se desconecta de registry
						- Se conecta al engine y pasa a modo coordenadas
						*/
						if( mensaje.equals( "YEA" )){
							infoJugador = jugador.leeSocket( s , mensaje );
							s.close();

							s = new Socket( hostEngine , Integer.parseInt( puertoEngine ) );
							jugador.escribeSocket( s , infoJugador ); // manda info de jugador para el constructor
	
							Lector lector = new Lector( s );
							lector.start();
	
							System.out.println("CONECTADO, a la espera de otros jugadores...");
							fase = 5;
						}

						/*
						INICIO DE SESION INCORRECTO
						- Se desconecta y vuelve al menu
						*/
						else{
							System.out.println( "Inicio de sesion incorrecto." );
							s.close();
							fase = 0;
							Thread.sleep(2000);
						}

					break;

					case 1: // CREAR PERFIL

						s = new Socket( hostRegistry , Integer.parseInt( puertoRegistry ) );

						jugador.escribeSocket( s , "C" ); // Indica al servidor que quiere crear un perfil

						System.out.print( "Introduce un nombre de usuario: " );

						do{
							mensaje = br.readLine();

							if( mensaje.length() < 1 || mensaje.length() > 20 ){
								System.out.println("El nombre debe ser de entre 1 y 20 caracteres.");
								mensaje = null;
							}
						} while( mensaje == null );

						jugador.escribeSocket( s , mensaje );

						System.out.print( "Introduce una contrasena (NO SE ENCRIPTA): " );

						do{
							mensaje = br.readLine();

							if( mensaje.length() < 1 || mensaje.length() > 20 ){
								System.out.println("La contrasena debe ser de entre 1 y 20 caracteres.");
								mensaje = null;
							}
						} while( mensaje == null );

						jugador.escribeSocket( s , mensaje );

						System.out.println( jugador.leeSocket( s , mensaje ) ); // Recibe el mensaje de confirmacion

						infoJugador = ( jugador.leeSocket( s , mensaje ) );
						System.out.println( jugador.prettyStats( infoJugador ) ); // y los stats

						Thread.sleep(3000); // Pausa un momento antes de volver a mostrar el menu
						
						s.close();
						fase = 0;

					break;

					case 2: // EDITAR PERFIL

						s = new Socket( hostRegistry , Integer.parseInt( puertoRegistry ) );

						jugador.escribeSocket( s , "E" ); // Indica al servidor que quiere editar un perfil

						System.out.print( "Introduce tu nombre de usuario: " );
						mensaje = br.readLine();
						jugador.escribeSocket( s , mensaje );

						System.out.print( "Introduce tu contrasena: " );
						mensaje = br.readLine();
						jugador.escribeSocket( s , mensaje );

						mensaje = ( jugador.leeSocket( s , mensaje ) );

						if( mensaje.equals( "YEA" )){ // Las credenciales se encuentran en el servidor
							System.out.print( "Introduce el nuevo nombre de usuario: " );
								
							do{
								mensaje = br.readLine();
	
								if( mensaje.length() < 1 || mensaje.length() > 20 ){
									System.out.println("El nombre debe ser de entre 1 y 20 caracteres.");
									mensaje = null;
								}
							} while( mensaje == null );
	
							jugador.escribeSocket( s , mensaje );
	
							System.out.print( "Introduce una nueva contrasena (NO SE ENCRIPTA): " );
	
							do{
								mensaje = br.readLine();
	
								if( mensaje.length() < 1 || mensaje.length() > 20 ){
									System.out.println("La contrasena debe ser de entre 1 y 20 caracteres.");
									mensaje = null;
								}
							} while( mensaje == null );
	
							jugador.escribeSocket( s , mensaje );

							System.out.println( jugador.leeSocket( s , mensaje ) ); // Recibe el mensaje de confirmacion

							infoJugador = ( jugador.leeSocket( s , mensaje ) );
							System.out.println( jugador.prettyStats( infoJugador ) );

							Thread.sleep(3000); // Pausa un momento antes de volver a mostrar el menu
						}
						else{
							System.out.println( "No se ha encontrado un usuario con esas credenciales." );
							Thread.sleep(2000);
						}

						s.close();
						fase = 0;

					break;

					case 4:
						
						System.out.println("\n\nCerrando juego...\n\n");
						System.exit(0);
						
					break;

					default: fase = 0; break;
				}
			}

			System.out.println( "ADIOS" );
		}
		catch( Exception e ){ System.err.println( "Parece que ha habido un problema." ); }
	}

	public String leeSocket( Socket p_sk , String p_Datos )
	{
		try
		{
			InputStream aux = p_sk.getInputStream();
			DataInputStream flujo = new DataInputStream( aux );
			p_Datos = flujo.readUTF();
		}
		catch (Exception e){ System.err.println( "Parece que ha habido un problema." ); }
      return p_Datos;
	}

	public void escribeSocket( Socket p_sk , String p_Datos )
	{
		try
		{
			OutputStream aux = p_sk.getOutputStream();
			DataOutputStream flujo= new DataOutputStream( aux );

			flujo.writeUTF(p_Datos);
			flujo.flush();
		}
		catch ( Exception e ){ System.err.println( "Parece que ha habido un problema." ); }
		return;
	}

	public int escogerOpcion( String num ){
		int fase = 0;

		try{ fase = Integer.parseInt( num ); }
		catch( NumberFormatException e ){ System.out.println( "La opcion debe ser un numero." ); }

		if( fase < 1 || fase > 4 ){
			System.out.println( "Opcion no disponible." );
			fase = 0;
		}

		return fase;
	}

	public String prettyStats( String infoJugador ){
		StringBuilder stats = new StringBuilder();
		String[] info = infoJugador.split(",");

		stats.append( " **** Ficha de jugador (" + info[0] + ") ****\n\n" );

		// stats.append( " Contrasena: " + info[1] + "\n" );

		stats.append( " Nivel: " + info[2] + "\n" );

		stats.append( " Efecto ante el calor (>= 25C): ");
		if( info[3].charAt(0) != '-' ) stats.append("+");
		stats.append( info[3] + "Nv" + "\n" );

		stats.append( " Efecto ante el frio  (<= 10C): ");
		if( info[4].charAt(0) != '-' ) stats.append("+");
		stats.append( info[4] + "Nv" + "\n" );

		return stats.toString();
	}
}

class Lector extends Thread // HILO PARA LEER MENSAJES DEL SERVIDOR
{
	String mensaje;
	boolean salir = false;
	Socket server;
	
	Lector( Socket s )
	{
	    super("Proceso lector");
		mensaje = "";
		server = s;
	}

	@Override
	public void run()
	{
	    try
	    {
	        while( !salir ){
				InputStream aux = server.getInputStream();
				DataInputStream flujo = new DataInputStream( aux );
				mensaje = flujo.readUTF();

				if( mensaje.length() > 0 ){
					// EL SERVIDOR MANDA EL TABLERO
					if( mensaje.charAt(0) == '\n' ) limpiarConsola();
				}

				if( !mensaje.equals("FIN") ) System.out.println( mensaje );
				else salir = true;
			}
			server.close();
			System.exit(0);
	    }
	    catch(Exception e){ System.out.println("Parece que ha habido un problema."); System.exit(1); }
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
        catch (Exception e){ System.out.println("Parece que ha habido un problema."); } 
    }
}
