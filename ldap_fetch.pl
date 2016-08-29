#!/usr/bin/perl
#############################################################################
#  Export LDAP entries in csv format to a person.csv file                   #
#                                                           				#
#  Author:  Stacy Rickel (srickel1@unl.edu)									#
#			Modified from a script (ldap-csvexport) 						#
#			by Benedikt Hallinger <beni@hallinger.org>						#
#                                                                           #
#  This program allows you to easily export entries to csv format.          #
#  It reads entries of an LDAP directory and prints selected attributes     #
#  in CSV format to a file. Multi valued attributes will be separated by    #
#  an user definable character sequence.                                    #
#                                                                           #
#  The file path and name are specified at the beginning,                   #
#  as are the attributes												    #
#                                                                           #
#  Required modules are                                                     #
#    Net::LDAP           													#
#	 XML::LibXML		                                                    #
#  You can get these modules from your linux package system or from CPAN.   #
#                                                                           #
#                                                                           #
#############################################################################

use Net::LDAP;
use XML::LibXML;
#use XMLHarvestFunctions; #custom harvesting functions
use Cwd;


$file_path = $ARGV[0]; # "/disk1/vivo/harvester/harvest-scripts/unl-ldap/incoming_people.csv";
$ldap = Net::LDAP->new('ldap.unl.edu',version=>3) or die "Error connecting to LDAP $!\n"; #connect to the domain
my $starttls_msg = $ldap->start_tls(capath=>'/etc/openldap/certs/'); #convert the connection to secure
die $starttls_msg->error() if $starttls_msg->is_error;


my $fieldquot   = '"';
my $fieldsep    = ",";
my $mvsep       = "|";
my $singleval   = 0;

