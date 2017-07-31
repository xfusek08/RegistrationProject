
var ResponsivePage = function(a_oParent, a_sPageResponse)
{
  this.i_oParent = a_oParent;
  this.i_oHmltObj = null;
  
  this.ProcessPageResponse = function(a_sPageResponse)
  {
    var v_oPageResponse = $('<elem>' + a_sPageResponse + '</elem>').find('page_response');
    var self = this;
    if (v_oPageResponse.attr('reload') === '1')
    {
      v_oPageResponse.attr('reload', '0');
      ReloadData(function(){
        self.ProcessPageResponse();
      }); 
      return;
    }
    v_oPageResponse.find('> actions action').each(function(){
      switch ($(this).text())
      {
        case 'Close': self.Close(); break;
        case 'ShowHtml': 
          self.ShowHTML(v_oPageResponse.find('showhtml').html()); 
          v_oPageResponse.find('> invaliddata > input').each(function(){
            HighlightInvalInput($(this).attr('name'), $(this).attr('message'), self.i_oHTMLObj);
          });
          break;        
      }
    });    
  }
  
  this.ShowHTML = function(a_sHtml)
  {
    var self = this;
    if (ClearContent())
    {
      this.i_oHTMLObj = $(a_sHtml).appendTo(this.i_oParent);   
      this.InitBaseAjaxSubmit();
    }
  }
  
  this.Close = function()
  {
    var self = this;
    if (ClearContent())
      this.SendAjax('Close', '', function(){ self = undefined; });
  }
  
  this.SendAjax = function(a_sType, a_sData, CallBack)
  {
    console.log("ResponsivePage.SendAjax("  + a_sType + ", "  + a_sData + "}");
    var self = this;
    SendAjaxRequest(
      "type=RespPageAjax"+ 
      "&RespPageAjaxType=" + a_sType + 
      ((a_sData !== "") ? a_sData : ''),
      true,
      function(response){
        self.ProcessPageResponse(response);
        if (typeof(CallBack) === 'function')
          CallBack();
      }
    );
  }
  this.InitBaseAjaxSubmit = function(){
    var self = this;
    this.i_oHTMLObj.on('click', '.ajaxsubmit', function(event){
      event.stopPropagation();
      event.preventDefault();
      var form = $(this).closest('form');
      var data = $(this).attr('data');
      self.SendAjax(
        $(this).attr('ajaxtype'),
        ((form) ? '&' + form.serialize() : '') + 
        ((data) ? '&' + data : ''));      
    });
  }
  this.ProcessPageResponse(a_sPageResponse);
}
