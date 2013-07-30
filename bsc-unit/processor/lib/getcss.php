<?php
/*
basecondition ~ getcss.php v2.2.0
copyright 2013 ~ Joachim Doerr ~ hello@basecondition.com
licensed under MIT or GPLv3
*/

class get_css
{
  /*
   define defaults 
  */
  public $strFolder               = '';
  public $strFileName             = NULL;
  public $strCacheFileName        = NULL;
  public $strLessFileName         = NULL;
  public $intLastModificationTime = NULL;
  public $boolMinimalizer         = false;
  public $boolFileIsCss           = false;
  public $boolCacheFileGenerate   = false;
  
  
  /*
   get ini settings
  */
  public function get_defaults ( $strFile = NULL )
  {
    if ( $strFile === NULL )
    {
      $strFile = "../data/config.ini";
    }
    
    // include core class
    require_once ( dirname ( __FILE__ ) . "/iniset.php" );
    
    // init ini object
    $objIni = new ini_set ();
    
    // get arr
    return $objIni->read_ini ( $strFile );
  }
  
  
  /*
   check last modification function
  */
  public function file_check_modification_time ( $strFile, $strTimeUpTo = 3600 )
  {
    if ( $strTimeUpTo == 0 )
    {
      if ( file_exists ( $strFile ) === true )
      {
        return filemtime ( $strFile );
      }
      else
      {
        return 0;
      }
    }
    else
    {
      return ( ! file_exists ( $strFile ) or ( time () - filemtime ( $strFile ) ) > $strTimeUpTo ) ? true : false;
    }
  }
  
  
  /*
   change detection function
  */
  public function file_cache_change_detection ( $boolLess = true )
  {
    $arrIni = $this->get_defaults ();
    $strFileName = $this->strFileName;
    
    if ( $boolLess === true )
    {
      $strFileName = $this->strLessFileName;
    }
    
    $strLessFileModificationTime = $this->file_check_modification_time ( $this->strFolder . $strFileName, 0 );
    $strCacheFileModificationTime = $this->file_check_modification_time ( $arrIni [ 'strCacheFolder' ] . $this->strCacheFileName, 0 );
    
    $this->intLastModificationTime = ( $strLessFileModificationTime > mktime ( 0,0,0,21,5,1980 ) ) ? $strLessFileModificationTime : mktime ( 0,0,0,21,5,1980 );
    
    if ( $strCacheFileModificationTime > 0 )
    {
      if ( $strCacheFileModificationTime < $strLessFileModificationTime )
      {
        $this->boolCacheFileGenerate = true;
        $this->intLastModificationTime = $strCacheFileModificationTime;
      }
      else
      {
        $this->boolCacheFileGenerate = false;
      }
    }
    else
    {
      $this->boolCacheFileGenerate = true;
    }
  }
  
  
  /*
   file name geneartion function
  */
  public function file_name_generation ( $strFile, $boolLess = true )
  {
    $arrFile = explode ( '/', $strFile );
    $arrFileName = explode ( '.', $arrFile [ sizeof ( $arrFile ) - 1 ] );
    
    $this->strCacheFileName = 'cache_' . $strFile;
    $this->strFileName = $strFile;
    
    if ( $boolLess === true )
    {
      $this->strLessFileName = str_replace ( array ( '.min', '.css' ), array ( '', '.less' ), $strFile );
    }
    
    foreach ( $arrFileName as $strFileNamePart )
    {
      switch ( $strFileNamePart )
      {
        case 'min' :
          $boolFileHasMin = true;
          $this->boolMinimalizer = true;
          break;
          
        case 'css' :
          $this->boolFileIsCss = true;
          break;
      }
    }
  }
  
  
  /*
   minimalizer function
  */
  public function process_minimalize ( $strBuffer )
  {
    /* remove comments */
    $strBuffer = preg_replace ( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $strBuffer );
    /* remove tabs, spaces, newlines, etc. */
    $strBuffer = str_replace ( array ( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $strBuffer );
    return $strBuffer;
  }
  
  
  /*
   file parser function
  */
  public function parse_file ( $strFile, $boolLess = true )
  {
    $this->file_name_generation ( $strFile, $boolLess );
    $arrIni = $this->get_defaults ();
    
    if ( $this->boolFileIsCss === true && $boolLess === true )
    {
      $this->file_cache_change_detection ( $boolLess );
      
      if ( $this->boolCacheFileGenerate === true )
      {
        // include lessc
        require_once ( dirname( __FILE__ ) . '/../vendor/lessc.php' );
        
        // init less object
        $objLess = new lessc ( $this->strFolder . $this->strLessFileName );
        
        // check compression name
        if ( $this->boolMinimalizer === true )
        {
          file_put_contents ( $arrIni[ 'strCacheFolder' ] . $this->strCacheFileName, str_replace ( array ( '{csskey}', '{year}' ), array ( str_replace ( 'cache_', '', $this->strCacheFileName), date ( Y ) ), $arrIni [ 'strCompressCopyright' ] ) . $this->process_minimalize ( $objLess->parse () ) );
        }
        else
        {
          file_put_contents ( $arrIni[ 'strCacheFolder' ] . $this->strCacheFileName, str_replace ( array ( '{csskey}', '{year}' ), array ( str_replace ( 'cache_', '', $this->strCacheFileName ), date ( Y ) ), $arrIni  [ 'strCopyright' ] ) . $objLess->parse () );
        }
      }
    }
    else if ( $this->boolFileIsCss === true )
    {
      $strFile = str_replace ( array ( '.min', '.cc', '.bsc' ), '', $strFile );
      
      if ( $this->boolMinimalizer === true )
      {
        file_put_contents ( $arrIni[ 'strCacheFolder' ] . $this->strCacheFileName, str_replace ( array ( '{csskey}', '{year}' ), array ( str_replace ( 'cache_', '', $this->strCacheFileName), date ( Y ) ), $arrIni [ 'strCompressCopyright' ] ) . $this->process_minimalize ( file_get_contents ( $this->strFolder . $strFile ) ) );
      }
      else
      {
        file_put_contents ( $arrIni[ 'strCacheFolder' ] . $this->strCacheFileName, str_replace ( array ( '{csskey}', '{year}' ), array ( str_replace ( 'cache_', '', $this->strCacheFileName ), date ( Y ) ), $arrIni  [ 'strCopyright' ] ) . file_get_contents ( $this->strFolder . $strFile ) );
      }
    }
  }
  
  
  /*
   get parsed file
  */
  public function get_parsed_file ( $strFolder, $strFile, $boolLess = true )
  {
    $this->strFolder = $strFolder;
    $arrIni = $this->get_defaults ();
    
    // init process
    $this->parse_file ( $strFile, $boolLess );
    
    // check gzhandler
    if ( substr_count ( $_SERVER ['HTTP_ACCEPT_ENCODING'], 'gzip' ) )
    {
      ob_start ( 'ob_gzhandler' );
    }
    else
    {
      ob_start ();
    }
    if ( $this->boolCacheFileGenerate === false && isset ( $_SERVER [ 'If-Modified-Since' ] ) && strtotime ( $_SERVER [ 'If-Modified-Since' ] ) >= $this->intLastModificationTime )
    {
      header ( 'HTTP/1.0 304 Not Modified' );
    }
    else
    {
      header ( 'Content-type: text/css' );
      header ( 'Last-Modified: ' . gmdate ( "D, d M Y H:i:s", $this->intLastModificationTime ) . " GMT" );
      
      return file_get_contents ( $arrIni [ 'strCacheFolder' ] . $this->strCacheFileName );
    }
  }
}