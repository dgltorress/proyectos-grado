
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Random;
import java.io.*;

public class Tablero {

    // ****** VARIABLES DE INSTANCIA ******
    private static byte[] mapa = new byte[400]; // Almacena los objetos del mapa y los niveles de jugador en negativo
    private static byte[] mapaIDs = new byte[400]; // Mapa alternativo con los identificadores de jugador

    private static String[] ciudades = new String[4]; // Contiene los nombres de las cuatro ciudades
    private static short[] clima = new short[4]; // Contiene los climas de las cuatro ciudades

    private static ArrayList<Jugador> jugadores = new ArrayList<>(); // Contiene la lista de jugadores en la partida
    private static Random rand = new Random();

    private static final char ALIMENTO = '.';
    private static final char MINA =     'x';

    private static registroMapa reg = null;
    private static boolean registrando = false;

    // ****** CONSTRUCTORES ******
    Tablero(){
        
    }

    // ****** METODOS ******
    // *** GETTERS Y SETTERS ***
    public static ArrayList<Jugador> getJugadores(){ return jugadores; }
    public static int getNumeroJugadores(){ return jugadores.size(); }
    public static boolean estaRegistrando(){ return registrando; }

    // *** OTROS ***
    // Procesa el movimiento de un jugador y maneja choques entre entidades
    public static boolean movimiento( Jugador j , char[] comando ){
        boolean valido = true;
        
        if( j.isAlive() ){
            short posOriginal = j.getPos();
            short pos = posOriginal;
            
            byte sinComandos = 0;
            
            switch( comando[0] ){ // NORTE - SUR
                case 'N': pos-=20; if( pos < 0 )   pos+=400; break;      // Norte (N)
                case 'S': pos+=20; if( pos > 399 ) pos-=400; break;      // Sur (S)
            
                default: ++sinComandos; break;
            }
        
            switch( comando[1] ){ // ESTE - OESTE
                case 'E': ++pos; if( pos%20 == 0 )       pos-=20; break; // Este (E)
                case 'W': --pos; if( (pos + 1)%20 == 0 ) pos+=20; break; // Oeste (W)
            
                default: ++sinComandos; break;
            }
        
            if( sinComandos == 2 ) valido = false;
        
            if( valido ){ // Si hay movimiento
                byte ent = mapa[pos]; // Entidad de la posicion hacia la que se pretende mover
            
                if( ent != ' ' ){ // CHOQUE: si en la nueva casilla hay una entidad, se produce un choque
                    if( ent >= 0 ){ // Es un objeto (No es un caracter reservado para niveles de jugador)
                        switch( ent ){
                        
                            // Alimento, sube de nivel y se borra el alimento del mapa
                            // Mina, ambas entidades se borran del mapa, y al jugador de la partida
                            case ALIMENTO: j.subeNv(); desplazar( j , posOriginal , pos ); break; 
                            case MINA: eliminarJugador( mapaIDs[posOriginal] ); mapa[pos] = ' '; break;
                        }
                    }
                    else{ // Es un jugador
                        byte nivel = j.getNivel();
                        // Jugador que se mueve gana  : se borra al otro jugador de la partida y se mueve al instigador a su posicion
                        // Jugador que se mueve pierde: se borra al instigador de la partida
                        // Empate                     : no ocurre nada y el movimiento se marca como no valido
                    
                        if( nivel < ent ){
                            eliminarJugador( mapaIDs[pos] );
                            desplazar( j , posOriginal , pos );
                        }
                        else if( nivel > ent ) eliminarJugador( mapaIDs[posOriginal] );
                        else valido = false;
                    }
                
                }

                else desplazar( j , posOriginal , pos ); // si la nueva casilla esta vacia, simplemente se desplaza al jugador
            
            }
        }
        else valido = false;

        return valido;
    }

    // Mueve un jugador de una casilla a otra sin hacer comprobaciones
    public static void desplazar( Jugador j , short posIni , short posFin ){
        j.setPos( posFin );
        j.recalcularNv( clima );
        
        mapa[posFin] = j.getNivel(); mapaIDs[posFin] = j.getId();
        mapa[posIni] = ' ';          mapaIDs[posIni] = -1;
    }

    // Elimina un jugador del tablero y de la lista de jugadores
    public static boolean eliminarJugador( byte id ){
        for( short i = 0 ; i < jugadores.size() ; ++i ){
            if( jugadores.get(i).getId() == id ){
                short pos = jugadores.get(i).getPos();
    
                mapa[pos] = ' ';
                mapaIDs[pos] = -1;
                
                jugadores.get(i).muere();
                jugadores.remove(i);
                return true;
            }
        }
        return false;
    }

