/* 
 * @name   Plugin for jquery "TabsPro" 
 * @author Николай Прокопов
 * @site   http://prokopov-nikolai.ru
 * @version v 0.3
 * @date 25.10.2011
 */
(function( $ ){
  jQuery.fn.debugTools = function() {
    var cookie_date_new = new Date ( );  // Текущая дата и время
    var nt = cookie_date_new.getTime() + 31104000000;
    cookie_date_new.setTime(nt);

    var cookie_date_del = new Date ( );  // Текущая дата и время
    var nt = cookie_date_del.getTime() - 1000;
    cookie_date_del.setTime(nt);

    var query_list =  this.find('.query-list');
    
    var string = window.location.href;
    var delimiter = '/';
    var host = string.toString().split ( delimiter.toString() );
    host = host[2];
    var domain = host.split('www').join('');    
  
  
    var panel = this;
    // схлопнем панель если нужно
    var results = document.cookie.match ( '(^|;) ?debug_tools=([^;]*)(;|$)' );
    if (results && results[2] == 'hide') {
      panel.find('.total-info').css('display', 'none');
      panel.find('.debug-tools-bug').css('display', 'block');
    }
    // отобразаим запросы
    results = document.cookie.match ( '(^|;) ?debug_tools_show=([^;]*)(;|$)' );
    if (results && results[2] == 'database') {
      query_list.css('display', 'block');
    }
    
    //--------------------------------------------------------------------------
    // показываем запросы
    this.find('.database').click(function(){
      if (query_list.css('display') == 'none') {
        query_list.css('display', 'block');
        // ставим куку
        document.cookie = "debug_tools_show=database; expires=" + cookie_date_new.toGMTString() + "; path=/; domain=" + domain;
      } else {
        query_list.css('display', 'none');
        // удаляем куку
        document.cookie = "debug_tools_show=; expires=" + cookie_date_del.toGMTString() + "; path=/; domain=" + domain;
      }
    });
    //--------------------------------------------------------------------------
      
    this.find('.collapse').click(function(){
      panel.find('.total-info').css('display', 'none');
      panel.find('.debug-tools-bug').css('display', 'block');
      // ставим куку
      document.cookie = "debug_tools=hide; expires=" + cookie_date_new.toGMTString() + "; path=/; domain=" + domain;
    });
    //--------------------------------------------------------------------------
    
    this.find('.debug-tools-bug').click(function(){
      panel.find('.total-info').css('display', 'block');
      panel.find('.debug-tools-bug').css('display', 'none');
      // удаляем куку
      document.cookie = "debug_tools=; expires=" + cookie_date_del.toGMTString() + "; path=/; domain=" + domain;
    });
    //--------------------------------------------------------------------------
  };
})( jQuery );