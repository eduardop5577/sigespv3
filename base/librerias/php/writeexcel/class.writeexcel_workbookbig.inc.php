<?php
/***********************************************************************************
* @fecha de modificacion: 11/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

/*
 * Copyleft 2002 Johann Hanne
 *
 * This is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This software is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this software; if not, write to the
 * Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA  02111-1307 USA
 */

/*
 * This is the Spreadsheet::WriteExcel Perl package ported to PHP
 * Spreadsheet::WriteExcel was written by John McNamara, jmcnamara@cpan.org
 */

require_once "class.writeexcel_workbook.inc.php";
require_once "class.ole_pps_root.php";
require_once "class.ole_pps_file.php";

class writeexcel_workbookbig extends writeexcel_workbook
{
    public function __construct($filename)
	{
        $this->writeexcel_workbook($filename);
    }
	
    public function writeexcel_workbookbig($filename)
	{
        $this->writeexcel_workbook($filename);
    }	

    function _store_OLE_file() {
        $file=new ole_pps_file(asc2ucs("Book"));
        $file->append($this->_data);

        for ($c=0;$c<sizeof($this->_worksheets);$c++) {
            $worksheet=&$this->_worksheets[$c];
            while ($data=$worksheet->get_data()) {
                $file->append($data);
            }
            $worksheet->cleanup();
        }

        $ole=new ole_pps_root(false, false, array($file));
        $ole->save($this->_filename);
    }

}

?>
