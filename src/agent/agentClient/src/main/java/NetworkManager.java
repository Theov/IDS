import java.io.IOException;
import java.io.OutputStream;
import java.io.PrintWriter;
import java.net.InetAddress;
import java.net.Socket;

public class NetworkManager {
    Socket agentSocket;

    public NetworkManager(String adress, int port) {
        connect(adress, port);
    }

    public void connect(String adress, int port){
        try {
            agentSocket = new Socket(InetAddress.getByName(adress), port);
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    public void disconnect() {
        try {
            agentSocket.close();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    public void send(String s) {
        try {
            PrintWriter out = new PrintWriter(agentSocket.getOutputStream());
            out.println(s);
            out.flush();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}
