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
var ruta ='../../controlador/scg/sigesp_ctr_scg_movimientos_mes.php'; //ruta del controlador
var ruta2 ='../../controlador/scg/sigesp_ctr_scg_balance_comprobacion.php'; //ruta del controlador
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
//--------------------------------------------------------------------------------------------
	var re_cencos = Ext.data.Record.create([
  		{name: 'codcencos'},
  		{name: 'denominacion'}
  	]);
  	
  	var dscencos=  new Ext.data.Store({
  	    reader: new Ext.data.JsonReader({
  		root: 'raiz',             
  		id: "id"},re_cencos)
  	});
  						
  	var cmcatcencos = new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'codcencos'},
          {header: "Denominaci&#243;n", width: 40, sortable: true, dataIndex: 'denominacion'}
  	]);
  	//componente campocatalogo para el campo cuentas contables
  	
  	comcampocatcencosdes = new com.sigesp.vista.comCampoCatalogo({
  			titvencat: "<H1 align='center'>Cat&#225;logo de Centro de Costos Contable</H1>",
  			anchoformbus: 450,
  			altoformbus:100,
  			anchogrid: 450,
  			altogrid: 400,
  			anchoven: 500,
  			altoven: 400,
  			anchofieldset: 850,
  			datosgridcat: dscencos,
  			colmodelocat: cmcatcencos,
  			rutacontrolador:'../../controlador/scg/sigesp_ctr_scg_cuentas.php',
  			parametros: "ObjSon={'operacion': 'buscarCentroCostos'}",
  			arrfiltro:[{etiqueta:'Codigo',id:'codicencos',valor:'codcencos',longitud:'10'},
  					   {etiqueta:'Denominaci&#243;n',id:'denominacioni',valor:'denominacion'}],
  			posicion:'position:absolute;left:25px;top:5px',
  			tittxt:'Desde',
  			idtxt:'codcencosdes',
  			campovalue:'codcencos',
  			anchoetiquetatext:35,
  			anchotext:150,
  			anchocoltext:0.23,
  			idlabel:'denominaciond',
  			labelvalue:'',
  			anchocoletiqueta:0.50,
  			anchoetiqueta:0,
  			tipbus:'L',
  			binding:'C',
  			hiddenvalue:'',
  			defaultvalue:'',
  			allowblank:false,
			validarMostrar:1,
			fnValidarMostrar: validarCentroCosContable,
			msjValidarMostrar: 'Debe estar configurado para trabajar con Centro de Costos'
  	});

  	comcampocatcencoshas = new com.sigesp.vista.comCampoCatalogo({
  			titvencat: "<H1 align='center'>Cat&#225;logo de Centro de Costos Contable</H1>",
  			anchoformbus: 450,
  			altoformbus:100,
  			anchogrid: 450,
  			altogrid: 400,
  			anchoven: 500,
  			altoven: 400,
  			anchofieldset: 850,
  			datosgridcat: dscencos,
  			colmodelocat: cmcatcencos,
  			rutacontrolador:'../../controlador/scg/sigesp_ctr_scg_cuentas.php',
  			parametros: "ObjSon={'operacion': 'buscarCentroCostos'}",
  			arrfiltro:[{etiqueta:'Codigo',id:'codicencos',valor:'codcencos',longitud:'10'},
  					   {etiqueta:'Denominaci&#243;n',id:'denominacioni',valor:'denominacion'}],
  			posicion:'position:absolute;left:440px;top:5px',
  			tittxt:'Hasta',
  			idtxt:'codcencoshas',
  			campovalue:'codcencos',
  			anchoetiquetatext:35,
  			anchotext:150,
  			anchocoltext:0.23,
  			idlabel:'denominacionh',
  			labelvalue:'',
  			anchocoletiqueta:0.50,
  			anchoetiqueta:0,
  			tipbus:'L',
  			binding:'C',
  			hiddenvalue:'',
  			defaultvalue:'',
  			allowblank:false,
			validarMostrar:1,
			fnValidarMostrar: validarCentroCosContable,
			msjValidarMostrar: 'Debe estar configurado para trabajar con Centro de Costos'
  	});
//--------------------------------------------------------------------------------------------
// Combo del A�o
var anio = Ext.data.Record.create([
	    {name:'anuales'}
	]);

	var stanio =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "anuales"},anio)
	});

//creando objeto combo filtrar
	var cmbaniodes = new Ext.form.ComboBox({
		store : stanio,
		fieldLabel : 'A&#241;o',
		labelSeparator : '',
		editable : false,
		displayField : 'anuales',
		valueField : 'anuales',
		id : 'anio',
		width : 90,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		emptyText:'Seleccione',
		mode : 'local'
	});

