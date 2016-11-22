jQuery.ajaxSetup({
    scriptCharset: "utf-8", //maybe "ISO-8859-1"
    contentType: "application/json; charset=utf-8"
});

/** 
 * init will be called on page load for each listing
 * 
 * @param view string value of either 'faculty', 'staff' or 'subjects'
 */
function init(view){
	//execute the function from letter_nav.js that displays the alphabet links
	letter_nav(view);
	//initialize the page
	var currentLetter= location.hash.replace('#','');
    if (!currentLetter){ currentLetter = 'a';}
	  if (view == 'subjects') { 
		  show_subjects({tag:'div',tag_class:'subject_info'},currentLetter);
	  }
	  else{
		  show_people({tag:'div',tag_class:'directory_info'},view,currentLetter);
	  }
		jQuery('html, body').animate({
	        scrollTop: jQuery("#directory_heading").offset().top
	    }, 0);
 }

  
/** format people listings
 * @param person main person entry array
 * @param libData array of associated library only data for the individual
 * @param view  the type of view: 'faculty' or 'staff'
 * @returns {String} formatted HTML string
 */
  function formatPersonData(person,libData,view){
      var thisPersonHtml ='';
      thisPersonHtml +='<div class="bp480-wdn-col-one-half box_hidden">';
      thisPersonHtml +='<div class="wdn-col box_dir">';
      if (view == 'faculty'){
    	  //we only show the person's picture if they are faculty
    	  thisPersonHtml +='<div class="bp640-wdn-col-one-half">';      
    	  thisPersonHtml +='    <figure class="wdn-frame">';
    	  thisPersonHtml +='        <img alt="Headshot Photo of Faculty Member at UNL Libraries" src="https://directory.unl.edu/avatar/'+person.userid+'?s=large">';
    	  thisPersonHtml +='    </figure>';
    	  thisPersonHtml +='</div>';
    	  thisPersonHtml +='<div class="bp640-wdn-col-one-half">';
      }
      else{
    	  //use the full column width for staff, since they have no pictures
    	  thisPersonHtml += '<div class="bp640-wdn-col-full">';
      }
      thisPersonHtml +='    <h5><a href="https://directory.unl.edu/people/'+person.userid+'" title="View '+person.display_name+' Profile">'+person.display_name+'</a>';
      thisPersonHtml +='<span class="wdn-subhead">'+person.unl_position;
      if (person.library_position) { thisPersonHtml += "<br />"+person.library_position+"\n";}
      thisPersonHtml += '</span></h5>';
      thisPersonHtml +='    <p class="clear-top">'+person.address+'<br>';
      thisPersonHtml +='    '+person.phone+'<br>';
      thisPersonHtml +='   <a href="mailto:'+person.email+'">'+person.email+'</a></p>';
      //websites
      thisPersonHtml += "<p>";
      if(person.website) { thisPersonHtml += "<a href='"+person.website+"' style='border-bottom:none;'><img src='http://libraries.unl.edu/images/SocialMedia/web-20.png' /><\/a>&nbsp;";}
      addLink = '';
      jQuery.each(libData.ExternalLinks,function(index,value){         
          if (value.link_type == 'linkedin'){ thisPersonHtml += "&nbsp;<a href='"+value.url+"' style='border-bottom:none;'><img src='http://libraries.unl.edu/images/SocialMedia/linkedin-20.png'/><\/a>";}
          if (value.link_type == 'facebook'){ thisPersonHtml += "&nbsp;<a href='"+value.url+"' style='border-bottom:none;'><img src='http://libraries.unl.edu/images/SocialMedia/facebook-20.png'/><\/a>";}
          if (value.link_type == 'digitalcommons') { addLink = "<br /><a title='Publications/Vita for "+person.display_name+"' href='"+value.url+"'>Publications/Vita</a>";}
      });
      thisPersonHtml += addLink;
      thisPersonHtml +='</p>';
      thisPersonHtml +='</div>';
      thisPersonHtml +='<div class="wdn-col-full">';
      thisPersonHtml +='    <div class="box_no_border_dir" style="height:10%;padding-top:1em;padding-bottom:1em;">';
      if (libData.Subjects.length > 0){
    	  thisPersonHtml +='<div class="zenbox">';
          thisPersonHtml += '<h6 class="clear-top">Subject Specialties:</h6>';
          thisPersonHtml += '<ul style="font-size:small;">';
         jQuery.each(libData.Subjects, function(index,elem){        	 
              thisPersonHtml+='<li>'+elem.subject+'</li>';
         });
         thisPersonHtml += "</ul>\n";
         thisPersonHtml += "</div>"; //zenbox
      }       
      
      thisPersonHtml +='    </div>'; //wdn-col-full
      thisPersonHtml +='</div>'; //bp640-wdn-col-one-half
      thisPersonHtml +='</div>'; //wd-col box_dir
      thisPersonHtml +='</div>'; //bp480-wd-col-one-half class
      
      return thisPersonHtml;
      }
  
  /** call to query for the json list of people and load them up in the divs 
   * 
   * @param personElement an array describing the element to update with the listings
   * @param view string indication whether to show 'faculty' or 'staff' records
   * @param lettertoShow the letter to filter on (or 'all' to show all records of the view type)
   */
