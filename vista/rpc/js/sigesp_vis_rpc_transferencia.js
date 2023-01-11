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

barraherramienta = true;
var gridSolicitud=null;
Ext.onReady(function()
{
	
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	//-------------------------------------------------------------------------------------------------------------------------	
	
	//Creando el campo de cuentas contables para las solicitudes a pagar
	var reCuenta = Ext.data.Record.create([
			{name: 'sc_cuenta'},
			{name: 'denominacion'},
      		{name: 'status'} //campo obligatorio
	]);
		
	//componente catalogo de cuentas contables
	comcampocatcuentacontable = new com.sigesp.vista.comCatalogoCuentaContable({
		idComponente:'scg',
		anchofieldset:700,
		reCatalogo: reCuenta,
		soloCatalogo: false,
		rutacontrolador:'../../controlador/scg/sigesp_ctr_scg_comcatcuentacontable.php',
		parametros: "ObjSon={'operacion': 'buscarCuentaContables'",
		valorStatus: 'C',
		datosadicionales: 0,
		posicion:'position:absolute;left:5px;top:15px', 
		tittxt:'Cuenta Contable',
		idtxt:'sc_cuenta',
		campovalue:'sc_cuenta',
		anchoetiquetatext:100,
		anchotext:120,
		anchocoltext:0.34, 
		idlabel:'denominacion',
		labelvalue:'denominacion',
		anchocoletiqueta:0.55, 
		anchoetiqueta:200,
		binding:'C',
		hiddenvalue:'',
		defaultvalue:'',
		allowblank:false,
		datosocultos:0,
		validarMostrar:0,
		numFiltroNoVacio: 1
	});
	//fin componente catalogo de proveedores
	
	//-------------------------------------------------------------------------------------------------------------------------	
	
	var botbusquedaDesde = new Ext.Button({
		id: 'botbusquedaDesde',
		iconCls: 'menubuscar',
		style:'position:absolute;left:180px;top:95px',
		listeners:{
	        'click' : function(boton)
	        {
				//componente catalogo de proveedores
				var reCatProveedor = Ext.data.Record.create([
					{name: 'codper'}, //campo obligatorio                             
					{name: 'cedper'},  //campo obligatorio
					{name: 'nomper'},  //campo obligatorio
					{name: 'apeper'}   //campo obligatorio
				]);
				
				var comcampocatpersonal = new com.sigesp.vista.comCatalogoPersonal({
					reCatalogo: reCatProveedor,
					rutacontrolador:'../../controlador/sno/sigesp_ctr_sno_comcatpersonal.php',
					parametros: "ObjSon={'operacion': 'buscarPersonal'",
					soloCatalogo: true,
					arrSetCampo:[{campo:'ced_benedesde',valor:'cedper'}],
					numFiltroNoVacio: 1,
					idComponente:'Desde'
				});
				//fin componente catalogo de proveedores
	        	comcampocatpersonal.mostrarVentana();
	        }
        }
	});
	
	var botbusquedaHasta = new Ext.Button({
		id: 'botbusquedaHasta',
		iconCls: 'menubuscar',
		style:'position:absolute;left:460px;top:95px',
		listeners:{
	        'click' : function(boton)
	        {
				//componente catalogo de proveedores
				var reCatProveedor = Ext.data.Record.create([
					{name: 'codper'}, //campo obligatorio                             
					{name: 'cedper'},  //campo obligatorio
					{name: 'nomper'},  //campo obligatorio
					{name: 'apeper'}   //campo obligatorio
				]);
				
				var comcampocatpersonal = new com.sigesp.vista.comCatalogoPersonal({
					reCatalogo: reCatProveedor,
					rutacontrolador:'../../controlador/sno/sigesp_ctr_sno_comcatpersonal.php',
					parametros: "ObjSon={'operacion': 'buscarPersonal'",
					soloCatalogo: true,
					arrSetCampo:[{campo:'ced_benehasta',valor:'cedper'}],
					numFiltroNoVacio: 1,
					idComponente:'Hasta'
				});
				//fin componente catalogo de proveedores
	        	comcampocatpersonal.mostrarVentana();
	        }
        }
	});
	
	//--------------------------------------------------------------------------------------------
	
	var botbusqueda = new Ext.Button({
		id: 'botbusqueda',
		iconCls: 'menubuscar',
		text: 'Buscar Personal',
		style:'position:absolute;left:380px;top:205px',
		handler:buscarFiltroPersonal,
	});
	
	
	var rePersonal = Ext.data.Record.create([
    	    {name: 'cedper'}, 
    	    {name: 'nomper'},
    	    {name: 'apeper'},
    	    {name: 'dirper'}, 
    	    {name: 'codpai'},
    	    {name: 'codest'},
    	    {name: 'codmun'},
    	    {name: 'codpar'},
    	    {name: 'coreleper'},
    	    {name: 'nacper'},
    	    {name: 'telhabper'},
    	    {name: 'telmovper'},
    	    {name: 'sc_cuenta'}
    ]);
	 	
	var dsPersonal =  new Ext.data.Store({
		 reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},rePersonal)
    });
    						
	var cmPersonal = new Ext.grid.ColumnModel([
		new Ext.grid.CheckboxSelectionModel(),
        {header: "<H1 align='center'>C&#233;dula / C&#243;digo</H1>", width: 30, sortable: true, dataIndex: 'cedper'},
        {header: "<H1 align='center'>Nombre y Apellido</H1>", width: 70, sortable: true, dataIndex: 'nomper'}          
	]);
    	
    //creando datastore y columnmodel para la grid de transferencia de beneficiarios
	 gridSolicitud = new Ext.grid.EditorGridPanel({
	 		width:500,
	 		height:200,
			frame:true,
			title:"<H1 align='center'>Personal de N&#243;mina</H1>",
			style: 'position:absolute;left:5px;top:235px',
			autoScroll:true,
     		border:true,
     		ds: dsPersonal,
       		cm: cmPersonal,
			sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
       		stripeRows: true,
      		viewConfig: {forceFit:true}
	});
	 
	//Creacion del formulario
	var Xpos = ((screen.width/2)-(280));
	pltransferencia = new Ext.FormPanel({
		applyTo: 'formulario',
		width:520,
		height: 475,
		title: "<H1 align='center'>Transferencia de Personal</H1>",
		frame:true,
		autoScroll:false,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:10px;',
		items: [{
				xtype:"fieldset", 
			    title:'Datos de la transferencia',
			    style: 'position:absolute;left:5px;top:10px',
				border:true,
			    height:185,
			    width:500,
			    cls:'fondo',
			    columnWidth:300,
			    items:[
			           comcampocatcuentacontable.fieldsetCatalogo,
			           {
			            layout: "column",
				    	defaults: {border: false},
				    	style: 'position:absolute;left:15px;top:65px',
				    	items: [{
					    		layout: "form",
					    		border: false,
					    		labelWidth: 200,
					    		items: [{
						    			xtype: 'label',
						    			style:'font-weight: bold;',
						    			text: 'Intervalo de Cedulas/Codigos de Personal',					
						    			id: 'label',
						    			width: 200							
						    		}]
				    			}]
			           	},
			           	{
				    	layout: "column",
				    	defaults: {border: false},
				    	style: 'position:absolute;left:15px;top:95px',
				    	items: [{
					    		layout: "form",
					    		border: false,
					    		labelWidth: 50,
					    		items: [{
						    			xtype: 'textfield',
						    			labelSeparator :'',
						    			fieldLabel: 'Desde',
						    			id: 'ced_benedesde',
						    			width: 100,
						    			binding:true,
						    			hiddenvalue:'',
						    			defaultvalue:'',
						    			autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '20', onkeypress: "return keyRestrict(event,'0123456789');"}
						    		}]
					    		}]
			           	},botbusquedaDesde,
			           	{
				    	layout: "column",
				    	defaults: {border: false},
				    	style: 'position:absolute;left:300px;top:95px',
				    	items: [{
					    		layout: "form",
					    		border: false,
					    		labelWidth: 50,
					    		items: [{
						    			xtype: 'textfield',
						    			labelSeparator :'',
						    			fieldLabel: 'Hasta',
						    			name: 'proveedesde',
						    			id: 'ced_benehasta',
						    			width: 100,
						    			binding:true,
						    			hiddenvalue:'',
						    			defaultvalue:'',
						    			autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '20', onkeypress: "return keyRestrict(event,'0123456789');"}	
						    		}]
				    			}]
					    },botbusquedaHasta,
					    {
					    	layout: "column",
					    	defaults: {border: false},
					    	style: 'position:absolute;left:15px;top:135px',
					    	items: [{
						    		layout: "form",
						    		border: false,
						    		labelWidth: 170,
						    		items: [{
					   					xtype: 'checkbox',
					   					labelSeparator :'',
					   					fieldLabel: 'Transferir todo el personal',
					   					id: 'tratod',
					   					inputValue:1,
					   					binding:true,
					   					hiddenvalue:'',
					   					defaultvalue:'0',
					   					allowBlank:false
					   				}]
					    	}]
						}]
				},botbusqueda ,gridSolicitud,
			   	{
		   		xtype: 'hidden',
		   		id: 'codsis',
		   		binding:true,
		   		defaultvalue:'RPC'
			   	},
			   	{
		   		xtype: 'hidden',
		   		id: 'nomven',
		   		binding:true,
		   		defaultvalue:'sigesp_vis_rpc_transferencia.html'
			   	}]
	})
	})

