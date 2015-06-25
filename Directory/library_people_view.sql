CREATE OR REPLACE VIEW library_people AS
select 
if(`publications`.`library_data`.`id`,`publications`.`library_data`.`id`,NULL) AS `staff_id`,
`publications`.`incoming_people`.`UNLUNCWID` AS `nuid`,
`publications`.`incoming_people`.`UID` AS `userid`,
if((`publications`.`library_data`.`preferred_name` <> ''),
`publications`.`library_data`.`preferred_name`,
`publications`.`incoming_people`.`GIVENNAME`) AS `first_name`,
`publications`.`incoming_people`.`SN` AS `last_name`,
`publications`.`incoming_people`.`DISPLAYNAME` AS `full_name`,
`publications`.`incoming_people`.`EDUPERSONPRINCIPALNAME` AS `email`,
`publications`.`incoming_people`.`TELEPHONENUMBER` AS `phone`,
`publications`.`incoming_people`.`UNLHRADDRESS` AS `address`,
`publications`.`incoming_people`.`UNLPRIMARYAFFILIATION` AS `unl_status`,
`publications`.`incoming_people`.`UNLACTIVE` AS `active`,
`publications`.`library_data`.`position` AS `unl_position`,
if((`publications`.`library_data`.`library_title` <> ''),`publications`.`library_data`.`library_title`,NULL) AS `library_position`,
`publications`.`library_data`.`unl_dept` AS `unl_dept`,
group_concat(distinct `publications`.`departments`.`name` order by `publications`.`departments`.`name` ASC separator '/') AS `library_dept`,
group_concat(distinct `publications`.`departments`.`abbreviation` order by `publications`.`departments`.`name` ASC separator '/') AS `library_dept_abbrev`,
`publications`.`library_data`.`location` AS `location`,
`publications`.`library_data`.`website` AS `website`,
`publications`.`library_data`.`libguide_profile` AS `libguide_profile`,

str_to_date(`publications`.`incoming_people`.`CREATETIMESTAMP`,'%Y%m%d') AS `created`,
str_to_date(`publications`.`incoming_people`.`MODIFYTIMESTAMP`,'%Y%m%d') AS `last_modified`,
`publications`.`incoming_people`.`DESCRIPTION1` AS `position_1`,
`publications`.`incoming_people`.`UNLROLEHRORGUNITNUMBER1` AS `org_unit_num_1`,
`publications`.`incoming_people`.`UNLROLEHRORGUNIT1` AS `org_unit_1`,
`publications`.`incoming_people`.`DESCRIPTION2` AS `position_2`,
`publications`.`incoming_people`.`UNLROLEHRORGUNITNUMBER2` AS `org_unit_num_2`,
`publications`.`incoming_people`.`UNLROLEHRORGUNIT2` AS `org_unit_2`,
`publications`.`incoming_people`.`DESCRIPTION3` AS `position_3`,
`publications`.`incoming_people`.`UNLROLEHRORGUNITNUMBER3` AS `org_unit_num_3`,
`publications`.`incoming_people`.`UNLROLEHRORGUNIT3` AS `org_unit_3` 
from (
(
(`publications`.`incoming_people` left join `publications`.`library_data` on((`publications`.`library_data`.`nuid` = `publications`.`incoming_people`.`UNLUNCWID`))) 
left join `publications`.`department_people` on((`publications`.`library_data`.`id` = `publications`.`department_people`.`staff_id`)))
left join `publications`.`departments` on((`publications`.`departments`.`id` = `publications`.`department_people`.`department_id`))) 
where (`publications`.`incoming_people`.`UNLHRPRIMARYDEPARTMENT` = 'University Libraries') group by `publications`.`incoming_people`.`SN`,`publications`.`incoming_people`.`GIVENNAME`
	-- libguides profile
SELECT DISTINCT people.id as data_row,a.first_name,a.last_name,g.owner as profile_id 
FROM libguides.accounts as a LEFT JOIN libguides.guides as g ON g.owner=a.id INNER JOIN 
(SELECT d.id,p.* from publications.library_data as d INNER JOIN publications.library_people as p ON d.nuid=p.nuid) as people ON (a.first_name=people.first_name and a.last_name=people.last_name) ORDER BY people.last_name 

--update query
UPDATE publications.library_data as d INNER JOIN publications.incoming_people as p ON p.UNLUNCWID=d.nuid LEFT JOIN libguides.accounts as a ON (a.email=p.EDUPERSONPRINCIPALNAME OR a.email=p.MAIL) SET d.libguide_profile=a.url

