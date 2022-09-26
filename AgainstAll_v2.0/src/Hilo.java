
import java.net.Socket;
import java.io.*;

public class Hilo extends Thread
{
	private Socket s;
	private byte id;
	private int fase = 0;

	private String infoJugador;

    public Hilo( Socket sc , byte identificador , String stats )
    {
        this.s = sc;
		id = identificador;
		infoJugador = stats;
    }

	public void setFase( int i ){ fase = i; }

    public String leeSocket (Socket sc, String datos)
	{
		try
		{
			InputStream aux = sc.getInputStream();
			DataInputStream flujo = new DataInputStream( aux );

			datos = new String();
			datos = flujo.readUTF();
		}
		catch ( Exception e ){ return ""; }

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
    
    @Override
    public void run(){
		String mensaje = "";

		Jugador j = new Jugador( id , infoJugador );
		Tablero.meterJugador(j);
		
        try {
			while (fase != -1)
			{
				switch( fase ){
					case 1: // EN PARTIDA

						mensaje = this.leeSocket ( s , mensaje );
						System.out.println( "Mensaje recibido [" + s.getInetAddress() + ":" + s.getPort() + "]: " + mensaje );

						// hace cosas

						if( j.isAlive() ){
							char[] direccion = new char[2];
							direccion[0] = mensaje.charAt(0); direccion[1] = mensaje.charAt(1);

							Tablero.movimiento( j , direccion );

							AA_Nucleo_Engine.mandarATodos( Tablero.aString() );
						}

						else{ this.escribeSocket( s , "HAS MUERTO" ); }

					break;

					case 0:

						mensaje = this.leeSocket ( s , mensaje );
						System.out.println( "Mensaje recibido: " + mensaje );

					break;

					default:

						mensaje = this.leeSocket ( s , mensaje );
						System.out.println( "Mensaje recibido: " + mensaje );
				}
				
				if( mensaje.equals("FIN") ) fase = -1;
			}
			s.close();			
        }
        catch ( Exception e ){ return; }
    }
}
