<?php
require_once("sigesp_lib_mensajes.php");
class grid_param
{
	var $numrows;
	var $titles;
	var $align;
	var $size;
	var $maxlength;
	var $validaciones;
	var $widthtable;
	var $titletable;
	
    public function __construct()
    {			
    }
	
    function makegrid($rows,$titulos,$object,$widthtable,$titletable,$name)
    {
		$this->numrows=$rows;
		$this->titles=$titulos;
		$this->widthtable=$widthtable;
		$this->titletable=$titletable;
		$totcols=count((array)$this->titles);
		print "<table class='table' align=center>";
		print "     <tr>";
		print "         <td class='titulos' >".$this->titletable."</td>";
		print "     </tr>";
		print "</table>";
                print "<table class='table table-striped' border=0 cellspacing=1 cellpadding=1 id=".$name." align=center>";
		print "     <thead>";
		print "         <tr>";
		for($i=1;$i<=$totcols;$i++)
		{
			print "<th scope='col' class='tablecabecera'>".$this->titles[$i]."</th>";
		}                
		print "         </tr>";
		print "     </thead>";
		print "     <tbody>";		
		for($z=1;$z<=$this->numrows;$z++)
		{			
                    print "<tr>";
                    for($y=1;$y<=$totcols;$y++)
                    {
                        if ($y==1)
                        {
                            print "<th>".$object[$z][$y]."</th>";
                        }
                        else
                        {
                            print "<td>".$object[$z][$y]."</td>";
                        }
                    }	
                    print "</tr>";
                }
		print "     </tbody>";
		print "</table>";
	}

    function makegridright($rows,$titulos,$object,$widthtable,$titletable,$name)
    {
		$this->numrows=$rows;
		$this->titles=$titulos;
		$this->widthtable=$widthtable;
		$this->titletable=$titletable;
		$totcols=count((array)$this->titles);
		print "<table class='table' align=center>";
		print "     <tr>";
		print "         <td align=center >".$this->titletable."</td>";
		print "     </tr>";
		print "</table>";
                print "<table class='table table-striped' border=0 cellspacing=1 cellpadding=1 id=".$name." align='right' width='".$widthtable."'>";
		print "     <thead>";
		print "         <tr>";
		for($i=1;$i<=$totcols;$i++)
		{
			print "<th scope='col' class='tablecabecera'>".$this->titles[$i]."</th>";
		}                
		print "         </tr>";
		print "     </thead>";
		print "     <tbody>";		
		for($z=1;$z<=$this->numrows;$z++)
		{			
                    print "<tr>";
                    for($y=1;$y<=$totcols;$y++)
                    {
                        if ($y==1)
                        {
                            print "<th>".$object[$z][$y]."</th>";
                        }
                        else
                        {
                            print "<td>".$object[$z][$y]."</td>";
                        }
                    }	
                    print "</tr>";
                }
		print "     </tbody>";
		print "</table>";
	}
        
}
?>

