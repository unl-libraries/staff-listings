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
                                	thisPerson += formatPersonData(value.Address,value.StaffData.ExternalLinks);
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