jQuery.ajaxSetup({
    scriptCharset: "utf-8", //maybe "ISO-8859-1"
    contentType: "application/json; charset=utf-8"
});
listings = new DirectoryListing();
function DirectoryListing(){
	this.DirectoryServer = '';  //this should be the value of the Library Directory application without a leading http[s]://
	this.currentLetter = 'a'; // letter currently viewing for 
	this.view = null; //the view : faculty, staff or subjects
	return this;
}

/** 
 * init will be called on page load for each listing
 * 
 *  @param view string value of either 'faculty', 'staff' or 'subjects'
 */
DirectoryListing.prototype.init = function(view){
	this.firstLetter; //first letter for each view with content
	if (location.hash.replace('#','')) {
		this.currentLetter = location.hash.replace('#','');
	}    
	//execute the function from letter_nav.js that displays the alphabet links
	this.view = view;
	this.letter_nav(view);
	//initialize the page

}

  
/** format people listings
 * @param person main person entry array
 * @param libData array of associated library only data for the individual
 * @param view  the type of view: 'faculty' or 'staff'
 * @returns {String} formatted HTML string
 */
DirectoryListing.prototype.formatPersonData = function(person,libData){
      var thisPersonHtml ='';
      thisPersonHtml +='<div class="bp480-wdn-col-one-half box_hidden">';
      thisPersonHtml +='<div class="wdn-col box_dir" style="padding-top:1em;">';
      if (this.view == 'faculty'){
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
      thisPersonHtml +='    <h5><a href="https://directory.unl.edu/people/'+person.userid+'" title="View '+person.display_name+' Profile">'+person.display_name+'&nbsp;&nbsp;<img src="images/icons/external-link-16.png"/></a>';
      thisPersonHtml +='<span class="wdn-subhead">'+person.unl_position;
      if (person.library_position) { thisPersonHtml += "<br />"+person.library_position+"\n";}
      thisPersonHtml += '</span></h5>';
      thisPersonHtml +='    <p class="clear-top">'+person.address+'<br>';
      thisPersonHtml +='    '+person.phone+'<br>';
      thisPersonHtml +='   <a href="mailto:'+person.email+'">'+person.email+'</a></p>';
      //websites
      thisPersonHtml += "<p>";
      if(person.website) { thisPersonHtml += "<a href='"+person.website+"' style='border-bottom:none;'><img src='//libraries.unl.edu/images/SocialMedia/web-20.png' /><\/a>&nbsp;";}
      addLink = '';
      jQuery.each(libData.ExternalLinks,function(index,value){         
          if (value.link_type == 'linkedin'){ thisPersonHtml += "&nbsp;<a href='"+value.url+"' style='border-bottom:none;'><img src='//libraries.unl.edu/images/SocialMedia/linkedin-20.png'/><\/a>";}
          if (value.link_type == 'facebook'){ thisPersonHtml += "&nbsp;<a href='"+value.url+"' style='border-bottom:none;'><img src='//libraries.unl.edu/images/SocialMedia/facebook-20.png'/><\/a>";}
          if (value.link_type == 'twitter'){ thisPersonHtml += "&nbsp;<a href='"+value.url+"' style='border-bottom:none;'><img src='//libraries.unl.edu/images/SocialMedia/twitter-20.png'/><\/a>";}
          if (value.link_type == 'digitalcommons') { addLink = "<br /><a title='Publications/Vita for "+person.display_name+"' href='"+value.url+"'>Publications/Vita</a>";}
      });
      thisPersonHtml += addLink;
      thisPersonHtml +='</p>';
      thisPersonHtml +='</div>';
      thisPersonHtml +='<div class="wdn-col-full">';
      thisPersonHtml +='    <div class="box_no_border_dir" style="height:10%;padding-top:1em;padding-bottom:1em;">';
      if (libData.Subjects.length > 0){
    	  thisPersonHtml +='<div class="sub-bg">';
          thisPersonHtml += '<h6 class="clear-top">Subject Specialties:</h6>';
          thisPersonHtml += '<ul style="font-size:small;">';
         jQuery.each(libData.Subjects, function(index,elem){        	 
              thisPersonHtml+='<li>'+elem.subject+'</li>';
         });
         thisPersonHtml += "</ul>\n";
         thisPersonHtml += "</div>"; //sub-bg
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
DirectoryListing.prototype.show_people = function(personElement, lettertoShow){

    var self = this;
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
     //console.debug("Starting to create profiles in "+personElement.tag + personElement.tag_class + " for letter "+lettertoShow);
	 jQuery.getJSON(this.DirectoryServer+'/addresses/'+this.view+'_listing/'+lettertoShow+'?callback=?',
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
	                		thisPerson +='<h6 style="text-align: right;line-height:2.5em;"><a href="javascript:window.scrollTo(0,0);" class="navToTop" style="border-bottom:0px;">return to top</a></h6>';
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
                	thisPerson += self.formatPersonData(value.Address,value.StaffData);
                	columnCount++;
                	if (columnCount==2){
                		columnCount=0;
                		//finish the row                          		
                   		thisPerson += '</div>'; //closes the wdn-col-full from column 1                                		                                			
                		                   
                	}    	
                 }); 
                thisPerson +='<h6 style="text-align: right;line-height:2.5em;"><a href="javascript:window.scrollTo(0,0);" class="navToTop" style="border-bottom:0px;">return to top</a></h6>';
        		jQuery('#people').html(thisPerson);        		
        
        }); //end of complete json call
	 window.scrollTo(0,0);
} //end of show_people function


