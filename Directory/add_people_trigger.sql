-- Set to run after an insert on the table incoming_people
BEGIN UPDATE library_data SET position=NEW.TITLE, library_data.unl_dept=NEW.UNLHRPRIMARYDEPARTMENT, last_updated=NOW() WHERE library_data.nuid=NEW.UNLUNCWID; 
IF NEW.UNLUNCWID NOT IN (SELECT nuid FROM library_data) AND NEW.UNLHRPRIMARYDEPARTMENT='University Libraries' THEN
	 INSERT INTO library_data (nuid,userid,position,unl_dept) VALUES (NEW.UNLUNCWID,NEW.UID,NEW.TITLE,NEW.UNLHRPRIMARYDEPARTMENT);
	 END IF;
END
-- command to recreate
CREATE TRIGGER `add_people` AFTER INSERT ON `incoming_people`
 FOR EACH ROW BEGIN UPDATE library_data SET position=NEW.TITLE, library_data.unl_dept=NEW.UNLHRPRIMARYDEPARTMENT, last_updated=NOW() WHERE library_data.nuid=NEW.UNLUNCWID; 
IF NEW.UNLUNCWID NOT IN (SELECT nuid FROM library_data) AND NEW.UNLHRPRIMARYDEPARTMENT='University Libraries' THEN
	 INSERT INTO library_data (nuid,userid,position,unl_dept) VALUES (NEW.UNLUNCWID,NEW.UID,NEW.TITLE,NEW.UNLHRPRIMARYDEPARTMENT);
	 END IF;
END