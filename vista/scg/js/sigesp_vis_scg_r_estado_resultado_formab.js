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
// Combo trimestre desde (TRIMESTRE)
	var trides = [ 
				 [ 'Enero - Marzo', '01-03' ], 
				 [ 'Abril - Junio', '04-06' ],
				 [ 'Julio - Septiembre', '07-09' ],
				 [ 'Octubre - Diciembre', '10-12' ]
				 ];
	
	var sttrides = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : trides
	}); //Fin de store para el tipo de impresion
	
	//creando objeto combo filtrar
	var cmbtrides = new Ext.form.ComboBox({
		store : sttrides,
		fieldLabel : 'Desde',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'trides',
		width : 200,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local',
		emptyText:'Seleccione'
	});

//--------------------------------------------------------------------------------------------------------------------------------	
// Combo trimestre hasta (TRIMESTRE)
	var trihas = [ 
				 [ 'Enero - Marzo', '01-03' ], 
				 [ 'Abril - Junio', '04-06' ],
				 [ 'Julio - Septiembre', '07-09' ],
				 [ 'Octubre - Diciembre', '10-12' ]
				 ];
	
	var sttrihas = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : trihas
	}); //Fin de store para el tipo de impresion
	
	//creando objeto combo filtrar
	var cmbtrihas = new Ext.form.ComboBox({
		store : sttrihas,
		fieldLabel : 'Hasta',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'trihas',
		width : 200,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local',
		emptyText:'Seleccione'
	});

//--------------------------------------------------------------------------------------------------------------------------------	
//--------------------------------------------------------------------------------------------------------------------------------		
//creacion del formulario de datos de estado resultado
	
		fieldsetPeriodos = new Ext.form.FieldSet({
		   	title:"Periodos",
			style: 'position:absolute;left:10px;top:15px',
			border:true,
			width: 705,
			cls :'fondo',
			height: 90,
			items: [{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:50px;top:45px',
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
									columns: [255,255],
									id:'hidbot',
									items: [
											{
											boxLabel: 'Trimestres', name: 'intervalp',inputValue: '2',checked:true,
											listeners:{		
														'check': function (checkbox, checked) 
														{
															if(checked)
															{
																Ext.getCmp('mesdes').reset();
																Ext.getCmp('meshas').reset();
																Ext.getCmp('mesdes').disable();
																Ext.getCmp('meshas').disable();
																Ext.getCmp('trides').enable();
																Ext.getCmp('trides').reset();
																Ext.getCmp('trihas').enable();
																Ext.getCmp('trihas').reset();
															}
														}
												 }
											},
											{
											boxLabel: 'Meses', name: 'intervalp', inputValue: '1',
											listeners:{		
														'check': function (checkbox, checked) 
														{
															if(checked)
															{
																Ext.getCmp('mesdes').reset();
																Ext.getCmp('mesdes').enable();
																Ext.getCmp('meshas').reset();
																Ext.getCmp('meshas').enable();
																Ext.getCmp('trides').disable();
																Ext.getCmp('trihas').disable();
															}
														}
													}
											}]
									}]
							}]
					}]

	});
//----------------------------------------------------------------------------------------------------------------------------------	
//**********************************************************************************************************************************	
//                                 				INICIO DEL FORMULARIO INTERVALO FECHAS
//**********************************************************************************************************************************
	//creacion del formulario de datos de intervalo de fechas
	fieldsetIntervaloFechasTrimestres = new Ext.form.FieldSet({
		title:"Intervalo de Fechas Trimestrales",
		style: 'position:absolute;left:10px;top:115px',
		border:true,
		width: 705,
		cls :'fondo',
		height: 100,						
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:25px;top:20px',
				border:false,
				items: [{
						layout:"form",
						border:false,
						labelWidth:50,
						items: [cmbtrides]
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
						items: [cmbtrihas]
						}]
				}]

	});			
//----------------------------------------------------------------------------------------------------------------------------------
//**********************************************************************************************************************************	
//                                 				INICIO DEL FORMULARIO COMPROBANTES
//**********************************************************************************************************************************
//creacion del formulario de datos de intervalo de fechas
	
		fieldsetIntervaloFechasMeses = new Ext.form.FieldSet({
			title:"Intervalo de Fechas Mensuales",
			style: 'position:absolute;left:10px;top:230px',
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
	height: 350,
	title: "<H1 align='center'>Estado de Resultado (Forma B)</H1>",
	frame: true,
	autoScroll: true,
	style: 'position:absolute;margin-left:'+Xpos+'px;margin-top:15px;',
	items: [	
        	fieldsetPeriodos,
           	fieldsetIntervaloFechasTrimestres,
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
			style: 'position:absolute;left:225px;top:45px',
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
//----------------------------------------------------------------------------------------------------------------------------------
//**********************************************************************************************************************************	
//                                  INICIO DE FUNCIONES PARA LOS CATALOGOS DE BUSQUEDA Y VALIDACIONES 
//**********************************************************************************************************************************
function irCancelar(){
	Ext.getCmp('mesdes').disable();
	Ext.getCmp('meshas').disable();
}


//**********************************************************************************************************************************	
//                                  						BOTONES 
//**********************************************************************************************************************************
function irImprimir()
{
	formato='';
	pagina='';
	if (Ext.getCmp('tipoimp').getValue()=='P')
	{
		formato='sigesp_scg_rpp_estado_resultado_sudeban_formab.php';
	}
	else
	{
		formato='sigesp_scg_rpp_estado_resultado_sudeban_formab_excel.php';
	}
	var radio= Ext.getCmp('hidbot');
	for (var j = 0; j < radio.items.length; j++)
	{
		if (radio.items.items[j].checked)
		{
			hidbot = radio.items.items[j].inputValue;
			break;
		}
	} 					
	if(hidbot==1) // MENSUAL
	{
		cmbmesdes  = Ext.getCmp('mesdes').getValue();
		cmbmeshas  = Ext.getCmp('meshas').getValue();
		if((cmbmesdes=="")||(cmbmeshas==""))
		{
			Ext.Msg.hide();
			alert ("Debe seleccionar los Parametros de Busqueda");
		}
		else
		{
			pagina="reportes/"+formato+"?mesdes="+cmbmesdes+"&meshas="+cmbmeshas+"&tipo=Mensual";
		}
	}
	if(hidbot==2) //TRIMESTRAL
	{
		cmbtrides  = Ext.getCmp('trides').getValue();
		cmbtrihas  = Ext.getCmp('trihas').getValue();
		if((cmbtrides=="")||(cmbtrihas==""))
		{
			Ext.Msg.hide();
			alert ("Debe seleccionar los Parametros de Busqueda");
		}
		else
		{
			pagina="reportes/"+formato+"?mesdes="+cmbtrides+"&meshas="+cmbtrihas+"&tipo=Trimestral";
		}
	}
	if(pagina!='')
	{
		window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
	}
	
}