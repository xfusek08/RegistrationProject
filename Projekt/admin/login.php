<?php
// pokud se nedo chce dostat pres url a neprosel pres index
unset($_SESSION['logged']);
session_regenerate_id();
?>
<div class="adm-body">  
  <div class="adm-topheader">
    <div class="adm-topheader-pagecaption">Přihlášení</div>      
  </div>
  <form method="post">
    <table>
      <tr>
        <td>Jméno: </td>
        <td><input name="name" type="text" value="<?php if (isset($_POST['name'])) { echo($_POST['name']); } ?>"/></td>
      </tr>
      <tr>
        <td>Heslo: </td>
        <td><input name="psw" type="password"/></td>
      </tr>        
    </table>
    <div>
      <input type="submit" name="send" value="Přihlásit"/>        
    </div>
  </form>         

  <?php
  if (isset($_POST['send']))
  { 
    echo('<hr>');      

    if ( $_POST['name'] == null || $_POST['psw'] == null )
    {
      echo '<p>Je třeba vyplnit jméno i heslo.';
      exit;
    }

    $fields = null;
    if(!MyDatabase::RunQuery($fields, SQL_LOGIN_SELECT, false, array($_POST['name'], $_POST['psw'])))
    {
      echo '<p>Nepodařilo se přihlásit - chyba databáze.</p>';
      Logging::WriteLog(LogType::Error, 'Login database error.');
      exit;
    }

    if ($fields)
    {
      $_SESSION['logged'] = true;
      session_regenerate_id();
      header("Refresh:0");      
    }
    else
    {
      echo '<p>Nesouhlasící jméno nebo heslo.';
    }
  }
  ?>
</div>
