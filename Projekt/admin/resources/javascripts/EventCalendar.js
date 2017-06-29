
// xml, ktere doruci server o rozmisteni jednotlivych udalosti, strukturovanych do mezicu a dni
var CalendarDataXML = ''; 

function CalendarInit(calendar, DateSelectFunc, MonthSelectFunc)
{
  // nastaveni date picker
  $.datepicker.regional['cs'] = {
    closeText: 'Cerrar',
    prevText: '<',
    nextText: '>',
    currentText: 'Hoy',
    monthNames: ['Leden', 'Únor', 'Březen', 'Duben', 'Květen', 'Červen', 'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec'],
    monthNamesShort: ['Leden', 'Únor', 'Březen', 'Duben', 'Květen', 'Červen', 'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec'],
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
    numberOfMonths: 1,
    changeMonth: true,
    changeYear: true,
    showButtonPanel: false
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
    afterShow: function(input, inst, td){
      // tlacitko pro vraceni se na dnesni datum
      $(this).find('.ui-datepicker-title').append('<button class="today">Dnes</button>');
      AfterLoadDays($(this), CalendarDataXML);      
    },
    onSelect: function(date){ 
      if (typeof(DateSelectFunc) === 'function')
        DateSelectFunc($(this), date);      
    },
    onChangeMonthYear: function(year, month){ 
      // zmenil se mesic ... aktualizace dat
      RequestCalendarhData(false, new Date(year, month - 2, 1), new Date(year, month + 1, 1), function(){
        if (typeof(MonthSelectFunc) === 'function')
          MonthSelectFunc(year, month);
      });
    }
  }).on('click', 'button.today', function(){
    DateSelectFunc(calendar, DateToStr(new Date()));
  });  
}

/**
 * Projde kalendar den po dni a podle ulozenych dat budou prirazeny statistiky ke kazdemu dni
 * @param JQobject calendar - objekt kalendare
 * @param string calendarData - data kalendare
 * @returns {undefined}
 */
function AfterLoadDays(calendar, calendarData)
{
  console.log('AfterLoadDays()');
  calendar.find("table td").each(function(){
    var day = parseInt($(this).find("*").text());
    var month = parseInt($(this).attr('data-month'));
    var year = parseInt($(this).attr('data-year'));
    var date = new Date(year, month, day);
    
    var dayelem = $(calendarData).find('day[date="' + DateToStr(date) + '"]');
    var html =
      '<div class="ondaydata">';

    if (dayelem.length > 0)
    {
      var openEventCount = parseInt(dayelem.find('event[state="open"]').size());
      var hiddentEventCount = parseInt(dayelem.find('event[state="hidden"]').size());
      var FullEventCount = parseInt(dayelem.find('event[state="full"]').size());
      
      if (openEventCount > 0)
        html = html + '<div class="open-event-color">' + openEventCount + '</div>';
      
      if (FullEventCount > 0)
        html = html + '<div class="full-event-color">' + FullEventCount + '</div>';

      if (hiddentEventCount > 0)
        html = html + '<div class="hidden-event-color">' + hiddentEventCount + '</div>';
      
    }
    
    html = html + '</div>';    
    
    $(html).appendTo($(this));    
  });
}

/**
 * Posle dotaz na server o rozmisteni udalosti v casovem rozsahu
 * @param bool asynch - bude dotaz asynchronni
 * @param Date fromdate - datum od ktereho se budou data vybirat
 * @param Date todate - datum do ktereho se budou data vybirat
 * @param function CallBack - funkce, ktera se provede po uspesnem nacteni dat, pokud NULL tak se nic neprovede
 * @returns {undefined}
 */
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
