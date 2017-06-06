<?php
if (!isset($_SESSION['logged']))
{
  header("Refresh:0; url=index.php");
  exit;
}
// zpracovani post
if (isset($_POST['logout']))
{ 
  unset($_SESSION['logged']);
  session_destroy();
  header("Refresh:0; url=index.php");
  exit;
}
?>
<script type="text/javascript" charset="UTF-8" src="resources/javascripts/EventCalendar.js"></script>
<script type="text/javascript" charset="UTF-8" src="resources/javascripts/AdminMainPage.js"></script>
    
<div class="adm-body">  
  <form method="post">
    <div class="adm-topheader">
      <div class="adm-topheader-pagecaption">Správce registrací</div>
      <input class="adm-bt-logout" type="submit" name="logout" value="odhlásit"/>
    </div>
  </form>
  <div class="adm-appbody">
    <div class="adm-bodytable">
      <div class="adm-leftpanel">
        <div class="adm-leftpanel-intable">
          <div style="display: table-row;">
            <div class="adm-calendarframe"><div id="datepicker"></div></div>
          </div>
          <div class="adm-calend">
            <div class="legend">
              <div class="icon open-event-color"></div>
              <div class="caption"> - otevřené událostí</div>              
            </div>
            <div class="legend">
              <div class="icon full-event-color"></div>
              <div class="caption"> - plné události</div>              
            </div>
            <div class="legend">
              <div class="icon hidden-event-color"></div>
              <div class="caption"> - skryté události</div>              
            </div>
          </div>
          <div style="display: table-row;">
            <div class="adm-dayevents-frame">
              <div class="adm-dayevents-caption">Události pro den:
              </div>
              <div class="adm-dayevents-tools">
                <div class="adm-dayevents-tools-newbt">Vytvořit událost</div>
              </div>
              
              <div class="adm-dayevents-view"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="adm-content">
        <!--
                <div class="adm-dayevents-newbt"><img src="../img/newbt.png"></div>
        <div class="adm-day-tools"></div>
        -->
        <div class="adm-upconn">
          <div>
            <div class="adm-day-header">
              <div class="adm-search">
                Vyhledat registrace: 
                <div class="search-textbox">
                  <input type="text"/>
                  <img src="../img/SearchGlass.png"/>
                </div>
              </div>
            </div>
            <div class="adm-day-conn"></div>             
          </div>
          <div class="adm-newresconn">
             <div class="adm-newresconn-caption"> Nové registrace: <span class="newrescount">0</span></div>
             <div class="adm-newresconn-conn"></div>
          </div>
        </div>
        <div class="adm-freeresconn">
          <div class="freeresconn-caption"> Volné registrace: <span class="rescount"></span>
            <div class="maxminbt">
              <img src="../img/Up.png"/>
            </div>
          </div>
          <div class="freeresconn-conn"></div>
        </div>
      </div>
    </div>
  </div>
</div>

