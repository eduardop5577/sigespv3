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
var dsbanco=null;
Ext.onReady(function(){
	
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	//-------------------------------------------------------------------------------------------------------------------------	
	
	var botbusquedaDesde = new Ext.Button({
		id: 'botbusquedaDesde',
		iconCls: 'menubuscar',
		style:'position:absolute;left:245px;top:20px',
		listeners:{
            'click' : function(boton)
            {
 				//componente catalogo de proveedores
				var reCatProveedor = Ext.data.Record.create([
					{name: 'cod_pro'}, //campo obligatorio                             
					{name: 'nompro'},  //campo obligatorio
					{name: 'dirpro'},  //campo obligatorio
					{name: 'rifpro'}   //campo obligatorio
				]);
				
				var comcampocatproveedor = new com.sigesp.vista.comCatalogoProveedor({
					reCatalogo: reCatProveedor,
					rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_comcatproveedor.php',
					parametros: "ObjSon={'operacion': 'buscarProveedores'",
					soloCatalogo: true,
					arrSetCampo:[{campo:'cod_prodesde',valor:'cod_pro'}],
					numFiltroNoVacio: 2,
					idComponente:'Desde'
				});
				//fin componente catalogo de proveedores
            	comcampocatproveedor.mostrarVentana();
            }
        }
	});
	var botbusquedaHasta = new Ext.Button({
		id: 'botbusquedaHasta',
		iconCls: 'menubuscar',
		style:'position:absolute;left:580px;top:20px',
		listeners:{
            'click' : function(boton)
            {
				//componente catalogo de proveedores
				var reCatProveedor2 = Ext.data.Record.create([
					{name: 'cod_pro'}, //campo obligatorio                             
					{name: 'nompro'},  //campo obligatorio
					{name: 'dirpro'},  //campo obligatorio
					{name: 'rifpro'}   //campo obligatorio
				]);
				
				var comcampocatproveedor2 = new com.sigesp.vista.comCatalogoProveedor({
					reCatalogo: reCatProveedor2,
					rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_comcatproveedor.php',
					parametros: "ObjSon={'operacion': 'buscarProveedores'",
					soloCatalogo: true,
					arrSetCampo:[{campo:'cod_prohasta',valor:'cod_pro'}],
					numFiltroNoVacio: 2,
					idComponente:'Hasta'
				});
				//fin componente catalogo de proveedores
            	comcampocatproveedor2.mostrarVentana();
            }
        }
	});
	
	var reSolicitud = Ext.data.Record.create([
    	    {name: 'cod_pro'}, 
    	    {name: 'nompro'},
    	    {name: 'estpro'}
    ]);
            	
	var dsSolicitud =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reSolicitud)
    });
    						
	var cmSolicitud = new Ext.grid.ColumnModel([
		new Ext.grid.CheckboxSelectionModel(),
        {header: "C&#243;digo", width: 40, sortable: true, dataIndex: 'cod_pro'},
        {header: "Nombre ", width: 90, sortable: true, dataIndex: 'nompro'}          
	]);
    	
    	//creando datastore y columnmodel para la grid de cambio de estatus de proveedor
	gridSolicitud = new Ext.grid.EditorGridPanel({
	 		width:630,
	 		height:260,
			frame:true,
			title:"<H1 align='center'>Proveedores</H1>",
			style: 'position:absolute;left:10px;top:240px',
			autoScroll:true,
     		border:true,
     		ds: dsSolicitud,
       		cm: cmSolicitud,
			sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
       		stripeRows: true,
      		viewConfig: {forceFit:true}
	});

