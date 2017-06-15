$(document).ready(function(){
  // inicializace kalendare
  CalendarInit($('#datepicker'), LoadDayEvents);
  
  $("body").on("click", ".adm-dayevents-tools-newbt", function(e){
    if (ClearContent(e))
      CreateEvent($("#datepicker").datepicker('getDate'));      
  });
  $("body").on("click", ".seltimebt", function(e){
    var timeinput = $(this).parent(".timeinput");
    CreateOnClickTimepicker(e, timeinput, $(this));
  });
});

function LoadDayEvents(datepicker, date, CallBack)
{
  ClearContent();
  console.log('LoadDayEvents()');
  SendAjaxRequest(
    "type=GetDetailEventsOnDay"+ 
    "&date=" + date,
    true, 
    function(xml)
    {
      console.log(xml);      
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
          html = $(this).html();
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

function CreateOnClickTimepicker(e, timeinput, bt)
{
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
}

// -------------------------- UDALOSTI ---------------------------------
function CreateEvent(date)
{
  console.log("CreateEvent(" + DateToStr(date) + ")");
  SendAjaxRequest(
    "type=CreateEvent"+ 
    "&date=" + DateToStr(date),  
    true,
    function(response){
      console.log(response); 
      ProcessEventState(response);
    }
  );    
}
function CloseEvent()
{
  console.log("CloseEvent(}");
  SendAjaxRequest(
    "type=CloseEvent"+ 
    "&date=" + DateToStr(date),  
    true,
    function(response){
      console.log(response); 
      ClearContent();
    }
  );    
}
function EventAjax(a_sType, a_sData)
{
  console.log("EventAjax("  + a_sType + ", "  + a_sData + "}");
  SendAjaxRequest(
    "type=EventAjax"+ 
    "&EventAjaxType=" + a_sType + 
    "&" + a_sData,  
    true,
    function(response){
      console.log(response); 
      ProcessEventState(response)
    }
  );    
}
/**
 * Spracuje xml odpoved objektu event ze serveru
 * 
 * @param xml response - odpoved udalosti ze serveru
 * @returns {undefined}
 */
function ProcessEventState(a_sEventSatate)
{
  console.log("ProcessEventState()");
  var v_oHtmlObj = null;
  
  $(a_sEventSatate).find('actions action').each(function(){
    var v_sAction = $(this).text();
    if (v_sAction === 'Close')
    {
      CloseEvent();
    }
    else if (v_sAction === 'ShowHtml')
    {
      v_oHtmlObj = ShowHTML($(a_sEventSatate).find('showhtml'));
    }
    else if (v_sAction === 'InitNewForm')
    {
      if (v_oHtmlObj !== null)
        InitNewForm(v_oHtmlObj);
    }
    else if (v_sAction === 'InitEditForm')
    {
      if (v_oHtmlObj !== null)
        InitEditForm(v_oHtmlObj);
    }
    else if (v_sAction === 'InitOverViewActions')
    {
      if (v_oHtmlObj !== null)
        InitOverViewActions(v_oHtmlObj);
    }
  });
}
/**
 * Prida na stranku html do '.adm-day-conn'
 * @param string} a_sHTML - html, ktere bude pridano na stranku
 * @returns {jQuery} - objekt, ktery byl pridan, null pokud a_sHTML neni definovano
 */
function ShowHTML(a_sHTML)
{
  console.log('ShowHTML(' + a_sHTML + ')');
  if (a_sHTML)
    return $(a_sHTML).appendTo('.adm-day-conn');  
  else 
    return null;
}

/**
 * specifikuje obsluzne metody specialni pro potreby formulare nove udalosti
 * 
 * @param {jQuery} a_oHtmlObj
 */
function InitNewForm(a_oHtmlObj)
{
  console.log("InitNewEventForm()");
  
}