function show_people(personElement, view, lettertoShow){
	jQuery("#people").html('<section class="wdn-band"><div class="wdn-center"><div id="floatingBarsG">\
			<div class="blockG" id="rotateG_01"></div>\
			<div class="blockG" id="rotateG_02"></div>\
			<div class="blockG" id="rotateG_03"></div>\
			<div class="blockG" id="rotateG_04"></div>\
			<div class="blockG" id="rotateG_05"></div>\
			<div class="blockG" id="rotateG_06"></div>\
			<div class="blockG" id="rotateG_07"></div>\
			<div class="blockG" id="rotateG_08"></div>\
		</div></div></section>');

	if (lettertoShow=='all') {lettertoShow='';}
	//else if (lettertoShow =='') { lettertoShow = 'a';}
     var people=[]; //array to hold people html elements
     console.debug("Starting to create profiles in "+personElement.tag + personElement.tag_class + " for letter "+lettertoShow);
	 jQuery.getJSON('http://libdirectory.unl.edu/addresses/'+view+'_listing/'+lettertoShow+'?callback=?',
			function(data){												
				var columnCount = 0;
				var letter='';  //the current first letter of the section to display 
				var lastLetter;  //previous letter so we know if we need to print the letter heading and close the section
	         	thisPerson = ''; //string for the HTML to be appended
                jQuery.each(data.people, function(index,value){                                	     
                	letter = value.Address.last_name.charAt(0);
                	//letter heading and navigation
                	if (lastLetter != letter){
                		if (lastLetter){
                			//only do these things if it's not the first letter 
                			console.debug("this is the first row at letter "+letter);
                			if (columnCount ==1){                		
                				//finish the row to start the next letter section
                				thisPerson += '</div>';                				
	                		}
	                		columnCount = 0; //reset the column count
	                		thisPerson +='<h6 style="text-align: right;"><a href="javascript:window.scrollTo(0,0);" class="navToTop" style="border-bottom:0px;">return to top</a></h6>';
	                		thisPerson += '</div>';
	                		thisPerson += '</div>';
	                		thisPerson += '</section>';
                		}
                		//create the letter navigation and header                                	
                		thisPerson += '<section class="wdn-band">';
                	        thisPerson += '<div class="wdn-inner-wrapper wdn-inner-padding-sm">';
                	        thisPerson += '<div class="wdn-grid-set">';
                	        //letter divider
                	        thisPerson += '<div class="wdn-col-full">';
                	        thisPerson += ' <div class="wdn-col-four-ninths wdn-center">';
                	        thisPerson += '  <hr class="dir_right">';
                	        thisPerson += ' </div>';
                	        thisPerson += '<div class="wdn-col-one-ninth wdn-center">';
                	        thisPerson += '<h3 class="clear-top" id='+letter.toLowerCase()+'>'+letter.toUpperCase()+'</h3>';
                	        thisPerson += '</div>';
                	        thisPerson += '<div class="wdn-col-four-ninths wdn-center">';
                	        thisPerson += '<hr class="dir_left"></div></div>';
                	        //wdn-grid-set div is still open
                	} //end of letter heading information                               	
                	
                	lastLetter = letter; //keep track of the letter we are on for next loop
                	                
                	if (columnCount==0){
                		//start a new row                                
                		thisPerson +='<div class="wdn-col-full" style="padding-top:10px;">';
                	}
                	thisPerson += formatPersonData(value.Address,value.StaffData,view);
                	columnCount++;
                	if (columnCount==2){
                		columnCount=0;
                		//finish the row                          		
                   		thisPerson += '</div>'; //closes the wdn-col-full from column 1                                		                                			
                		                   
                	}    	
                 }); 
                thisPerson +='<h6 style="text-align: right;"><a href="javascript:window.scrollTo(0,0);" class="navToTop" style="border-bottom:0px;">return to top</a></h6>';
        		jQuery('#people').html(thisPerson);        		
        
        }); //end of complete json call
	 window.scrollTo(0,0);
} //end of show_people function


