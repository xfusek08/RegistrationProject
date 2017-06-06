$(document).ready(function(){
  // inicializace kalendare
  CalendarInit($('#datepicker'), LoadDayEvents);
  
});

function LoadDayEvents(datepicker, date, CallBack)
{
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