/** call to query for the json list of subjects
 * 
 * @param subjectElement an array describing the element to update with the listings
 * @param lettertoShow the letter to filter on (or 'all' to show all records of the view type)
 */
DirectoryListing.prototype.show_subjects = function(subjectElement,lettertoShow){
    var self = this;
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
	//console.log("showing subjects for letter "+lettertoShow);
	if (lettertoShow=='all') {lettertoShow='';}
	//console.log("requesting json from "+'http://libdirectory.unl.edu/subjects/subject_listing/'+lettertoShow+'?callback=?');
     var pageCount;
	jQuery.getJSON(this.DirectoryServer+'/subjects/subject_listing/'+lettertoShow+'?callback=?',
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
                        	   thisSubject +='<h6 style="text-align: right;line-height:2.5em;"><a href="javascript:window.scrollTo(0,0);" class="navToTop" style="border-bottom:0px;">return to top</a></h6>';	 	                	  
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
	                    	thisSubject += self.formatSubjectData(value.Subject.subject,faculty.Address);
	                    	//console.log(faculty.Address.display_name + ' is column '+ columnCount);
	                    	columnCount ++;	                    	
	                        if (columnCount==2){	                        	
	 	                	   columnCount = 0;
	 	                	   //finish the row
	 	                	   thisSubject += '</div>'; //close the wdn-col-full from column 1
	 	                   } 
	                    }); 
                	}
                 });
                thisSubject +='<h6 style="text-align: right;line-height:2.5em;"><a href="javascript:window.scrollTo(0,0);" class="navToTop" style="border-bottom:0px;">return to top</a></h6>';
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
DirectoryListing.prototype.formatSubjectData = function(subject,subjectperson){
    var thisSubjectHtml ='';
    thisSubjectHtml +='<div class="bp480-wdn-col-one-half box_hidden">';
    thisSubjectHtml +='<div class="wdn-col-full" style="width:100%;padding:0;">';
    thisSubjectHtml +='     <div class="sub-bg wdn-center" style="padding-top:1em;">';
    thisSubjectHtml +='         <h5>'+subject+'</h5>';
    thisSubjectHtml +='     </div>';
    thisSubjectHtml +=' </div>';
    thisSubjectHtml +=' <div class="wdn-col box_dir" style="padding-top:1em;">';
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
DirectoryListing.prototype.letter_nav = function(){

    var self = this;
	var currentURL = location.href.replace(location.hash,"");
	if (this.view=='staff'){ letter_query = this.DirectoryServer+'/addresses/get_letters/staff/?callback=?';}
	else if (this.view=='subjects'){letter_query = this.DirectoryServer+'/subjects/get_letters?callback=?';}
	else if (this.view=='faculty') {letter_query=this.DirectoryServer+'/addresses/get_letters/faculty/?callback=?';}
	else{return false;}
//	console.debug("current letter:"+this.currentLetter, this.view);
   jQuery.getJSON(letter_query,
    function(data) {
         jQuery.each(data.letters, function(letter,count){
                 if (count > 0) { 
                     jQuery("<a>",{html:letter.toUpperCase(), href:currentURL+"#"+letter,class:"letter letter_link letter_"+letter}).appendTo(".letters");
                     if (!self.firstLetter){
                    	 self.firstLetter = letter;
//                    	 console.debug("first letter:"+self.firstLetter);
                     }
                      
                }
                 else { 
                	 if (self.currentLetter == letter){
                		self.currentLetter = null;
                		 
                	 }
                	 jQuery("<span>",{html:letter.toUpperCase(),class:"letter"}).appendTo(".letters");
                	 
                 }                    
       });
         if (!self.currentLetter){ self.currentLetter = self.firstLetter;}
//         console.debug(self.currentLetter);
         //add the all option - eww - hate this
         jQuery("<a>",{html:"[ All ]", href:currentURL+"#all",class:"letter letter_link letter_all"}).appendTo(".letters");
         
	   	  if (self.view == 'subjects') { 
			  self.show_subjects({tag:'div',tag_class:'subject_info'},self.currentLetter);
		  }
		  else{
			  self.show_people({tag:'div',tag_class:'directory_info'},self.currentLetter);
		  }
			jQuery('html, body').animate({
		        scrollTop: jQuery("#directory_heading").offset().top
		    }, 0);
         jQuery(".letter_"+self.currentLetter).addClass('selected'); //start with a as default
         jQuery(".letter_link").click(function(){   
        	 
        	 self.currentLetter = jQuery(this).attr('class').replace(/letter letter_link letter_(\w+)(.*)/,'$1');   
        	 //remove the selected in case someone clicks the same letter twice!
        	
        	 
             if (self.view=='subjects'){            	 
            	 self.show_subjects({tag:'div',tag_class:'subject_info'}, self.currentLetter);            	 
             }
             else{
            	 
            	 self.show_people({tag:'div',tag_class:'directory_info'}, self.currentLetter);
            	}
             jQuery('.letter_link').removeClass('selected');
             //jQuery(this).addClass('selected');
             jQuery(".letter_"+self.currentLetter).addClass('selected'); //start with a as default
 			jQuery('html, body').animate({
		        scrollTop: jQuery("#directory_heading").offset().top
		    }, 0);	
         });
         
   });

} //end of letter_nav function
               