    // Anade a un jugador a la partida
    public static boolean meterJugador( Jugador j ){
        jugadores.add(j);
        return true;
    }

    // Sitúa a los jugadores en el mapa al inicio de la partida
    public static void situarJugadores(){
        int numJugadores = jugadores.size();
        boolean situado = false;
        
        for( int i = 0 ; i < numJugadores ; ++i ){
            while( !situado ){
                short num = (short)rand.nextInt(400); // 0 - 399
    
                if( mapa[num] == ' ' ){
                    jugadores.get(i).setPos(num);
                    mapa[num] = jugadores.get(i).getNivel();
                    mapaIDs[num] = jugadores.get(i).getId();
    
                    situado = true;
                }
            }
            situado = false;
        }
    }

    // Llena los huecos vacíos del mapa con alimento y minas
    public static void confitar( int dificultad ){
        byte pA;
        byte pM; // probabilidad de alimento y minas ( pM real = 100 - pM )

        switch( dificultad ){
            case 0: pA = 40; pM = 90; break;  // FACIL:   alimento abundante y minas escasas
                                              // NORMAL:  alimento y minas a niveles moderados
            case 2: pA = 20; pM = 60; break;  // DIFICIL: alimento escaso y minas comunes

            case 3: pA = 40; pM = 100; break; // MODO FUNKY: solo alimento

            default: pA = 30; pM = 75; break; // dificultad NORMAL por defecto 75
        }

        for( short i = 0 ; i < 400 ; ++i ){
            if( mapa[i] == 32 ){
                int num = rand.nextInt(100) + 1; // 1 - 100

                if( num < pA ) mapa[i] = ALIMENTO;
                else if( num > pM ) mapa[i] = MINA;
            }
        }
    }

    public static void preparar( String[] cs , short[] clms ){
        Arrays.fill( mapa , (byte)' ' );
        Arrays.fill( mapaIDs , (byte)-1 );
        situarJugadores();
        confitar( 1 );

        ciudades = cs;
        clima = clms;
    }

    public static String aString(){
        StringBuilder s = new StringBuilder();
        byte ent;
        
        s.append("\n\n                                    N\n");
        s.append("  *********************************************************************\n");
        
        short pos = 0;
    
        for( char i = 0 ; i < 10 ; ++i ){
            s.append("  || ");
    
            ent = mapa[pos];
            if( ent < 0 ){
                s.append((-ent) + " ");
                if( ent > -10 ) s.append(" ");
            }
            else{ s.append((char)ent + "  "); }
            ++pos;
    
            for( char j = 1 ; j < 10 ; j++ ){
                ent = mapa[pos];
                if( ent < 0 ){
                    s.append((-ent) + " ");
                    if( ent > -10 ) s.append(" ");
                }
                else{ s.append((char)ent + "  "); }
                ++pos;
            }
            s.append("| | ");
            for( char j = 10 ; j < 20 ; j++ ){
                ent = mapa[pos];
                if( ent < 0 ){
                    s.append((-ent) + " ");
                    if( ent > -10 ) s.append(" ");
                }
                else{ s.append((char)ent + "  "); }
                ++pos;
            }
            s.append("||\n");
        }
    
        s.append("W ||=================================================================|| E\n");
    
        for( char i = 10 ; i < 20 ; ++i ){
            s.append("  || ");
    
            ent = mapa[pos];
            if( ent < 0 ){
                s.append((-ent) + " ");
                if( ent > -10 ) s.append(" ");
            }
            else{ s.append((char)ent + "  "); }
            ++pos;
    
            for( char j = 1 ; j < 10 ; j++ ){
                ent = mapa[pos];
                if( ent < 0 ){
                    s.append((-ent) + " ");
                    if( ent > -10 ) s.append(" ");
                }
                else{ s.append((char)ent + "  "); }
                ++pos;
            }
            s.append("| | ");
            for( char j = 10 ; j < 20 ; j++ ){
                ent = mapa[pos];
                if( ent < 0 ){
                    s.append((-ent) + " ");
                    if( ent > -10 ) s.append(" ");
                }
                else{ s.append((char)ent + "  "); }
                ++pos;
            }
            s.append("||\n");
        }
    
        s.append("  *********************************************************************\n");
        s.append("                                    S\n");

        return s.toString();
    }

