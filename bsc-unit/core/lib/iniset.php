<?php
/*
basecondition ~ iniset.php v2.2.0
copyright 2013 ~ Joachim Doerr ~ hello@basecondition.com
licensed under MIT or GPLv3
*/

class ini_set
{
  /*
   methode generate_ini_settings
  */
  public function generate_ini_settings ( $arrSettings )
  {
    $arrIniSet = array ();
    
    foreach ( $arrSettings as $aKey => $aVal )
    {
      if ( is_array ( $aVal ) === true )
      {
        $arrIniSet [] = "[$aKey]";
        foreach ( $aVal as $bKey => $bVal )
        {
          $arrIniSet [] = "$bKey = " . ( is_numeric ( $bVal ) ? $bVal : '"'.$bVal.'"');
        }
      }
      else
      {
        $arrIniSet [] = "$aKey = " . ( is_numeric ( $aVal ) ? $aVal : '"'.$aVal.'"' );
      }
    }
    return implode ( "\r\n", $arrIniSet );
  }
  
  
  /*
   methode write_ini_file
  */
  public function write_ini_file ( $strSettings, $strFile )
  {
    $strPath = realpath ( dirname ( __FILE__ ) . '/..' );
    $strFile = $strPath . $strFile;
    if ( ( $objOpenFile = fopen ( $strFile, 'w' ) ) === true ) { 
      return false;
    } 
    if ( ( fwrite ( $objOpenFile, $strSettings ) ) === true ) { 
      return false;
    } 
    fclose ( $objOpenFile ) ;
    return true; 
  }
  
  
  /*
   methode read_ini
  */
  public function read_ini ( $strFile )
  {
    return parse_ini_file ( dirname ( __FILE__ ) . '/' . $strFile );
  }
  
  
  /*
   methode write_ini_settings
  */
  public function write_ini_settings ( $arrSettings, $strFile, $boolShow = false )
  {
    $this->write_ini_file ( $this->generate_ini_settings ( $arrSettings ), $strFile );
    
    if ( $boolShow === true )
    {
      return $this->read_ini ( $strFile );
    }
  }
}