my @people = (); ## the array of hashes to store the people records in.
# the attributes fields we want
my @attributes = ('sn','givenName','displayName','unlUNCWID','uid','title','mail','telephoneNumber','unlPrimaryAffiliation','unlHROrgUnitNumber','unlHRPrimaryDepartment','postalAddress','unlHRAddress','createTimeStamp','modifyTimeStamp','unlActive');
# take out unlactive and the mail extra conditions of the view structure
		
	
my @letters = ("a".."z"); ## the letters we will loop through for the query since we can't get them all at once.
foreach $letter (@letters){

	$ldap->bind(
		dn		=>	"uid=libraries-vivo,ou=service,dc=unl,dc=edu",
		password =>	"LjFsibDLZ74k",
		);

	
	$mesg = $ldap->search( #perform a search for UNL faculty unlPrimaryAffiliation=faculty and library staff, filtering out retirees.)
					base =>	"ou=people, dc=unl,dc=edu",
					filter	=>	"(&(unlHROrgUnitNumber=50000905)(SN=$letter*))",
					attrs => ['*', 'createTimeStamp','modifyTimeStamp']
					);
	# original filter: "(&(&(unlPrimaryAffiliation=faculty)(unlHROrgUnitNumber=50000905))(uid=$letter*))"
	# HR Org unit number 50000905 is the UNL Libraries department, so above query gets all UNL faculty + Library staff (for publication recording)
	# attributes option requests all default attributes with the '*' and then creates an additional timestamp for update times
	
	
	$mesg->code && die $mesg->error;
	
	## if no errors proceed to process the entries 
	print STDOUT "Processing ldap entries in letter $letter\n";
	## Now turn each ldap entry into a hashed entry for later use
	while (my $entry = $mesg->shift_entry()){
		# Retrieve each fields value and store it in a hash.
		my $person={};
		#@attributes = $entry->attributes;
		# if attr is multivalued, separate each value
		foreach my $a (@attributes) {
			if ($entry->exists($a)) {
				my $attr    = $entry->get_value($a, 'asref' => 1);
				my @values  = @$attr;
				my $val_str = "";
				if (!$singleval) {
					# retrieve all values and separate them via $mvsep	
					$val_str = join($mvsep,@values); #"$val_str$val$mvsep"; # add all values to field														
				} 
				else {
					$val_str = shift(@values); # user wants only the first value
				}
				if ($a eq 'postalAddress'){
					# parse it out to delimit it ahead of time into 3 groups - we'll put them back together with the $mvsep character
					# this field can contain data such as: 
					# "Dow AgroSciences 9330 Zionsville Rd, Indianapolis, 46268"
					# "Extension Office 510 North Pearl St, Suite C, Wayne, 68787"
					# "4101 Woolworth Ave, Research #151 VAH, CAAT; R309A; #151, UNMC, 68198-8090"
					# "3901 Rainbow Blvd, MS 1026, Univ of Kansas Med Ctr, Kansas City, 66160"
					
					my @address_values = split(',',$val_str);
					# we want at max 4 groups counting backwards from the last field, which contains the zip code, and including the state identified from zipcode
					if ($#address_values == 5){
						
					}
					elsif ($#address_values == 4){
						
					}
					elsif ($#address_values == 3){
						
					}
				}
				$person->{$a}=$val_str;							
			}
			else {
				# no value found: just add fieldquotes
				$person{$a}='';#$current_line .= $fieldquot.$fieldquot;
			}				
		}
		push @people,$person;				
	}
} ## now we have a big hashed array of people entries

	## now go through each of the people entries
	## use the uid to retrieve the extra position data to add to it
		
	my $max_positions=0; # maximum count of positions that could be had in the system for the csv file
	my	@position_fields = ("description","unlRoleHROrgUnitNumber","unlRoleHROrgUnit");
	for $i (0..$#people){
		## check for  positions - use the second LDAP query information
		## description has the name of the role in it, 
		## unlRoleHROrgUnitNumber is the organization unit # for the position/role
		## unlRoleHROrgUnit is the name of the unit (to save for cases we can't find any information by unitnumber later)
		## unlRoleHROrgUnit sometimes contains "Entomology                          I" which we need to strip
						
		my $uid=$people[$i]{uid};
		
		$position_data = $ldap->search( 
					base=>"ou=people, dc=unl, dc=edu",
					filter => "(cn=$uid-*)",
				);
		$position_data->code && die $position_data->error;
		
		my $position_count=0;		
		foreach $position ($position_data->sorted("unlRoleHROrgUnitNumber")){						
			if (!$position->exists('unlListingOrder') || ($position->get_value('unlListingOrder') ne 'NL')){
				$position_count++;
				foreach my $position_field(@position_fields) {
					if ($position->exists($position_field)){
						my $position_attr    = $position->get_value($position_field);
						if ($position_field eq 'unlRoleHROrgUnit'){
							## we need to strip the extra whitespace followed by I or U at the ends of these
							$position_attr =~ s/\s+I$//;
							$position_attr =~ s/\s+U?$//;							
						}											
						$people[$i]{$position_field.$position_count}=$position_attr; #add new field data to end of line
					}				
				}			
			}
		}
		if ($position_count > $max_positions) {$max_positions=$position_count;}	
	}
	
	#open the file we want to write the information to
	open (CSVFILE, ">",$file_path) or die "Could not open $file_path";
	#create the header for the csv file
	foreach my $a (@attributes) {
			$csv_header = "$csv_header$fieldquot$a$fieldquot$fieldsep";
	}
	
	for $max_count (1..$max_positions){
		foreach my $positionfield (@position_fields){
			push @attributes, "$positionfield$max_count";
			$csv_header .="$fieldquot$positionfield$max_count$fieldquot$fieldsep";
		}
	}
	
	$csv_header =~ s/\Q$fieldsep\E$//; # eat last $fieldsep
	print CSVFILE "$csv_header\n";
	print STDOUT "Adding people to csv file...\n";
	foreach my $person_entry(@people){
		my $current_line='';
		foreach my $person_att(@attributes){
			$current_line .= $fieldquot.$person_entry->{$person_att}.$fieldquot; # add field data to current line	
			$current_line .= $fieldsep; # close field and add to current line
		}				
		
		$current_line =~ s/\Q$fieldsep\E$//; # eat last $fieldsep					
		$current_line =~ s/'/&#39;/g; #escape any quotes
		print CSVFILE "$current_line\n"; # print line			
	}
	$ldap->unbind;
	close(CSVFILE);