function buscarFiltroPersonal()
{
	obtenerMensaje('procesar','','Buscando Datos');
	//buscar modificaciones a aprobar
	
	var cedperdes = Ext.getCmp('ced_benedesde').getValue();
	var cedperhas = Ext.getCmp('ced_benehasta').getValue();

	if(cedperdes =='' || cedperhas==''){
		Ext.MessageBox.show({
			title:'Mensaje',
			msg: 'Debe establecer el Rango de C&#233;dulas/C&#243;digos para realizar la B�squeda',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.INFO
		});
	}
	else{
		if(parseFloat(cedperdes) > parseFloat(cedperhas)){
			Ext.MessageBox.show({
				title:'Mensaje',
				msg: 'Error en rango de C&#233;dulas/C&#243;digos',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.INFO
			});
		}
	    else{
	    	 
			var JSONObject = {
				'operacion' : 'buscarFiltroPersonal',
				'cedperdes'    : cedperdes,
				'cedperhas'    : cedperhas,
			}		 
			var ObjSon = JSON.stringify(JSONObject);
			var parametros = 'ObjSon='+ObjSon; 
			Ext.Ajax.request({
				url : '../../controlador/rpc/sigesp_ctr_rpc_transferencia.php',
				params : parametros,
				method: 'POST',
				success: function ( resultado, request){
					Ext.Msg.hide();
					var datos = resultado.responseText;
					var objetoTransferencia = eval('(' + datos + ')');
					if(objetoTransferencia!=''){
						if(objetoTransferencia!='0'){
							if(objetoTransferencia.raiz == null || objetoTransferencia.raiz ==''){
								Ext.MessageBox.show({
									title:'Advertencia',
									msg:'No existen datos para mostrar',
									buttons: Ext.Msg.OK,
				 					icon: Ext.MessageBox.WARNING
				 				});
							}
							else{
								gridSolicitud.store.loadData(objetoTransferencia);
							}
						}
						else{
							Ext.MessageBox.show({
								title:'Advertencia',
								msg:'Debe configurar en Empresa los digitos de las cuentas de gastos',
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.WARNING
				 			});
						}
					}
				}
			});
    	}
	}
}

