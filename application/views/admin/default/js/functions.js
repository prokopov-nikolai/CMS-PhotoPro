$(function(){
  render_results($('.answers'));
});

function show_form($obj){
  $obj.parent().find('.form').slideToggle();
  $obj.attr('onclick', '');
}

function render_results(jdata){
  answers = $.parseJSON(jdata);
  $r = $('<div/>').addClass('answers');
  sum = get_vote_sum(answers);
  for(aid in answers) {
    a = answers[aid];
    w = a.count / sum * 100 + '%';
    $r.append($('<div/>').addClass('answer-text').html(a.answer_text));
    $r.append($('<div/>').addClass('answer-votes').css({width: w}).html(
      $('<span/>').html(a.count).addClass('answer-count')
    )); 
  }
  return $r;
}
// -----------------------------------------------------------------------------
// Подсчитываем сумму голосов
function get_vote_sum(answers){
  sum = 0;
  for (aid in answers) {
    sum += parseInt(answers[aid]['count']);
  }
  return sum;
}
