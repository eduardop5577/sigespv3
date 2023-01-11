/***********************************************************************************
* @fecha de modificacion: 04/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var frmPagos = null;  //instancia del formulario de pagos
var Actualizar = null;
var fechaPrimera = obtenerPrimerDiaMes();
var formato1 = '';
var cencos = '';

barraherramienta = true;
Ext.onReady(function()
{
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
//--------------------------------------------------------------------------------------------------------------------------------	
// Combo mes desde (MES)
	var mesdesde = [ 
				 [ 'Enero', '01' ], 
	             [ 'Febrero', '02' ],
				 [ 'Marzo', '03' ],
				 [ 'Abril', '04' ],
				 [ 'Mayo', '05' ],
				 [ 'Junio', '06' ],
				 [ 'Julio', '07' ],
				 [ 'Agosto', '08' ],
				 [ 'Septiembre', '09' ],
				 [ 'Octubre', '10' ],
				 [ 'Noviembre', '11' ],
				 [ 'Diciembre', '12' ],
				 ];
	
	var stmesdesde = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : mesdesde
	}); //Fin de store para el tipo de impresion
	
	//creando objeto combo filtrar
	var cmbmesdesde = new Ext.form.ComboBox({
		store : stmesdesde,
		fieldLabel : 'Desde',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'mesdes',
		width : 100,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local',
		emptyText:'Seleccione'
	});

//--------------------------------------------------------------------------------------------------------------------------------		
// Combo mes hasta (MES)
	var meshasta = [ 
				 [ 'Enero', '01' ], 
	             [ 'Febrero', '02' ],
				 [ 'Marzo', '03' ],
				 [ 'Abril', '04' ],
				 [ 'Mayo', '05' ],
				 [ 'Junio', '06' ],
				 [ 'Julio', '07' ],
				 [ 'Agosto', '08' ],
				 [ 'Septiembre', '09' ],
				 [ 'Octubre', '10' ],
				 [ 'Noviembre', '11' ],
				 [ 'Diciembre', '12' ],
				 ];
	
	var stmeshasta = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : meshasta
	}); //Fin de store para el tipo de impresion
	
	//creando objeto combo filtrar
	var cmbmeshasta = new Ext.form.ComboBox({
		store : stmeshasta,
		fieldLabel : 'Hasta',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'meshas',
		width : 100,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local',
		emptyText:'Seleccione'
	});
//--------------------------------------------------------------------------------------------------------------------------------	
//creacion del formulario de datos de intervalo de fechas
	
		fieldsetIntervaloFechasMeses = new Ext.form.FieldSet({
			title:"Intervalo de Fechas Mensuales",
			style: 'position:absolute;left:10px;top:50px',
			border:true,
			width: 705,
			cls :'fondo',
			height: 75,
			items: [{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:25px;top:20px',
					border:false,
					items: [{
							layout:"form",
							border:false,
							labelWidth:50,
							items: [cmbmesdesde]
							}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:400px;top:20px',
					border:false,
					items: [{
							layout:"form",
							border:false,
							labelWidth:50,
							items: [cmbmeshasta]
							}]
					}]

	});	
//----------------------------------------------------------------------------------------------------------------------------------

var opcimp = [ 
				 [ 'PDF', 'P' ], 
	             [ 'EXCEL', 'E' ] 
				 ];
	
	var stOpcimp = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : opcimp
	}); //Fin de store para el tipo de impresion
	
	//creando objeto combo filtrar
	var cmbtiporeporte = new Ext.form.ComboBox({
		store : stOpcimp,
		fieldLabel : 'Tipo Impresi&#243;n',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'tipoimp',
		width : 150,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local'
	});
	
	cmbtiporeporte.setValue('P');
//----------------------------------------------------------------------------------------------------------------------------------	

//Creacion del formulario pagos
	var Xpos = ((screen.width/2)-(380));
	frmPagos = new Ext.FormPanel({
	applyTo: 'formulario',
	width: 750,
	height: 200,
	title: "<H1 align='center'>0206 - Resultado Economico Financiero</H1>",
	frame: true,
	autoScroll: true,
	style: 'position:absolute;margin-left:'+Xpos+'px;margin-top:15px;',
	items: [	
        	fieldsetIntervaloFechasMeses,
			{
			xtype: 'hidden',
			id: 'estcencos',
			binding:true,
			defaultvalue:'',
			allowBlank:true
			},
			{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:10px;top:20px',
			border:false,
			items: [{
					layout:"form",
					border:false,
					labelWidth:100,
					items: [cmbtiporeporte]
					}]
			}]	
	});
	irCancelar();
});
//**********************************************************************************************************************************
//**********************************************************************************************************************************	
//                                  						BOTONES 
//**********************************************************************************************************************************
function irCancelar()
{
    Ext.getCmp('mesdes').setValue('01');
    Ext.getCmp('meshas').setValue('01');
}


function irImprimir()
{
	formato='';
	pagina='';
	if (Ext.getCmp('tipoimp').getValue()=='P')
	{
		formato='sigesp_scg_rpp_resultadoeconomicofinanciero0206.php';
	}
	else
	{
		formato='sigesp_scg_rpp_resultadoeconomicofinanciero0206_excel.php';
	}
        cmbmesdes  = Ext.getCmp('mesdes').getValue();
        cmbmeshas  = Ext.getCmp('meshas').getValue();
        if((cmbmesdes=="")||(cmbmeshas==""))
        {
            Ext.Msg.hide();
            alert ("Debe seleccionar los Parametros de Busqueda");
        }
        else
        {
            pagina="reportes/"+formato+"?mesdes="+cmbmesdes+"&meshas="+cmbmeshas;
        }
	if(pagina!='')
	{
            window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
	}	
}
