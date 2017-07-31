<?php

/**
 * Interface pro Obecnou stranku, ktera se ma jednoduse vykreslit mimo ResponsiveObject
 */
abstract class ResponsivePage
{
  public $i_bClosed = false;
  public $i_bReload = false;
  public $i_oAlertStack;
  
  public function __construct()
  {
    $this->i_oAlertStack = new AlertStack();    
  }  
  /**
  * Vraci ridici xml pro javascript podle aktualniho stavu
  * 
  * @returns struktura:
  * 
  *  <page_response reload="0/1">      - reload: index zda se ma stranka nejdrive obnovit
  * 
  *    <alerts> ... </alerts>            - automaticky zpracovana upozorneni
  *    <actions>                         - seznam akci, ktere ma ridici jednotka provedst
  *      <action>Close</action>          - zavre formular a posle dotas ke zniceni objektu
  *      <action>ShowHtml</action>       - zobrazi stranku
  *    </actions>
  *    <showhtml> ... </showhtml>        - obsah toho co se ma zobrazit pomoci ShowHtml
  *                                        obycejne nacteno z nejake sablony
  *  </page_response>
  */
  public abstract function GetResponse();
  public abstract function ProcessAjax();  
}
