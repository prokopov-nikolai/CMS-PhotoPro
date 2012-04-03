/* 
 * @name   Plugin for jquery "Catalog Pro" 
 * @author Николай Прокопов
 * @site   http://prokopov-nikolai.ru
 * @version v 0.1
 * @date 23.03.2012
 */
$(function(){
  $('.preview').click(function(){
    $this = $(this);
    if ($this.hasClass('current')) return false;
    $('.current .loader').fadeOut();
    var filename = $this.attr('data-filename');
    var width = 400;
    var height = 550;
    var url = '/image/' + width + 'x' + height + '/' + filename;
    $this.find('.loader').fadeIn();
    $('#conteiner').html('<img src=' + url + ' />').find('img').bind('load',function(){
      //$this.find('.loader').fadeOut();
      $('#slider-img').fadeOut('fast', function(){
        $(this).attr('src', url);
        $(this).attr('data-filename', filename);
        $(this).fadeIn('normal', function(){
          $('.current').removeClass('current');
          $this.addClass('current');          
        });
      });
    });
    return false;
  });
});