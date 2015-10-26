<?php
namespace RadriaCore\Radria;
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt
   /**
    * RadriaException
    *
    * Use default sample from PHP for when we will need to extend it
    *
    * @author Philippe Lewicki  phil at sqlfusion.com
    * @version 4.0
    * @package RadriaCore
    */
class RadriaException extends \Exception {
   // Redefine the exception so message isn't optional
   public function __construct($message, $code = 0) {
       // some code
  
       // make sure everything is assigned properly
       parent::__construct($message, $code);
   }

   // custom string representation of object
   public function __toString() {
       return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
   }

   public function customFunction() {
       echo "A Custom function for this type of exception\n";
   }
}
?>