//--------------------------------------------------------------------------------------------
	var	fromCambioEstatus= new Ext.form.FieldSet({
				title:'Estatus Actual',
				style: 'position:absolute;left:10px;top:10px',
				border:true,
				width: 630,
				cls :'fondo',
				height: 130,
				items:[{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:20px;top:20px',
				items: [{
					layout: "form",
					border: false,
					labelWidth: 110,
					items: [{
						xtype: 'textfield',
						labelSeparator :'',
						fieldLabel: 'Proveedor desde',
						name: 'proveedesde',
						id: 'cod_prodesde',
						width: 100,
						binding:true,
						hiddenvalue:'',
						defaultvalue:'',
						changeCheck: function(){
							var textvalor = this.getValue();
							dsSolicitud.filter('cod_pro',textvalor,true);
							if(String(textvalor) !== String(this.startValue)){
								this.fireEvent('change', this, textvalor, this.startValue);
							} 
						}, 
						initEvents: function(){
							AgregarKeyPress(this);
						}
					}]
				}]
			},botbusquedaDesde,
			{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:350px;top:20px',
				items: [{
					layout: "form",
					border: false,
					labelWidth: 110,
					items: [{
						xtype: 'textfield',
						labelSeparator :'',
						fieldLabel: 'Proveedor hasta',
						name: 'proveedesde',
						id: 'cod_prohasta',
						width: 100,
						binding:true,
						hiddenvalue:'',
						defaultvalue:'',
					}]
				}]
			},botbusquedaHasta,
		    {
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:20px;top:60px',
				items: [{
					layout: "form",
					border: false,
					labelWidth: 200,
					items:[{		    				    	
						xtype: "radiogroup",
						fieldLabel: "Estatus del proveedor",
						labelSeparator:":",
						binding:true,
						hiddenvalue:'',
						defaultvalue:'',	
						columns: [200,200,200,200],
						id:'estprov',
						items: [
						        {boxLabel: 'Activo', name: 'estatus',inputValue: '0',checked:true},
						        {boxLabel: 'Inactivo', name: 'estatus', inputValue: '1'},
						        {boxLabel: 'Bloqueado', name: 'estatus', inputValue: '2'},
						        {boxLabel: 'Suspendido', name: 'estatus', inputValue: '3'}

						        ]
					}]
				}]
		    }]  	
	
  	});
	var	fromproveedores = new Ext.form.FieldSet({
		
			title:'Estatus Nuevo',
			style: 'position:absolute;left:10px;top:140px',
			border:true,
			width: 630,
			cls :'fondo',
			height: 130,
			items:[{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:20px;top:20px',
				items: [{
					layout: "form",
					border: false,
					labelWidth: 200,
					items:[{		    				    	
						xtype: "radiogroup",
						fieldLabel: "Estatus nuevo",
						labelSeparator:":",
						binding:true,
						hiddenvalue:'',
						defaultvalue:'',	
						columns: [200,200,200,200],
						id:'estprovnew',
						items: [
						        {boxLabel: 'Activo', name: 'estatus',inputValue: '0',checked:true},
						        {boxLabel: 'Inactivo', name: 'estatus', inputValue: '1'},
						        {boxLabel: 'Bloqueado', name: 'estatus', inputValue: '2'},
						        {boxLabel: 'Suspendido', name: 'estatus', inputValue: '3'}

						        ]
					}]
				}]
			}]  
  	});

	//Creacion del formulario
	var Xpos = ((screen.width/2)-(350));
	plCambioEstatus = new Ext.FormPanel({
		applyTo: 'formulario',
		width:685,
		height: 500,
		title: "<H1 align='center'>Actualizar Estatus de Proveedores en lote</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:15px;',
		items: [
		        fromCambioEstatus,
		        fromproveedores,
		        gridSolicitud
		        ]
	});	
	fromCambioEstatus.doLayout();
	fromproveedores.doLayout();
  	
});

function irCancelar()
{
	limpiarFormulario(plCambioEstatus);
	gridSolicitud.store.removeAll();
}

