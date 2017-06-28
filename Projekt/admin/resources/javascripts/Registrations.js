
var Registration = function(v_oParentEvent, v_oObjectResponse)
{
  this.i_oObjResponse = v_oObjectResponse;
  this.i_oParentEvent = v_oParentEvent;
  this.i_oHTMLObj = null;
  this.i_sPK = '';
  
  this.ProcessState = function(){
    console.log('Registration.ProcessState()');
    this.i_oHTMLObj = null;
    var self = this;
    
    self.i_sPK = self.i_oObjResponse.find('primary_key').text();

    this.i_oObjResponse.find('> actions action').each(function(){
      switch ($(this).text())
      {
        case 'Close': self.Close(); break;
        case 'ShowHtml': self.ShowHTML(); break;
        case 'InitNewForm': 
        case 'InitEditForm': 
        case 'InitOverViewActions': self.InitBaseAjaxSubmit(); break;
      }
    });
  };

  this.ShowHTML = function(){
    console.log('Registration.ShowHTML()');
    var self = this;
    if (this.i_oParentEvent.i_oHTMLObj)
    {
      this.i_oHTMLObj = $(this.i_oObjResponse.find('> showhtml').html()).appendTo(
        this.i_oParentEvent.i_oHTMLObj.find('.registrationconn'));    
      this.i_oObjResponse.find('> invaliddata > input').each(function(){
        HighlightInvalInput($(this).attr('name'), $(this).attr('message'), self.i_oHTMLObj);
      });
      this.i_oHTMLObj.attr('pk', this.i_sPK);      
    }
  };
  
  this.InitBaseAjaxSubmit = function(){
    var self = this;
    this.i_oHTMLObj.on('click', '.ajaxsubmit', function(event){
      event.stopPropagation();
      event.preventDefault();
      if ($(this).attr('ajaxtype') === 'delete')
        self.i_oParentEvent.DeleteRegistration(self.i_sPK);
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

  this.Close = function(){
    if (this.i_oHTMLObj !== null)
      this.i_oHTMLObj.remove();
  };
  
  this.SendAjax = function(a_sType, a_sData){
    console.log("Registration.SendAjax("  + a_sType + ", "  + a_sData + "}");
    this.i_oParentEvent.SendAjax(
      "RegistrationAjax", 
      "&RegistrationAxajType=" + a_sType + 
      "&RegistrationPK=" + this.i_sPK + 
      ((a_sData !== "") ? '&' + a_sData : ''));    
  };
};
