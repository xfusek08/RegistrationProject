<?php
if (!isset($_SESSION['logged']))
{
  header("Refresh:0; url=index.php");
  exit;
}

if (isset($_POST['logout']))
{ 
  unset($_SESSION['logged']);
  session_destroy();
  header("Refresh:0; url=index.php");
  exit;
}
?>
<link rel="stylesheet" href="resources/styles/events.css" type="text/css" media="screen" />  

<script type="text/javascript" charset="UTF-8" src="resources/javascripts/jquery.timepicker.min.js"></script>
<script type="text/javascript" charset="UTF-8" src="resources/javascripts/EventCalendar.js"></script>
<script type="text/javascript" charset="UTF-8" src="resources/javascripts/Registrations.js"></script>
<script type="text/javascript" charset="UTF-8" src="resources/javascripts/Events.js"></script>
<script type="text/javascript" charset="UTF-8" src="resources/javascripts/RegistrationMover.js"></script>
<script type="text/javascript" charset="UTF-8" src="resources/javascripts/AdminMainPage.js"></script>
<script type="text/javascript" charset="UTF-8" src="resources/javascripts/ResponsivePage.js"></script>
<script type="text/javascript" charset="UTF-8" src="resources/javascripts/NewRegistrations.js"></script>
<!--   
<script type="text/javascript" charset="UTF-8" src="resources/javascripts/AdminJScripts.min.js"></script>
-->
    
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
              <div class="caption"> - otevřené <?php echo P_EVENT_1P; ?></div>              
            </div>
            <div class="legend">
              <div class="icon full-event-color"></div>
              <div class="caption"> - plné <?php echo P_EVENT_1P; ?></div>              
            </div>
            <div class="legend">
              <div class="icon hidden-event-color"></div>
              <div class="caption"> - skryté <?php echo P_EVENT_1P; ?></div>              
            </div>
          </div>
          <div style="display: table-row;">
            <div class="adm-dayevents-frame">
              <div class="adm-dayevents-caption">
                <?php echo ucfirst(P_EVENT_1P); ?> pro den: <span class="daydate"><?php echo (isset($_SESSION['actday']) ? $_SESSION['actday'] : '--'); ?></span>
              </div>
              <div class="adm-dayevents-tools">
                <div class="adm-dayevents-tools-newbt">Vytvořit <?php echo S_EVENT_1P; ?></div>
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
              <div class="action_bt ajaxsubmit" ajaxtype="searchReg">
                <div class="imgconn"><img src="../img/searchGlass.png"/></div>
                <div class="textconn">Hledat registrace</div>
              </div>
              <div class="action_bt ajaxsubmit" ajaxtype="searchCourses">
                <div class="imgconn"><img src="../img/searchGlass.png"/></div>
                <div class="textconn">Hledat kurzy</div>
              </div>
              <div class="action_bt ajaxsubmit" ajaxtype="openSettings" style="float: right; border: none;">
                <div class="imgconn"><img src="../img/Settings.png"/></div>
                <div class="textconn">Nastavení</div>
              </div>
            </div>
            <div class="adm-day-conn"></div>             
          </div>
          <div class="adm-newregconn">
             <div class="adm-newregconn-caption"> Nové registrace: <span class="newregcount">0</span></div>
             <div class="adm-newregconn-conn"></div>
          </div>
        </div>
        <!--
        <div class="adm-freeresconn">
          <div class="freeresconn-caption"> Volné registrace: <span class="rescount"></span>
            <div class="maxminbt">
              <img src="../img/Up.png"/>
            </div>
          </div>
          <div class="freeresconn-conn"></div>
        </div>
        -->
      </div>
    </div>
  </div>
</div>

