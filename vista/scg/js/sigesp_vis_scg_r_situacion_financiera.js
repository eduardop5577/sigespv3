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
//**********************************************************************************************************************************	
//                                     		INICIO DEL FORMULARIO PROVEEDOR / BENEFICIARIO	
//**********************************************************************************************************************************
// Combo del A�o
var anio = [ 
				 [ '2011', '2011' ],
				 [ '2012', '2012' ],
				 [ '2013', '2013' ],
				 [ '2014', '2014' ],
				 [ '2015', '2015' ],
				 [ '2016', '2016' ],
				 [ '2017', '2017' ], 
	             [ '2018', '2018' ],
				 [ '2019', '2019' ],
				 [ '2020', '2020' ],
				 [ '2021', '2021' ],
				 [ '2022', '2022' ],
				 [ '2023', '2023' ],
				 [ '2024', '2024' ],
				 [ '2025', '2025' ],
				 [ '2026', '2026' ],
				 [ '2027', '2027' ],
				 [ '2028', '2028' ],
				 [ '2029', '2029' ],
				 [ '2030', '2030' ]
				 ];
	
	var staniodes = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : anio
	}); //Fin de store para el tipo de impresion

	//creando objeto combo filtrar
	var cmbaniodes = new Ext.form.ComboBox({
		store : staniodes,
		fieldLabel : 'A&#241;o',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'anodes',
		width : 100,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local',
		emptyText:'Seleccione'
	});
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
		fieldLabel : 'Mes',
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
	
	//cmbtiporeporte.setValue('P');
