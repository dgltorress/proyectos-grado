


public class Jugador {

    // ****** VARIABLES DE INSTANCIA ******
        private short pos;
        private String alias;
        private byte id, nivel, nivelBase, coldbuff, heatbuff;
        private byte efecto;

        private boolean vivo;

    // ****** CONSTRUCTORES ******
    public Jugador(){
        id = 0;
        pos = 0;

        nivelBase = (byte)-1;
        nivel = nivelBase; // se almacena como negativo

        heatbuff = 0;
        coldbuff = 0;

        efecto = 0; // 0: ninguno, 1: calor, -1: frio

        alias = "user";
        vivo = true;
    }

    public Jugador( byte identificador , String infoJugador ){
        id = identificador;
        pos = 0;

        String[] stats = infoJugador.split(",");

		nivelBase = (byte)-(Byte.parseByte( stats[2] ));
        nivel = nivelBase;

		heatbuff = Byte.parseByte( stats[3] );
		coldbuff = Byte.parseByte( stats[4] );

        efecto = 0;

        alias = stats[0];
        vivo = true;
    }

    // ****** METODOS ******
    // *** GETTERS Y SETTERS ***
    public byte getNivel(){ return nivel; }
    public byte getNivelBase(){ return nivelBase; }

    public byte getId(){ return id; }

    public short getPos(){ return pos; }
    public void setPos( short nuevaPos ){ pos = nuevaPos; }

    public String getAlias(){ return alias; }

    public byte getEfecto(){ return efecto; }

    public byte getBufoCalor(){ return heatbuff; }
    public byte getBufoFrio(){ return coldbuff; }

    public void setColdBuff( byte buff ){ coldbuff = buff; }
    public void setHeatBuff( byte buff ){ heatbuff = buff; }

    public boolean isAlive(){ return vivo; }
    public boolean muere(){
        if( vivo ){
            vivo = false;
            return true;
        }
        return false;
    }

    // *** OTROS ***
    // Intenta subir al jugador de nivel y reporta si ha habido cambios
    public boolean subeNv(){
        --nivel; --nivelBase;

        switch( nivel ){
            case -100: ++nivel; break;
            case 0: --nivel; break;
        }

        switch( nivelBase ){
            case -100: ++nivelBase; return false;
            case 0: --nivelBase; return false;
        }

        return true;
    }

    // Recalcula el nivel (a ejecutarse en cada movimiento)
    public boolean recalcularNv( short[] clima ){
        nivel = nivelBase; // Anula los efectos de cualquier modificador previo
        int auxn = nivel; // Almacena el nivel

        short posCopia = pos;
        short nuevoClima = 20;

        // Elige un clima segun el cuadrante (izquierda a derecha y arriba a abajo)
        if( posCopia > 199 ){
            while( posCopia > 219 ){ posCopia -= 20; }

            if( posCopia < 210 ) nuevoClima = clima[2];
            else                 nuevoClima = clima[3];
        }

        else{
            while( posCopia > 19 ){ posCopia -= 20; }

            if( posCopia < 10 ) nuevoClima = clima[0];
            else                nuevoClima = clima[1];
        }

        boolean correcto = true;
    
        if     ( nuevoClima >= 25 ){ auxn = nivelBase - heatbuff; efecto = 1; } // Aplica un buff si corresponde
        else if( nuevoClima <= 10 ){ auxn = nivelBase - coldbuff; efecto = -1; }
        else efecto = 0;
    
        if     ( auxn < -99 ){ auxn = -99; correcto = false; } // Si el nivel se sale del rango, se ajusta al límite de ese rango
        else if( auxn > -1  ){ auxn = -1; correcto = false; }

        nivel = (byte)auxn; // Se aplica el nuevo nivel, haya habido cambios o no
    
        return correcto; // El valor devuelto indica si el buff de temperatura ha podido aplicarse en su totalidad
    }
}
