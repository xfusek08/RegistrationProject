<?php
/**
 * Vycet stavu, ktere objekt muze nabyvat
 */
class ObjectState
{
  const osNew = 0;
  const osOverview = 1;
  const osEditing = 2;
  const osClose = 3;
}

/**
 * Třída představující objekt, ktery komunikuje pomoci axaju a meni svuj vnitrni stav
 */
abstract class ResponsiveObject extends DatabaseEntity
{
  // Aktualni stav objektu udalosti
  // typ: ObjectState
  public $i_tState;
  
  // Zasobnik upozorneni, ktere se budou vypisovat na obrazovku s kazdou odpovedi klientovi
  // typ: AlertStack
  public $i_oAlertStack;

  // Zasobnik akci, ktere se budou provadet s kazdou odpovedi klientovi
  // typ: array
  public $i_aActionStack;
  
  // Priznak, zda se ma proveds obnoveni dat na strance
  // typ: boolean
  public $i_bReload;
  
  // Priznak, zda doslo k submitu nebo ne, pro zvyraznovani chybnych poli
  // typ: boolean
  private $i_bSubmited;
  
  
  // ---------------------------- PUBLIC -------------------------------

  /**
   * konstruktor neobsahuje logiku z duvodu moznosti jeho prepisu a vetsi variability
   */
  public function __construct($a_iPK = 0, $ExternTransaction = false)
  {
    parent::__construct($a_iPK, $ExternTransaction);
  }  

  /**
   * Spracuje ajax dotaz patrici tomuto objektu
   * Z post dat si nacte ty ktere ocekava, pokud data nebudou platna stav 
   * se nezmeni a vypise se chybove hlaseni
   * 
   * @param string $a_sType - typ dotazu
   * @return -- 
   */
  public function ProcessAjax($a_sType)
  {
    $invalidRequestType = false;
    
    if ($a_sType == 'close')
    {
      $this->i_tState = ObjectState::osClose;
      return;
    }
    
    switch ($this->i_tState)
    {
      case ObjectState::osNew:
        if ($a_sType == 'submitdata')
        {
          $this->i_bSubmited = true;
          $this->LoadFromPostData();
          if ($this->IsDataValid())
          {
            if ($this->SaveNew())
            {
              $this->i_tState = ObjectState::osOverview;
              $this->i_bReload = true;
            }
          }
          else
            $this->i_oAlertStack->Push('red', 'Formulář obsahuje nevalidní data.');
        }
        else  
          $invalidRequestType = true;
        break;
      case ObjectState::osOverview:
        if ($a_sType == 'edit')
          $this->i_tState = ObjectState::osEditing;        
        else
          $invalidRequestType = true;
        break;
      case ObjectState::osEditing:
        if ($a_sType == 'submitdata')
        {
          $this->i_bSubmited = true;
          $this->LoadFromPostData();
          if ($this->IsDataValid())
          {
            if ($this->SaveEdit())
            {
              $this->i_tState = ObjectState::osOverview;
              $this->i_bReload = true;
            }
          }
          else
            $this->i_oAlertStack->Push('red', 'Formulář obsahuje nevalidní data.');
        }
        else if ($a_sType == 'cancel')
        {
          $this->InitFromDB(false);
          $this->i_tState = ObjectState::osOverview;
        }
        else
          $invalidRequestType = true;
        break;
    }
    if ($invalidRequestType)
      $this->i_oAlertStack->Push('red', 'Invalid request type.');    
    
  }
  
  /**
   * Vraci ridici xml pro javascript podle aktualniho stavu
   * 
   * @returns struktura:
   * 
   *  <object_response reload="0/1">      - reload: index zda se ma stranka nejdrive obnovit
   *    <alerts> ... </alerts>            - automaticky zpracovana upozorneni
   *    <actions>                         - seznam akci, ktere ma ridici jednotka provedst
   *      <action>Close</action>          - zavre formular a posle dotas ke zniceni objektu
   *      <action>ShowHtml</action>         
   *        - zobrazi predane html do '.adm-day-conn' a vrati 
   *          jQuery objekt onoho html
   *      <action>InitNewForm</action>      
   *        - vytvori vychozi obsluzne metody pro formular vytvareni noveho objektu
   *          nad objektem vracenym z ShowHtml
   *      <action>InitEditForm</action>      
   *        - vytvori vychozi obsluzne metody pro formular editace existujiciho objektu
   *          nad objektem vracenym z ShowHtml
   *      <action>InitOverViewActions</action> 
   *        - vytvori vychozi obsluzne metody pro zobrazeny objekt
   *          nad objektem vracenym z ShowHtml
   *    </actions>
   *    <showhtml> ... </showhtml>        - obsah toho co se ma zobrazit pomoci ShowHtml
   *                                        obycejne nacteno z nejake sablony
   *  </object_response>
   */
  public function GetResponse()
  {
    $this->LoadStateActions();
    $v_sResponse = '<object_response reload="' . BoolTo01Str($this->i_bReload) . '">';
    
    $v_sResponse .= $this->i_oAlertStack->GetXML();
    
    if ($this->i_bSubmited)
      if (!$this->IsDataValid())
        $v_sResponse .= $this->GetInvalidDataXML(); 
      
    $v_sResponse .= $this->GetAxtionsXML();
    
    $v_sResponse .= '<showhtml>';
    switch ($this->i_tState)
    {
      case ObjectState::osNew: $v_sResponse .= $this->BuildNewHTML(); break;
      case ObjectState::osEditing: $v_sResponse .= $this->BuildEditHTML(); break;
      case ObjectState::osOverview: $v_sResponse .= $this->BuildOverviewHTML(); break;
    }
    $v_sResponse .= '</showhtml>';

    $v_sResponse .= $this->GetResponseAddition();
    $v_sResponse .= '</object_response>';    
    
    return $v_sResponse;
  }
  
  public function SaveToDB($ExternalTrans)
  {
    if (parent::SaveToDB($ExternalTrans))
    {
      $this->i_oAlertStack->Push('green', 'Uloženo.');
      return true;
    }
    else
    {
      $this->i_oAlertStack->Push('red', 'Běhěm ukládání nastala chyba.');
      return false;
    }
  }
  
  public function SaveNew()
  {
    return $this->SaveToDB(false);
  }
  
  public function SaveEdit()
  {
    return $this->SaveToDB(false);
  }
  
  public function AddAction($a_sActionStr)
  {
    array_push($this->i_aActionStack, $a_sActionStr);
  }
  public function GetAxtionsXML()
  {
    
    $res = '<actions>';    
    while (count($this->i_aActionStack) > 0)
       $res .= '<action>' . array_shift($this->i_aActionStack) . '</action>';
    $res .= '</actions>';
    return $res;
  }
  // ---------------------------- PROTECTED -------------------------------
  protected function LoadStateActions()
  {
    // pridame akce v zavislosti na zmenenem stavu
    if ($this->i_tState == ObjectState::osClose)
    {
      $this->AddAction('Close');      
      return;
    }
    $this->AddAction('ShowHtml');
    switch ($this->i_tState)
    {
      case ObjectState::osNew: 
        $this->AddAction('InitNewForm');
        break;
      case ObjectState::osEditing:
        $this->AddAction('InitEditForm');
        break;
      case ObjectState::osOverview:
        $this->AddAction('InitOverViewActions');
        break;
      // close se obsluhuje ze zacatku
    }    
  }
  
  protected abstract function BuildNewHTML();  
  protected abstract function BuildEditHTML();  
  protected abstract function BuildOverviewHTML();  
  
  protected abstract function GetResponseAddition();
}  
