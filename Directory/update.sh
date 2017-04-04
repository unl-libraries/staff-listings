python harvest_people.py
mysqlimport --ignore-lines=1 --fields-enclosed-by='"' --fields-terminated-by=',' --lines-terminated-by='\n' --local --delete --columns='displayName,givenName,sn,uid,eduPersonPrimaryAffiliation,unlHRPrimaryDepartment,title,mail,telephoneNumber,unlHRAddress' --user 'user' --password='password' staff incoming_people.csv

