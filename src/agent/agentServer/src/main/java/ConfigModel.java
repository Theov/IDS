import java.sql.ResultSet;
import java.sql.SQLException;

/**
 * Created by thiiozz on 11/05/2015.
 */
public class ConfigModel {
    private MySQL db;
    private String adress;
    private String port;

    public ConfigModel(MySQL _db){
        db = _db;
        getConfigDataFromDb();
    }

    public void getConfigDataFromDb() {
        ResultSet rs = db.select("SELECT adress, port FROM agentConfig WHERE id = 1");
        String l_adress = "";
        String l_port = "";

        try {
            rs.next();
            l_adress = rs.getString("adress");
            l_port = rs.getString("port");
        } catch (SQLException e) {
            e.printStackTrace();
        }

        adress = l_adress;
        port = l_port;
    }

    public String adress(){
        return adress;
    }

    public int port(){
        return Integer.parseInt(port);
    }


}
