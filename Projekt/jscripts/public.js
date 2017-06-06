
$(document).ready(function (){
  $('body').on('click', '.paragraph .cap', function(){
    var content = $(this).parent('.paragraph').children('.content');
    content.stop().slideToggle(300, "swing");
  });
});
