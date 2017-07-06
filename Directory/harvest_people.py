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
    if str(person["eduPersonPrimaryAffiliation"][0]) != 'emeriti':        
        uid = person["uid"]            
        ## second query gets the personal information for each entry to ensure completeness of the data
        # https://directory.unl.edu/people/{uid}.json
        person_url = urllib2.urlopen("https://directory.unl.edu/people/"+str(uid)+".json")
        #print person_url
        person_data = json.load(person_url)
        print >> output, '"'+"\",\"".join([
            ";".join(person_data['displayName']),
            ";".join(person_data['givenName']),            
            ";".join(person_data['sn']),
             str(uid),
            ";".join(person_data['eduPersonPrimaryAffiliation']),            
            ";".join(person_data['unlHRPrimaryDepartment']),
            ";".join(person_data['title']),
            ";".join(person_data['mail']),
            ";".join(person_data['telephoneNumber']),               
            ";".join(person_data['unlHRAddress'])
            ])+'"'
        # use unlDirectoryAddress? as it is already parsed in the json from the person data
#                     "unlDirectoryAddress": {
#             "street-address": "318 LLS",
#             "locality": "Lincoln",
#             "region": "NE",
#             "postal-code": "68588-4100",
    
        
    


