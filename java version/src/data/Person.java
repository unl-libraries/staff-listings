package data;

import com.google.gson.JsonElement;

public class Person {
	private String displayName;
	private String givenName;
	private String sn;
	private String uid;
	private String unlPrimaryAffiliation;
	private String unlHRPrimaryDepartment;
	private String title;
	private String mail;
	private String telephoneNumber;
	private String unlHRAddress;
	
	public String getDisplayName() {
		return displayName;
	}
	public void setDisplayName(JsonElement displayName) {
		if(displayName == null || displayName.isJsonNull()){
			this.displayName = "";
		}else{
			this.displayName = displayName.getAsString();
		}
	}
	public String getGivenName() {
		return givenName;
	}
	public void setGivenName(JsonElement givenName) {
		if(givenName == null || givenName.isJsonNull()){
			this.givenName = "";
		}else{
			this.givenName = givenName.getAsString();
		}
	}
	public String getSn() {
		return sn;
	}
	public void setSn(JsonElement sn) {
		if(sn == null || sn.isJsonNull()){
			this.sn = "";
		}else{
			this.sn = sn.getAsString();
		}
		
	}
	public String getUid() {
		return uid;
	}
	public void setUid(String uid) {
		if(uid == null){
			this.uid = "";
		}else{
			this.uid = uid;
		}
	}
	public String getUnlPrimaryAffiliation() {
		return unlPrimaryAffiliation;
	}
	public void setUnlPrimaryAffiliation(JsonElement unlPrimaryAffiliation) {
		if(unlPrimaryAffiliation == null || unlPrimaryAffiliation.isJsonNull()){
			this.unlPrimaryAffiliation = "";
		}else{
			this.unlPrimaryAffiliation = unlPrimaryAffiliation.getAsString();
		}
	}
	public String getUnlHRPrimaryDepartment() {
		return unlHRPrimaryDepartment;
	}
	public void setUnlHRPrimaryDepartment(JsonElement unlHRPrimaryDepartment) {
		if(unlHRPrimaryDepartment == null || unlHRPrimaryDepartment.isJsonNull()){
			this.unlHRPrimaryDepartment = "";
		}else{
			this.unlHRPrimaryDepartment = unlHRPrimaryDepartment.getAsString();
		}
	}
	public String getTitle() {
		return title;
	}
	public void setTitle(JsonElement title) {
		if(title == null || title.isJsonNull()){
			this.title = "";
		}else{
			this.title = title.getAsString();
		}		
	}
	public String getMail() {
		return mail;
	}
	public void setMail(JsonElement mail, JsonElement mailalias) {
		if(mail == null || mail.isJsonNull()){
			if(mailalias == null || mailalias.isJsonNull()){
				this.mail = "";
			}else{
				this.mail = mailalias.getAsString()+"@unl.edu";
			}
		}else{
			this.mail = mail.getAsString();
		}
	}
	public String getTelephoneNumber() {
		return telephoneNumber;
	}
	public void setTelephoneNumber(JsonElement telephoneNumber) {
		if(telephoneNumber == null || telephoneNumber.isJsonNull()){
			this.telephoneNumber = "";
		}else{
			this.telephoneNumber = telephoneNumber.getAsString();
		}
	}
	public String getUnlHRAddress() {
		return unlHRAddress;
	}
	public void setUnlHRAddress(JsonElement unlHRAddress) {
		if(unlHRAddress == null || unlHRAddress.isJsonNull()){
			this.unlHRAddress = "";
		}else{
			this.unlHRAddress = unlHRAddress.getAsString();
		}
	}
	public String toString(){
		StringBuilder output = new StringBuilder();
		String separator = ",";
		output.append(displayName);
		output.append(separator);
		output.append(givenName);
		output.append(separator);
		output.append(sn);
		output.append(separator);
		output.append(uid);
		output.append(separator);
		output.append(unlPrimaryAffiliation);
		output.append(separator);
		output.append(unlHRPrimaryDepartment);
		output.append(separator);
		output.append(title);
		output.append(separator);
		output.append(mail);
		output.append(separator);
		output.append(telephoneNumber);
		output.append(separator);
		output.append(unlHRAddress);
		
		return output.toString();
	}
}
