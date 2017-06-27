
var Registration = function(a_oEventHTMLObj, a_sStateXML)
{
  this.i_oHTMLobj = null;
  this.i_oParent = null;
  this.i_sPK = '';
  
  this.Create = function(a_oEventHTMLObj, a_sStateXML){
    console.log('Registration.Create()');
    this.i_oHTMLobj = null;
    this.i_oParent = a_oEventHTMLObj;
    this.i_sPK = $(a_sStateXML).find('primary_key').text();
    var self = this;

    $(a_sStateXML).find('actions action').each(function(){
      switch ($(this).text())
      {
        case 'Close': self.Close(); break;
        case 'ShowHtml': 
          self.ShowHTML($(a_sStateXML).find('showhtml').html());
          $(a_sStateXML).find('invaliddata > input').each(function(){
            HighlightInvalInput($(this).attr('name'), $(this).attr('message'), self.i_oHTMLobj);
          });
          break;
        case 'InitNewForm':
        case 'InitEditForm':
        case 'InitOverViewActions':
          if (self.i_oHTMLobj !== null)
            self.InitBaseAjaxSubmit();
          break;
      }
    });
  };

  this.ShowHTML = function(a_sHTML){
    console.log('Registration.ShowHTML()');
    this.i_oHTMLobj = null;
    if (a_sHTML)
      this.i_oHTMLobj = $(a_sHTML).appendTo(this.i_oParent);  
  };
  
  this.InitBaseAjaxSubmit = function(){
    var self = this;
    this.i_oHTMLobj.on('click', '.ajaxsubmit', function(event){
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
          '&RegistrationPK=' + self.i_sPK + 
          ((form) ? '&' + form.serialize() : '') + 
          ((data) ? '&' + data : ''),
          function(resp){
            ProcessEventState(resp);
          }
        );
      }
    });
  };

  this.Close = function(){};
  
  // zavolame konstruktor
  this.Create(a_oEventHTMLObj, a_sStateXML);
};
