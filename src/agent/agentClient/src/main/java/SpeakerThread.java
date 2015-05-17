public class SpeakerThread extends Thread{
    private NetworkManager net;
    private MySQL db;

    private ConfigModel modelConfiguration;
    private Agent agent;

    private boolean stop = true;

    public SpeakerThread(){
        db = new MySQL();
        db.connect();

        modelConfiguration = new ConfigModel(db);
        modelConfiguration.getConfigDataFromDb();

        net = new NetworkManager(modelConfiguration.adress(), modelConfiguration.port());
        agent = new Agent();
        net.send(agent.location());
    }

    @Override
    public void run(){
        while (!stop){
            net.send(agent.location()  + " : " + agent.state());
            agent.resolve(net.read());
            wait(500);
        }
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

    public void wait(int n){
        try {
            Thread.sleep(n);
        } catch (InterruptedException e) {
            e.printStackTrace();
        }
    }
}
