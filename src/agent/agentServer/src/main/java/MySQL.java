import java.sql.*;

/**
 * Created by thiiozz on 11/05/2015.
 */
public class MySQL {
    private String url = "jdbc:mysql://localhost/hids";
    private String login = "root";
    private String pwd = "";
    private Connection db;
    private Statement query;

    public void connect(){
        try {
            db = DriverManager.getConnection(url, login, pwd);
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    public ResultSet select(String queryString){
        ResultSet result = null;

        try {
            query = db.createStatement();
            result = query.executeQuery(queryString);
        } catch (Exception e) {
            e.printStackTrace();
        }

        return result;
    }

    public void disconnect(){
        try {
            db.close();
            query.close();
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    public void clearTable(String table) {
        try {
            query.executeUpdate("TRUNCATE " + table);
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    public void exec(String _query) {
        try {
            query.executeUpdate(_query);
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }
}
