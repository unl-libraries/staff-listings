jQuery.ajaxSetup({
    scriptCharset: "utf-8", //maybe "ISO-8859-1"
    contentType: "application/json; charset=utf-8"
});
listings = new DirectoryListing();
function DirectoryListing(){
	this.DirectoryServer = '';  //this should be the value of the Library Directory application without a leading http[s]://	
	this.currentLetter = 'a'; // letter currently viewing for 
	this.view = null; //the view : faculty, staff 
	return this;
	
}

/** 
 * init will be called on page load for each listing
 * 
 *  @param view string value of either 'faculty', 'staff' 
 */
DirectoryListing.prototype.init = function(view){
	this.firstLetter; //first letter for each view with content
	this.loading_html = '<div class="dcf-bleed dcf-pt-8"><div class="dcf-text-center"><div id="floatingBarsG">\
		<div class="blockG" id="rotateG_01"></div>\
		<div class="blockG" id="rotateG_02"></div>\
		<div class="blockG" id="rotateG_03"></div>\
		<div class="blockG" id="rotateG_04"></div>\
		<div class="blockG" id="rotateG_05"></div>\
		<div class="blockG" id="rotateG_06"></div>\
		<div class="blockG" id="rotateG_07"></div>\
		<div class="blockG" id="rotateG_08"></div>\
	</div></div></div>';
	if (location.hash.replace('#','')) {
		this.currentLetter = location.hash.replace('#','');
	}    
	//execute the function from letter_nav.js that displays the alphabet links
	this.view = view;
	this.letter_nav(view);
	//initialize the page
	console.log(this.DirectoryServer);
}

/** format the links to libguides and publications/vita 
 * @param externalLinks array
 * @returns {String} formatted HTML string 
 */
DirectoryListing.prototype.specialLinks = function(person,links){
	var linkHtml = '';
	//if (person.libguide_profile){ linkHtml += "<p><a href='"+person.libguide_profile+"' class='dcf-btn dcf-btn-primary dcf-w-max-xs'>Subject Specialties</a></p>";}
	 if (links){
	      jQuery.each(externalLinks,function(index,value){         
	          if (value.link_type == 'digitalcommons'){ linkHtml += "<p><a href='"+value.url+"' class='dcf-btn dcf-btn-primary dcf-w-max-xs'>Publications / Vitae</a></p>";}	          
	      });	      
     }
	 return linkHtml;
}
  
/** format people listings
 * @param person main person entry array
 * @param libData array of associated library only data for the individual
 * @param view  the type of view: 'faculty' or 'staff'
 * @returns {String} formatted HTML string
 */
