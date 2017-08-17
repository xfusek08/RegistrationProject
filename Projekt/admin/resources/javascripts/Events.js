
/**
 * @brief Zazada server o vytvoreni udalosti a na zaklade odpovedi vytvori objekt Event
 * a jeho html nechá vykreslit do predaneho onjektu
 * 
 * @param string v_sDate - datum na kterem se ma udalost vytvorit
 * @param jQobj v_oObjConn - objekt do ktereho vlozit Udalost
 */
function CreateEvent(v_sDate, v_oObjConn)
{
  console.log("CreateEvent(" + DateToStr(v_sDate) + ")");
  SendAjaxRequest(
    "type=CreateEvent"+ 
    "&date=" + DateToStr(v_sDate),  
    true,
    function(response){
      if (ClearContent())
      {
        var v_oEvent = new Event($('.adm-day-conn'), $(response).find('> object_response'));
        v_oEvent.ProcessState();
      }
    }
  );    
}

/**
 * @brief Zazada server o otevreni existujici udalosti podle jejiho primarniho klice
 * a na zaklade odpovedi vytvori objekt Event a jeho html nechá vykreslit do predaneho onjektu
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
      if (ClearContent())
      {
        var v_oEvent = new Event($('.adm-day-conn'), $(response).find('> object_response'));
        v_oEvent.ProcessState();
      }
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
        var v_oEvent = new Event($('.adm-day-conn'), $(response).find('> object_response'));
        v_oEvent.ProcessState();
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
 * @brief Zazada server o zavreni udalosti a vycisti obsah
 */
function CloseEvent()
{
  console.log("CloseEvent()");
  if (ClearContent())
  {
    SendAjaxRequest(
      "type=CloseEvent",  
      true,
      function(response){}
    );
  }
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

/**
 * Objekt, ktery predstavuje udalost
 * 
 * @param jQobj v_oParent - objekt do ktereho se udalost vlozi
 * @param jQobj v_oObjectResponse - objekt ktery odpovida vygenerovanemu xml ze serveru
 */
var Event = function(v_oParent, v_oObjectResponse){
  this.i_oObjResponse = v_oObjectResponse;
  this.i_oParent = v_oParent;
  this.i_oHTMLObj = null;
  this.i_sPK = '';
  this.i_aRegistrations = [];
  
  this.ProcessState = function(){
    console.log("Event.ProcessState()");
    var self = this;
    this.i_aRegistrations = [];
    this.i_sPK = this.i_oObjResponse.find('> primary_key').text();

    if (this.i_oObjResponse.attr('reload') === '1')
    {
      this.i_oObjResponse.attr('reload', '0');
      ReloadData(function(){
        self.ProcessState();
      }); 
      return;
    }

    this.i_oObjResponse.find('> actions action').each(function(){
      switch ($(this).text())
      {
        case 'Close': self.Close(); break;
        case 'ShowHtml': self.ShowHTML(); break;
        case 'CantAddNew':  
          CreateConfirm(
            self.i_oHTMLObj.find('.registarationcaption'), 
            'warning', 
            'Není možné přidávat další registrace, kapacita byla naplněna.', 
            [{submit: false, message: 'ok'}]
          );
          break;
        case 'InitNewForm':
        case 'InitEditForm':
        case 'InitOverViewActions': self.InitBaseAjaxSubmit(); break;
      }
    });
    
    self.i_oObjResponse.find('> registrations > registration').each(function(){
      self.AddRegistration($(this).find('> object_response'));
    });
    
    this.ProcessRegistrations();
  };

  this.ShowHTML = function(){    
    if (this.i_oHTMLObj !== null)
      this.i_oHTMLObj.remove();
    var self = this;
    this.i_oHTMLObj = $(this.i_oObjResponse.find('> showhtml').html()).appendTo(this.i_oParent);    
    this.i_oObjResponse.find('> invaliddata > input').each(function(){
      HighlightInvalInput($(this).attr('name'), $(this).attr('message'), self.i_oHTMLObj);
    });
    SelectFromOverview(this.i_sPK);
  };
  
  this.SendAjax = function(a_sType, a_sData){
    console.log("Event.SendAjax("  + a_sType + ", "  + a_sData + "}");
    var self = this;
    SendAjaxRequest(
      "type=EventAjax"+ 
      "&EventAjaxType=" + a_sType + 
      ((a_sData !== "") ? '&' + a_sData : ''),
      true,
      function(response){
        self.i_oObjResponse = $(response).find('> object_response');
        self.ProcessState();        
      }
    );
  };
  
  this.Close = function(){
    CloseEvent();
  };
  
  this.InitBaseAjaxSubmit = function(){
    var self = this;
    this.i_oHTMLObj.on('click', '.ajaxsubmit', function(event){
      event.stopPropagation();
      event.preventDefault();
      if ($(this).attr('ajaxtype') === 'delete')
        CreateDeleteConfirm(self.i_oHTMLObj.find('.caption').eq(0), 'Opravdu si přejete vymazat událost?', DeleteEvent);        
      else
      {
        var form = self.i_oHTMLObj.find('form');
        var data = $(this).attr('data');
        self.SendAjax(
          $(this).attr('ajaxtype'),
          ((form) ? '&' + form.serialize() : '') + 
          ((data) ? '&' + data : ''));
      }
    });
  };
  
  // -------------------------------- REGISTRACE -------------------------------
  this.DeleteRegistration = function(a_sPK){
    for (var i = 0; i < this.i_aRegistrations.length; i++)
    {
      if (this.i_aRegistrations[i].i_sPK === a_sPK)
      {
        var self = this;
        var v_oRes = this.i_aRegistrations[i];
        v_oRes.InitDeleteForm(function(){
          v_oRes.i_oHTMLObj.slideUp(200, function(){
            self.SendAjax('deletegistration','RegistrationPK=' + v_oRes.i_sPK);
          });
        });
        break;
      }
    }
  };
  
  this.AddRegistration = function(v_oObjectResponse){
    var v_oReg = new Registration(this, v_oObjectResponse);
    this.i_aRegistrations.push(v_oReg);
  };
  
  this.ProcessRegistrations = function(){
    for (var i = 0; i < this.i_aRegistrations.length; i++)
    {
      var v_oReg = this.i_aRegistrations[i];
      v_oReg.ProcessState();
    }    
  };
};
