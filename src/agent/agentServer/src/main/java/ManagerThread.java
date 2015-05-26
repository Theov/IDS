import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.net.Socket;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.HashMap;
import java.util.Map;

public class ManagerThread extends Thread{
    private Map<String, Socket> agentsMap;
    private MySQL db;
    private boolean stop = true;

    @Override
    public void run(){
        while (!stop){
            resolveEventualCommand();
            Map<String, Socket> agentsMap_duplicate = agentsMap;

            for(String agentKey : agentsMap_duplicate.keySet()){
                String msg = readDataFromSocket(agentsMap_duplicate.get(agentKey));

                if(msg.contains("error")){
                    System.out.println("agent " + agentKey + " lost !");
                    agentsMap_duplicate = deleteAgent(agentsMap_duplicate, agentKey);
                }else {
                    resolve(msg);
                }

                waitNSeconds(300);
            }

            agentsMap = agentsMap_duplicate;

            waitNSeconds(1000);
        }
    }

    private Map<String, Socket> deleteAgent(Map<String, Socket> agentsMap, String location) {
        db.clearTable("agent");
        agentsMap.remove(location);

        return  agentsMap;
    }

    private void resolveEventualCommand() {
        boolean aCommandIsWaiting = false;
        String query = "SELECT id, location, command FROM agentcmd WHERE id = 1";

        ResultSet result = db.select(query);

        try {
            aCommandIsWaiting = result.first();
        } catch (SQLException e) {
            e.printStackTrace();
        }

        if(aCommandIsWaiting){
            try {
                System.out.println("Command to resolve : " + result.getString("command") + " from agent located at : " + result.getString("location"));
                send(result.getString("command"), agentsMap.get(result.getString("location")));
            } catch (SQLException e) {
                e.printStackTrace();
            }

            db.clearTable("agentcmd");
        }
    }

    private void resolve(String msg) {
        String location = msg.split(" : ")[0];
        String state = msg.split(" : ")[1];

        if(state.contains("to register")){
            send("ping", agentsMap.get(location));
        }else if(state.contains("working")){
            send("pong", agentsMap.get(location));
        }else if(state.contains("ready")){
            send("ping", agentsMap.get(location));
        }else{
            System.out.println("Agent " + location + " lost !");
        }

        updateAgentState(location, state);
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

    public void send(String s, Socket agentSocket) {
        try {
            PrintWriter out = new PrintWriter(agentSocket.getOutputStream());
            out.println(s);
            out.flush();
        } catch (IOException e) {
            e.printStackTrace();
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

    private void waitNSeconds(int N) {
        try {
            Thread.sleep(N);
        } catch (InterruptedException e) {
            e.printStackTrace();
        }
    }

    public void addAgent(String agentName, Socket newAgent) {
        agentsMap.put(agentName, newAgent);
        System.out.println("New agent connected : " + agentName);
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
        agentsMap = new HashMap<String, Socket>();
    }

    private void cleanOldAgentsData() {
        db.clearTable("agentcmd");
        db.clearTable("agent");
    }
}