//--------------------------------------------------------------------------------------------------------------------------------	
// Combo de nivel
	var nivel = [ 
				 [ '1', '01' ], 
	             [ '2', '02' ],
				 [ '3', '03' ],
				 [ '4', '04' ],
				 [ '5', '05' ],
				 [ '6', '06' ],
				 [ '7', '07' ],
				 [ '8', '08' ],
				 [ '9', '09' ],
				 [ '10', '10' ],
			];
	
	var stnivel = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : nivel
	}); //Fin de store para el tipo de impresion
	
	//creando objeto combo filtrar
	var cmbnivel = new Ext.form.ComboBox({
		store : stnivel,
		fieldLabel : 'Nivel',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'cmbnivel',
		width : 50,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local'
	});
	
	cmbnivel.setValue('01');

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
		width: 700,
		height: 70,
		title: "Periodo",
		style: 'position:absolute;left:10px;top:125px', //150
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
//**********************************************************************************************************************************	
//                                 				INICIO DEL FORMULARIO ORDENADO POR
//**********************************************************************************************************************************
//creacion del formulario de datos de centro de costos
	fieldsetdos = new Ext.form.FieldSet({
		width: 700,
		height: 70,
		title: "Centros de Costos",
		style: 'position:absolute;left:10px;top:205px', //150
		cls :'fondo',
		items: [comcampocatcencosdes.fieldsetCatalogo,
				comcampocatcencoshas.fieldsetCatalogo
		]
	});	