function irProcesar(){
	
  if(Ext.getCmp('estprov').items.items[0].checked)
  {
	  estprov = Ext.getCmp('estprov').items.items[0].inputValue;
  }
  else if (Ext.getCmp('estprov').items.items[1].checked)
  {
	  estprov = Ext.getCmp('estprov').items.items[1].inputValue;
  }
  else if(Ext.getCmp('estprov').items.items[2].checked)
  {
	  estprov = Ext.getCmp('estprov').items.items[2].inputValue;
  }
  else if(Ext.getCmp('estprov').items.items[3].checked)
  {
	  estprov = Ext.getCmp('estprov').items.items[3].inputValue;
  }	
  if(Ext.getCmp('estprovnew').items.items[0].checked)
  {
	  estprovnew = Ext.getCmp('estprovnew').items.items[0].inputValue;
  }
  else if (Ext.getCmp('estprovnew').items.items[1].checked)
  {
	  estprovnew = Ext.getCmp('estprovnew').items.items[1].inputValue;
  }
  else if(Ext.getCmp('estprovnew').items.items[2].checked)
  {
	  estprovnew = Ext.getCmp('estprovnew').items.items[2].inputValue;
  }
  else if(Ext.getCmp('estprovnew').items.items[3].checked)
  {
	  estprovnew = Ext.getCmp('estprovnew').items.items[3].inputValue;
  }	
  
  if(estprov == estprovnew)
  {
		Ext.Msg.show({
			title:'Advertencia',
			msg: 'El estatus actual es igual al estatus nuevo. Debe cambiarlo para procesar',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.WARNING
		});		
	}
	else
	{ 
		var cadenajson = "{'operacion':'actualizar','codsis':'RPC','nomven':'sigesp_vis_rpc_cambioestatus_proveedor.html','estprovnew':'"+estprovnew+"','arrProveedor':[";
		var detalles = gridSolicitud.getSelectionModel().getSelections(); 
		var total = detalles.length; 
		if (total>0){
		obtenerMensaje('procesar','','Procesando Datos');
		for (var i = 0; i <= total - 1; i++)
		{
			if (i > 0)
			{
				cadenajson = cadenajson + ",";				
			}			
			cadenajson = cadenajson + "{'cod_pro':'"+detalles[i].get('cod_pro')+"','estpro' :'"+detalles[i].get('estpro')+"'}";               
		}
		   cadenajson = cadenajson +"]}";	
			var parametros = 'ObjSon='+cadenajson;
			Ext.Ajax.request({
				url : '../../controlador/rpc/sigesp_ctr_rpc_cambioestatus.php',
				params : parametros,
				method: 'POST',
				success: function ( resultado, request)
				{
					datos = resultado.responseText;
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
					Ext.MessageBox.alert('Error', 'Error al procesar la Información'); 
					irCancelar();
				}
			});	
		}
		   else{
			Ext.MessageBox.show({
				title:'Mensaje',
				msg:'Debe seleccionar al menos un documento a procesar',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.INFO
			});
		}
	}
}

function irBuscar()
{
	obtenerMensaje('procesar','','Buscando Datos');
	var cod_prodesde  = Ext.getCmp('cod_prodesde').getValue();
	var cod_prohasta  = Ext.getCmp('cod_prohasta').getValue();

	if(Ext.getCmp('estprov').items.items[0].checked)
	{
		estprov = Ext.getCmp('estprov').items.items[0].inputValue;
	}
	else if (Ext.getCmp('estprov').items.items[1].checked)
    {
  		estprov = Ext.getCmp('estprov').items.items[1].inputValue;
  	}
	else if(Ext.getCmp('estprov').items.items[2].checked)	
    {
   		estprov = Ext.getCmp('estprov').items.items[2].inputValue;
    }
  	else if(Ext.getCmp('estprov').items.items[3].checked)	
    {
    	estprov = Ext.getCmp('estprov').items.items[3].inputValue;
    }	

	var JSONObject = {
		'operacion' : 'cargarProveedores',
		'cod_prodesde' : cod_prodesde,
		'cod_prohasta' : cod_prohasta,
		'estprov'      : estprov
	}
	
	var ObjSon = JSON.stringify(JSONObject);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/rpc/sigesp_ctr_rpc_cambioestatus.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request){
			Ext.Msg.hide();
			var datos = resultado.responseText;
			var objetoProveedores = eval('(' + datos + ')');
			if(objetoProveedores!='' || objetoProveedores.raiz!=''){
				gridSolicitud.store.loadData(objetoProveedores);
			}
			else {
				Ext.Msg.show({
					title:'Advertencia',
					msg: 'No se ha Proveedores',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.WARNING
				});  				
			}
		}	
	});
}