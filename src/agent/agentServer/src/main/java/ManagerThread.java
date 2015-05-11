import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.Socket;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;

public class ManagerThread extends Thread{
    private ArrayList<Socket> agentsSockets;
    private MySQL db;
    private boolean stop = true;

    @Override
    public void run(){
        while (!stop){
            for(Socket agent : agentsSockets){
                String msg = readDataFromSocket(agent);
                resolve(msg);
            }

            try {
                Thread.sleep(1000);
            } catch (InterruptedException e) {
                e.printStackTrace();
            }
        }
    }

    private void resolve(String msg) {
        if(msg.contains(":")){
            String location = msg.split(" : ")[0];
            String state = msg.split(" : ")[1];
            updateAgentState(location, state);
        }
    }

    private void updateAgentState(String location, String state) {
        boolean agentAlreadyRegister = false;
        String query = "SELECT id FROM agent WHERE location = '" + location + "'";
        ResultSet result = db.select(query);

        try {
            agentAlreadyRegister = result.first();
        } catch (SQLException e) {
            e.printStackTrace();
        }

        if(agentAlreadyRegister){
            try {
                db.exec("UPDATE agent SET state = '" + state + "' WHERE id = " + Integer.parseInt(result.getString("id")));
            } catch (SQLException e) {
                e.printStackTrace();
            }
        }else{
            db.exec("INSERT INTO agent (`location`, `state`) VALUES ('" + location + "', '" + state + "')");
        }
    }

    private String readDataFromSocket(Socket agent) {
        String messageFromAgent = "empty";

        try {
            BufferedReader br = new BufferedReader(new InputStreamReader(agent.getInputStream()));
            messageFromAgent = br.readLine();
        } catch (IOException e) {
            messageFromAgent = "error";

            try {
                agent.close();
            } catch (IOException e1) {
                e1.printStackTrace();
            }
        }

        return messageFromAgent;
    }

    public void addAgent(Socket newAgent){
        agentsSockets.add(newAgent);
    }

    public void startThread(){
        stop = false;
        this.start();
    }

    public void stopThread(){
        stop = true;
        this.interrupt();
    }

    public ManagerThread(MySQL _db){
        db = _db;
        cleanOldAgentsData();
        agentsSockets = new ArrayList<Socket>();
    }

    private void cleanOldAgentsData() {
        db.clearTable("agent");
    }
}
