<?php 
    /** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
    // Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
    /** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
     * RecurrentInvoice class
     * Using the DataObject
	 * 
	 * 
		+--------------------+--------------+------+-----+---------+----------------+
		| Field              | Type         | Null | Key | Default | Extra          |
		+--------------------+--------------+------+-----+---------+----------------+
		| idrecurrentinvoice | int(10)      | NO   | PRI | NULL    | auto_increment | 
		| iduser             | int(15)      | NO   |     |         |                | 
		| idinvoice          | int(15)      | NO   |     |         |                | 
		| nextdate           | date         | NO   |     |         |                | 
		| recurrence         | int(10)      | NO   |     |         |                | 
		| recurrencetype     | varchar(200) | NO   |     |         |                | 
		+--------------------+--------------+------+-----+---------+----------------+

	 * 
     */

class RecurrentInvoice extends DataObject {
    
    public $table = "recurrentinvoice";
    protected $primary_key = "idrecurrentinvoice";
    
    private $report = Array (
    );

    private $savedquery = Array (
      
    );

    public $frequencyComboArray = Array("Day","Week","Month","Year"); 

    /*
        Method to generate the Combo box with the frequency options
    */
    public function generateRecurrentFrequencyCombo($selected=""){
        if (empty($selected)) { $selected = "Month"; }
        $html = '<select name = "frequency" id = "frequency">';
        foreach($this->frequencyComboArray as $optval){
            if($optval == $selected){
               $html .= '<option selected value = "'.$optval.'">'.$optval.'</option>';
            }else{
               $html .= '<option value = "'.$optval.'">'.$optval.'</option>';
            }
        }
        $html .='</select>';
        return $html;
    }

    
    /*
        Method to check if an invoice is already set to the Recurrent
    */
    public function checkIfInvoiceIsInRecurrent($idinvoice){
        $q = new sqlQuery($this->getDbCon());
       // echo "<br /> select * from ".$this->table." where idinvoice = ".$idinvoice.'<br />';
        $q->query("select * from ".$this->table." where idinvoice = ".$idinvoice);
        if($q->getNumRows()){
            $q->fetch();
            return $q->getData("idrecurrentinvoice");
        }else{
            return false;
        }
    }

    /*
        Method to add the Recurrent Part of an Invoice
    */
    public function addRecurrentInvoice($idinvoice,$rec,$rec_type,$date_created,$iduser=""){
        $next_date = $this->getNextDate($rec,$rec_type,$date_created);
        if($iduser == "")    
            $this->iduser = $_SESSION['do_User']->iduser;
        else
            $this->iduser = $iduser ;
        
        $this->idinvoice = $idinvoice;
        $this->nextdate = $next_date;
        $this->recurrence = $rec;
        $this->recurrencetype = $rec_type;
        $this->add();
    }

    /*
        This the method called while an invoice is edited. If the invoice has a recurrent part
        it will update else it will be a new entry.
    */
    function updateRecurrentInvoice($idinvoice,$rec,$rec_type,$date_created){
        $idrecurrentinvoice = $this->checkIfInvoiceIsInRecurrent($idinvoice);
        if($idrecurrentinvoice){
            // Update the recurrent
            $this->getId($idrecurrentinvoice);
            $this->nextdate = $this->getNextDate($rec,$rec_type,$date_created);
            $this->recurrence = $rec;
            $this->recurrencetype = $rec_type;
            $this->update();
        }else{
            // Add a new one
            $this->addRecurrentInvoice($idinvoice,$rec,$rec_type,$date_created);
        }
    }

    /*
        Function to delete recurrent part of an invoice
    */
    public function deleteRecurrentInvoice($idinvoice){
        $idrecurrentinvoice = $this->checkIfInvoiceIsInRecurrent($idinvoice);
        if($idrecurrentinvoice){
            $this->getId($idrecurrentinvoice);
            $this->delete();
			return true;
        } else {
			return false;
		}
    }

    /*
        Method to generate the next date of the recurrent
    */
    public function getNextDate($rec,$rec_type,$date_created){
        $conv_date = strtotime("+".$rec." ".$rec_type,strtotime($date_created));
        $next_date = date("Y-m-d",$conv_date);
        return $next_date;
    }

    /*
       Getting the all the recurrent invoices which has to be created today
    */
    function getRecurrentInvoiceForTheDay(){
        //$today = date('Y-m-d');
        $qry = "select * from recurrentinvoice
                Inner join invoice on invoice.idinvoice = recurrentinvoice.idinvoice
                where recurrentinvoice.nextdate <= curdate() AND invoice.status <> 'Quote'";

	//echo $qry;exit;

        
        $this->query($qry);
    }

    /*
        Getting all the recurrent invoices to process the CC.
    */
    function getRecInvoiceForCCProcess(){
        //$today = date('Y-m-d');
        $qry = "SELECT *
                FROM recurrentinvoice
                INNER JOIN invoice ON invoice.idinvoice = recurrentinvoice.idinvoice
                INNER JOIN recurrent_invoice_cc ON recurrent_invoice_cc.idrecurrentinvoice = recurrentinvoice.idrecurrentinvoice
                where due_date <=curdate() AND 
                status = 'Sent'
                ";
       $this->query($qry);
    }

	function getRecurrentInvoiceDetail($idinvoice) {
		$sql = "SELECT *
				FROM `{$this->table}`
				WHERE `idinvoice` = {$idinvoice}
			   ";

		$this->query($sql);
	}
}
?>
