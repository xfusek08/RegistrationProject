
$(document).ready(function(){
  $('body').on('click', '.adm-adm-newregconn-conn .new_registration', function(){
    // TODO: navigovat na konkretni registraci
  });
  $('body').on('mouseenter', '.adm-newregconn-conn .new_registration', function(){
    $(this).css({background: 'rgba(220,250,255,1)'});
    $('.adm-dayevents-view .event[pk="' + $(this).attr('courpk') + '"]').addClass('selhover');
    $('.registrationconn .registration[pk="' + $(this).attr('pk') + '"] .header').addClass('selhover');
  });
  $('body').on('mouseleave', '.adm-newregconn-conn .new_registration', function(){
    $(this).css({background: 'rgb(245, 245, 100)'});
    $('.adm-dayevents-view .event[pk="' + $(this).attr('courpk') + '"]').removeClass('selhover');
    $('.registrationconn .registration[pk="' + $(this).attr('pk') + '"] .header').removeClass('selhover');
  });

  $('body').on('mouseenter', '.adm-dayevents-view .event', function(){
    $(this).addClass('selhover');
    $('.adm-newregconn-conn .new_registration[courpk="' + $(this).attr('pk') + '"]').css({background: 'rgba(220,250,255,1)'})
  });
  $('body').on('mouseleave', '.adm-dayevents-view .event', function(){
    $(this).removeClass('selhover');
    $('.adm-newregconn-conn .new_registration[courpk="' + $(this).attr('pk') + '"]').css({background: 'rgb(245, 245, 100)'});
  });
  
  $('body').on('mouseenter', '.registrationconn .registration .header', function(){
    $(this).addClass('selhover');
    $('.adm-newregconn-conn .new_registration[pk="' + $(this).closest('.registration').attr('pk') + '"]').css({background: 'rgba(220,250,255,1)'})
  });
  $('body').on('mouseleave', '.registrationconn .registration .header', function(){
    $(this).removeClass('selhover');
    $('.adm-newregconn-conn .new_registration[pk="' + $(this).closest('.registration').attr('pk') + '"]').css({background: 'rgb(245, 245, 100)'});
  });
});

var NewRegistrationConn = function()
{
  /* Inicializace */
  this.i_oConnObj = $('.adm-newregconn')
  
  /* Funkce */  
  this.GetNewRegistrations = function()
  {
    var self = this;
    SendAjaxRequest(
      "type=GetNewRegistrations",  
      true,
      function(response){
        // naplnime pole novimy rezervacemi
        var v_iRegCount = 0;
        $(response).find('registration').each(function(i){
          var v_oReg = BuildRegistrationObj($(this));
          if (self.i_oConnObj.find('.new_registration[pk="' + v_oReg.attr('pk') + '"]'))
          {
            v_iRegCount++;
            v_oReg.css({background: 'rgb(245,245,100)'}).hide();
            v_oReg.appendTo(self.i_oConnObj.find('.adm-newregconn-conn'));
            setTimeout(function(){
              v_oReg.toggle( "slide", {direction: 'right'});
            }, 150 + i * 70);           
            var td = GetCalenDayTDByDate(StrToDate($(this).attr('courdate')), ':not(hasnew)');
            if (td != null)
            {
              td.animate({
                backgroundColor: 'rgb(250,250,0)'
              }, 300, "swing", function(){
                $(this).addClass('hasnew');
              });
            }
            
          }
        });
        // aktualizujeme pocet
        $('.newregcount').text(v_iRegCount);
      }
    );  
  };
      
}
function BuildRegistrationObj(v_oReservationElem)
{
  var html = 
    '<div'+
    ' class="new_registration"'+
    ' pk="' + v_oReservationElem.attr('pk') + '"'+
    ' courpk="' + v_oReservationElem.attr('courpk') + '"'+
    ' date="' + v_oReservationElem.attr('courdate') + '">';
  html += 
    '<div class="term">' + v_oReservationElem.attr('courdatetime') +  '</div>'+ 
    '<table>'+
       '<tr><td>Jméno:</td><td>' + v_oReservationElem.attr('firstname') +' '+ v_oReservationElem.attr('lastname') + '</td></tr>'+
       '<tr><td>Kurz:</td><td>' + v_oReservationElem.attr('language') + ' - ' + v_oReservationElem.attr('courname') + '</td></tr>'+
    '</table>'+
    '<div class="created"><span>Vytvořeno:</span><span>' + v_oReservationElem.attr('created') + '</span></div>' +            
  '</div>';
  return $(html);
}

function GetCalenDayTDByDate(date, selector)
{
  var td = null;
  $('#datepicker').find(
      'table tbody td' +
      '[data-month="' + date.getMonth() + '"]' +
      '[data-year="' + date.getFullYear() + '"]' + selector).each(function(){
    if ($(this).find('a').text() == date.getDate())
    {
      td = $(this);
    }    
  });
  return td;  
}