DirectoryListing.prototype.formatPersonData = function(person,libData,version){
	  // handle the different versions for smooth migration from 2 to 3
	if (version && version > "3.7"){    	  
		  //version 3
  	  externalLinks = libData.external_links;
  	  subjects = libData.subjects;
      }
	else{
		//version 2
		 externalLinks =libData.ExternalLinks;
		 subjects = libData.Subjects;
	}
      
      var thisPersonHtml ='';
      thisPersonHtml += '<div class="dcf-col unl-bg-cream" >'; //'<div class="bp480-wdn-col-one-half box_hidden">';
      thisPersonHtml += '<div class="dcf-pt-2 box_dir" style="padding:1em; border: 3px solid rgb(233, 233, 233);">'
      thisPersonHtml += '    <div class="dcf-grid-halves@md dcf-col-100% dcf-col-gap-5 dcf-row-gap-5">'; //'<div class="wdn-col box_dir" style="padding-top:1em;">';
      if (this.view == 'faculty'){
    	  //we only show the person's picture if they are faculty
    	  thisPersonHtml += '<div class="dcf-col dcf-pt-2 dcf-pl-3">';    	  
    	  thisPersonHtml += '    <img class="unl-frame-quad" alt="Headshot Photo of '+person.display_name+' at UNL Libraries" src="https://directory.unl.edu/avatar/'+person.userid+'?s=large">';    	  
    	  //move the libguides profile button and digital commons/publications -vita here
    	  if (externalLinks) {thisPersonHtml += this.specialLinks(person,externalLinks);}
    	  else {thisPersonHtml += this.specialLinks(person);}
    	  thisPersonHtml += '</div>';
    	  thisPersonHtml += '<div class="dcf-col dcf-pt-2 dcf-pr-3 dcf-pl-3">'; //<div class="bp640-wdn-col-one-half">';
      }
      else{
    	  //use the full column width for staff, since they have no pictures
    	  thisPersonHtml += '<div class="dcf-col-100% dcf-pt-2 dcf-pr-3 dcf-pl-3">'; //'<div class="bp640-wdn-col-full">';
      }
      thisPersonHtml +='    <h5><a href="https://directory.unl.edu/people/'+person.userid+'" title="View '+person.display_name+' Profile">'+person.display_name+'&nbsp;&nbsp;<img alt = "Profile link of '+person.display_name+'" src="//libraries.unl.edu/images/icons/external-link-16.png"/></a>';
      thisPersonHtml +='<span class="dcf-subhead">'+person.unl_position;
      if (person.library_position) { thisPersonHtml += "<br />"+person.library_position+"\n";}
      thisPersonHtml += '</span></h5>';
      thisPersonHtml +='    <p class="dcf-mt-0">'+person.address+'<br>'; //was clear-top class
      thisPersonHtml +='    '+person.phone+'<br>';
      thisPersonHtml +='   <a class="long-email dont-break-out" href="mailto:'+person.email+'">'+person.email+'</a></p>';
      //websites
      thisPersonHtml += "<p>";
      if(person.website) { thisPersonHtml += "<a href='"+person.website+"' style='border-bottom:none;' title='View website for "+person.display_name+"'><img src='//libraries.unl.edu/images/SocialMedia/web-20.png' alt='Website for "+person.display_name+"'/><\/a>&nbsp;";}
      addLink = '';
      if (externalLinks){
	      jQuery.each(externalLinks,function(index,value){         
	          if (value.link_type == 'linkedin'){ thisPersonHtml += "&nbsp;<a href='"+value.url+"' style='border-bottom:none;'><img alt='Linked in link' src='//libraries.unl.edu/images/SocialMedia/linkedin-20.png'/><\/a>";}
	          if (value.link_type == 'facebook'){ thisPersonHtml += "&nbsp;<a href='"+value.url+"' style='border-bottom:none;'><img alt='Facebook link' src='//libraries.unl.edu/images/SocialMedia/facebook-20.png'/><\/a>";}
	          if (value.link_type == 'twitter'){ thisPersonHtml += "&nbsp;<a href='"+value.url+"' style='border-bottom:none;'><img alt='Twitter link' src='//libraries.unl.edu/images/SocialMedia/twitter-20.png'/><\/a>";}
	           //move the digital commons link out to under the photo
	      });
      }
      thisPersonHtml += addLink;
      thisPersonHtml +='</p>';
      thisPersonHtml +='</div>'; //dcf-col dcf-pt-2
      thisPersonHtml += '</div>'; //dcf-grid-halves
      
      if (subjects && subjects.length > 0){
    	  thisPersonHtml += '<div class="dcf-grid dcf-col-gap-5 dcf-row-gap-5">'; //'<div class="wdn-col-full">';
    	  thisPersonHtml += '<div class="dcf-col-100% box_no_border_dir">'; //'    <div class="box_no_border_dir" style="height:10%;padding-top:1em;padding-bottom:1em;">';
      
    	  thisPersonHtml +='<div class="sub-bg dcf-m-4 dcf-p-4 unl-bg-light-gray">';
          thisPersonHtml += '<h4>Subject Specialties:</h4>'; //'<h6 class="clear-top">Subject Specialties:</h6>';
          thisPersonHtml += '<ul>'; //'<ul style="font-size:small;">';
         jQuery.each(subjects, function(index,elem){        	 
              thisPersonHtml+='<li>'+elem.subject+'</li>';
         });
         thisPersonHtml += "</ul>\n";
         thisPersonHtml += "</div>"; //sub-bg         
         thisPersonHtml +='    </div>'; //dcf-col-100%  box no border
         thisPersonHtml +='</div>'; // dcf-grid
      }
      thisPersonHtml += '</div>';
      thisPersonHtml +='</div>'; // dcf-col unl-bg-cream      
      
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
	jQuery("#people").html(self.loading_html);

	if (lettertoShow=='all') {lettertoShow='';}
	//else if (lettertoShow =='') { lettertoShow = 'a';}
     var people=[]; //array to hold people html elements
     //console.debug("Starting to create profiles in "+personElement.tag + personElement.tag_class + " for letter "+lettertoShow);
	 jQuery.getJSON(this.DirectoryServer+'/addresses/'+this.view+'_listing/'+lettertoShow+'?callback=?',
			function(data){									
				var letter='';  //the current first letter of the section to display 
				var lastLetter;  //previous letter so we know if we need to print the letter heading and close the section
	         	thisPerson = ''; //string for the HTML to be appended	   
	         	var version = data.version;
                jQuery.each(data.people, function(index,value){   
                	if (version && version > "3.7"){
                		letter = value.last_name.charAt(0);
                	}
                	else{
                		letter = value.Address.last_name.charAt(0);
                	}
                	//letter heading and navigation
                	if (lastLetter != letter){                		
                		if (lastLetter){
                			//only do these things if it's not the first letter
                    		thisPerson += '</div></div></div>'; //close the boxes from the previous set
	                		thisPerson +='<h6 style="text-align: right;line-height:2.5em;"><a href="javascript:window.scrollTo(0,0);" class="navToTop" style="border-bottom:0px;">return to top</a></h6>';
                		}
                		//create the letter navigation and header                                	
                		thisPerson += self.letter_divider(letter);
            	        //start the box layouts
            	        thisPerson += '<div class="dcf-bleed dcf-pt8" ><div class="dcf-wrapper dcf-pb-5"><div class="dcf-grid-halves@sm dcf-col-gap-5 dcf-row-gap-5">'; //starts off the boxes
                	} //end of letter heading information                               	
                	
                	lastLetter = letter; //keep track of the letter we are on for next loop
                	if (version && version > "3.7"){
                		thisPerson += self.formatPersonData(value,value.employee,version);
                	}
                	else{
                		thisPerson += self.formatPersonData(value.Address,value.StaffData,'');
                	}
                 }); 
                thisPerson += '</div></div></div>'; //close the boxes layout
                thisPerson +='<h6 style="text-align: right;line-height:2.5em;"><a href="javascript:window.scrollTo(0,0);" class="navToTop" style="border-bottom:0px;">return to top</a></h6>';
              
        		jQuery('#people').html(thisPerson);        		
        
        }); //end of complete json call
	 window.scrollTo(0,0);
} //end of show_people function




