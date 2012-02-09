(function( $ ){
  var methods = {
    init : function( ) {
      this.each(function(){
        // приглушим превью
        if (!$(this).hasClass('photopro-current'))
          $(this).css('opacity', 0.5);
          
        // анимируем превьюшки
        $(this).hover(
          function(){
            $(this).stop().animate({'opacity': 1}, 'slow');
          },
          function(){
            if (!$(this).hasClass('photopro-current'))
              $(this).stop().animate({'opacity': 0.5}, 'slow');
          }
        );
        
        // событие клика по превью
        $(this).click(function(){
          if($(this).hasClass('photopro-current')) return false;
          
          // переключим активную превью
          $cur = $('.photopro-current');
          $cur.animate({'opacity':0.5}, 'slow');
          $('.photopro-current').removeClass('photopro-current');
          $(this).addClass('photopro-current');            
          $(this).animate({'opacity':1}, 'slow');
          
          // покажим большую картинку
          methods.show($(this).attr('data-url'));
        })
      });
    },
    show : function(url) {
      // удалим текущую фотку
      $('.photopro-box').find('.box').animate({'opacity': 0}, 'slow', function(){
        $(this).html('<img src=' + url + ' />').find('img').bind('load',function(){
          $(this).parents('.box').animate({opacity: 1}, 'slow');
        });
      });
      
    },
    next : function( ) {
    },
    prev : function( ) {
      
    }
  };  

  $.fn.photopro = function( method ) {
     
    // логика вызова метода
    if ( methods[method] ) {
      return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
    } else if ( typeof method === 'object' || ! method ) {
      return methods.init.apply( this, arguments );
    } else {
      $.error( 'Метод ' +  method + ' в jQuery.tooltip не существует' );
    }   
   
  };
})( jQuery );