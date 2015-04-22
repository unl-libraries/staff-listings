CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `subject_librarians` AS 
select `subjects`.`subject` AS `subject`,
`library_people`.`userid` AS `userid`,
`library_people`.`first_name` AS `first_name`,
`library_people`.`last_name` AS `last_name` from (((`library_people` join `subjects`) join `subject_assignments`) join `library_data`)
where ((`library_data`.`id` = `subject_assignments`.`person_id`) and (`subject_assignments`.`subject_id` = `subjects`.`id`) and (`library_data`.`nuid` = `library_people`.`nuid`)) order by `subjects`.`subject`