//**********************************************************************************************************************************
//creacion del formulario de datos de intervalo de fechas
	fieldset = new Ext.form.FieldSet({
		width: 705,
		height: 70,
		title: "Rango en Meses",
		style: 'position:absolute;left:10px;top:105px',
		cls :'fondo',
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:200px;top:15px',
				border:false,
				items: [{
						layout:"form",
						border:false,
						labelWidth:30,
						items: [cmbmesdesde]
					}]
				},
				{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:370px;top:15px',
				border:false,
				items: [{
						layout:"form",
						border:false,
						labelWidth:30,
						items: [cmbaniodes]
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
	
	fieldsetIntervaloFechasDias = new Ext.form.FieldSet({
		title:"Rango en Dias",
		style: 'position:absolute;left:10px;top:180px',
		border:true,
		width: 705,
		cls :'fondo',
		height: 75,
		items: [{
				style:'position:absolute;left:25px;top:20px',
				layout:"column",
				border:false,
				items: [{
						layout:"form",
						border:false,
						labelWidth:50,
						items: [{
								 xtype:"datefield",
								 labelSeparator:'',
								 fieldLabel:'Desde',
								 name:'fechadesde',
								 id:'fecdesde',
								 value: fechaPrimera,
								 width:150,
								 binding:true,
								 hiddenvalue:'',
								 defaultvalue:'1900-01-01',
								 allowBlank:true,
								 autoCreate:{tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
							}]
					}]
				},
				{
				style:'position:absolute;left:400px;top:25px',
				layout:"column",
				border:false,
				items: [{
						layout:"form",
						border:false,
						labelWidth:50,
						items: [{
								 xtype:"datefield",
								 labelSeparator:'',
								 fieldLabel:'Hasta',
								 name:'fechahasta',
								 id:'fechasta',
								 value: new Date().format('Y-m-d'),
								 width:150,
								 binding:true,
								 hiddenvalue:'',
								 defaultvalue:'1900-01-01',
								 allowBlank:true,
								 autoCreate:{tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
						}]
				}]
	});
	
	fieldsetPeriodos = new Ext.form.FieldSet({
		title:"Rengo de Fecha",
		style: 'position:absolute;left:10px;top:40px',
		border:true,
		width: 705,
		cls :'fondo',
		height: 60,
		items: [{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:20px;top:10px',
			items: [{
				layout: "form",
				border: false,
				items:[{
					xtype: "radiogroup",
					fieldLabel: "",
					labelSeparator:"",
					binding:true,
					hiddenvalue:'',
					defaultvalue:'',	
					columns: [250,250],
					id:'hidbot',
					items: [{
						boxLabel: 'Meses', name: 'intervalp', inputValue: '1', checked:true,
						listeners:{
							'check': function (checkbox, checked) {
								if(checked) {
									Ext.getCmp('mesdes').reset();
									Ext.getCmp('mesdes').enable();
									Ext.getCmp('anodes').reset();
									Ext.getCmp('anodes').enable();
									Ext.getCmp('fecdesde').reset();
									Ext.getCmp('fechasta').reset();
									Ext.getCmp('fecdesde').disable();
									Ext.getCmp('fechasta').disable();
								}
							}
						}
					},{
						boxLabel: 'Dias', name: 'intervalp', inputValue: '2',
						listeners:{
							'check': function (checkbox, checked) {
								if(checked) {
									Ext.getCmp('mesdes').reset();
									Ext.getCmp('mesdes').disable();
									Ext.getCmp('anodes').reset();
									Ext.getCmp('anodes').disable();
									Ext.getCmp('fecdesde').reset();
									Ext.getCmp('fechasta').reset();
									Ext.getCmp('fecdesde').enable();
									Ext.getCmp('fechasta').enable();
								}
							}
						}
					}]
				}]
			}]
		}]
	});
	
	//Creacion del formulario REPORTE
	var Xpos = ((screen.width/2)-(380));
	frmPagos = new Ext.FormPanel({
		applyTo: 'formulario',
		width: 750,
		height: 500,
		title: "<H1 align='center'>Situacion Financiera</H1>",
		frame: true,
		autoScroll: true,
		style: 'position:absolute;margin-left:'+Xpos+'px;margin-top:15px;',
		items: [fieldsetPeriodos,fieldset,fieldsetIntervaloFechasDias,
		{
			xtype: 'hidden',
			id: 'estcencos',
			binding:true,
			defaultvalue:'',
			allowBlank:true
		},{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:40px;top:10px',
			border:false,
			items: [{
				layout:"form",
				border:false,
				labelWidth:100,
				items: [cmbtiporeporte]
			}]
		}]	
	});
	irNuevo();
});
//----------------------------------------------------------------------------------------------------------------------------------
//**********************************************************************************************************************************	
//                                  INICIO DE FUNCIONES PARA LOS CATALOGOS DE BUSQUEDA Y VALIDACIONES 
//**********************************************************************************************************************************
function irNuevo(){
	
	Ext.getCmp('mesdes').reset();
	Ext.getCmp('mesdes').enable();
	Ext.getCmp('anodes').reset();
	Ext.getCmp('anodes').enable();
	Ext.getCmp('fecdesde').reset();
	Ext.getCmp('fechasta').reset();
	Ext.getCmp('fecdesde').disable();
	Ext.getCmp('fechasta').disable();
	
}





//**********************************************************************************************************************************	
//                                  						BOTONES 
//**********************************************************************************************************************************
function irImprimir() {
	
	var cmbmes = Ext.getCmp('mesdes').getValue();
	var cmbagno = Ext.getCmp('anodes').getValue();
	var fecdesde = Ext.getCmp('fecdesde').getValue();
	var fechasta = Ext.getCmp('fechasta').getValue();
	var txtfecdes = fecdesde.format(Date.patterns.fechacorta);
	var txtfechas = fechasta.format(Date.patterns.fechacorta);
	var pagina="";
	
	var radio= Ext.getCmp('hidbot');
	for (var j = 0; j < radio.items.length; j++)
	{
	  if (radio.items.items[j].checked)
	  {
		hidbot = radio.items.items[j].inputValue;
		break;
	  }
	} 
	
	if((cmbmes=="" || cmbagno=="") && hidbot==1)
	{
		alert ("Debe Seleccionar los Parametros de Busqueda");
	}
	else
	{
		if (Ext.getCmp('tipoimp').getValue()=='P') {
			pagina="reportes/sigesp_scg_rpp_situacionfinanciera.php"
		}
		else
		{
			pagina="reportes/sigesp_scg_rpp_situacionfinanciera_excel.php";
		}
		pagina = pagina+"?cmbmes="+cmbmes+"&rango="+hidbot;
		pagina = pagina+"&cmbagno="+cmbagno+"&fecdesde="+txtfecdes+"&fechasta="+txtfechas;
		window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
	}
	
	
}
