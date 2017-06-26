
function ProcessRegistrationState(a_oEventHTMLObj, a_sRegistrtationStateXML)
{
  console.log('ProcessRegistrationState()');
  var v_oHtmlObj = null;
  $(a_sRegistrtationStateXML).find('actions action').each(function(){
    var v_sAction = $(this).text();
    switch (v_sAction)
    {
      case 'Close':
        CloseEvent();
        break;
      case 'ShowHtml':
        v_oHtmlObj = ShowRegistrationHTML(a_oEventHTMLObj, $(a_sRegistrtationStateXML).find('showhtml').html());
        $(a_sRegistrtationStateXML).find('invaliddata > input').each(function(){
          HighlightInvalInput($(this).attr('name'), $(this).attr('message'), v_oHtmlObj);
        });
        break;
      case 'InitNewForm':
      case 'InitEditForm':
      case 'InitOverViewActions':
        if (v_oHtmlObj !== null)
          InitBaseAjaxSubmitReg(v_oHtmlObj);
        break;
    }
  });
}

function ShowRegistrationHTML(a_oEventHTMLObj, a_sHTML)
{
  console.log('ShowRegistrationHTML(' + a_sHTML + ')');
  if (a_sHTML)
  {
    return $(a_sHTML).appendTo(a_oEventHTMLObj.find('.registrationconn'));  
  }
  else 
    return null;
}

function InitBaseAjaxSubmitReg(a_oHtmlObj)
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
        'RegistrationAjax',
        'RegistrationAxajType=' + $(this).attr('ajaxtype') + 
        ((form) ? '&' + form.serialize() : '') + 
        ((data) ? '&' + data : ''),
        function(resp){
          ProcessEventState(resp);
        }
      );
    }
  });
}