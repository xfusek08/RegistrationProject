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