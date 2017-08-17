var v_aOpenRegStatus = [];

var Registration = function(v_oParentEvent, v_oObjectResponse)
{
  this.i_oObjResponse = v_oObjectResponse;
  this.i_oParentEvent = v_oParentEvent;
  this.i_oHTMLObj = null;
  this.i_sPK = '';
  this.i_bClosed = true;
  this.i_bIsNew = false;
  
  this.ProcessState = function(){
    //console.log('Registration.ProcessState()');
    this.i_oHTMLObj = null;
    var self = this;
    
    self.i_sPK = self.i_oObjResponse.find('primary_key').text();
    self.i_bClosed = self.i_oObjResponse.find('isopendetail').text() === '0';
    
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
    //console.log('Registration.ShowHTML()');
    var self = this;
    if (this.i_oParentEvent.i_oHTMLObj)
    {
      this.i_oHTMLObj = $(this.i_oObjResponse.find('> showhtml').html()).appendTo(
        this.i_oParentEvent.i_oHTMLObj.find('.registrationconn'));    
      this.i_oObjResponse.find('> invaliddata > input').each(function(){
        HighlightInvalInput($(this).attr('name'), $(this).attr('message'), self.i_oHTMLObj);
      });
      this.i_oHTMLObj.attr('pk', this.i_sPK);  
      if (this.i_oHTMLObj.attr('isnew') == '1')
      {
        this.i_bIsNew = true;
        this.i_oHTMLObj.find('.header').append('<div class="newtag">nová</div>');
      }
    }
    
    if (this.i_oHTMLObj.hasClass('noclose'))
    {
      v_aOpenRegStatus[this.i_sPK] = 'open';
      return;
    }
    
    if (!this.i_bClosed)
      v_aOpenRegStatus[this.i_sPK] = 'open';

    var v_sStatus = v_aOpenRegStatus[this.i_sPK];
    if (v_sStatus === 'closed' || v_sStatus !== 'open')
    {
      this.i_bClosed = true;
      this.CloseDetail(0);
    }
    else
      this.i_bClosed = false;

    this.i_oHTMLObj.find('.header').click(function(){
      if (self.i_bClosed)
        self.OpenDetail();
      else
        self.CloseDetail();
    });
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
          ((form) ? form.serialize() : '') + 
          ((data) ? '&' + data : ''));
      }
    });
  };

  this.Close = function(){
    if (this.i_oHTMLObj !== null)
      this.i_oHTMLObj.remove();
  };
  
  this.SendAjax = function(a_sType, a_sData){
    //console.log("Registration.SendAjax("  + a_sType + ", "  + a_sData + "}");
    this.i_oParentEvent.SendAjax(
      "RegistrationAjax", 
      "RegistrationAxajType=" + a_sType + 
      "&RegistrationPK=" + this.i_sPK + 
      ((a_sData !== "") ? '&' + a_sData : ''));    
  };
  
  this.InitDeleteForm = function(CallBack){
    CreateDeleteConfirm(this.i_oHTMLObj.find('.header'), 'Opravdu si přejete vymazat registraci?', CallBack);
  };
  
  this.ReadNewRegistration = function(CallBack){
    //console.log('ReadNewRegistration()');
    var self = this;
    setTimeout(function(){
      self.i_oHTMLObj.find('.newtag').fadeOut(500, function(){
      self.SendAjax('readnew');
      });
      var v_oNewReg = $('.adm-newregconn-conn .new_registration[pk=' + self.i_sPK + ']');
      v_oNewReg.slideUp(500, function(){ 
        v_oNewReg.remove();
        $('.newregcount').text($('.adm-newregconn-conn .new_registration').size());
        if ($('.adm-newregconn-conn .new_registration[date="' + v_oNewReg.attr('date') + '"]').size() == 0)
        {
          GetCalenDayTDByDate(StrToDate(v_oNewReg.attr('date')), '.hasnew').animate({
            backgroundColor: 'rgb(255,255,255)'
          }, 300, "swing", function(){
            $(this).removeClass('hasnew');
          });
        }
      });
    }, 1000);
    if (typeof(CallBack) == "function")
      CallBack();
  };
  
  this.OpenDetail = function(a_iSpeed = 250, CallBack){
    var self = this;
    this.i_oHTMLObj.find('.content').slideDown(a_iSpeed, function(){
      self.i_bClosed = false;
      v_aOpenRegStatus[self.i_sPK] = 'open';
      if (self.i_bIsNew)
        self.ReadNewRegistration(CallBack);
      else
        if (typeof(CallBack) == "function")
          CallBack();
    });   
  };
  
  this.CloseDetail = function(a_iSpeed = 250, CallBack){
    var self = this;
    this.i_oHTMLObj.find('.content').slideUp(a_iSpeed, function(){
      self.i_bClosed = true;
      v_aOpenRegStatus[self.i_sPK] = 'closed';
      if (typeof(CallBack) == "function")
        CallBack();
    });   
  };
};