/**
 * format the letter dividers 
 */
DirectoryListing.prototype.letter_divider = function(letter){
	//letter divider
	var letter_divider_html = '';
    letter_divider_html += '<div class="dcf-grid-thirds">'; //'<div class="wdn-col-full">';
    letter_divider_html += ' <div class="dcf-txt-center">'; //' <div class="wdn-col-four-ninths wdn-center">';
    letter_divider_html += '  <hr class="dir_right">';
    letter_divider_html += ' </div>';
    letter_divider_html += '<div class="dcf-txt-center">';//'<div class="wdn-col-one-ninth wdn-center">';
    letter_divider_html += '<h3 class="dcf-mt-0 dcf-txt-h1" id='+letter.toLowerCase()+'>'+letter.toUpperCase()+'</h3>';
    letter_divider_html += '</div>';
    letter_divider_html += '<div class="dcf-txt-center">'; //'<div class="wdn-col-four-ninths wdn-center">';
    letter_divider_html += '<hr class="dir_left"></div></div>';
    return letter_divider_html;
}
/**
 * Creates the letter navigation at the top of a page
 * @param view string indicating what type of view ('staff','faculty')
 * @returns {Boolean} returns false if incorrect view sent
 */
DirectoryListing.prototype.letter_nav = function(){

    var self = this;
	var currentURL = location.href.replace(location.hash,"");
	if (this.view=='staff'){ letter_query = this.DirectoryServer+'/addresses/get_letters/staff/?callback=?';}	
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
         
	   	  self.show_people({tag:'div',tag_class:'directory_info'},self.currentLetter);
		
			jQuery('html, body').animate({
		        scrollTop: jQuery("#directory_heading").offset().top
		    }, 0);
         jQuery(".letter_"+self.currentLetter).addClass('selected'); //start with a as default
         jQuery(".letter_link").click(function(){   
        	 
        	 self.currentLetter = jQuery(this).attr('class').replace(/letter letter_link letter_(\w+)(.*)/,'$1');   
        	 //remove the selected in case someone clicks the same letter twice!
        	
        	 
             self.show_people({tag:'div',tag_class:'directory_info'}, self.currentLetter);
            
             jQuery('.letter_link').removeClass('selected');
             //jQuery(this).addClass('selected');
             jQuery(".letter_"+self.currentLetter).addClass('selected'); //start with a as default
 			jQuery('html, body').animate({
		        scrollTop: jQuery("#directory_heading").offset().top
		    }, 0);	
         });
         
   });

} //end of letter_nav function
               