    public static String verMapaIDs(){
        StringBuilder s = new StringBuilder();
        byte ent;
        
        s.append("\n\n                                    N\n");
        s.append("  *********************************************************************\n");
        
        short pos = 0;
    
        for( char i = 0 ; i < 10 ; ++i ){
            s.append("  || ");
    
            ent = mapaIDs[pos];
            if( ent < 0 ){
                s.append(' ' + " ");
                if( ent > -10 ) s.append(" ");
            }
            else{ s.append(ent + "  "); }
            ++pos;
    
            for( char j = 1 ; j < 10 ; j++ ){
                ent = mapaIDs[pos];
                if( ent < 0 ){
                    s.append(' ' + " ");
                    if( ent > -10 ) s.append(" ");
                }
                else{ s.append(ent + "  "); }
                ++pos;
            }
            s.append("| | ");
            for( char j = 10 ; j < 20 ; j++ ){
                ent = mapaIDs[pos];
                if( ent < 0 ){
                    s.append(' ' + " ");
                    if( ent > -10 ) s.append(" ");
                }
                else{ s.append(ent + "  "); }
                ++pos;
            }
            s.append("||\n");
        }
    
        s.append("W ||=================================================================|| E\n");
    
        for( char i = 10 ; i < 20 ; ++i ){
            s.append("  || ");
    
            ent = mapaIDs[pos];
            if( ent < 0 ){
                s.append(' ' + " ");
                if( ent > -10 ) s.append(" ");
            }
            else{ s.append(ent + "  "); }
            ++pos;
    
            for( char j = 1 ; j < 10 ; j++ ){
                ent = mapaIDs[pos];
                if( ent < 0 ){
                    s.append(' ' + " ");
                    if( ent > -10 ) s.append(" ");
                }
                else{ s.append(ent + "  "); }
                ++pos;
            }
            s.append("| | ");
            for( char j = 10 ; j < 20 ; j++ ){
                ent = mapaIDs[pos];
                if( ent < 0 ){
                    s.append(' ' + " ");
                    if( ent > -10 ) s.append(" ");
                }
                else{ s.append(ent + "  "); }
                ++pos;
            }
            s.append("||\n");
        }
    
        s.append("  *********************************************************************\n");
        s.append("                                    S\n");

        return s.toString();
    }

    // Almacena el mapa actual y los jugadores
    public static void registrarMapa()
    {
        StringBuilder registro = new StringBuilder();

        for( int i = 0 ; i < mapa.length-1 ; ++i ) registro.append( mapa[i] + "," );
        registro.append( mapa[399] );

        try{
            // abre el fichero del mapa y jugadores en modo sustituir
            Writer ficheroMapa =      new BufferedWriter(new FileWriter("mapa.txt"));
            Writer ficheroJugadores = new BufferedWriter(new FileWriter("player.txt"));

            ficheroMapa.write( registro.toString() ); // escribe los datos actualizados del ultimo movimiento

            // almacena el nombre y la posicion de cada jugador
            for( int i = 0 ; i < jugadores.size()-1 ; ++i )
            {
                Jugador jugador = jugadores.get(i);

                ficheroJugadores.append( jugador.getPos() + ";" + jugador.getAlias() + ";" + jugador.getNivel() + ":" );
            }Jugador jugador = jugadores.get(jugadores.size()-1); ficheroJugadores.append( jugador.getPos() + ";" + jugador.getAlias() + ";" + jugador.getNivel() );

            // cierra el fichero
            if( ficheroMapa != null )      ficheroMapa.close();
            if( ficheroJugadores != null ) ficheroJugadores.close();

        } catch( IOException e ){ System.out.println( "ERROR AL ACTUALIZAR LA BASE DE DATOS" ); }
    }

    public static void registrarFin()
    {
        try{
            Writer ficheroMapa = new BufferedWriter(new FileWriter("mapa.txt"));
            ficheroMapa.write( "FIN" );
            if( ficheroMapa != null ) ficheroMapa.close();

        } catch( IOException e ){ System.out.println( "ERROR AL ACTUALIZAR LA BASE DE DATOS" ); }
    }

    public static void comenzar()
    {
        registrando = true;
        reg = new registroMapa( 1 ); // actualiza el mapa cada 1 segundo
        reg.start();
    }

    public static void detener(){ registrando = false; registrarFin(); }
}

class registroMapa extends Thread // HILO PARA GUARDAR EL MAPA EN LA BD PERIODICAMENTE
{
	private int velocidadContador;
    
    registroMapa( int pace )
	{
	    super("Registro periodico del mapa");
		velocidadContador = pace;
	}

	@Override
	public void run()
	{
	    try
	    {
            // registra el mapa actual y pausa durante los segundos indicados
	        while( Tablero.estaRegistrando() ){
                Tablero.registrarMapa();
                Thread.sleep( velocidadContador * 1000 );
			}
	    }
	    catch(Exception e){}
	}
}
