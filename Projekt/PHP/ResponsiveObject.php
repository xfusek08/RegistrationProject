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
          $this->LoadFromPostData();
          if ($this->SaveNew())
            $this->i_tState = ObjectState::osOverview;
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
          $this->LoadFromPostData();
          if ($this->SaveEdit())
            $this->i_tState = ObjectState::osOverview;
        }
        else if ($a_sType == 'cancel')
          $this->i_tState = ObjectState::osOverview;
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
   * Popis vystupniho XML
   * 
   *  <respxml>
   *    <object_response>
   *      <alerts> ... </alerts>            - automaticky zpracovana upozorneni
   *      <actions>                         - seznam akci, ktere ma ridici jednotka provedst
   *        <action>Close</action>          - zavre formular a posle dotas ke zniceni objektu
   *        <action>ShowHtml</action>         
   *          - zobrazi predane html do '.adm-day-conn' a vrati 
   *            jQuery objekt onoho html
   *        <action>InitNewForm</action>      
   *          - vytvori vychozi obsluzne metody pro formular vytvareni noveho objektu
   *            nad objektem vracenym z ShowHtml
   *        <action>InitEditForm</action>      
   *          - vytvori vychozi obsluzne metody pro formular editace existujiciho objektu
   *            nad objektem vracenym z ShowHtml
   *        <action>InitOverViewActions</action> 
   *          - vytvori vychozi obsluzne metody pro zobrazeny objekt
   *            nad objektem vracenym z ShowHtml
   *      </actions>
   *      <showhtml> ... </showhtml>                - obsah toho co se ma zobrazit pomoci ShowHtml
   *                                          obycejne nacteno z nejake sablony
   *    </object_response>
   *  </respxml>
   */
  public function GetResponse()
  {
    $v_sResponse = '<object_response>';
    
    $v_sResponse .= $this->i_oAlertStack->GetXML();
    
    switch ($this->i_tState)
    {
      case ObjectState::osClose:
        $v_sResponse .= '<actions><action>Close</action><actions>';
        break;
      case ObjectState::osNew:
        $v_sResponse .= 
          '<actions>'.
            '<action>ShowHtml</action>'.
            '<action>InitNewForm</action>'.
          '<actions>';
        $v_sResponse .= '<showhtml>' . $this->BuildNewHTML() . '</showhtml>';
        break;
      case ObjectState::osEditing:
        $v_sResponse .= 
          '<actions>'.
            '<action>ShowHtml</action>'.
            '<action>InitEditForm</action>'.
          '<actions>';
        $v_sResponse .= '<showhtml>' . $this->BuildEditHTML() . '</showhtml>';
        break;
      case ObjectState::osOverview:
        $v_sResponse .= 
          '<actions>'.
            '<action>ShowHtml</action>'.
            '<action>InitOverViewActions</action>'.
          '<actions>';
        $v_sResponse .= '<showhtml>' . $this->BuildOverviewHTML() . '</showhtml>';
        break;
    }
    $v_sResponse .= $this->GetResponseAddition();
    $v_sResponse .= '</object_response>';    
    
    return $v_sResponse;
  }
  
  public function SaveNew()
  {
    if ($this->SaveToDB(false))
    {
      $this->i_oAlertStack->Push('green', 'Uloženo.');
      return true;
    }
    else
    {
      $this->i_oAlertStack->Push('green', 'Běhěm ukládání nastala chyba.');
      return false;
    }
  }
  
  public function SaveEdit()
  {
    return SaveNew();
  }
  
  // ---------------------------- PROTECTED -------------------------------
  
  protected abstract function BuildNewHTML();  
  protected abstract function BuildEditHTML();  
  protected abstract function BuildOverviewHTML();  
  
  protected abstract function GetResponseAddition();
}  
