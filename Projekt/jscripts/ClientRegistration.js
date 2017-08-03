$(document).ready(function ()
{ 
  drawmonth = (new Date()).getMonth();
  CalendarInit($('#datepicker'), DaySelect);
  
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
function DaySelect(datepicker, v_sDateString, v_fnCallBack)
{
  console.log('DaySelect()');
  datepicker.datepicker('setDate', v_sDateString);
  LoadCoursesonDay(v_sDateString);
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

function LoadCoursesonDay(a_sDateString)
{
  var DayView = $('.dayview');
  //DayView.css('display', 'block');
  if (DayView.attr("date") != a_sDateString)
  {

    $("input[name=c_selterm]").remove();

    DayView.attr("date", a_sDateString);
    DayView.find('.conn .header').text(
      'Lekce pro den: ' + a_sDateString + '(' + $.datepicker.formatDate('DD', StrToDate(a_sDateString)) + ')');

    var DayViewContent = DayView.find('.conn .content');
    DayViewContent.empty();
    var html = '';

    $(CalendarDataXML).find('day[date="' + a_sDateString + '"] language').each(function ()
    {
      html += '<p class="lang">' + $(this).attr('text') + ':</p>';
      $(this).find('course').each(function(){
        html += 
          '<div class="course" pk="' + $(this).attr("pk") + '" title="Vybrat pro podrobnosti">' + 
            '<div class="time">' + $(this).attr('time') + '</div>' + 
            '<div class="name">' + $(this).attr('name') + '</div>' +
          '</div>';
      });
    });
    DayViewContent.append(html);
    DayView.slideDown(200);
    DayViewContent.on('click', '.course', function ()
    {
      $('.selected').removeClass('selected');
      $(this).addClass('selected');
      SelectCourse($(this).attr('pk'));
    });
  }  
}

function RequestCalendarhData(asynch, fromdate, todate, CallBack)
{
  console.log('RequestCalendarhData(' + DateToStr(fromdate) + ', ' + DateToStr(todate) + ')');
  SendAjaxRequest(
    "type=GetCalendarData"+ 
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
  console.log('SelectCourse(' + a_sPK + ')');
  SendAjaxRequest(
    "type=SelectCourse"+ 
    "&pk=" + a_sPK,
    true, 
    function(response)
    {
      var v_bNoslide = $('.selectedcourse').length > 0;
      $('.selectedcourse').remove();
      var v_oObj = $(response).find('courhtml > div');
      if (!v_bNoslide)
        v_oObj
          .css({display: "none"})
          .appendTo('td.selcourse')
          .slideDown(100);
      else
        v_oObj.appendTo('td.selcourse');
        
      if (typeof(CallBack) == 'function')
        CallBack(response);
    }
  );
}
