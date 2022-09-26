
import java.net.Socket;
import java.io.*;

public class HiloRegistry extends Thread
{
	private Socket s;

    public HiloRegistry( Socket sc ){ this.s = sc; }

    public String leeSocket (Socket sc, String datos)
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

    public void escribeSocket (Socket sc, String datos)
	{
		try
		{
			OutputStream aux = sc.getOutputStream();
			DataOutputStream flujo= new DataOutputStream( aux );

			flujo.writeUTF(datos);      
		}
	catch ( Exception e ){ System.out.println( "Parece que ha habido un problema." ); }
		return;
	}

	public String[] calcularEfectos( String nombreUsuario ){
        int len = nombreUsuario.length();
        
        if( len > 0 ){
            char primeraLetra = Character.toLowerCase(nombreUsuario.charAt(0));
            byte heatbuff = 0, coldbuff = 0;
			String[] efectos = new String[2];

            if( primeraLetra > 'v' )     { heatbuff = 4;  coldbuff = -4; }
            else if( primeraLetra > 'q' ){ heatbuff = 2;  coldbuff = -2; }
            else if( primeraLetra > 'j' ){ heatbuff = 0;  coldbuff = 0;  }
            else if( primeraLetra > 'e' ){ heatbuff = -2; coldbuff = 2;  }
            else                         { heatbuff = -4; coldbuff = 4;  }

            if( len > 15 )     { heatbuff += 3; coldbuff += 3; }
            else if( len > 10 ){ heatbuff += 2; coldbuff += 2; }
            else if( len > 7 ) { heatbuff += 1; coldbuff += 1; }
            else if( len < 4 ) { heatbuff -= 1; coldbuff -= 1; }

			efectos[0] = Byte.toString( heatbuff );
			efectos[1] = Byte.toString( coldbuff );

            // System.out.println( "Buffs aplicables para \"" + nombreUsuario + "\":\n" );
            // System.out.println( " CALOR: " + heatbuff + "Nv  |  FRIO: " + coldbuff + "Nv" );

			return efectos;
        }

		return new String[2];
    }
    
    @Override
    public void run(){
		String mensaje = "";

		String username = "";
		String password = "";
		String[] efectos = new String[2];
		
        try {
			mensaje = this.leeSocket ( s , mensaje );

			if( mensaje != null ){
				if( mensaje.equals("C") ){ // EL CLIENTE QUIERE CREAR UN PERFIL
					System.out.println( "NUEVA SOLICITUD DE CREACION DE PERFIL" );

					System.out.println( "A la espera de un nombre de usuario..." );
					username = this.leeSocket ( s , username );
					System.out.println( "Nombre de usuario solicitado: " + username );

					System.out.println( "A la espera de una contrasena..." );
					password = this.leeSocket ( s , password );
					System.out.println( "Contrasena solicitada: " + password );

					efectos = calcularEfectos( username );
					
					Writer fichero = new BufferedWriter(new FileWriter("perfiles.txt", true)); // abre en modo escritura

					String infoJugador = username + "," + password + "," + "1" + "," + efectos[0] + "," + efectos[1];
					fichero.append( infoJugador + "\n" );

					if( fichero != null ) fichero.close();

					this.escribeSocket( s , "Usuario creado con exito.\n" );
					this.escribeSocket( s , infoJugador ); // envia la informacion del jugador

					System.out.println( "Usuario creado con exito. Cerrando socket " + s.getInetAddress() + ":" + s.getPort() + "..." );
				}

				else if( mensaje.equals("E") ){ // EL CLIENTE QUIERE EDITAR UN PERFIL
					System.out.println( "NUEVA SOLICITUD DE EDICION DE PERFIL" );

					System.out.println( "A la espera de un nombre de usuario..." );
					username = this.leeSocket ( s , username );
					System.out.println( "Nombre provisto: " + username );

					System.out.println( "A la espera de una contrasena..." );
					password = this.leeSocket ( s , password );
					System.out.println( "Contrasena provista: " + password );

					FileReader fichero = new FileReader("perfiles.txt");
					BufferedReader lectura = new BufferedReader(fichero);

					String linea = lectura.readLine();
					boolean encontrado = false;

					while( linea != null ) {
						String[] infoJugador = linea.split(",");
						if( username.equals( infoJugador[0] ) && password.equals( infoJugador[1] ) ){
							encontrado = true;
							break;
						}

						linea = lectura.readLine();
					}
				
					if( fichero != null ) fichero.close();
					if( lectura != null ) lectura.close();

					if( encontrado ){
						this.escribeSocket( s , "YEA" );

						efectos = calcularEfectos( username ); // mantiene los efectos del usuario anterior

						System.out.println( "A la espera de un nombre de usuario..." );
						username = this.leeSocket ( s , username );
						System.out.println( "Nuevo nombre de usuario solicitado: " + username );

						System.out.println( "A la espera de una contrasena..." );
						password = this.leeSocket ( s , password );
						System.out.println( "Nueva contrasena solicitada: " + password );

						Writer fichero2 = new BufferedWriter(new FileWriter("perfiles.txt", true)); // abre en modo escritura

						String infoJugador = username + "," + password + "," + "1" + "," + efectos[0] + "," + efectos[1];
						fichero2.append( infoJugador + "\n" );

						if( fichero2 != null ) fichero2.close();

						this.escribeSocket( s , "Usuario actualizado con exito.\n" );
						this.escribeSocket( s , infoJugador );
					}
					else this.escribeSocket( s , "NAY" );

					System.out.println( "Cerrando socket " + s.getInetAddress() + ":" + s.getPort() + "..." );
				}

				else if( mensaje.equals("L") ){ // CONCEDE PERMISO PARA ENTRAR EN UNA PARTIDA
					System.out.println( "NUEVA SOLICITUD DE INICIO DE SESION" );

					System.out.println( "A la espera de un nombre de usuario..." );
					username = leeSocket ( s , username );
					System.out.println( "Nombre provisto: " + username );

					System.out.println( "A la espera de una contrasena..." );
					password = leeSocket ( s , password );
					System.out.println( "Contrasena provista: " + password );

					FileReader fichero = new FileReader("perfiles.txt");
					BufferedReader lectura = new BufferedReader(fichero);

					String linea = lectura.readLine();
					boolean encontrado = false;

					while( linea != null ) {
						String[] infoJugador = linea.split(",");
						if( username.equals( infoJugador[0] ) && password.equals( infoJugador[1] ) ){
							encontrado = true;
							break;
						}

						linea = lectura.readLine();
					}
                    
					if( fichero != null ) fichero.close();
					if( lectura != null ) lectura.close();

					if( encontrado ){
						this.escribeSocket( s , "YEA" );
						this.escribeSocket( s , linea );
						System.out.print("Inicio de sesion correcto. ");
					}
					else{
						this.escribeSocket( s , "NAY" );
						System.out.print("Credenciales incorrectas. ");
					}

					System.out.println( "Cerrando socket " + s.getInetAddress() + ":" + s.getPort() + "..." );
				}
			}
			s.close();
			System.out.println("Socket cerrado.");
        }
        catch ( Exception e ){ System.out.println( "Parece que ha habido un problema." ); Thread.currentThread().interrupt(); return; }
    }
}
