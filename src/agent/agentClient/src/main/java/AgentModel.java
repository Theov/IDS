public class AgentModel {
    private String location;
    private String state;

    public AgentModel(){
        location = getClass().getProtectionDomain().getCodeSource().getLocation().getFile();
        state = "ready";
    }

    public String location(){
        return location;
    }

    public String state(){
        return state;
    }
}