/** call to query for the json list of subjects
 * 
 * @param subjectElement an array describing the element to update with the listings
 * @param lettertoShow the letter to filter on (or 'all' to show all records of the view type)
 */
function show_subjects(subjectElement,lettertoShow){
	jQuery("#subject_people").html('<section class="wdn-band"><div class="wdn-center"><div id="floatingBarsG">\
			<div class="blockG" id="rotateG_01"></div>\
			<div class="blockG" id="rotateG_02"></div>\
			<div class="blockG" id="rotateG_03"></div>\
			<div class="blockG" id="rotateG_04"></div>\
			<div class="blockG" id="rotateG_05"></div>\
			<div class="blockG" id="rotateG_06"></div>\
			<div class="blockG" id="rotateG_07"></div>\
			<div class="blockG" id="rotateG_08"></div>\
		</div></div></section>');
	console.log("showing subjects for letter "+lettertoShow);
	if (lettertoShow=='all') {lettertoShow='';}
	console.log("requesting json from "+'http://libdirectory.unl.edu/subjects/subject_listing/'+lettertoShow+'?callback=?');
     var pageCount;
	jQuery.getJSON('http://libdirectory.unl.edu/subjects/subject_listing/'+lettertoShow+'?callback=?',
			function(data){
				var subjectCount = 0;
				var columnCount = 0;
				var letter='';
				var lastLetter;
				thisSubject = '';
                jQuery.each(data.subjects, function(index,value){
                	subjectCount++;                	
                    if (value.Faculty.length > 0){   //we know we have at least one person assigned to the subject               
                    	letter = value.Subject.subject.charAt(0);
                       	if (lastLetter != letter){
                       		//new letter section coming up
                       		if (lastLetter){     
                       			//if it's not the first letter we encountered close up the previous section
                         	   if (columnCount ==1){
                         		 //finish the row
	 	                	   	thisSubject += '</div>'; //close the wdn-col-full from column 1	 	                	   
                         	   }
                        	   columnCount = 0;
                        	  // thisSubject += '<div style="float:right;"><button class="navToTop" value="Back to top">Back to top</button></div>';
	 	                	   //finish the letter section
                        	   thisSubject +='<h6 style="text-align: right;"><a href="javascript:window.scrollTo(0,0);" class="navToTop" style="border-bottom:0px;">return to top</a></h6>';	 	                	  
                       			thisSubject += '</div>';
                       			thisSubject += '</div>';
                       			thisSubject += '</section>';
                       			
                       		}
                       		thisSubject += '<section class="wdn-band">';
                	        thisSubject += '<div class="wdn-inner-wrapper wdn-inner-padding-sm">';
                	        thisSubject += '<div class="wdn-grid-set">';
                	        //letter divider
                	        thisSubject += '<div class="wdn-col-full">';
                	        thisSubject += ' <div class="wdn-col-four-ninths wdn-center">';
                	        thisSubject += '  <hr class="dir_right">';
                	        thisSubject += ' </div>';
                	        thisSubject += '<div class="wdn-col-one-ninth wdn-center">';
                	        thisSubject += '<h3 class="clear-top" id='+letter.toLowerCase()+'>'+letter.toUpperCase()+'</h3>';
                	        thisSubject += '</div>';
                	        thisSubject += '<div class="wdn-col-four-ninths wdn-center">';
                	        thisSubject += '<hr class="dir_left"></div></div>';
                	        //wdn-grid-set div is still open                       		                       
                       	}
                       	lastLetter = letter;
                    	//end of letter heading information	                   	                    
                      //TODO: Alter code to display mulitple librarians for one subject together instead of separately in one long column entry.
	                    jQuery.each(value.Faculty, function(findex,faculty){
	                       	if (columnCount==0){
	                       		//start a new row ?
	                       		thisSubject += '<div class="wdn-col-full" style="padding-top:10px;">';
	                       	}
	                    	thisSubject += formatSubjectData(value.Subject.subject,faculty.Address);
	                    	console.log(faculty.Address.display_name + ' is column '+ columnCount);
	                    	columnCount ++;	                    	
	                        if (columnCount==2){	                        	
	 	                	   columnCount = 0;
	 	                	   //finish the row
	 	                	   thisSubject += '</div>'; //close the wdn-col-full from column 1
	 	                   } 
	                    }); 
                	}
                 });
                thisSubject +='<h6 style="text-align: right;"><a href="javascript:window.scrollTo(0,0);" class="navToTop" style="border-bottom:0px;">return to top</a></h6>';
                jQuery("#subject_people").html(thisSubject);
                
			}); //end of get json call
	window.scrollTo(0,0); 
} 

