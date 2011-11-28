// @name   Plugin for jquery "TabsPro" 
// @author Николай Прокопов
// @site   http://prokopov-nikolai.ru

jQuery.fn.tabsPro = function(options) {
	var options = jQuery.extend({
		classTabPassive: 'tab',
		classTabActive:  'tab-active',
		classTabContent: 'tab-content',
		showErrors: true
	},options);
	
	return this.each(function() {
		jQuery(this).find('.' + options.classTabPassive)
			.click(function() {
				jQuery('.' + options.classTabActive).toggleClass(options.classTabActive);
				jQuery(this).toggleClass(options.classTabActive);
				jQuery('.' + options.classTabContent).css('display', 'none');
				jQuery('#' + jQuery(this).attr('rel')).css('display', 'block');
				if (typeof jQuery('#' + jQuery(this).attr('rel')).attr('id') == 'undefined' && options.showErrors === true) {
					alert('Вкладка с идентификатором "' + jQuery(this).attr('rel') +'" не найдена!')
				}
				return false;
			});
	});
};