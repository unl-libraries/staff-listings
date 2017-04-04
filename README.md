# Staff listings
Code used to produce the faculty and staff listings in applications

## Includes fetches for data that can be imported in mysql database
### harvest_people.py
 Python script that queries the UNL api at https://directory.unl.edu/ for individuals by department number.  
  The attributes we need are written to a csv file.  

 Requires the following python imports:
`json`
`urllib2` for querying the api
`codecs` for opening and writing to the file

The csv writing is very simple joined output and didn't seem necessary to use full csv support.

## javascript that uses json feeds to produces listings formatted to match the local CMS specifications

### directory_listings.js

Contains functions for 2 types of listings : people and subject librarian assignments.  Additional parameters allow us to filter by the status of people (faculty vs. staff) and format them differently based on that value.  

A function to create an alphabetical navigation for each version is included as well.  

To set up the javascript for use in a page:

  1. Include the css file `<link href="[path to css]/listings.css" rel="stylesheet" type="text/css" />`
  2. Include the js file `<script language="javascript" src="[path to js file]/directory_listings.js" type="text/javascript"></script>`
  3. Include an element with class "letters" for the letter navigation to be inserted into.  You can have multiple  (top and bottom for example)
  4. Include an element for the formatted listings to go into: 
  
      `<div id="subject_people" class="dont-break-out"></div>` for subjects

       `<div id="people" class="dont-break-out"></div>` for people
  5. Include the following to initialize the page where [view name] = 'subjects' or 'staff' or 'faculty':
      
      `<script language="javascript" type="text/javascript">init('[view name]');</script>` 
      
