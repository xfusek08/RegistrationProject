$(document).ready(function ()
{ 
  drawmonth = (new Date()).getMonth();
  CalendarInit($('#datepicker'), DaySelect);
  HighlightAllInvalInputs($('body'));
  $('body').on('change', 'select[name="languagesel"]', function(){
    ChangeLanguage($('#datepicker'));
  });
  $('body').on('click', '.daycourses .course:not(.disabled)', function ()
  {
    SelectCourse($(this).attr('pk'));
  });
  SelectCourseCalendByPK($('.coursechoose').attr('pk'));
  setInterval(UpdateActStatus, 5000);
});

/**
 * @brief Funkce pro vybrani konkretniho datumu
 * 
 * Vola se, kdyz uzivatel klikne na datum v kalendari nevo skript si vyzada zmenu
 * Pri zavolani se:
 *  - vymaze obsah
 *  - zmeni datum na strance
 *  - nactou udalosti pro nove vybrany den
 *  
 * @param jQobj datepicker      - instance datepickeru
 * @param string v_sDateString  - datum na ktere se ma presunout v textove podobe
 * @param function v_fnCallBack - funkce, ktera se zavola po dokonceni vsech kroku
 */
function DaySelect(datepicker, v_sDateString, CallBack)
{
  //console.log('DaySelect()');
  var v_dtPreDate = datepicker.datepicker('getDate');
  datepicker.datepicker('setDate', v_sDateString);
  LoadCoursesonDay(v_sDateString);
  if (typeof(CallBack) == 'function')
    CallBack();
}


/************************** CALENDAR ******************************************/


// xml, ktere doruci server o rozmisteni jednotlivych udalosti, strukturovanych do mezicu a dni
var CalendarDataXML = ''; 
var drawmonth = 0;

function CalendarInit(calendar, DateSelectFunc, MonthSelectFunc)
{
  // nastaveni date picker
  $.datepicker.regional['cs'] = {
    closeText: 'Cerrar',
    prevText: '<',
    nextText: '>',
    currentText: 'Hoy',
    monthNames: ['Leden', 'Únor', 'Březen', 'Duben', 'Květen', 'Červen', 'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec'],
    monthNamesShort: ['Le', 'Ún', 'Bř', 'Du', 'Kv', 'Čn', 'Čc', 'Sr', 'Zá', 'Ří', 'Li', 'Pr'],
    dayNames: ['Neděle', 'Pondělí', 'Úterý', 'Středa', 'Čtvrtek', 'Pátek', 'Sobota'],
    dayNamesShort: ['Ne', 'Po', 'Út', 'St', 'Čt', 'Pá', 'So', ],
    dayNamesMin: ['Ne', 'Po', 'Út', 'St', 'Čt', 'Pá', 'So'],
    weekHeader: 'Sm',
    dateFormat: 'dd.mm.yy',
    firstDay: 1,
    isRTL: true,
    showMonthAfterYear: false,
    yearSuffix: '',
    showOtherMonths: true,
    selectOtherMonths: true,
    numberOfMonths: 2,
    stepMonths: 1
  };
  
  $.datepicker.setDefaults($.datepicker.regional['cs']);
  // nacteni dat ze serveru
  var date = new Date();
  var month = date.getMonth(); // pocitame 0 - 11
  var DateFrom = new Date(date.getFullYear(), month - 1, 1);
  var DateTo = new Date(date.getFullYear(), month + 2, 0);
  RequestCalendarhData(false, DateFrom, DateTo);
  
  // nejprve bez dat vykreslime kalendar
  calendar.datepicker({
    beforeShowDay: function (date)
    {
      result = new Array(false, '');
      LoadDayData(result, $(this), date);
      return result;
    },
    onSelect: function (datestr, datepicker){
      if (typeof(DateSelectFunc) === 'function')
        DateSelectFunc($(this), datestr);      
      datepicker.drawMonth = drawmonth;
    },
    onChangeMonthYear: function(year, month){ 
      $('.dayview').hide();
      $("input[name=c_selterm]").remove();
      var date = new Date(year, month - 1, 1);
      $(this).datepicker('setDate', date);
      RequestCalendarhData(false, new Date(year, month - 2, 1), new Date(year, month + 1, 1), function(){
        if (typeof(MonthSelectFunc) === 'function')
          MonthSelectFunc(year, month);
      });
      drawmonth = month - 1;
    }
  }).on('click', 'button.today', function(){
    DateSelectFunc(calendar, DateToStr(new Date()));
  });
}

function LoadDayData(result, datepicker, date)
{
  var courses = null;
  courses = $(CalendarDataXML).find('day[date="' + DateToStr(date) + '"]');
  var nowdate = new Date();
  nowdate.setHours(0, 0, 0, 0);
  if (courses.length > 0 && date >= nowdate) // na dnesek je uz pozde ? 
  {
    result[0] = true;
  }
}