//----------------------------------------------------------------------------------------------------------------------------------
//creacion del formulario de datos de proveedor / beneficiario
	fieldsettres = new Ext.form.FieldSet({
		width: 700,
		height: 70,
		title: "Cuentas Contables",
		style: 'position:absolute;left:10px;top:45px', //150
		cls :'fondo',
		items: [{
				style:'position:absolute;left:25px;top:15px',
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
				style:'position:absolute;left:235px;top:15px',
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
				style:'position:absolute;left:450px;top:15px',
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
				style:'position:absolute;left:660px;top:15px',
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
//creacion del formulario de datos de estado resultado
	fieldsetcuatro = new Ext.form.FieldSet({
		width: 700,
		height: 65,
		title: "Ordenar",
		style: 'position:absolute;left:10px;top:285px', //150
		cls :'fondo',
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:110px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 100,
						items:[{
								xtype: "radiogroup",
								fieldLabel: "",
								labelSeparator:"",
								binding:true,
								hiddenvalue:'',
								defaultvalue:'',	
								columns: [200,200],
								id:'rdorden',
								items: [
								        {boxLabel: 'Ascendente', name: 'rdorden1',inputValue: '0',checked:true},
								        {boxLabel: 'Descendente', name: 'rdorden1', inputValue: '1'}
								        ]
							}]
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
//creando funcion para llenar el combo
function llenarComboAnio()
{
	var myJSONObject ={
			"operacion": 'comboanio'
	};	
	var ObjSon=JSON.stringify(myJSONObject);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : ruta,
		params : parametros,
		method: 'POST',
		success: function (resultado, request) { 
			var datosc = resultado.responseText;  
			if(datosc!='')
			{
				var DatosAnio = eval('(' + datosc + ')');
				stanio.loadData(DatosAnio);
			}
		}
	});
}
	//cmbformato.setValue('1');

//----------------------------------------------------------------------------------------------------------------------------------	
//Creacion del formulario pagos
	var Xpos = ((screen.width/2)-(380));
	frmPagos = new Ext.FormPanel({
	applyTo: 'formulario',
	width: 733,
	height: 450,
	title: "<H1 align='center'>Movimientos del Mes</H1>",
	frame: true,
	autoScroll: true,
	style: 'position:absolute;margin-left:'+Xpos+'px;margin-top:15px;',
	items: [
	        fieldset,fieldsetdos,fieldsettres,fieldsetcuatro,
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
			style: 'position:absolute;left:240px;top:10px',
			border:false,
			items: [{
					layout:"form",
					border:false,
					labelWidth:100,
					items: [cmbtiporeporte]
					}]
			},
			{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:240px;top:375px',
			items: [{
					layout: "form",
					border: false,
					labelWidth: 200,
					items: [{
							xtype: 'checkbox',
							labelSeparator :'',
							fieldLabel: 'Mostrar Cuentas sin Movimiento',
							id: 'chktodas',
							inputValue:0,
							allowBlank:true
						}]
					}]
			}]	
	});
	irNuevo();
	irBuscarFormato();
	llenarComboAnio();
	//llenarComboMesDesAnio();
});
//----------------------------------------------------------------------------------------------------------------------------------
//**********************************************************************************************************************************	
//                                  INICIO DE FUNCIONES PARA LOS CATALOGOS DE BUSQUEDA Y VALIDACIONES 
//**********************************************************************************************************************************
function irNuevo(){
	var myJSONObject = {
			"operacion":"verificar_estatus_estcencos" 
		};
			
	var ObjSon=Ext.util.JSON.encode(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request({
		url: ruta,
		params: parametros,
		method: 'POST',
		success: function ( result, request ) 
		{ 
			cencos = result.responseText;
			if (cencos != "")
			{
				Ext.getCmp('estcencos').setValue(cencos);
			}
		},
		failure: function ( result, request){ 
				Ext.MessageBox.alert('Error', 'El Registro no pudo ser '+mensaje); 
		}
	});
}

function validarCentroCosContable()
{
	var unidadOk = true;
	if(cencos==0){
		unidadOk = false;
	}
	
	return unidadOk;
}

function irBuscarFormato()
{
	var myJSONObject =
	{
		'operacion'   : 'buscarFormato',
		'sistema'	  : 'SCG',
		'seccion'     : 'REPORTE',
		'variable'    : 'BALANCE_GENERAL',
		'valor'		  : 'sigesp_scg_rpp_balance_general.php',
		'tipo'		  : 'C'
	};	
	var ObjSon=Ext.util.JSON.encode(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request(
	{
		url: ruta,
		params: parametros,
		method: 'POST',
		success: function (result, request)
		{ 
			formato1 = result.responseText;			
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
		ls_cuentadesde  = Ext.getCmp('sc_cuenta1').getValue(); 
		ls_cuentahasta  = Ext.getCmp('sc_cuenta2').getValue(); 
		ls_mes=Ext.getCmp('mesdes').getValue(); 
		ls_agno=Ext.getCmp('anio').getValue(); 
		ls_periodo="01/"+ls_mes+"/"+ls_agno;
		//ls_periodo = fecdes2.format(Date.patterns.fechacorta);
		
		if(cencos=='1')
		{
			ls_costodesde  = Ext.getCmp('codcencosdes').getValue(); 
			ls_costohasta  = Ext.getCmp('codcencosdes').getValue(); 
		}
		else
		{
			ls_costodesde  = '';
			ls_costohasta  = '';
		}
		
		var radio= Ext.getCmp('rdorden');
		for (var j = 0; j < radio.items.length; j++)
		{
		  if (radio.items.items[j].checked)
		  {
			orden = radio.items.items[j].inputValue;
			break;
		  }
		} 
		
		if (Ext.getCmp('chktodas').checked)
		{
			mostrar='1';
		}
		else
		{
			mostrar='0';
		}
		
		if(ls_cuentadesde>ls_cuentahasta)
		{
		    Ext.Msg.hide();
			Ext.MessageBox.alert('Error', 'Intervalo de cuentas incorrecto.'); 
			Ext.getCmp('sc_cuenta1').setValue('');
			Ext.getCmp('sc_cuenta2').setValue('');
		}
		else
		{
			pagina="reportes/sigesp_scg_rpp_movimientos_mes.php?fecha="+ls_periodo+"&cuentadesde="+ls_cuentadesde+
			       "&cuentahasta="+ls_cuentahasta+"&orden="+orden+"&mostrar="+mostrar+
			       "&costodesde="+ls_costodesde+"&costohasta="+ls_costohasta;
			window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
		}
	}
	else
	{
		ls_cuentadesde  = Ext.getCmp('sc_cuenta1').getValue(); 
		ls_cuentahasta  = Ext.getCmp('sc_cuenta2').getValue(); 
		ls_mes=Ext.getCmp('mesdes').getValue(); 
		ls_agno=Ext.getCmp('anio').getValue(); 
		ls_periodo="01/"+ls_mes+"/"+ls_agno;
		//ls_periodo = fecdes2.format(Date.patterns.fechacorta);
		
		if(cencos=='1')
		{
			ls_costodesde  = Ext.getCmp('codcencosdes').getValue(); 
			ls_costohasta  = Ext.getCmp('codcencosdes').getValue(); 
		}
		else
		{
			ls_costodesde  = '';
			ls_costohasta  = '';
		}
		
		if(ls_cuentadesde>ls_cuentahasta)
		{
		    Ext.Msg.hide();
			Ext.MessageBox.alert('Error', 'Intervalo de cuentas incorrecto.'); 
			Ext.getCmp('sc_cuenta1').setValue('');
			Ext.getCmp('sc_cuenta2').setValue('');
		}
		else
		{
			pagina="reportes/sigesp_scg_rpp_movimientos_mes_excel.php?fecha="+ls_periodo+"&cuentadesde="+ls_cuentadesde+
			       "&cuentahasta="+ls_cuentahasta+
			       "&costodesde="+ls_costodesde+"&costohasta="+ls_costohasta;
			window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
		}	
	}
}
