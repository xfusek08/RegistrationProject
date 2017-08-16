var g_AnouncementManager;
$(document).ready(function ()
{
  $('body').on("keyup", 'input[type=text].nval', function ()
  {
    $(this).addClass("fadeoutcolor");

    setTimeout(function ()
    {
      $(this).removeClass("fadeoutcolor");
    }, 400);
  });
  g_AnouncementManager = new AnnouncementManager();  
  CheckForAlertXML('body');
});

function DateToStr(date)
{
  var d = date.getDate().toString();
  var m = (date.getMonth() + 1).toString();
  if (d.length == 1)
  {
    d = '0' + d;
  }
  if (m.length == 1)
  {
    m = '0' + m;
  }
  return d + '.' + m + '.' + date.getFullYear();
}

function StrToDate(str)
{
  var arr = str.split(".");
  var date = new Date(arr[2], arr[1] - 1, arr[0]);
  return date;
}

function SendAjaxRequest(data, asynch, callback)
{
  StartLoading();
  $.ajax({
    url: location.protocol + '//' + location.host + location.pathname,
    type: "POST",
    async: asynch,
    data: "ajax=true&" + data,
    success: function (html)
    {
      StopLoading();
      CheckForAlertXML(html);
      callback(html);
    }
  });
}
function SubmitForm(type, form, ProcFnc)
{
  SendAjaxRequest(
    "type=" + type +
    "&" + form.serialize(),
    true,
    ProcFnc
    );
}
function OnClickAjaxSubmit(event, type, button, ProcFnc)
{
  event.preventDefault();

  var
    self = button,
    form = button.closest("form"),
    tempElement = $("<input type='hidden'/>");

  tempElement
    .attr("name", button.attr('name'))
    .val(self.val())
    .appendTo(form);

  SubmitForm(type, form, ProcFnc);
}

function isOdd(num)
{
  return num % 2;
} // je liche ? 

function CheckForAlertXML(code)
{
  //console.log(code);
  $(code).find('alert').each(function(){
    g_AnouncementManager.AddAnouncement($(this).find('color').text(), $(this).find('message').text());
  });
}
// vcelku univerzalni
function RasiceComfirmForm(caption, text, CallBack)
{
  var HTML = '';
  HTML += '<div class="floatform">';
  HTML +=   '<div>';
  HTML +=     '<div class="caption">' + caption + '</div>';
  HTML +=     '<div class="text">' + text + '</div>';
  HTML +=     '<div class="buttonline">';
  HTML +=       '<button value="ok">Ok</button>';
  HTML +=       '<button value="stor">Storno</button>';
  HTML +=     '</div>';
  HTML +=     '</div>';
  HTML +=   '</div>';
  HTML += '</div>';
  
  $(HTML).appendTo('body');
  var obj = $('.floatform > div');
  CenterFloatForm(obj);
  obj.find('button[value="stor"]').focus();
  obj.on('click', 'button', function(){
    if ($(this).attr('value') === 'ok')
    {
      if(typeof CallBack === 'function')
        CallBack();
    }
    obj.parent('.floatform').remove();
  });
  obj.keydown(function(e){
    e.stopPropagation();
    if (e.keyCode === 37 && obj.find('button[value="ok"]').is(':focus')) // left arrow
      obj.find('button[value="stor"]').focus();
    else if (e.keyCode === 39 && obj.find('button[value="stor"]').is(':focus')) // right arrow
      obj.find('button[value="ok"]').focus();
    else if (e.keyCode === 13)
      obj.find('button:focus').click();
    else if (e.keyCode === 27)
      obj.find('button[value="stor"]').click();
  });
}
function CenterFloatForm(inForm)
{
  var h = $(window).height()/2  - inForm.outerHeight()/2;
  if (h < 10) h = 10;
  var w = $(window).width()/2  - inForm.outerWidth()/2;
  if (w < 10) w = 10;
  inForm.css({
    marginLeft: w,
    marginTop: h
  });
}
function AnnouncementManager()
{
  this.Anouncements = [];
  this.ToRaiseCounter = 0;
  this.ClearingInterval;  
  this.ClearingTimeout;  
  this.AddAnouncement = function(a_sColor, a_sText){
    this.Anouncements.push(new Announcement(a_sColor, a_sText));
    var elem = this.Anouncements[this.Anouncements.length - 1].element;
    elem.appendTo('body');
    this.ToRaiseCounter++;
    var me = this;
    var order = this.Anouncements.length;
    setTimeout(function(){
      elem.animate({top: '-=' + (elem.outerHeight() * order)+ 'px'}, 150, "swing", me.startClear());
      me.ToRaiseCounter--;
    }, me.ToRaiseCounter * 200);
  };  
  this.startClear = function(){
    clearInterval(this.ClearingInterval);
    clearTimeout(this.ClearingTimeout);
    var me = this;
    this.ClearingTimeout = setTimeout(function(){
      me.ClearingInterval = setInterval(function(){
        for(var i = me.Anouncements.length - 1; i >= 0; i--)
        {
          var element = me.Anouncements[i].element;
          element.animate({top: '+=' + element.outerHeight() + 'px'}, 250, "swing", function(){
            if ($(this).offset().top >= $(window).outerHeight())
            {            
              element.remove();
              me.Anouncements.shift();
              if(me.Anouncements.length == 0)
                clearInterval(me.ClearingInterval);
            }
          });
        }
      }, 1500);
    }, 3000);
  };
}
function Announcement(a_sColor, a_sText, a_iIndex)
{
  var color = "rgba(255,255,255,0.85)";  
  var textcolor = 'black';
  var todelete = false;
  switch (a_sColor.toLowerCase())
  {
    case "red":
      color = "rgba(255,100,100,0.85)";
      textcolor = 'white';
      break;
    case "green":
      color = "rgba(100,255,100, 0.85)";
      break;
    case "white": break; // nic 
    default:             // chyba
      console.log("wrong paramter AnnouncementColor");
      break;
  }
  this.element = $('<div class="announcement">' + a_sText + '</div>');
  this.element.css({
    top: $(window).outerHeight() + 'px',
    width: $(window).outerWidth() + 'px',
    background: color,
    color: textcolor
  });
  this.index = a_iIndex;
}

