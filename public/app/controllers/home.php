<?php
class HomeC extends Q_Controller
{
  function _init()
  { 
    LH::langCheck($this->lang);
    // initially run this method before actions 
  }
  public function home()
  {
    $this->viewVars['myvar'] = "hello viewvar"; //send var to view
    $this->viewVars['lang'] =$this->lang; //send var to view
    $this->viewVars['page'] =$this->action; //send var to view
    $llang=$this->lang =="tr"?"tr":"tr";
    if(isset($_GET["introduction"]))
    {
      header('Location: /docs/SolidElectron_Tanıtım_'.$llang.'.pdf');
      // header('Location: ./introduction');
      exit('301');

    }
    if(isset($_GET["products"]))
    {
      header('Location: /docs/SolidElectron_Katalog_'.$llang.'.pdf');
      // header('Location: ./introduction');
      exit('301');

    }
    
  }

  // public function about()
  // {
  //   $this->viewVars['myvar'] = "hello page2"; //send var to view
    
  // }
}
