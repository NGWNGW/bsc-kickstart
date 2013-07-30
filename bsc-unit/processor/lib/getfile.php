<?php
/*
basecondition ~ getfile.php v2.2.0
copyright 2013 ~ Joachim Doerr ~ hello@basecondition.com
licensed under MIT or GPLv3
*/

class get_file
{
  /*
   is less file exist
  */
  public function is_less_exist ( $strFolder, $strFileName )
  {
    $arrFileName = explode ( '.', $strFileName );
    $strLessFileName = str_replace ( array ( $arrFileName [ sizeof ( $arrFileName ) - 1 ], '.min', '.bsc', '.cc' ), array ( 'less','', '', '' ), $strFileName );
    
    if ( $this->is_file_exist ( $strFolder, $strLessFileName, true ) === true )
    {
      return true;
    }
    else
    {
      return false;
    }
  }
  
  
  /*
   is css file exist
  */
  public function is_css_exist ( $strFolder, $strFileName )
  {
    $arrFileName = explode ( '.', $strFileName );
    $strFileName = str_replace ( array('.min', '.bsc', '.cc' ), '', $strFileName );
    
    if ( $this->is_file_exist ( $strFolder, $strFileName, true ) === true )
    {
      return true;
    }
    else
    {
      return false;
    }
  }
  
  
  /*
   is file exist
  */
  public function is_file_exist ( $strFolder, $strFileName, $boolSet404 = false )
  {
    if ( file_exists ( $strFolder . $strFileName ) === true )
    {
      return true;
    }
    else
    {
      if ( $boolSet404 === true )
      {
        header ( 'HTTP/1.0 404 Not Found' );
      }
      return false;
    }
  }
  
  
  /*
   get file
  */
  public function get_it ( $strFile )
  {
    $strFolder = '../';
    $strFile = str_replace ( array ( '.bsc', '.cc' ), '', $strFile );
    $arrFileName = explode ( '/', $strFile );
    
    if ( sizeof ( $arrFileName ) > 1 )
    {
      $arrPath = $arrFileName;
      unset ( $arrPath [ sizeof ( $arrPath ) - 1 ] );
      $strFolder = $strFolder . implode ( '/', $arrPath ) . '/';
    }
    
    $strFile = $arrFileName [ sizeof ( $arrFileName ) - 1 ];
    $arrFileName = explode ( '.', $strFile );
    
    if ( $arrFileName [ sizeof ( $arrFileName ) - 1 ] == 'css' )
    {
      // include core class
      require_once ( dirname ( __FILE__ ) . "/getcss.php" );
      
      // init css object
      $objCSS = new get_css();
      
      if ( $this->is_less_exist ( $strFolder, $strFile ) === true )
      {
        // get output
        echo $objCSS->get_parsed_file ( $strFolder, $strFile, true );
      }
      else if ( $this->is_css_exist ( $strFolder, $strFile ) === true )
      {
        // get output
        echo $objCSS->get_parsed_file ( $strFolder, $strFile, false );
      }
    }
    else
    {
      if ( $this->is_file_exist ( $strFile, true ) === true )
      {
        // return file
        echo file_get_contents ( $strFolder . $strFile );
      }
    }
  }
}