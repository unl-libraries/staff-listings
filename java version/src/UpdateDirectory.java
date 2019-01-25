import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLConnection;
import java.util.ArrayList;

import com.google.gson.JsonArray;
import com.google.gson.JsonElement;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;

import data.Person;
import database.DirectoryDB;

public class UpdateDirectory {

	public static void main(String[] args){
		
		String sURL = "https://directory.unl.edu/departments/84/personnel?format=json"; //just a string

	    // Connect to the URL using java's native library
		ArrayList<Person> people = new ArrayList<Person>();
		System.out.println("Getting library directory data");
		try {
			URL url = new URL(sURL);
			URLConnection request = url.openConnection();
			request.connect();
			
		    JsonParser jp = new JsonParser();
		    JsonElement root = jp.parse(new InputStreamReader((InputStream) request.getContent())); 
		    
		    JsonArray elements = root.getAsJsonArray();
		    		    
		    for(int i =0 ; i < elements.size(); i++){
		       	JsonObject person = elements.get(i).getAsJsonObject();

		    	if(person.get("eduPersonPrimaryAffiliation") != null && person.get("unlHROrgUnitNumber") != null){
		    		JsonArray primaryAffiliation = person.get("eduPersonPrimaryAffiliation").getAsJsonArray();
		    		JsonArray unitNumber = person.get("unlHROrgUnitNumber").getAsJsonArray();
		    
		    		if((primaryAffiliation.contains(new JsonParser().parse("student")) == false) &&
		    		   unitNumber.contains(new JsonParser().parse("\"50000905\""))){
		    				    			
		    			if(primaryAffiliation.contains(new JsonParser().parse("emeriti")) == false
		    			   && primaryAffiliation.contains(new JsonParser().parse("affiliate")) == false){
		    				
		    				JsonArray personAffiliation = person.get("eduPersonAffiliation").getAsJsonArray();
		    				if(personAffiliation.contains(new JsonParser().parse("retiree")) == false){
		    					if(personAffiliation.contains(new JsonParser().parse("faculty")) ||
		    					   personAffiliation.contains(new JsonParser().parse("staff"))){
		    						
		    						String uid = person.get("uid").getAsString();
		    						people.add(getPerson(uid));
		    						
		    					}
		    				}		    				
		    			}
		    		}
		    	} 	
		   }   
		} catch (MalformedURLException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
		System.out.println("Retrieved "+people.size()+" people for the database");
		System.out.println("Updating database");
		//Load into Database
		DirectoryDB db = new DirectoryDB();
		db.EmptyTable();
		db.InsertPeople(people);
		
	   
	}
	
	public static Person getPerson(String uid){
		Person person = new Person();
		try {
			URL url = new URL("https://directory.unl.edu/people/"+uid+".json");
			URLConnection request = url.openConnection();
			request.connect();
			
		    JsonParser jp = new JsonParser();
		    JsonElement root = jp.parse(new InputStreamReader((InputStream) request.getContent())); 
		
		    JsonObject persondata = root.getAsJsonObject();
		    
		    person.setDisplayName(persondata.get("displayName"));
		    person.setGivenName(persondata.get("givenName"));
		    person.setSn(persondata.get("sn"));
		    person.setUid(uid);
		    person.setUnlPrimaryAffiliation(persondata.get("eduPersonPrimaryAffiliation"));
		    person.setUnlHRPrimaryDepartment(persondata.get("unlHRPrimaryDepartment"));
		    person.setTitle(persondata.get("title"));
		    person.setMail(persondata.get("mail"),persondata.get("unlEmailAlias"));
		    person.setTelephoneNumber(persondata.get("telephoneNumber"));
		    person.setUnlHRAddress(persondata.get("unlHRAddress"));

		} catch (MalformedURLException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
		
		return person;
	}
	
}
