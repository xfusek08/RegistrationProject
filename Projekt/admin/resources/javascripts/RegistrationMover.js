var FreeResConn = null;

$(document).ready(function(){
  BuildFreeResConn();
});

var Registrations = [];
var FreeResConn = null;
var Freeresconnheight = 250;
var Minimized  = true;
var SelBcgrColor = "rgb(200,255,200)";

function BuildFreeResConn()
{    
  FreeResConn = $('.adm-freeresconn');
  var newheight = $(window).outerHeight() - $('.adm-topheader').outerHeight();
  if (newheight < 500) newheight = 500;
  $('.adm-leftpanel-intable').height(newheight);
  SetUpConnHeight();
  FreeResConn.width($('.adm-upconn').width());
  $(window).resize(function(){
    var newheight = $(window).outerHeight() - $('.adm-topheader').outerHeight();
    if (newheight < 500) newheight = 500;
    $('.adm-leftpanel-intable').height(newheight);
    SetUpConnHeight();
    $('.adm-upconn').width($(window).outerWidth() - $('.adm-leftpanel').outerWidth());
    FreeResConn.width($('.adm-upconn').width());
  });
  FreeResConn.on('click', '.freeresconn-caption', ToggleUpDown);
};
function SetUpConnHeight()
{
  var newheight = 0;
  if (Minimized)
  {
    FreeResConn.find('.freeresconn-conn').height(0);
    newheight = $('.adm-leftpanel-intable').outerHeight() - FreeResConn.find('.freeresconn-caption').outerHeight() - 36;
  }
  else
  {
    FreeResConn.find('.freeresconn-conn').height(Freeresconnheight - FreeResConn.find('.freeresconn-caption').outerHeight());
    newheight = $('.adm-leftpanel-intable').outerHeight() - Freeresconnheight - 36;
  }

  $('.adm-upconn > div .adm-newregconn-conn').css({height: newheight+ 'px'});
  $('.adm-upconn > div .adm-day-conn').css({height: newheight + 'px'});
  $('.adm-upconn').height(newheight);
}
function ToggleUpDown()
{
  var offsetanim = 0;
  if (Minimized)
  {
    offsetanim = Freeresconnheight - FreeResConn.find('.freeresconn-caption').outerHeight();
    FreeResConn.find('.freeresconn-conn').animate({
      height: '+=' + offsetanim + 'px'
    }, 250, "swing");
    $('.adm-upconn > div .adm-day-conn').animate({
      height: '-=' + offsetanim + 'px'
    }, 250, "swing");
    $('.adm-upconn > div .adm-newregconn-conn').animate({
      height: '-=' + offsetanim + 'px'
    }, 250, "swing");
    $('.conndetail-inhtml .reservations').animate({
      maxHeight: '-=' + offsetanim + 'px'
    }, 250, "swing");
    $('.adm-upconn').stop().animate({
        height: '-=' + offsetanim + 'px'
      },
      250, "swing", function(){
        FreeResConn.find('.freeresconn-caption').find('.maxminbt img').attr('src', '../img/Down.png');
        Minimized = false;
        SetUpConnHeight();
      }
    ); 
  }
  else
  {
    offsetanim = Freeresconnheight - FreeResConn.find('.freeresconn-caption').outerHeight();
    FreeResConn.find('.freeresconn-conn').animate({
      height: '-=' + offsetanim + 'px'
    }, 250, "swing");
    $('.adm-upconn > div .adm-day-conn').animate({
      height: '+=' + offsetanim + 'px'
    }, 250, "swing");
    $('.adm-upconn > div .adm-newregconn-conn').animate({
      height: '+=' + offsetanim + 'px'
    }, 250, "swing");
    $('.conndetail-inhtml .reservations').animate({
      maxHeight: '+=' + offsetanim + 'px'
    }, 250, "swing");
    $('.adm-upconn').stop().animate({
      height: '+=' + offsetanim + 'px'
      },
      250, "swing", function(){
        FreeResConn.find('.freeresconn-caption').find('.maxminbt img').attr('src', '../img/Up.png');
        Minimized = true;
        SetUpConnHeight();
      }
    );
  }
};
