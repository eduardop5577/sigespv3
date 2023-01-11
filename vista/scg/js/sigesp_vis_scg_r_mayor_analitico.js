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
var ruta ='../../controlador/scg/sigesp_ctr_scg_mayor_analitico.php'; //ruta del controlador
var fechaPrimera = obtenerPrimerDiaMes();
var formato_resumen = '';
var formato = '';
var formato_excel = '';

barraherramienta = true;
Ext.onReady(function()
{
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
//--------------------------------------------------------------------------------------------------------------------------------	
//**********************************************************************************************************************************	
//                                     		INICIO DEL FORMULARIO PROVEEDOR / BENEFICIARIO	
//**********************************************************************************************************************************
//componente catalogo de cuentas contables
	var reCuentaContable = Ext.data.Record.create([
        {name: 'sc_cuenta'}, //campo obligatorio                             
        {name: 'denominacion'}, //campo obligatorio
        {name: 'status'}, //campo obligatorio
    ]);

	var comcampocatcuentacontable1 = new com.sigesp.vista.comCatalogoCuentaContable({
		idComponente:'scg1',
		reCatalogo: reCuentaContable,
		rutacontrolador:'../../controlador/scg/sigesp_ctr_scg_comcatcuentacontable.php',
		parametros: "ObjSon={'operacion': 'buscarCuentaContables'",
		soloCatalogo: true,
		valorStatus: '',
		arrSetCampo:[{campo:'sc_cuenta1',valor:'sc_cuenta'}]
	});
	//fin componente catalogo de cuentas contables
//--------------------------------------------------------------------------------------------------------------------------------	
//componente catalogo de cuentas contables
	var reCuentaContable2 = Ext.data.Record.create([
        {name: 'sc_cuenta'}, //campo obligatorio                             
        {name: 'denominacion'}, //campo obligatorio
        {name: 'status'}, //campo obligatorio
    ]);

	var comcampocatcuentacontable2 = new com.sigesp.vista.comCatalogoCuentaContable({
		idComponente:'scg2',
		reCatalogo: reCuentaContable2,
		rutacontrolador:'../../controlador/scg/sigesp_ctr_scg_comcatcuentacontable.php',
		parametros: "ObjSon={'operacion': 'buscarCuentaContables'",
		soloCatalogo: true,
		valorStatus: '',
		arrSetCampo:[{campo:'sc_cuenta2',valor:'sc_cuenta'}]
	});
	//fin componente catalogo de cuentas contables

//--------------------------------------------------------------------------------------------------------------------------------	

//creacion del formulario de datos de proveedor / beneficiario
	fieldset = new Ext.form.FieldSet({
		width: 715,
		height: 70,
		title:"Cuentas Contables",
		style: 'position:absolute;left:10px;top:5px',
		cls :'fondo',
		items: [{
				style:'position:absolute;left:25px;top:10px',
				layout:"column",
				border:false,
				items: [{
						layout:"form",
						border:false,
						labelWidth:50,
						items: [{
								xtype:"textfield",
								labelSeparator:'',
								fieldLabel:'Desde',
								name:'sc_cuenta1',
								id:'sc_cuenta1',	
								width:150,
								binding:true,
								hiddenvalue:'',
								defaultvalue:'',
								allowBlank:true,
								autoCreate:{tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789');"}
							}]
						}]
				},
				{
				style:'position:absolute;left:235px;top:10px',
				layout:"column",
				border:false,
				items: [{
						layout:"form",
						border:false,
						items: [{
								xtype:"button",
								id:'btnBuscarProv',
								iconCls:'menubuscar',
								handler:	function(boton)
								{
									comcampocatcuentacontable1.mostrarVentana();	
								}
							}]
						}]
				},
				{
				style:'position:absolute;left:450px;top:10px',
				layout:"column",
				border:false,
				items: [{
						layout:"form",
						border:false,
						labelWidth:50,
						items: [{
								xtype:"textfield",
								labelSeparator:'',
								fieldLabel:'Hasta',
								name:'sc_cuenta2',
								id:'sc_cuenta2',
								width:150,
								binding:true,
								hiddenvalue:'',
								defaultvalue:'',
								allowBlank:true,
								autoCreate:{tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789');"}
							}]
						}]
				},
				{
				style:'position:absolute;left:660px;top:10px',
				layout:"column",
				border:false,
				items: [{
						layout:"form",
						border:false,
						items: [{
								xtype:"button",
								id:'btnBuscarben',
								iconCls:'menubuscar',
								handler:	function(boton)
								{
									comcampocatcuentacontable2.mostrarVentana();	
								}
							}]
						}]
				}]
	});
//----------------------------------------------------------------------------------------------------------------------------------	
//**********************************************************************************************************************************	
//                                 				INICIO DEL FORMULARIO INTERVALO FECHAS
//**********************************************************************************************************************************
	//creacion del formulario de datos de intervalo de fechas
	fieldsetdos = new Ext.form.FieldSet({
		width: 715,
		height: 65,
		title:"Intervalo de Fechas",
		style: 'position:absolute;left:10px;top:85px',
		cls :'fondo',
		items: [{
				style:'position:absolute;left:25px;top:10px',
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
				style:'position:absolute;left:480px;top:10px',
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
//----------------------------------------------------------------------------------------------------------------------------------
//**********************************************************************************************************************************	
//                                 				INICIO DEL FORMULARIO LISTADO DE PAGOS
//**********************************************************************************************************************************
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
	//creando store para el filtrar
	var filtrar = 	[
					['Comprobante','scg_dt_cmp.comprobante'],
					['Fecha','scg_dt_cmp.fecha'],
					['Cuenta','scg_dt_cmp.sc_cuenta'],
					['Procede','scg_dt_cmp.procede'],
					['Monto','scg_dt_cmp.monto'],
					['Debe - Haber','scg_dt_cmp.debhab'],
					['Proveedor','nompro'],
					['Beneficiario','nombene']
					]; // Arreglo que contiene los Documentos que se pueden controlar
	
	var stfiltrar = new Ext.data.SimpleStore({
		fields : ['etiqueta','valor'],
		data : filtrar
	});
	//fin creando store para el combo filtrar

	//creando objeto combo filtrar
	var cmbfiltrar = new Ext.form.ComboBox({
		store : stfiltrar,
		fieldLabel : 'Ordenar',
		labelSeparator : '',
		editable : false,
		emptyText:'--- Seleccione ---',
		displayField : 'etiqueta',
		valueField : 'valor',
		id : 'orden',
		width : 150,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local'
	});
//----------------------------------------------------------------------------------------------------------------------------------
	//creando store para el tipo de reporte
	var tiporep = 	[
					['Mayor Anal�tico','mayor'],
					['Resumen Mensual','resumen']
					]; // Arreglo que contiene los Documentos que se pueden controlar
	
	var sttiporep = new Ext.data.SimpleStore({
		fields : ['etiqueta','valor'],
		data : tiporep
	});
	//fin creando store para el combo filtrar

	//creando objeto combo filtrar
	var cmbtiporep = new Ext.form.ComboBox({
		store : sttiporep,
		fieldLabel : 'Reporte',
		labelSeparator : '',
		editable : false,
		emptyText:'--- Seleccione ---',
		displayField : 'etiqueta',
		valueField : 'valor',
		id : 'cmb_reporte',
		width : 150,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local'
	});
//----------------------------------------------------------------------------------------------------------------------------------

//Creacion del formulario pagos
	var Xpos = ((screen.width/2)-(380));
	frmPagos = new Ext.FormPanel({
	applyTo: 'formulario',
	width: 750,
	height: 400,
	title: "<H1 align='center'>Mayor Anal&#225;tico</H1>",
	frame: true,
	autoScroll: true,
	style: 'position:absolute;margin-left:'+Xpos+'px;margin-top:15px;',
	items: [
	        fieldset,fieldsetdos,
			{
			style:'position:absolute;left:15px;top:185px',
			layout:"column",
			border:false,
			items: [{
					layout:"form",
					border:false,
					labelWidth:60,
					items: [cmbfiltrar]
					}]
			},
			{
			style:'position:absolute;left:15px;top:220px',
			layout:"column",
			border:false,
			items: [{
					layout:"form",
					border:false,
					labelWidth:60,
					items: [cmbtiporep]
					}]
			},
			{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:15px;top:255px',
			items: [{
					layout: "form",
					border: false,
					labelWidth: 200,
					items: [{
							xtype: 'checkbox',
							labelSeparator :'',
							fieldLabel: 'Ocultar campo Detalles de los Comprobantes',
							id: 'chkocultar',
							inputValue:0,
							allowBlank:true
						}]
					}]
			},
			{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:15px;top:290px',
			items: [{
					layout: "form",
					border: false,
					labelWidth: 200,
					items: [{
							xtype: 'checkbox',
							labelSeparator :'',
							fieldLabel: 'Color Reporte',
							id: 'chkcolor',
							inputValue:0,
							allowBlank:true
						}]
					}]
			},
			{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:15px;top:320px',
			items: [{
					layout: "form",
					border: false,
					labelWidth: 50,
					items: [{
							xtype: 'checkbox',
							labelSeparator :'',
							fieldLabel: '',
							hideLabel: true,
							id: 'chkrecortar',
							inputValue:0,
							allowBlank:true
						}]
					}]
			},
			{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:85px;top:320px',
			items: [{
					layout: "form",
					border: false,
					items: [{
							xtype: 'textfield',
							labelSeparator :'',
							hideLabel: true,
							id: 'txtlencad',
							autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '3', onkeypress: "return keyRestrict(event,'0123456789');"},
							width: 40,
							formatonumerico:false,
							binding:true,
							hiddenvalue:'',
							defaultvalue:'',
							allowBlank:true,
						}]
					}]
			},
			{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:40px;top:326px',
			items: [{
					layout: "form",
					border: false,
					labelWidth: 80,
					items: [{
							xtype: 'label',
							text: 'Mostrar'
						}]
					}]
			},
			{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:130px;top:326px',
			items: [{
					layout: "form",
					border: false,
					labelWidth: 200,
					items: [{
							xtype: 'label',
							text: 'Caracteres en el concepto'
						}]
					}]
			},
			{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:425px;top:185px',
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
	irBuscarFormatoResumen();
	irBuscarFormato();
	irBuscarFormatoExcel();
});
//----------------------------------------------------------------------------------------------------------------------------------
//**********************************************************************************************************************************	
//                                  INICIO DE FUNCIONES PARA LOS CATALOGOS DE BUSQUEDA Y VALIDACIONES 
//**********************************************************************************************************************************
function irNuevo(){
	Ext.getCmp('chkcolor').setValue(true);
}
function irBuscarFormatoResumen()
{
	var myJSONObject =
	{
		'operacion'   : 'buscarFormatoResumen',
		'sistema'	  : 'SCG',
		'seccion'     : 'REPORTE',
		'variable'    : 'ESTADO_RESULTADO',
		'valor'		  : 'sigesp_scg_rpp_estado_resultado.php',
		'tipo'		  : 'C'
	};	
	var ObjSon=Ext.util.JSON.encode(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request(
	{
		url: '../../controlador/scg/sigesp_ctr_scg_mayor_analitico.php',
		params: parametros,
		method: 'POST',
		success: function (result, request)
		{ 
			formato_resumen = result.responseText;			
		},
		failure: function (result, request){ 
			Ext.MessageBox.alert('Error', 'error al accesar al sistema.'); 
		}
	})
}
function irBuscarFormato()
{
	var myJSONObject =
	{
		'operacion'   : 'buscarFormato',
		'sistema'	  : 'SCG',
		'seccion'     : 'REPORTE',
		'variable'    : 'MAYOR_ANALITICO',
		'valor'		  : 'sigesp_scg_rpp_mayor_analitico_pdf.php',
		'tipo'		  : 'C'
	};	
	var ObjSon=Ext.util.JSON.encode(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request(
	{
		url: '../../controlador/scg/sigesp_ctr_scg_mayor_analitico.php',
		params: parametros,
		method: 'POST',
		success: function (result, request)
		{ 
			formato = result.responseText;			
		},
		failure: function (result, request){ 
			Ext.MessageBox.alert('Error', 'error al accesar al sistema.'); 
		}
	})
}
function irBuscarFormatoExcel()
{
	var myJSONObject =
	{
		'operacion'   : 'buscarFormatoExcel',
		'sistema'	  : 'SCG',
		'seccion'     : 'REPORTE',
		'variable'    : 'MAYOR_ANALITICO_EXCEL',
		'valor'		  : 'sigesp_scg_rpp_mayor_analitico_excel.php',
		'tipo'		  : 'C'
	};	
	var ObjSon=Ext.util.JSON.encode(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request(
	{
		url: '../../controlador/scg/sigesp_ctr_scg_mayor_analitico.php',
		params: parametros,
		method: 'POST',
		success: function (result, request)
		{ 
			formato_excel = result.responseText;			
		},
		failure: function (result, request){ 
			Ext.MessageBox.alert('Error', 'error al accesar al sistema.'); 
		}
	})
}
//**********************************************************************************************************************************	
//                                  						BOTONES 
//**********************************************************************************************************************************
function irImprimir()
{
	if (Ext.getCmp('tipoimp').getValue()=='P')
	{
		if(Ext.getCmp('cmb_reporte').getValue() == 'resumen')
		{
			cuentadesde  = Ext.getCmp('sc_cuenta1').getValue();		
			cuentahasta  = Ext.getCmp('sc_cuenta2').getValue();	
			sc_cuenta    = "---";
			if(cuentadesde!=cuentahasta)
			{
				Ext.Msg.hide();
				Ext.MessageBox.alert('Error', 'El reporte resumido se genera para una sola cuenta'); 
			}
			else
			{
				sc_cuenta=cuentadesde;
			}	
			if(sc_cuenta=="")
			{
				Ext.Msg.hide();
				Ext.MessageBox.alert('Error', 'Debe seleccionar una cuenta'); 
			}
			else if (sc_cuenta != "---")
			{
				pagina="reportes/"+formato_resumen+"?sc_cuenta="+sc_cuenta;
				window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
			}
		}
		else
		{
			var fecdes2 = Ext.getCmp('fecdesde').getValue();
			var fechas2 = Ext.getCmp('fechasta').getValue();
			ld_fecdesde = fecdes2.format(Date.patterns.fechacorta);
			ld_fechasta = fechas2.format(Date.patterns.fechacorta);
			ls_cuentadesde  = Ext.getCmp('sc_cuenta1').getValue();	
			ls_cuentahasta  = Ext.getCmp('sc_cuenta2').getValue();
			ls_orden=Ext.getCmp('orden').getValue();
			tiporeporte=Ext.getCmp('cmb_reporte').getValue();
			
			if (Ext.getCmp('chkrecortar').checked)
			{
				li_recortar='1';
			}
			else
			{
				li_recortar='0';
			}
			
			if (Ext.getCmp('chkcolor').checked)
			{
				jcolor='1';
			}
			else
			{
				jcolor='0';
			}
			
			li_longitud=Ext.getCmp('txtlencad').getValue();
			
			if(Ext.getCmp('chkocultar').checked==true)
			{
				ocultar=1;
			}
			else
			{
				ocultar=0;
			}
			
			if(ls_cuentadesde > ls_cuentahasta)
			{
				Ext.Msg.hide();
				Ext.MessageBox.alert('Error', 'Intervalo de cuentas incorrecto.'); 
				Ext.getCmp('sc_cuenta1').setValue('');
				Ext.getCmp('sc_cuenta2').setValue('');
			}
			else
			{
				pagina="reportes/"+formato+"?fecdes="+ld_fecdesde+"&fechas="+ld_fechasta+"&orden="+ls_orden+"&ocultar="+ocultar;
				pagina=pagina+"&cuentadesde="+ls_cuentadesde+"&cuentahasta="+ls_cuentahasta+"&tiporeporte="+tiporeporte+"&recortar="+li_recortar+"&lenconcepto="+li_longitud+"&color="+jcolor;
				window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
			}
		}
	}
	else
	{
		var fecdes2 = Ext.getCmp('fecdesde').getValue();
		var fechas2 = Ext.getCmp('fechasta').getValue();
		ld_fecdesde = fecdes2.format(Date.patterns.fechacorta);
		ld_fechasta = fechas2.format(Date.patterns.fechacorta);
		ls_cuentadesde  = Ext.getCmp('sc_cuenta1').getValue();	
		ls_cuentahasta  = Ext.getCmp('sc_cuenta2').getValue();
		ls_orden=Ext.getCmp('orden').getValue();
		tiporeporte=Ext.getCmp('cmb_reporte').getValue();
		
		if (Ext.getCmp('chkrecortar').checked)
		{
			li_recortar='1';
		}
		else
		{
			li_recortar='0';
		}
		
		li_longitud=Ext.getCmp('txtlencad').getValue();
		
		if(ls_cuentadesde>ls_cuentahasta)
		{
			Ext.Msg.hide();
			Ext.MessageBox.alert('Error', 'Intervalo de cuentas incorrecto.'); 
			Ext.getCmp('sc_cuenta1').setValue('');
			Ext.getCmp('sc_cuenta2').setValue('');
		}
		else
		{
			pagina="reportes/"+formato_excel+"?fecdes="+ld_fecdesde+"&fechas="+ld_fechasta+"&orden="+ls_orden;
			pagina=pagina+"&cuentadesde="+ls_cuentadesde+"&cuentahasta="+ls_cuentahasta+"&tiporeporte="+tiporeporte+"&recortar="+li_recortar+"&lenconcepto="+li_longitud;
			window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
		}
	}
}