/**
 * format a particular subject entry in HTML
 * @param subject array of the subject record
 * @param subjectperson associated person data
 * @returns {string} HTML formatted string to render in page
 */
function formatSubjectData(subject,subjectperson){
    var thisSubjectHtml ='';
    thisSubjectHtml +='<div class="bp480-wdn-col-one-half box_hidden">';
    thisSubjectHtml +='<div class="wdn-col-full" style="width:100%;padding:0;">';
    thisSubjectHtml +='     <div class="zenbox wdn-center">';
    thisSubjectHtml +='         <h5>'+subject+'</h5>';
    thisSubjectHtml +='     </div>';
    thisSubjectHtml +=' </div>';
    thisSubjectHtml +=' <div class="wdn-col box_dir">';
    thisSubjectHtml +='         <div class="bp640-wdn-col-one-half">';
    thisSubjectHtml +='             <figure class="wdn-frame">';
    thisSubjectHtml +='                 <img alt="Headshot Photo of Staff Member at UNL Libraries" src="https://directory.unl.edu/avatar/'+subjectperson.userid+'?s=large">';
    thisSubjectHtml +='         </figure>';
    thisSubjectHtml +='         </div>';
    thisSubjectHtml +='         <div class="bp640-wdn-col-one-half">';
    thisSubjectHtml +='             <h5><a href="https://directory.unl.edu/people/'+subjectperson.userid+'" title="View '+subjectperson.display_name+' Profile">'+subjectperson.display_name+'</a>';
    thisSubjectHtml +='<span class="wdn-subhead">'+subjectperson.unl_position;
    if (subjectperson.library_position){thisSubjectHtml +='<br/>'+subjectperson.library_position;}
    thisSubjectHtml +='</span></h5>';
    thisSubjectHtml +='             <p class="clear-top">'+subjectperson.address+'<br>';
    thisSubjectHtml +='             '+subjectperson.phone+'<br>';
    thisSubjectHtml +='         <a href="mailto:'+subjectperson.email+'">'+subjectperson.email+'</a></p>';
    thisSubjectHtml +='     </div>';
    thisSubjectHtml +=' </div>';
    thisSubjectHtml +='</div>';
    return thisSubjectHtml;
}

/**
 * Creates the letter navigation at the top of a page
 * @param view string indicating what type of view ('staff','faculty' or 'subjects')
 * @returns {Boolean} returns false if incorrect view sent
 */
function letter_nav(view){
	var currentURL = location.href.replace(location.hash,"");
	if (view=='staff'){ letter_query = 'http://libdirectory.unl.edu/addresses/get_letters/staff/?callback=?';}
	else if (view=='subjects'){letter_query = 'http://libdirectory.unl.edu/subjects/get_letters?callback=?';}
	else if (view=='faculty'){letter_query='http://libdirectory.unl.edu/addresses/get_letters/faculty/?callback=?';}
	else{return false;}
   jQuery.getJSON(letter_query,
    function(data) {
         jQuery.each(data.letters, function(letter,count){
                 if (count > 0) { 
                     jQuery("<a>",{html:letter.toUpperCase(), href:currentURL+"#"+letter,class:"letter letter_link letter_"+letter}).appendTo(".letters");
                      
                }
                 else { jQuery("<span>",{html:letter.toUpperCase(),class:"letter"}).appendTo(".letters");}
       });
         //add the all option - eww - hate this
         jQuery("<a>",{html:"[ All ]", href:currentURL+"#all",class:"letter letter_link letter_all"}).appendTo(".letters");
         
         var currentLetter = location.hash.replace("#",'');
         if (!currentLetter) currentLetter = 'a';
         jQuery(".letter_"+currentLetter).addClass('selected'); //start with a as default
         jQuery(".letter_link").click(function(){   
        	 
        	 currentLetter = jQuery(this).attr('class').replace(/letter letter_link letter_(\w+)(.*)/,'$1');   
        	 //remove the selected in case someone clicks the same letter twice!
        	
        	 console.debug("Clicked the letter "+currentLetter);
             if (view=='subjects'){            	 
            	 show_subjects({tag:'div',tag_class:'subject_info'}, currentLetter);            	 
             }
             else{
            	 
            	 show_people({tag:'div',tag_class:'directory_info'}, view, currentLetter);
            	}
             jQuery('.letter_link').removeClass('selected');
             //jQuery(this).addClass('selected');
             jQuery(".letter_"+currentLetter).addClass('selected'); //start with a as default
            	
         });
         
   });

} //end of letter_nav function
               