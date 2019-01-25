package database;

import java.io.FileInputStream;
import java.io.IOException;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.Properties;

import data.Person;

public class DirectoryDB {

	Connection con;
	public DirectoryDB(){
		Properties dbsettings = new Properties();
		try {
			dbsettings.loadFromXML(new FileInputStream("dbsettings.xml"));
		} catch (IOException e) {
			System.err.println("No dbsettings.xml file found");
			System.exit(1);
		}
		
		
		String url = dbsettings.getProperty("url");
		Properties props = new Properties();
		props.setProperty("user", dbsettings.getProperty("user"));
		props.setProperty("password", dbsettings.getProperty("password"));
		props.setProperty("useSSL", "false");
			
		
		try {
			Class.forName("com.mysql.jdbc.Driver").newInstance();
			//Class.forName("com.mysql.cj.jdbc.Driver").newInstance();
			
			con =  DriverManager.getConnection(url,props);
			
		} catch (InstantiationException e) {
			e.printStackTrace();
		} catch (IllegalAccessException e) {
			e.printStackTrace();
		} catch (ClassNotFoundException e) {
			e.printStackTrace();
		} catch (SQLException e) {
			e.printStackTrace();
		}		
		
	}
	
	public void EmptyTable(){
		try {
			Statement stmt = con.createStatement();
			String query = "truncate table incoming_people;";
			
			stmt.execute(query);
			stmt.close();
		} catch (SQLException e) {
			e.printStackTrace();
		}
		
	}
	public void InsertPeople(ArrayList<Person> people){
		
		
		String query = ("INSERT into incoming_people(displayName,givenName,sn,uid,unlPrimaryAffiliation,"
				+ "unlHRPrimaryDepartment,title,mail,telephoneNumber,unlHRaddress) VALUES (?,?,?,?,?,?,?,?,?,?)");
		
		try {
			PreparedStatement stmt = con.prepareStatement(query);
			
			for(int i = 0; i < people.size(); i++){
				Person person = people.get(i);
				
				stmt.setString(1, person.getDisplayName());
				stmt.setString(2,person.getGivenName());
				stmt.setString(3,person.getSn());
				stmt.setString(4,person.getUid());
				stmt.setString(5,person.getUnlPrimaryAffiliation());
				stmt.setString(6,person.getUnlHRPrimaryDepartment());
				stmt.setString(7,person.getTitle());
				stmt.setString(8,person.getMail());
				stmt.setString(9,person.getTelephoneNumber());
				stmt.setString(10,person.getUnlHRAddress());
				stmt.addBatch();
			}
			
			stmt.executeBatch();
			
		} catch (SQLException e) {
			e.printStackTrace();
		}

	}
	
}
