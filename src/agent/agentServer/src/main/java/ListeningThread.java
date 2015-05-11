import java.io.IOException;
import java.net.ServerSocket;
import java.net.Socket;

public class ListeningThread extends Thread{
    private ServerSocket serverSocket;
    private MySQL db;

    private ConfigModel modelConfiguration;

    private ManagerThread managerThread;

    private boolean stop = true;

    public ListeningThread(){
        db = new MySQL();
        db.connect();

        modelConfiguration = new ConfigModel(db);
        modelConfiguration.getConfigDataFromDb();

        managerThread = new ManagerThread(db);

        createServerSocket();
        managerThread.startThread();
    }

    private void createServerSocket() {
        try {
            serverSocket = new ServerSocket(modelConfiguration.port());
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    public void startThread(){
        stop = false;
        this.start();
    }

    public void stopThread(){
        stop = true;

        try {
            serverSocket.close();
        } catch (IOException e) {
            e.printStackTrace();
        }

        db.disconnect();

        this.interrupt();
    }

    @Override
    public void run(){
        while (!stop){
            try {
                Socket newSocket = serverSocket.accept();
                managerThread.addAgent(newSocket);
            } catch (IOException e) {
                e.printStackTrace();
            }
        }
    }
}
