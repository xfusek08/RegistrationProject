
/*
 * inicializace pri nacteni dokumentu
 *  • inicializace hlavniho kalendare
 *  • vytvoreni obsluznych udalosti pro klikani do prave nabidky
 *    - Nova udalost
 *    - obecna obsluha vsech timepickeru
 *    - vybrani existujici udalosti
 */
$(document).ready(function(){
  // inicializace kalendare
  CalendarInit($('#datepicker'), DaySelect);
  
  $("body").on("click", ".adm-dayevents-tools-newbt", function(e){
    if (ClearContent(e))
      CreateEvent($("#datepicker").datepicker('getDate'));      
  });
  $("body").on("click", ".seltimebt", function(e){
    var timeinput = $(this).parent(".timeinput");
    var bt = $(this);
    e.preventDefault();        
    if (!bt.is(".wait"))
    {
      timeinput.find("input[type=text]").timepicker({
        timeFormat: 'H:i',
        scrollDefault: 'now'
      }).on('hideTimepicker', function(){
        $(this).timepicker('remove');
        bt.addClass("wait");
        setTimeout(function(){
          bt.removeClass("wait");  
        },400);
      }).on('showTimepicker', function(){
        $(".ui-timepicker-wrapper").css({
          left: $(this).position().left - 4,
        });            
      }).timepicker('show');
    }
  });
  $("body").on("click", ".adm-dayevents-view .event:not(.selected)", function(e){
    if (ClearContent(e))
      OpenEvent($(this).attr('pk'));
  });
  $("body").on("click", ".ajaxsubmit", function(e){
    ProcessGeneralAjaxSubmit($(this), e);
  });
  GetNavigation();
});

/**
 * @brief Funkce pro vyprani konkretniho datumu
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
  ClearContent();
  datepicker.datepicker('setDate', v_sDateString);
  $('.daydate').text(v_sDateString);
  LoadEventsOnDay(v_sDateString, v_fnCallBack);  
}

/**
 * @brief Zazada server o udalosti na konkretim datu a vykresli je do konteineru udalosti
 * @param string datestring   - datum na ktere potrebujeme dostat udalosti
 * @param {type} CallBack     - funkce, ktera se zavola po dokonceni vsech kroku
 */
function LoadEventsOnDay(datestring, CallBack)
{
  console.log('LoadEventsOnDay()');
  SendAjaxRequest(
    "type=SelectDay"+ 
    "&date=" + datestring,
    true, 
    function(xml)
    { 
      $('.adm-dayevents-view').empty();     
      var html = '';

      if (!$(xml).find("event_html"))
      {
        html = '<div class="adm-dayevents-view-nodata">Žádná data</div>';
        $(html).appendTo('.adm-dayevents-view');
      }
      else
      {    
        $(xml).find("event_html").each(function(){
          html = 
            '<div class="event" pk="' + $(this).attr('pk') + '" state="' + $(this).attr('state') + '">' + 
              $(this).html() +
            '</div>';
          $(html).appendTo('.adm-dayevents-view');
        });  
      } 
      
      //CreateDroppables();
      if (CallBack && typeof(CallBack) == "function")
      {
        CallBack();
      }
    }
  );          
}

/*
 * @brief Vymaze obsah v ".adm-day-conn" konteineru
 * 
 * Pokud ".adm-day-conn" ma tridu ".checkbeforeclose" tak se pred zavrenim objevi dialogove okno
 * @param {type} event
 * @returns {Boolean}
 */
function ClearContent(event)
{
  console.log("ClearContent()");
  var success = true; 
  if ($(".adm-day-conn .checkbeforeclose").length > 0)
  {
    // nahradit vlastnim dialog. oknem
    success = confirm("Přejete si opravdu opustit neuložený formulář ?");
  }
  
  if (success)
  {
    $(".adm-day-conn").empty();   
    SelectFromOverview('0');
  }
  else
  {
    if (event)
    {
      event.stopPropagation();
    }
  }
  
  return success;
}

/**
 * @brief Zazada server o aktualni data k navigaci a nasledne podle odpovedi zmeni navigaci na strance
 * 
 *  - presune se na predany den
 *  - vykresli otevrenou udalost
 */
function GetNavigation()
{
  console.log("GetNavigation()");
  SendAjaxRequest(
    "type=GetNavigation",  
    true,
    function(response){
      var v_sActDate = $(response).find('actday').text();
      DaySelect($('#datepicker'), v_sActDate, function(){   
        var v_oEvent = $(response).find('openevent > object_response');
        var v_oPage = $(response).find('openpage');
        if (v_oEvent.length > 0)
        {
          v_oEvent = new Event($('.adm-day-conn'), $(response).find('openevent > object_response'));
          v_oEvent.ProcessState();
        }
        else if (v_oPage.length > 0)
          v_oPage = new ResponsivePage($('.adm-day-conn'), v_oPage.html());        
      });      
    }
  );   
}

/**
 * @brief zepta se serveru na nova data k aktualnimu dni a obnovy data ukazovana v kalendari
 * @param function CallBack - funkce ktera se provede az bude hotovo
 */
function ReloadData(CallBack)
{
  console.log('RealoadData()');
  var nowdate = $('#datepicker').datepicker('getDate');
  var year = nowdate.getFullYear();
  var month = nowdate.getMonth();
  RequestCalendarhData(true, new Date(year, month - 1, 1), new Date(year, month+1, 1), function(){
    DaySelect($('#datepicker'), DateToStr(nowdate), CallBack);
  });
}

function ProcessGeneralAjaxSubmit(actionButton, event, CallBack)
{
  console.log('ProcessGeneralAjaxSubmit()');
  event.stopPropagation();
  event.preventDefault();

   var form = actionButton.closest('form');
   var data = actionButton.attr('data');
   SendAjaxRequest(
      "type=" + actionButton.attr('ajaxtype') + 
      ((form) ? '&' + form.serialize() : '') + 
      ((data) ? '&' + data : ''),
      true,
      function(response){
        $(response).find('general_response').find('actions action').each(function(){
          var v_sAction = $(this).text();
          switch (v_sAction)
          {
            case 'NewResponsivePage':
              var v_oResponsivePage = new ResponsivePage(
                $('.adm-day-conn'), 
                $(response).find('general_response pagedata').html()
              );
              break;
            case 'NewResponsiveObject':
              // tady mozna inicializovat kliknuti na udalost
              // rozhodnout ktery se ma vytvorit
              break;
          }
        });
        
      }
  );  
}