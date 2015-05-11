public class SpeakerThread extends Thread{
    private NetworkManager net;
    private MySQL db;

    private ConfigModel modelConfiguration;
    private AgentModel agentModel;

    private boolean stop = true;

    public SpeakerThread(){
        db = new MySQL();
        db.connect();

        modelConfiguration = new ConfigModel(db);
        modelConfiguration.getConfigDataFromDb();

        net = new NetworkManager(modelConfiguration.adress(), modelConfiguration.port());
        agentModel = new AgentModel();
    }

    public void startThread(){
        stop = false;
        this.start();
    }

    public void stopThread(){
        stop = true;

        net.disconnect();
        db.disconnect();

        this.interrupt();
    }

    @Override
    public void run(){
        while (!stop){
            net.send(agentModel.location()  + " : " + agentModel.state());
            wait(500);
        }
    }

    public void wait(int n){
        try {
            Thread.sleep(n);
        } catch (InterruptedException e) {
            e.printStackTrace();
        }
    }
}
