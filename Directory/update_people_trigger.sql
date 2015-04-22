-- Set to be triggered after an update to table 'incoming_people'
BEGIN
UPDATE library_data SET position=NEW.TITLE,library_data.unl_dept=NEW.UNLHRPRIMARYDEPARTMENT, last_updated=NOW() WHERE library_data.nuid=NEW.UNLUNCWID;
END

--recreation command
CREATE TRIGGER `update_people` AFTER UPDATE ON `incoming_people`
 FOR EACH ROW BEGIN UPDATE library_data 
SET position=NEW.TITLE, library_data.unl_dept=NEW.UNLHRPRIMARYDEPARTMENT, last_updated=NOW() 
WHERE library_data.nuid=NEW.UNLUNCWID; END