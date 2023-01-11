/***********************************************************************************
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

barraherramienta    = true; 
var Actualizar      = null
var banderaNuevo    = false; 															// Indicador si se posee un Metodo Nuevo distinto al Original de funciones.js
var banderaGrabar 	= false;															// Indicador si se posee un Metodo de Guardar distinto al Original de funciones.js
var banderaEliminar = false;
var ruta            ='../../controlador/cfg/sigesp_ctr_cfg_scg_planinstitucional.php'; 	// Ruta del Controlador de la Pantalla

var formcont =empresa["formcont"];
formcont=replaceAll(formcont,'-','');
formcont=replaceAll(formcont,' ','');
var longitud=formcont.length;

var Campos =new Array(
	        ['sc_cuenta','novacio|'],
	        ['denominacion','novacio|'],
	        ['cueproacu',''],
	        ['dencuentaacum','']
)
	    
Ext.onReady(function()
{
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	function catalogoPlanUnico()
	{
		if (Actualizar == null)
		{
			var rePlanUnico = Ext.data.Record.create([
				{name: 'sc_cuenta'},
				{name: 'denominacion'}
			]);
										
			var dsPlanUnico =  new Ext.data.Store({
				reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},rePlanUnico)
			});
			
			var formBusquedaPlan = new Ext.FormPanel({
				frame:true,
				title: 'B&uacute;squeda',
				bodyStyle:'padding:5px 5px 0',
				width: 630,
				height:150,
				items: [{
					xtype:"textfield",
					fieldLabel: 'Cuenta',
					labelSeparator : '',
					id:'codcue',
					width: 120,
					autoCreate: {tag: 'input', type: 'text', maxlength: 25, onkeypress: "return keyRestrict(event,'0123456789');"},
					changeCheck: function(){
						var v = this.getValue();
						dsPlanUnico.filter('sc_cuenta',v);
					},
					initEvents : function(){
						AgregarKeyPress(this);
					}
				},{
					xtype:"textfield",
					fieldLabel: 'Denominaci&#243;n',
					id:'dencue',
					labelSeparator : '',
					width: 400,
					autoCreate: {tag: 'input', type: 'text', maxlength: 254, onkeypress: "return keyRestrict(event,'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=°;:[]{}·ÈÌÛ˙¡…Õ”⁄ ');"},
					changeCheck: function(){
						var v = this.getValue();
						dsPlanUnico.filter('denominacion',v,true,false);
					},
					initEvents : function(){
						AgregarKeyPress(this);
					}
				},{
				xtype: 'button',
				fieldLabel: '',
				id: 'btbuscar',
				text: 'Buscar',
				style:'position:absolute;left:450px;top:80px;',
				iconCls: 'menubuscar',
				handler: function(){
					obtenerMensaje('procesar','','Buscando Datos');
								
					var JSONObject = {
						'oper'   : 'catalogo',
						'codcue' : Ext.getCmp('codcue').getValue(),
						'dencue' : Ext.getCmp('dencue').getValue()
					}
						
					var ObjSon = JSON.stringify(JSONObject);
					var parametros = 'ObjSon='+ObjSon; 
					Ext.Ajax.request({
						url : '../../controlador/cfg/sigesp_ctr_cfg_scg_planpatrimonial.php',
						params : parametros,
						method: 'POST',
						success: function ( resultado, request)
						{
							Ext.Msg.hide();
							var datos = resultado.responseText;
							var objData = eval('(' + datos + ')');
							if(objData!='')
							{
								if(objData.raiz == null || objData.raiz =='')
								{
									Ext.MessageBox.show({
										title:'Advertencia',
										msg:'No existen datos para mostrar',
										buttons: Ext.Msg.OK,
										icon: Ext.MessageBox.WARNING
									});
								}
								else
								{
									dsPlanUnico.loadData(objData);
									Actualizar=null;
								}
							}
						}//fin del success	
					});//fin del ajax request
				}
			}]
			});
			
			var gridPlanUnico = new Ext.grid.GridPanel({
				width:760,
				height:370,
				tbar: formBusquedaPlan,
				autoScroll:true,
				border:true,
				ds: dsPlanUnico,
				cm: new Ext.grid.ColumnModel([
					  {header: "Cuenta", width: 20, sortable: true,   dataIndex: 'sc_cuenta'},
					  {header: "Denominaci&#243;n", width: 80, sortable: true, dataIndex: 'denominacion'}
				]),
				stripeRows: true,
				viewConfig: {forceFit:true}
			});
			
			gridPlanUnico.on({
				'celldblclick': {
					fn: function(){
						var registro = gridPlanUnico.getSelectionModel().getSelected();
						Ext.getCmp('sc_cuenta').setValue(registro.get('sc_cuenta'));
						Ext.getCmp('denominacion').setValue(registro.get('denominacion'));
						venCatalogoPlanUnico.destroy();		
					}
				}
			});
			
			var venCatalogoPlanUnico = new Ext.Window({
				title: 'Cat&#225;logo de cuentas del plan &#250;nico',
				autoScroll:true,
				width:800,
				height:450,
				modal: true,
				closable:false,
				plain: false,
				items:[gridPlanUnico],
				buttons: [{
							text:'Aceptar',  
							handler: function(){
								var registro = gridPlanUnico.getSelectionModel().getSelected();
								Ext.getCmp('sc_cuenta').setValue(registro.get('sc_cuenta'));
								Ext.getCmp('denominacion').setValue(registro.get('denominacion'));
								Ext.getCmp('sc_cuenta').setDisabled(false);
								venCatalogoPlanUnico.destroy();                      
							}
						},{
							text: 'Salir',
							handler: function(){
								venCatalogoPlanUnico.destroy();
							}
						}]
			});
			venCatalogoPlanUnico.show();
		}	
 	}
	
 	//creando datastore y columnmodel para el catalogo de bancos
	var reCuentaAcum = Ext.data.Record.create([
		{name: 'codcueacum'},
		{name: 'dencueacum'}
	]);
	
	var dsCuentaAcum =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reCuentaAcum)
	});
						
	var cmCuentaAcum = new Ext.grid.ColumnModel([
		{header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'codcueacum'},
		{header: "Denominaci&#243;n", width: 40, sortable: true, dataIndex: 'dencueacum'}
	]);
	//fin creando datastore y columnmodel para el catalogo de bancos
	
	//componente campocatalogo para el campo banco
	var comCampoCatCuentaAcum = new com.sigesp.vista.comCampoCatalogo({
		titvencat: 'Cat&#225;logo de Cuentas Contables de Provisiones Acumuladas y Reservas Tecnicas o de Depreciaci&#243;n y Amortizaci&#243;n Acumulada',
		anchoformbus: 550,
		altoformbus:100,
		anchogrid: 550,
		altogrid: 400,
		anchoven: 600,
		altoven: 480,
		anchofieldset:800,
		datosgridcat: dsCuentaAcum,
		colmodelocat: cmCuentaAcum,
		rutacontrolador:'../../controlador/cfg/sigesp_ctr_cfg_scg_planinstitucional.php',
		parametros: "ObjSon={'oper': 'catalogoCuentaAcum'}",
		arrfiltro:[{etiqueta:'C&#243;digo',id:'codcueacu',valor:'codcueacum',ancho:150,longitud:'25',anyMatch:false},
				   {etiqueta:'Descripci&#243;n',id:'descueacu',valor:'dencueacum',ancho:350,longitud:'254'}],
		posicion:'position:absolute;left:-10px;top:50px',
		tittxt:'Cuenta Contable de Provisiones Acumuladas y Reservas T&#233;cnicas o de Depreciaci&#243;n y Amortizaci&#243;n Acumulada',
		idtxt:'cueproacu',
		campovalue:'codcueacum',
		anchoetiquetatext:320,
		anchotext:120,
		anchocoltext:0.60,
		idlabel:'dencuentaacum',
		labelvalue:'dencueacum',
		anchocoletiqueta:1.35,
		anchoetiqueta:250,
		tipbus:'L'
	});
	//fin componente campocatalogo para el campo banco
 	
 	
 	
	var Xpos = ((screen.width/2)-(350));
	formPlanCuentaInstitucional = new Ext.FormPanel({
		applyTo: 'formulario_plan_cta_institucional',
		width: 650,
		height: 160,
		title: 'Plan de cuentas institucional',
		frame:true,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:50px;',
		items: [{
					xtype: 'textfield',
					fieldLabel: 'C&#243;digo contable',
					name: 'Codigo Contable',
					id: 'sc_cuenta',
					maxLength: longitud,
					width:longitud*10,
					autoCreate:{tag: 'input', 
						type: 'text', 
						size: longitud,
						autocomplete: 'off',
						onkeypress: "return keyRestrict(event,'0123456789');",
						maxlength: longitud},
						disabled:true,
						listeners:{
			            'blur' : function(campo){
							var valor = "";
							var formcont =empresa["formcont"];
							formcont=replaceAll(formcont,'-','');
							formcont=replaceAll(formcont,' ','');
							var longitud = formcont.length;
							valor = rellenarCampoCerosDerecha(String.trim(campo.getValue()),longitud);
							campo.setValue(valor);
						}
		            }
				},{
					xtype:'button',
					iconCls: 'menubuscar',
					style:'position:absolute;left:320px;top:0px;',
					handler:catalogoPlanUnico
				},{
					xtype:'label',
					style:'position:absolute;left:360px;top:0px;',
					text:empresa['formcont']
				},{
					xtype: 'textfield',
					fieldLabel: 'Denominaci&#243;n',
					name: 'denominacion',
					id: 'denominacion',
					autoCreate: {tag: 'input', type: 'text', maxlength: 254, onkeypress: "return keyRestrict(event,'0123456789·ÈÌÛ˙¡…Õ”⁄abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!°;: ');"},
					width: 500
				},
				comCampoCatCuentaAcum.fieldsetCatalogo
		]
	});
		
});

function irBuscar()
{
	//creando datastore y columnmodel para el catalogo de agencias
	var reCuenta = Ext.data.Record.create([
		{name: 'sc_cuenta'},
		{name: 'denominacion'}
	]);
	
	var dsCuenta =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reCuenta)
	});
						
	var cmCuenta = new Ext.grid.ColumnModel([
        {header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'sc_cuenta'},
        {header: "Denominaci&#243;n", width: 80, sortable: true, dataIndex: 'denominacion'}
    ]);
	//fin creando datastore y columnmodel para el catalogo de agencias
	
	var comCatalogoPlanUnico = new com.sigesp.vista.comCatalogo({
		titvencat: 'Plan de Cuentas Institucional',
		anchoformbus: 570,
		altoformbus:150,
		anchogrid: 570,
		altogrid: 430,
		anchoven: 600,
		altoven: 500,
		datosgridcat: dsCuenta,
		colmodelocat: cmCuenta,
		arrfiltro:[{etiqueta:'C&#243;digo',id:'codcuenta',valor:'sc_cuenta',ancho:150,longitud:'25',anyMatch:false},
				   {etiqueta:'Denominaci&#243;n',id:'dencuenta',valor:'denominacion',ancho:350,longitud:'254'}],
		rutacontrolador:'../../controlador/scg/sigesp_ctr_scg_cuentacontable.php',
		parametros: "ObjSon={'operacion': 'catalogo'",
		tipbus:'P',
		setdatastyle:'F',
		formulario:formPlanCuentaInstitucional
	});
	
	comCatalogoPlanUnico.mostrarVentana();
	Actualizar=true;
	Ext.getCmp('sc_cuenta').setDisabled(true);
}

function irNuevo()
{
	limpiarFormulario(formPlanCuentaInstitucional);
	Actualizar=null;
	Ext.getCmp('sc_cuenta').setDisabled(false);	
}

function irCancelar()
{
	irNuevo();
	Ext.getCmp('sc_cuenta').setDisabled(true);	
}

function irGuardar()
{
	if(Actualizar==null)
	{
		operacion='incluir';
		mensaje='incluido';
	}
	else
	{	
		operacion='actualizar';
		mensaje='modificado';			
	}
	if(validarObjetos2()==false)
	{
		return false;
	}
	else
	{
		var Json    = cargarJson(operacion);
		try
		{
			var objjson = Ext.util.JSON.decode(Json);
			if (typeof(objjson) == 'object')
			{
				var parametros = 'ObjSon=' + Json;
				obtenerMensaje('procesar','','Guardando Datos');
				Ext.Ajax.request({
					url : ruta,
					params : parametros,
					method: 'POST',	
					success: function ( resultad, request )
					{ 
						Ext.Msg.hide();
						var datos = resultad.responseText;
						var respuesta = datos.split("|");
						if (respuesta[1] == '1')
						{
							Ext.MessageBox.alert('mensaje','Registro '+mensaje + ' con &#233;xito');
							Actualizar=null;
						}
						else
						{
							if(respuesta[1] == '-10')
							{
								Ext.MessageBox.alert('Error', 'La combinaci&#243;n afectaci&#243;n presupuestaria y tipo ya existe');
							}
							else
							{
								if(respuesta[0]!='')
								{
									Ext.MessageBox.alert('Error', respuesta[0]);
								}
								else
								{
									Ext.MessageBox.alert('Error', 'Ocurri&#243; un error el registro no pudo ser '+mensaje);
								}
							}
						}
						irNuevo();
					},
					failure: function (result, request)
					{ 
						Ext.Msg.hide();
						Ext.MessageBox.alert('Error', resultad.responseText);
						mascara.hide();
					}
				});
			}
		}
		catch(e)
		{
			//no imprimo la excepcion
		}
	}
}

function irEliminar()
{
	function eliminarRegistro(btn)
	{
		if(btn=='yes')
		{
			var Json    = cargarJson('eliminar');
			try
			{
				var objjson = Ext.util.JSON.decode(Json);
				if (typeof(objjson) == 'object')
				{
					var parametros = 'ObjSon=' + Json;
					obtenerMensaje('procesar','','Eiminando Datos');
					Ext.Ajax.request({
						url : ruta,
						params : parametros,
						method: 'POST',	
						success: function ( resultad, request )
						{ 
							Ext.Msg.hide();
							var datos = resultad.responseText;
							var Registros = datos.split("|");
						 	if (Registros[1] == '1')
							{
						 		Ext.MessageBox.alert('mensaje','Registro Eliminado con &#233;xito');
								Actualizar=null;
							}
							else
							{
								if(Registros[1]=='-9')
								{
									Ext.MessageBox.alert('Error', 'El registro no puede ser eliminado, esta vinculado con otros registros');
							  	}
								else
								{
									if (Registros[0]!='')
									{
										Ext.MessageBox.alert('Error', Registros[0]);
								  	}
									else
									{
										Ext.MessageBox.alert('Error', 'El registro no pudo ser eliminado');
								  	}
								}
							}
							irNuevo();
						},
						failure: function (result, request)
						{ 
							Ext.Msg.hide();
							Ext.MessageBox.alert('Error', resultad.responseText);
						}
					});
				}
			}
			catch(e)
			{
				//no imprimo la excepcion
			}
		}
	}
	Ext.MessageBox.confirm('Confirmar', '&#191;Desea eliminar este registro&#63;', eliminarRegistro);
}