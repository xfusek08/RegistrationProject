
function CalendarInit(calendar)
{
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
  
  calendar.datepicker({
    onSelect: function(date){ 
      DateSelect($(this), date);
    },
    afterShow: function(input, inst, td){
      AfterLoadDays($(this));
      $(this).find('.ui-datepicker-title').append('<button class="today">Dnes</button>');
    },
    onChangeMonthYear: function(year, month){ 
      var dtpic = $(this).datepicker('getDate');
      LoadMonthData(true, new Date(year, month - 2, 1), new Date(year, month, 1), function(){
        
       
      });
    }
  }).on('click', 'button.today', function(){
    DateSelect(calendar, DateToStr(new Date()));
  });
}
function AfterLoadDays(calendar)
{
  console.log('AfterLoadDays()');
}
function DateSelect(datepic, date, CallBack)
{ 
  console.log('DateSelect()');
}
/**
 * Posle dotaz na server o rozmisteni udalosti v casovem rozsahu
 * @param bool asynch - bude dotaz asynchronni
 * @param Date fromdate - datum od ktereho se budou data vybirat
 * @param Date todate - datum do ktereho se budou data vybirat
 * @param function CallBack - funkce, ktera se provede po uspesnem nacteni dat, pokud NULL tak se nic neprovede
 * @returns {undefined}
 */
function LoadMonthData(asynch, fromdate, todate, CallBack)
{
  console.log('LoadMonthData()');
}
