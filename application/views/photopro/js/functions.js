$(function(){
  $('#menu a').hover(
    function(){
      if (typeof($(this).attr('class')) != 'undefined' && 
           $(this).attr('class').indexOf('current', 0) != -1) {
             // ничего не делаем
      } else {
        $(this).animate({'opacity': 0.5}, 200);  
        
      }
    },
    function(){
       if (!$(this).parent().hasClass('current'))
    	   $(this).animate({'opacity': 1}, 200);
    }
  );
});

String.prototype.showAddress = 
function (_hamper,_prefix,_postfix,_face) { 
 _hamper= 
 _prefix+ 
 "@"+ 
 this+ 
 (_postfix || '') 
 document.write((_face||_hamper).link("mailto:"+_hamper)); 
} 