function LoadCoursesonDay(a_sDateString, force)
{
  //console.log('LoadCoursesonDay(' + a_sDateString + ')');
  var DayView = $('.dayview');
  //DayView.css('display', 'block');
  if (DayView.attr("date") != a_sDateString || force)
  {
    $('.daycourses').remove();
    DayView.attr("date", a_sDateString);
    DayView.find('.conn .date').text(
      a_sDateString + ' (' + $.datepicker.formatDate('DD', StrToDate(a_sDateString)) + ')');

    var DayViewContent = DayView.find('.conn .content');
    DayViewContent.empty();
    var html = '';
    
    /* verze podle jazyka */
    /*
    $(CalendarDataXML).find('day[date="' + a_sDateString + '"] language').each(function ()
    {
      html += '<p class="lang">' + $(this).attr('text') + ':</p>';
      html += '<table class="daycourses">';
      $(this).find('course').each(function(){
        html += 
          '<tr class="course' + (($(this).attr('state') == 'open') ? '' : ' disabled') + '"' +
              ' pk="' + $(this).attr("pk") + '"' + 
              ' title="' + (($(this).attr('state') == 'open') ? 'vybrat pro podrobnosti' : 'obsazeno - nelze vybrat') + '">' + 
            '<td class="time">' + $(this).attr('time') + '</td>' + 
            '<td class="name">' + $(this).attr('name') + '</td> +
            '<td class="capacity">' + $(this).attr('capacity') + '</td>' +
          '</tr>';
      });
      html += '</table>';      
    });
    */
    /* verze podle datumu */
    $(CalendarDataXML).find('day[date="' + a_sDateString + '"]').each(function ()
    {
      html += 
        '<table class="daycourses">' + 
          '<thead>'+
            '<th>zahájení</th>'+
            '<th>jazyk</th>'+
            '<th>název</th>'+
            '<th>obsazenost</th>'+
          '</thead><tbody>';
        
      $(this).find('course').each(function(){
        html += 
          '<tr class="course' + (($(this).attr('state') == 'open') ? '' : ' disabled') + '"' +
              ' pk="' + $(this).attr("pk") + '"' + 
              ' title="' + (($(this).attr('state') == 'open') ? 'vybrat pro podrobnosti' : 'obsazeno - nelze vybrat') + '">' + 
            '<td class="time">' + $(this).attr('time') + '</td>' + 
            '<td class="language">' + $(this).attr('language') + '</td>' +
            '<td class="name">' + $(this).attr('name') + '</td>' +
            '<td class="capacity">' + $(this).attr('capacity') + '</td>' +
          '</tr>';
      });
      html += '</tbody></table>';      
    });
    
    DayViewContent.append(html);
    DayView.slideDown(200);
  }  
}

function ChangeLanguage(calendar)
{
  var v_dtActDate = calendar.datepicker('getDate');
  var year = v_dtActDate.getFullYear();
  var month = v_dtActDate.getMonth();
  var selectedpk = $('.dayview .course.selected').attr('pk');
  RequestCalendarhData(false, new Date(year, month - 2, 1), new Date(year, month + 1, 1), function(){
    DaySelect(calendar, DateToStr(v_dtActDate), function(){
      LoadCoursesonDay(DateToStr(v_dtActDate), true);
    });    
  });
}

function RequestCalendarhData(asynch, fromdate, todate, CallBack)
{
  //console.log('RequestCalendarhData(' + DateToStr(fromdate) + ', ' + DateToStr(todate) + ')');
  SendAjaxRequest(
    "type=GetCalendarData"+ 
    "&language=" + $('select[name="languagesel"]').val() + 
    "&fromdate=" + DateToStr(fromdate) +  
    "&todate=" + DateToStr(todate),
    asynch, 
    function(xml)
    {
      CalendarDataXML = $(xml).html();
      if (typeof(CallBack) == 'function')
        CallBack(xml);
    }
  );
}

/************************** CALENDAR END **************************************/

function SelectCourse(a_sPK, CallBack)
{
  //console.log('SelectCourse(' + a_sPK + ')');
  SendAjaxRequest(
    "type=SelectCourse"+ 
    "&pk=" + a_sPK,
    true, 
    function(response)
    {
      $('.selected').removeClass('selected');
      $('.daycourses .course[pk="' + a_sPK + '"]').addClass('selected');
     
      var v_oObj = $('<div class="coursedetailview">' + $(response).find('courhtml').html() + '</div>');
      $('.coursedetailview').remove();
      v_oObj.insertAfter('.dayview');
      if (typeof(CallBack) == 'function')
        CallBack(response);
    }
  );
}

function SelectCourseCalendByPK(courpk)
{
  if (parseInt(courpk))
  {
    //console.log($(CalendarDataXML).find('course[pk=' + courpk + ']').closest('day').attr('date'));
    var date = $(CalendarDataXML).find('course[pk=' + courpk + ']').closest('day').attr('date');
    if (date.length > 0)
    {
      DaySelect($('#datepicker'), date, function(){
        SelectCourse(courpk);
      });
    }
  } 
}

/*
 * Funkce si postupne zazada o oktualni data ze serveru a aplikuje zmeny
 * Vse probiha asynchronne
 */
function UpdateActStatus()
{
  if ($('#datepicker').length > 0)
  {
    var 
      v_oDatepicker = $('#datepicker'),
      v_dtDate = v_oDatepicker.datepicker('getDate'),
      v_sDateString = DateToStr(v_dtDate),
      v_iYear = v_dtDate.getFullYear(),
      v_iMonth = v_dtDate.getMonth(),
      v_sSelectedPK = '0';

    if ($('.daycourses .course.selected').length > 0)
      v_sSelectedPK = $('.daycourses .course.selected').attr('pk');

    RequestCalendarhData(true, new Date(v_iYear, v_iMonth - 2, 1), new Date(v_iYear, v_iMonth + 1, 1), function(){
      DaySelect(v_oDatepicker, v_sDateString, function(){
        LoadCoursesonDay(v_sDateString, true);
        if ($('.daycourses .course[pk="' + v_sSelectedPK + '"]').length > 0)
          $('.daycourses .course[pk="' + v_sSelectedPK + '"]').addClass('selected');
      });
    });
  }
}