UPDATE publications.library_data as d INNER JOIN publications.incoming_people as p ON p.UNLUNCWID=d.nuid LEFT JOIN libguides.accounts as a ON (a.first_name=p.GIVENNAME AND a.last_name=p.SN) SET d.libguide_profile=a.url 
SELECT g.owner as profile_id FROM libguides.accounts as a LEFT JOIN libguides.guides as g ON g.owner=a.id INNER JOIN (SELECT d.id,p.* from publications.library_data as d INNER JOIN publications.library_people as p ON d.nuid=p.nuid) as people ON (a.first_name=people.first_name and a.last_name=people.last_name) GROUP BY people.id ORDER BY people.last_name 
-- institutional engagement
CREATE OR REPLACE VIEW international_engagement AS
select `publications`.`ldap_people`.`ROWID` AS `ROWID`,`publications`.`ldap_people`.`DISPLAYNAME` AS `DISPLAYNAME`,`publications`.`ldap_people`.`GIVENNAME` AS `GIVENNAME`,`publications`.`ldap_people`.`UNLUNCWID` AS `UNLUNCWID`,`publications`.`ldap_people`.`OBJECTCLASS` AS `OBJECTCLASS`,`publications`.`ldap_people`.`UNLPRIMARYAFFILIATION` AS `UNLPRIMARYAFFILIATION`,`publications`.`ldap_people`.`UNLHRORGUNITNUMBER` AS `UNLHRORGUNITNUMBER`,`publications`.`ldap_people`.`EDUPERSONAFFILIATION` AS `EDUPERSONAFFILIATION`,`publications`.`ldap_people`.`OU` AS `OU`,`publications`.`ldap_people`.`UID` AS `UID`,`publications`.`ldap_people`.`CN` AS `CN`,`publications`.`ldap_people`.`EDUPERSONPRINCIPALNAME` AS `EDUPERSONPRINCIPALNAME`,`publications`.`ldap_people`.`UNLACTIVE` AS `UNLACTIVE`,`publications`.`ldap_people`.`UNLHRPRIMARYDEPARTMENT` AS `UNLHRPRIMARYDEPARTMENT`,`publications`.`ldap_people`.`SN` AS `SN`,`publications`.`ldap_people`.`EDUPERSONPRIMARYAFFILIATION` AS `EDUPERSONPRIMARYAFFILIATION`,`publications`.`ldap_people`.`TITLE` AS `TITLE`,`publications`.`ldap_people`.`CREATETIMESTAMP` AS `CREATETIMESTAMP`,`publications`.`ldap_people`.`TELEPHONENUMBER` AS `TELEPHONENUMBER`,`publications`.`ldap_people`.`POSTALADDRESS` AS `POSTALADDRESS`,`publications`.`ldap_people`.`UNLHRADDRESS` AS `UNLHRADDRESS`,`publications`.`ldap_people`.`STREET` AS `STREET`,`publications`.`ldap_people`.`MAIL` AS `MAIL`,`publications`.`ldap_people`.`MODIFYTIMESTAMP` AS `MODIFYTIMESTAMP`,`publications`.`ldap_people`.`DESCRIPTION1` AS `DESCRIPTION1`,`publications`.`ldap_people`.`UNLROLEHRORGUNITNUMBER1` AS `UNLROLEHRORGUNITNUMBER1`,`publications`.`ldap_people`.`UNLROLEHRORGUNIT1` AS `UNLROLEHRORGUNIT1`,`publications`.`ldap_people`.`DESCRIPTION2` AS `DESCRIPTION2`,`publications`.`ldap_people`.`UNLROLEHRORGUNITNUMBER2` AS `UNLROLEHRORGUNITNUMBER2`,`publications`.`ldap_people`.`UNLROLEHRORGUNIT2` AS `UNLROLEHRORGUNIT2`,`publications`.`ldap_people`.`DESCRIPTION3` AS `DESCRIPTION3`,`publications`.`ldap_people`.`UNLROLEHRORGUNITNUMBER3` AS `UNLROLEHRORGUNITNUMBER3`,`publications`.`ldap_people`.`UNLROLEHRORGUNIT3` AS `UNLROLEHRORGUNIT3`,`publications`.`ldap_people`.`DESCRIPTION4` AS `DESCRIPTION4`,`publications`.`ldap_people`.`UNLROLEHRORGUNITNUMBER4` AS `UNLROLEHRORGUNITNUMBER4`,`publications`.`ldap_people`.`UNLROLEHRORGUNIT4` AS `UNLROLEHRORGUNIT4`,`publications`.`ldap_people`.`DESCRIPTION5` AS `DESCRIPTION5`,`publications`.`ldap_people`.`UNLROLEHRORGUNITNUMBER5` AS `UNLROLEHRORGUNITNUMBER5`,`publications`.`ldap_people`.`UNLROLEHRORGUNIT5` AS `UNLROLEHRORGUNIT5`,`publications`.`ldap_people`.`DESCRIPTION6` AS `DESCRIPTION6`,`publications`.`ldap_people`.`UNLROLEHRORGUNITNUMBER6` AS `UNLROLEHRORGUNITNUMBER6`,`publications`.`ldap_people`.`UNLROLEHRORGUNIT6` AS `UNLROLEHRORGUNIT6` from `publications`.`ldap_people` where ((`publications`.`ldap_people`.`UNLHRPRIMARYDEPARTMENT` like '%International Engagement%') and (`publications`.`ldap_people`.`POSTALADDRESS` like '%LLS 127%'))