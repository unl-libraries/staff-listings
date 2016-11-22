jQuery.ajaxSetup({
    scriptCharset: "utf-8", //maybe "ISO-8859-1"
    contentType: "application/json; charset=utf-8"
});
var currentURL = location.href.replace(location.hash,"");
  jQuery.getJSON('http://libdirectory.unl.edu/addresses/get_letters?callback=?',
    function(data) {
         jQuery.each(data.letters, function(letter,count){
                 if (count > 0) { 
                     jQuery("<a>",{html:letter.toUpperCase(), href:currentURL+"#"+letter,class:"letter"}).appendTo("#letters");
                }
                 else { jQuery("<span>",{html:letter.toUpperCase(),class:"letter"}).appendTo("#letters");}
       });
   });
  
  function formatPersonData(person,libData){
      var thisPersonHtml ='';
      if(person.libguide_profile) {
      thisPersonHtml +="<strong><a href='"+person.libguide_profile+"'>"+person.display_name+"<\/a><\/strong>&nbsp;";
      }
      else{ thisPersonHtml += "<strong>"+person.display_name+"<\/strong>";}
      if(person.website) { thisPersonHtml += "&nbsp;<a href='"+person.website+"' class='social_media_icon'><img src='images/SocialMedia/web-20.png' /><\/a>";}
      jQuery.each(libData.ExternalLinks,function(index,value){
      if (value.link_type == 'twitter'){ thisPersonHtml += "&nbsp;<a href='"+value.url+"' class='social_media_icon'><img src='images/SocialMedia/twitter-20.png' /><\/a>";}
      if (value.link_type == 'linkedin'){ thisPersonHtml += "&nbsp;<a href='"+value.url+"' class='social_media_icon'><img src='images/SocialMedia/linkedin-20.png'/><\/a>";}
      if (value.link_type == 'facebook'){ thisPersonHtml += "&nbsp;<a href='"+value.url+"' class='social_media_icon'><img src='images/SocialMedia/facebook-20.png'/><\/a>";}
      });
      thisPersonHtml += "<br />\n";     
      thisPersonHtml += person.unl_position+"<br />";
      if(person.library_position){ thisPersonHtml += person.library_position+"<br />\n"; }
      if (libData.Subjects.length > 0){
        thisPersonHtml += "Subject specialties: ";
       thisPersonHtml += libData.Subjects.map(function(elem){
            return elem.subject;
       }).join(", ");
       thisPersonHtml += "<br />\n";
      }
      thisPersonHtml +=person.address+"<br />\n";
      thisPersonHtml +=person.phone+"<br />\n";
      thisPersonHtml +="<a href='mailto:"+person.email+"'>"+person.email+"<\/a>\n";             
      return thisPersonHtml;
  }
  
function show_people(personElement){
     var pageCount;
	jQuery.getJSON('http://libdirectory.unl.edu/addresses/public_feed/?callback=?',
			function(data){
				pageCount = data.params.params.paging.Address.pageCount;
				recordCount = data.params.params.paging.Address.current;
				for (i=1; i <= pageCount; i++){
					jQuery.getJSON('http://libdirectory.unl.edu/addresses/public_feed/page:'+i+'?callback=?',
							function(data) {
								
								var personCount = 0;
								var letter='';
								var lastLetter;
                                jQuery.each(data.people, function(index,value){
                                	personCount++;                          
                                	letter = value.Address.last_name.charAt(0);
                                	if (lastLetter != letter){
                                		thisPerson = '<span id='+letter.toLowerCase()+'/>';
                                	}
                                	else thisPerson = '';
                                	lastLetter = letter;
                                	thisPerson += formatPersonData(value.Address,value.StaffData);
                                	if (personCount==6){
                                		personCount=0;
                                		//thisPerson +='<div style="float:right;"><a href="'+currentURL+'#letters">Back to top</a>';
                                		thisPerson +='<h6 style="text-align: right;"><a href="'+currentURL+'#letters" style="border-bottom:0px;">return to top</a></h6>';
                                	}                                    
                                    jQuery('<'+personElement.tag+'>',{
                                          class:personElement.tag_class,
                                          id:value.Address.last_name+value.Address.first_name,
                                          html:thisPerson 
                                    }).appendTo("#people");                                      
                                	var people = jQuery('p.directory_info');
                                	people.sort(function(a,b){
                                	var an = a.getAttribute('id'),
                                		bn = b.getAttribute('id');

                                	if(an > bn) {
                                		return 1;
                                	}
                                	if(an < bn) {
                                		return -1;
                                	}
                                	return 0;
                                	});

                                	people.detach().appendTo("#people");
                                 });
                                
                     }); //end of get json call
					
                 }
				
             });

} //end of function