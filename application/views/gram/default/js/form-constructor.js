var formConstructor = {
  
  // инициализация
  init: function($obj){
    var fc = this;
    $obj.each(function(){
      var fields = $.parseJSON($(this).attr('data-form-content'));
      var id = $(this).attr('data-form-id');
      $(this).html(fc.construct(fields, id));
      $(this).attr('data-form-content', '');
    });
  },
  
  // строим форму
  construct: function(fields, id){
    var fc = this;
    var form = fc.form(id);
    for(k in fields) {
      var f = fields[k];
      if (f.field_type == 'checkbox') {
        var lb = fc.label(f);
        var text = ' ' + lb.html()
        lb = lb.html('').append(fc[f.field_type](f)).append(text);
        form.append(lb);
      } else {
        form.append(fc.label(f));
        form.append(fc[f.field_type](f));
      }
    }
    form.append(fc.submit());
    return form;
  },
  
  // форма
  form: function(id){
    return $('<form/>')
             .attr('method', 'post')
             .append($('<input/>').attr('type', 'hidden').attr('name', 'form_id').val(id));
  },
  
  // лейбл
  label: function(f){
    var ness = {0: '', 1: '<span style="color:#d60000;"> *</span>'}
    return $('<label/>')
             .attr('for', f.field_url)
             .css({'margin-top': '20px'})
             .html(f.field_title + ness[f.field_necessarily]);
  },
  
  // отправить
  submit: function(){
    return $('<input/>')
             .attr('type', 'submit')
             .css({'margin-top': '20px'})
             .val('Отправить');  
  },
  
  // текстовое поле
  text: function(f){
    return $('<input/>')
             .attr('name', f.field_url)
             .attr('id', f.field_url)
             .attr('type', 'text');
  },
  
  // текстовое поле
  textarea: function(f){
    var ta = $('<textarea/>')
             .attr('name', f.field_url)
             .attr('id', f.field_url)
             .css({height: 200, width:400 });
    if (f.field_redactor == 1) {
      ta.addClass('redactor');
    }
    return ta;
  },
  
  // выпадающий список
  select: function(f){
     var list = $('<select/>')
                  .attr('name', f.field_url)
                  .attr('id', f.field_url)
                  .append($('<option/>').attr('value', '').html('---'));
     var arr = f.field_select.split(';');
     for (k in arr){
       list.append($('<option/>').attr('value', arr[k]).html(arr[k]));
     }
     return list;
  },
  
  // поле для телефона
  phone: function(f){
    var ph = $('<input/>')
             .attr('name', f.field_url)
             .attr('id', f.field_url)
             .attr('type', 'text')
             .addClass('phone');
    if (f.field_mask != ''){
      ph.attr('data-mask', f.field_mask);
    }
    return ph;
  },
  
  // поле для email
  email: function(f){
    return $('<input/>')
             .attr('name', f.field_url)
             .attr('id', f.field_url)
             .attr('type', 'text');
  },
  
  // чекбокс да-нет
  checkbox: function(f){
    return $('<input/>')
             .attr('name', f.field_url)
             .attr('id', f.field_url)
             .attr('type', 'checkbox')
             .removeAttr('cheched')
             .val('Нет').click(function(){
               if ($(this).val() == 'Нет') 
                 $(this).val('Да');
               else 
                 $(this).val('Нет');
             });
  }
}
