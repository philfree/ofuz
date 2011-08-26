<?php 
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 

/**
 * Utility class for the internationalization
*/    
   
class OfuzUtilsi18n extends BaseObject {
    private static $lang = "en_US";
    


   /**
     * Constructor to set the current language set
     * Incase we want an object instentiation 
    */
   function __construct(){
      if (isset($GLOBALS['cfg_lang'])) { 
	$this->setLanguage($GLOBALS['cfg_lang']);
      }
    }

    
    /**
      * Function to format the date in short format
      * Example Y/m/d ,m/d/Y etc
      * @param string  $date ,the sending date must be MYSQL comatable date to do the conversion
      * @param boolean $showtime , display the time also else display just the date 
      * @return the formated date
    */
    static function formatDateShort($date,$show_time = false){
      if($show_time === true){
          return strftime("%x %H:%M %P",strtotime($date));
      }else{
          return strftime("%x",strtotime($date));
      }
    }

    /**
      * Function to fomat the date in long format 
      * @param strung $date ,the sending date must be MYSQL compatable date to do the conversion
      * @param boolean $showtime , display the time also else display just the date 
      * @return formated date
      * @see includes/i18n.conf.inc.php where we can set the GLOBALS
    */
    static function formatDateLong($date,$show_time = false ){
      //$curr_lang = $this->getLanguage();
      $curr_lang = self::getLanguage();
      
      if($show_time === true){
        if(isset($GLOBALS['cfg_time_formats'][$curr_lang]['time'])){
            return strftime($GLOBALS['cfg_time_formats'][$curr_lang]['time'],strtotime($date));
        }else{
            return strftime("%A, %B %e, %Y, %l:%M %P",strtotime($date));
        }
      }else{
        if(isset($GLOBALS['cfg_time_formats'][$curr_lang]['date'])){
          return strftime($GLOBALS['cfg_time_formats'][$curr_lang]['date'],strtotime($date));
        }else{
          return strftime("%A, %B %e, %Y",strtotime($date));
        }    
      }
    }

    /**
      * Method to set the language
    */
    function setLanguage(){
	//$this->lang = $lang;
	/*if (isset($GLOBALS['cfg_lang'])) { 
	    $this->setLanguage($GLOBALS['cfg_lang']);
	}*/
    }

    /**
      * Function to get language
     */
    static function getLanguage(){
	//return $this->lang ;
	if (isset($GLOBALS['cfg_lang'])) { 
	    //$this->lang = $GLOBALS['cfg_lang'];
	    self::$lang = $GLOBALS['cfg_lang'];
	}
	return self::$lang ;
    }
    

}	
?>
