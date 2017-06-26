// -------------------------- UDALOSTI ---------------------------------

/**
 * @brief Zazada server o vytvoreni udalosti a vysledny stav nove 
 * vytvoreneho objetu preda funkci ProcessEventState()
 * 
 * @param string date - datum na kterem se ma udalost vytvorit
 */
function CreateEvent(date)
{
  console.log("CreateEvent(" + DateToStr(date) + ")");
  SendAjaxRequest(
    "type=CreateEvent"+ 
    "&date=" + DateToStr(date),  
    true,
    function(response){
      ProcessEventState(response);
    }
  );    
}

/**
 * @brief Zazada server o otevreni existujici udalosti podle jejiho primarniho klice
 * xmlstav otevreneho objetu preda funkci ProcessEventState()
 * 
 * @param string a_sPK - textova podoba primarniho klice otevirane udalosti
 */
function OpenEvent(a_sPK)
{
  console.log('OpenEvent(' + a_sPK + ')');
  SendAjaxRequest(
    "type=OpenEvent"+ 
    "&pk=" + a_sPK,  
    true,
    function(response){
      ProcessEventState(response);
    }
  );
}

/**
 * @brief Zazada server o zavreni udalosti a vycisti obsah
 */
function CloseEvent()
{
  console.log("CloseEvent()");
  SendAjaxRequest(
    "type=CloseEvent",  
    true,
    function(response){ 
      ClearContent();
    }
  );    
}

/**
 * @brief Zazada server o zniceni udalosti a vycisti obsah, pokud se mazani nepovedlo, 
 * preda odpoved funkci ProcessEventState()
 */
function DeleteEvent()
{
  console.log("DeleteEvent()");
  SendAjaxRequest(
    "type=DeleteEvent",  
    true,
    function(response){ 
      if ($(response).find('error').length > 0)
      {
        ProcessEventState(response);
      }
      else
      {
        ClearContent();
        ReloadData();
      }
    }
  );    
}

/**
 * @brief Zazada server o zpracovani dotazu urceneho pro udalost a
 * preda odpoved funkci ProcessEventState()
 * @param string a_sType - typ udalosti
 * @param string a_sData - dalsi data (vyssich urovni)
 */
function EventAjax(a_sType, a_sData)
{
  console.log("EventAjax("  + a_sType + ", "  + a_sData + "}");
  if (a_sData !== '')
    a_sData = '&' + a_sData;
  SendAjaxRequest(
    "type=EventAjax"+ 
    "&EventAjaxType=" + a_sType + 
    '&'+ a_sData,  
    true,
    function(response){
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
  if ($(a_sEventSatate).find('object_response').attr('reload') === '1')
  {
    var v_sEventSatate = $('<p>').append($(a_sEventSatate).find('object_response').attr('reload', '0')).html();
    ReloadData(function(){
      ProcessEventState(v_sEventSatate);
    }); 
    return;
  }
  
  $(a_sEventSatate).find('actions action').each(function(){
    var v_sAction = $(this).text();
    if (v_sAction === 'Close')
    {
      CloseEvent();
    }
    else if (v_sAction === 'ShowHtml')
    {
      v_oHtmlObj = ShowHTML($(a_sEventSatate).find('showhtml').html());
      $(a_sEventSatate).find('invaliddata > input').each(function(){
        HighlightInvalInput($(this).attr('name'), $(this).attr('message'), v_oHtmlObj);
      });
      SelectFromOverview($(a_sEventSatate).find('primary_key').text());
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
  
  $(a_sEventSatate).find('registration').each(function(){
    ProcessRegistrationState(v_oHtmlObj, $(this).html());
  });
  
}

/**
 * Prida na stranku html do '.adm-day-conn'
 * @param string} a_sHTML - html, ktere bude pridano na stranku
 * @returns {jQuery} - objekt, ktery byl pridan, null pokud a_sHTML neni definovano
 */
function ShowHTML(a_sHTML)
{
  console.log('ShowHTML()');
  ClearContent();
  if (a_sHTML)
  {
    return $(a_sHTML).appendTo('.adm-day-conn');  
  }
  else 
    return null;
}

/**
 * specifikuje obsluzne metody specialni pro potreby formulare nove udalosti
 * 
 * @param {jQuery} a_oHtmlObj - ovekt otevrene udalosti nad kterym se budou vytvaret ubsluhy
 */
function InitNewForm(a_oHtmlObj)
{
  InitBaseAjaxSubmit(a_oHtmlObj)
}

/**
 * specifikuje obsluzne metody specialni pro potreby formulare upravy udalosti
 * 
 * @param {jQuery} a_oHtmlObj - ovekt otevrene udalosti nad kterym se budou vytvaret ubsluhy
 */
function InitEditForm(a_oHtmlObj)
{
  InitBaseAjaxSubmit(a_oHtmlObj)
}

/**
 * specifikuje obsluzne metody specialni pro potreby prehledu udalosti
 * 
 * @param {jQuery} a_oHtmlObj - ovekt otevrene udalosti nad kterym se budou vytvaret ubsluhy
 */
function InitOverViewActions(a_oHtmlObj)
{
  InitBaseAjaxSubmit(a_oHtmlObj);
}

/**
 * Specifikuje obsluzne metody vsech objektu s klikaci udalosti (tridy ".ajaxsubmit")
 * 
 * @param {jQuery} a_oHtmlObj - ovekt otevrene udalosti nad kterym se budou vytvaret ubsluhy
 */
function InitBaseAjaxSubmit(a_oHtmlObj)
{
  a_oHtmlObj.on('click', '.ajaxsubmit', function(event){
    event.stopPropagation();
    event.preventDefault();
    if ($(this).attr('ajaxtype') === 'delete')
      DeleteEvent();
    else
    {
      var form = $(this).closest('form');
      var data = $(this).attr('data');
      EventAjax(
        $(this).attr('ajaxtype'),
        ((form) ? form.serialize() : '') + 
        ((data) ? '&' + data : ''),
        function(resp){
          ProcessEventState(resp);
        }
      );
    }
  });
}

/**
 * @brief zvirazni udalosti, s prislusnym pk
 * @param string a_sPk - pk otevrene udalosti
 */
function SelectFromOverview(a_sPk)
{
  $('.adm-dayevents-view .event').removeClass('selected');
  $('.adm-dayevents-view .event[pk=' + a_sPk + ']').addClass('selected');
}
