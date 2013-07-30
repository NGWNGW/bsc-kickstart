<?php
/*
basecondition ~ generate.php v2.2.0
copyright 2013 ~ Joachim Doerr ~ hello@basecondition.com
licensed under MIT or GPLv3
*/

if ( $_REQUEST [ 'f' ] != '' )
{
  // include getfile class
  require_once ( dirname ( __FILE__ ) . "/processor/lib/getfile.php" );
  
  // init object
  $objFile = new get_file ();
  
  // get output
  print $objFile->get_it ( $_REQUEST [ 'f' ] );
}