function irCancelar()
{
	limpiarFormulario(pltransferencia);
	gridSolicitud.store.removeAll();
}

function irProcesar()
{
	var sc_cuenta= Ext.getCmp('sc_cuenta').getValue();
	if(sc_cuenta =='')
	{
		Ext.MessageBox.show({
			title:'Mensaje',
			msg: 'Debe establecer la cuenta contable que sera asociada al Beneficiario',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.INFO
		});
	}
	else
	{	
		if(Ext.getCmp('tratod').checked){
			var cadenajson = "{'operacion':'transTodo','codsis':'RPC','nomven':'sigesp_vis_rpc_transferencia.html','sc_cuenta':'"+Ext.getCmp('sc_cuenta').getValue()+"'}";
			var parametros = 'ObjSon='+cadenajson;
			obtenerMensaje('procesar','','Procesando Datos');
			Ext.Ajax.request({
				url : '../../controlador/rpc/sigesp_ctr_rpc_transferencia.php',
				params : parametros,
				method: 'POST',
				success: function ( resultado, request) {
					var datos = resultado.responseText;
					Ext.Msg.hide();
					var datajson = eval('(' + datos + ')');
					if (datajson.raiz.valido==true)
					{	
						Ext.MessageBox.alert('Mensaje', datajson.raiz.mensaje);
					}
					else
					{
						Ext.MessageBox.alert('Error', datajson.raiz.mensaje);
					}
					irCancelar();
				},
				failure: function (result,request) 
				{ 
					Ext.Msg.hide();
					Ext.MessageBox.alert('Error', 'Error al procesar la Informaci�n'); 
					irCancelar();
				}
			});
		}
		else{
			var cadenajson = "{'operacion':'procesar','codsis':'RPC','nomven':'sigesp_vis_rpc_transferencia.html','arrPersonal':[";
			var detalles = gridSolicitud.getSelectionModel().getSelections(); 
			var total = detalles.length;
			if (total>0)
			{
				obtenerMensaje('procesar','','Procesando Datos');
				for (var i = 0; i <= total - 1; i++)
				{
					if (i > 0)
					{
						cadenajson = cadenajson + ",";				
					}			
		   			 cadenajson = cadenajson + "{'ced_bene':'"+detalles[i].get('cedper')+"',"+
		   			 "'nombene' :'"+detalles[i].get('nomper')+"',"+
					 "'apebene' :'"+detalles[i].get('apeper')+"',"+
					 "'dirbene' :'"+detalles[i].get('dirper')+"',"+
					 "'codpai' :'"+detalles[i].get('codpai')+"',"+
					 "'codest' :'"+detalles[i].get('codest')+"',"+
					 "'codmun' :'"+detalles[i].get('codmun')+"',"+
					 "'codpar' :'"+detalles[i].get('codpar')+"',"+
					 "'email'  :'"+detalles[i].get('coreleper')+"',"+
					 "'nacben' :'"+detalles[i].get('nacper')+"',"+
					 "'telbene':'"+detalles[i].get('telhabper')+"',"+
					 "'celbene':'"+detalles[i].get('telmovper')+"',"+
					 "'codbansig':'---',"+
					 "'sc_cuenta':'"+Ext.getCmp('sc_cuenta').getValue()+"'}";     
				}
				cadenajson = cadenajson +"]}";		
				var parametros = 'ObjSon='+cadenajson;
				Ext.Ajax.request({
					url : '../../controlador/rpc/sigesp_ctr_rpc_transferencia.php',
					params : parametros,
					method: 'POST',
					success: function ( resultado, request)
					{
						var datos = resultado.responseText;
						Ext.Msg.hide();
						var datajson = eval('(' + datos + ')');
						if (datajson.raiz.valido==true)
						{	
							Ext.MessageBox.alert('Mensaje', datajson.raiz.mensaje);
						}
						else
						{
							Ext.MessageBox.alert('Error', datajson.raiz.mensaje);
						}
						irCancelar();
					},
					failure: function (result,request) 
					{ 
						Ext.Msg.hide();
						Ext.MessageBox.alert('Error', 'Error al procesar la Informaci�n'); 
						irCancelar();
					}
				});
			}
			else
			{
				Ext.MessageBox.show({
					title:'Mensaje',
					msg:'Debe seleccionar al menos un personal a procesar',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.INFO
				});
			}
		}
	}
}