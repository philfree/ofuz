<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

/**
 *  Delete Inactive user 
 *  Users who is not logged in since last 60 days and user has count of Invoice, Task, Project and Contact is less than 10 
 *  Those users are treated as Inactive user and all the releated data correspoinding to that user will been taking backup as an xml file. 
 *  After taking the backup we are cleaning up the DB against those users.  
 *  Note : we need xml_export folder inside ofuz project with write permission. 
 * */
    include_once('config.php');

		$du = new user();
		$y=$du->eventDeleteInactiveUsers();
		echo $y;
?>
