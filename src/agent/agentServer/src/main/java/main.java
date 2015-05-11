public class main {
    public static void main (String[] args){
        System.out.println("Server started");
        ListeningThread lt = new ListeningThread();
        lt.startThread();
    }
}
