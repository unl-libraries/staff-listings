## harvest_people.py
##
## A new script to harvest people for the Libraries organization from the 
## directory.unl.edu api
##
## replaces an old ldap_fetch.pl script from a harvesting directory 
##
## Authored: srickel1
## Date: March 2017
##

import json
import urllib2
import codecs

#configuration items

## open the csv file
output = codecs.open("incoming_people.csv","w+")
log_file = codecs.open("log.txt", "w+")
## print the headers
print >> output, ",".join(['displayName',
                           'givenName',
                           'sn',
                            'uid',
                           'unlPrimaryAffiliation',                      
                            'unlHRPrimaryDepartment',
                            'title',
                            'mail',
                            'telephoneNumber',
                            'unlHRAddress'])


## first query gets the listing of personnel from https://directory.unl.edu/departments/84/personnel?format=json
data = urllib2.urlopen("https://directory.unl.edu/departments/84/personnel?format=json")
personnel = json.load(data)
for person in personnel:
  if person["eduPersonPrimaryAffiliation"] and str(person["eduPersonPrimaryAffiliation"][0]) != 'student' and person['unlHROrgUnitNumber'] and person['unlHROrgUnitNumber'][0]=='50000905': 
	if (str(person["eduPersonPrimaryAffiliation"][0]) != 'emeriti' and str(person["eduPersonPrimaryAffiliation"][0]) != 'affiliate' and 'retiree' not in person["eduPersonAffiliation"] and ('staff' in person['eduPersonAffiliation'] or 'faculty' in person['eduPersonAffiliation'])):        
        	uid = person["uid"]            
        	## second query gets the personal information for each entry to ensure completeness of the data
        	# https://directory.unl.edu/people/{uid}.json
        	person_url = urllib2.urlopen("https://directory.unl.edu/people/"+str(uid)+".json")
        	#print "https://directory.unl.edu/people/"+str(uid)+".json" 
        	person_data = json.load(person_url)
		#json.dump(person_data,log_file)
		print >> log_file, person_data;
		print >> output, '"'+'","'.join([
            ";".join(person_data['displayName']),
            ";".join(person_data['givenName']),            
            ";".join(person_data['sn']),
             str(uid),
            ";".join(person_data['eduPersonPrimaryAffiliation']),            
            ";".join(person_data['unlHRPrimaryDepartment']),
            ";".join(person_data['title']) if person_data['title'] else '',
            ";".join(person_data['mail']).lower() if person_data['mail'] else person_data['unlEmailAlias'][0]+'@unl.edu' if person_data['unlEmailAlias'] else '',
            ";".join(person_data['telephoneNumber']) if person_data['telephoneNumber'] else '',               
            ";".join(person_data['unlHRAddress']) if person_data['unlHRAddress'] else ''
            ])+'"'