var loagingTimeout;
var loadingcounter = 0;
function StartLoading()
{
  if (loadingcounter == 0)
  {
    $('<div class="loading"><img src="../img/ajax-loader.gif " /></div>').appendTo('.adm-topheader');            
  }
  loadingcounter++;
}
function StopLoading()
{
  if (loadingcounter == 1)
  {
    //clearTimeout(loagingTimeout);
    $('.loading').remove();
  }
  loadingcounter--;
}
function HighlightAllInvalInputs(searchin)
{
  searchin.find('input[valid=0]').each(function(){
    HighlightInvalInput($(this).attr('name'), $(this).attr('message'), searchin);
  });
}
function HighlightInvalInput(name, mgs, searchin)
{
  if (searchin == null) searchin = $('body');
  var input = searchin.find('*[name="' + name + '"]');
  input.attr('title', mgs);
  var precolor = input.css('backgroundColor');
  
  input.animate({ 'backgroundColor': "rgb(255,150,150)", color: "white"}, 600, function(){
    input.focusin(function(){
      UnHighlightInput(input, precolor);
    });
    input.change(function(){
      UnHighlightInput(input, precolor);
    });
  });
  // TODO: otaznicek, co je spatne...
}
function UnHighlightInput(input, precolor)
{
  input.animate({ 'backgroundColor': precolor, color: "black"}, 600, function(){
    input.removeAttr('title');
  });
}
function in_array(a_var, a_Array)
{
  for (var i = 0; i < a_Array.length; i++)
    if (a_Array[i] === a_var) return true;

  return false;
}

/**
 * Vytvori listu s definovanymi podtvrzovacimi akcemi
 * 
 * @param {type} a_oParent
 * @param {type} a_sClass
 * @param {type} a_sMessage
 * @param {type} v_aButtons
 * @param {type} CallBack
 * @returns {undefined}
 */
function CreateConfirm(a_oParent, a_sClass, a_sMessage, a_aButtons, CallBack)
{
  if (a_oParent.next().hasClass('confirm'))
    return;
  
  $('.confirm').slideUp(150, function (){
    $(this).remove();
  });
  
  var v_shtml = 
    '<div class="confirm' + ((a_sClass.length > 0) ? ' ' + a_sClass : '') + '">' + 
      '<div class="message">' + a_sMessage + '</div>'+
      '<div class="confirmactions">';
  for (var i = 0; i < a_aButtons.length; i++)
  {
    v_shtml += 
      '<button name="' + ((a_aButtons[i].submit === true) ? 'ok' : 'cancel') + '">' + 
        a_aButtons[i].message + 
      '</button>';
  }
  v_shtml += '</div></div>';
  
  var v_ohtml = $(v_shtml);
  v_ohtml.hide();
  v_ohtml.insertAfter(a_oParent);
  v_ohtml.on('click', 'button', function(e){
    e.preventDefault();
    if ($(this).attr('name') == 'ok')
    {
      if (typeof (CallBack) === 'function')
        CallBack();
    }
    v_ohtml.slideUp(150, function (){
      v_ohtml.remove();
    });
  });
  v_ohtml.slideDown(150);
}
function CreateDeleteConfirm(a_oParent, a_sMessage, CallBack)
{
  var v_aButtons = [];
  v_aButtons.push({submit: true, message: 'Odstranit'});
  v_aButtons.push({submit: false, message: 'Zrušit'});
  
  CreateConfirm(a_oParent, 'delete', a_sMessage, v_aButtons, CallBack);
}