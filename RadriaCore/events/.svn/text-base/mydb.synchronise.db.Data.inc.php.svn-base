<?php 
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt
       /**
        *  Event Synchronise db data
        *
        * This event import the current backupsync.data.sql into the database
        * It is called remotly by the synchronisation tools of WebIDE or SiteManager.
        * It requires the that files : backupsync.struct.sql has previously been
        * uploaded localy.
        * The event check if the remote mydb_key is the same as the on localy setup in the config.php file.
        * Then it parse the file and execute all the queries.
        * 		
        * <br>- param String $mydb_key
        * <br>- global $cfg_notrefererequestkey
        *
        * @package RadriaEvents
        * @author Philippe Lewicki  <phil@sqlfusion.com>
        * @copyright  SQLFusion LLC 2001-2007
        * @version 3.0
		*/
     
     global $cfg_notrefererequestkey;

     function getSQLStatements($source_code) {
        $instring = false;
        $sqls = array();
        $sqlstatement = "";
        $length = strlen($source_code);
        for ($i=0;$i<=$length;$i++) {
            $cc = substr($source_code,$i,1);
            //echo "<br>$cc, invar=$invar, instring=$instring, waitingstring=$waitingstring";
            if ($cc == "\"" && substr($source_code,$i-1,1) != "\\") { //\"
                if ($instring) {
                    $instring = false;
                } else {
                    $instring = true;
                }
            }
            if ($cc == "'" && substr($source_code,$i-1,1) != "\\") { //\"
                if ($instring) {
                    $instring = false;
                } else {
                    $instring = true;
                }
            }            
            if ($cc == ";" && !$instring) {
                $sqls[] = $sqlstatement ;
                $sqlstatement = "";
            } else {
                $sqlstatement .= $cc;
            }
        }
        if (strlen($sqlstatement)>0 ) { $sqls[] = $sqlstatement;}
        return $sqls;
    }
	 
     $conx = $this->getDbCon();
     if ($mydb_key == $cfg_notrefererequestkey) {
        $fp = fopen($conx->getProjectDirectory()."backupsync.data.sql", "r") ;
        $datastructure = fread ($fp, filesize ($conx->getProjectDirectory()."backupsync.data.sql"));
        fclose ($fp);
        $queries = getSQLStatements($datastructure) ;
        $conx->setBackupSync(false) ;
        $runquery = new sqlQuery($conx) ;
        foreach($queries as $query) {
            $query = trim($query);
            if (strlen($query)>3)  {
                $runquery->query($query) ;
            }
        }
        $fp = fopen($conx->getProjectDirectory()."backupsync.data.sql", "w") ;
        fclose($fp);
        $error = false ;
        if (strlen($runquery->getError()) > 0) {
            $this->setError("SQL query Error during data importation : ".$runquery->getError()) ;
            $error = true ;
        }
        if ($error) {
            $message = "Error, Data import one or more query didn't go true during the data importation";
            $this->setError($message) ;
        } else {
            $message = "Importation done" ; 
        }
     } else {
        $message = "Error, Event Controler, Data  : mydb_key doesn't match" ;
        $this->setError($message) ;
     }
        $disp = new Display($this->getMessagePage()) ;
        $disp->addParam("message", $message) ;
        $this->setDisplayNext($disp) ;

?>