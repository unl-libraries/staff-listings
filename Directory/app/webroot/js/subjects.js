jQuery.ajaxSetup({
    scriptCharset: "utf-8", //maybe "ISO-8859-1"
    contentType: "application/json; charset=utf-8"
});
var currentURL = location.href.replace(location.hash,"");
  jQuery.getJSON('http://libdirectory.unl.edu/subjects/get_letters?callback=?',
    function(data) {
         jQuery.each(data.letters, function(letter,count){
                 if (count > 0) { 
                     jQuery("<a>",{html:letter.toUpperCase(), href:currentURL+"#subject"+letter,class:"letter"}).appendTo("#subject_letters");
                }
                 else { jQuery("<span>",{html:letter.toUpperCase(),class:"letter"}).appendTo("#subject_letters");}
       });
   });
function show_subjects(subjectElement){
     var pageCount;
	jQuery.getJSON('http://libdirectory.unl.edu/subjects/feed/?callback=?',
			function(data){
				pageCount = data.params.params.paging.Subject.pageCount;
				recordCount = data.params.params.paging.Subject.current;
				for (i=1; i <= pageCount; i++){
					jQuery.getJSON('http://libdirectory.unl.edu/subjects/feed/page:'+i+'?callback=?',
							function(data) {
								
								var subjectCount = 0;
								var letter='';
								var lastLetter;
                                jQuery.each(data.subjects, function(index,value){
                                	subjectCount++;                                	
                                	if (value.Faculty.length > 0){
                                		letter = value.Subject.subject.charAt(0);
                                	   	if (lastLetter != letter){
                                	   		thisSubject = '<span id="subject'+letter.toLowerCase()+'">';                          
                                	   	}
	                                	else thisSubject = '<span>';
	                                	thisSubject += '<strong>'+value.Subject.subject+'</strong></span>';
	                                	lastLetter = letter;
	                                	jQuery.each(value.Faculty, function(findex,faculty){
	                                		thisSubject += "<br />"+formatSubjectData(faculty.Address);
	                                	});                                	
	                                	console.log(recordCount);
	                                	thisSubject += "<br />";
	                                	if (subjectCount==recordCount){
	                                		thisSubject +='<div style="float:right;"><a href="'+currentURL+'#subject_letters">Back to top</a>';
	                                	}                                    
	                                    
	                                	jQuery('<'+subjectElement.tag+'>',{
	                                          class:subjectElement.tag_class,
	                                          id:value.Subject.subject,
	                                          html:thisSubject 
	                                    }).appendTo("#subject_people");                                      
	                                    
	                                    var subjects = jQuery(subjectElement.tag+'.'+subjectElement.tag_class);
	                                	subjects.sort(function(a,b){
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
	
	                                	subjects.detach().appendTo("#subject_people");
                                	}
                                 });
                                
                     }); //end of get json call
					
                 }
				
             });
     
} //end of function