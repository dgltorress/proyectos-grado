
import java.io.PrintWriter;
import java.net.ServerSocket;
import java.net.Socket;

public class AA_Nucleo_Registry
{
    public static void main( String[] args )
    {
        if( args.length < 1 )
        {
            System.out.println("Introduce el puerto como argumento.");
            System.out.println("Ejemplo: " + "AA_Nucleo_Registry" + " 3333");
            System.exit(1);
        }

        try
        {
            new PrintWriter("perfiles.txt").close(); // Limpia el fichero

            ServerSocket ss = new ServerSocket( Integer.parseInt(args[0]) ); // Se abre el socket
            System.out.println( " -- A LA ESCUCHA EN: " + args[0] + " -- " );

            for(;;)
            {
                Socket sc = ss.accept();
                System.out.println("Atendiendo a: " + sc.getInetAddress() + ":" + sc.getPort());

                Thread t = new HiloRegistry( sc );
                t.start();
            }
        }
        catch ( Exception e ){ System.out.println( "Parece que ha habido un problema." ); }
    }
}
