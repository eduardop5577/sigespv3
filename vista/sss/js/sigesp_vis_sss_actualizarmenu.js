/***********************************************************************************
* @Archivo javascript que incluye tanto los componentes como los eventos asociados 
* al proceso de actualizar menu. 
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var cambiar = false;
var panel      = '';
var pantalla   = 'actualizarmenu';
var ruta = '../../controlador/sss/sigesp_ctr_sss_actualizarmenu.php';
var RecordDefSis = '';
var gridSist   = '';
var dsSistema = '';
var arrSistema	= new Array();
var sistemaEliminar = '';
var datosNuevo={'raiz':[{'codsis':'','nomsis':''}]};
barraherramienta    = true;
Ext.onReady
(
	function()
	{
		Ext.QuickTips.init();
		Ext.Ajax.timeout=36000000000;
		Ext.form.Field.prototype.msgTarget = 'side';
		var agregar = new Ext.Action(
		{
			text: 'Agregar',
			handler: irAgregar,
			iconCls: 'bmenuagregar',
        	tooltip: 'Agregar Sistema'
		});
		
		var quitar = new Ext.Action(
		{
			text: 'Quitar',
			handler: irQuitar,
			iconCls: 'bmenuquitar',
        	tooltip: 'Quitar Sistema'
		});
		
		Xpos = ((screen.width/2)-(550/2)); 
		Ypos = ((screen.height/2)-(500/2));
		//Panel con los componentes del formulario
		panel = new Ext.FormPanel({
        labelWidth: 75,
     	title: 'Proceso de Actualizar Menu',
        bodyStyle:'padding:5px 5px 5px',
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',
	  	tbar: [],
        defaults: {width: 230},		   
		items:[{
					xtype:'panel',
    				autoHeight:true,
					autoWidth:true,
					cls :'fondo',	
					title:'Sistemas',
					tbar: [agregar,quitar],
					contentEl:'grid-sistema'
			}]
		});
	panel.render(document.body);
	
	//llamada a la funci�n
	obtenerGridSistema();
});	//FIN

		
/***********************************************************************************
* @Funci�n para agregar un registro en la grid y llamar al cat�logo de Sistemas.
* @parametros: 
* @retorno: 
* @fecha de creaci�n: 30/11/2011. 
* @autor: Ing. Yesenia Moreno de Lang. 
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/	
	function irAgregar()
	{
		ParamGridTarget = gridSist;
		var arreglotxt     = new Array('','');		
		var arreglovalores = new Array('codsis','nomsis');
		ObjSistema      = new catalogoSistema();
		ObjSistema.mostrarCatalogoSistema(arreglotxt, arreglovalores);
	}			


/***********************************************************************************
* @Funci�n para crear la grid y pasarle los datos del usuario.
* @parametros: 
* @retorno: 
* @fecha de creaci�n: 30/11/2011. 
* @autor: Ing. Yesenia Moreno
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/
	function obtenerGridSistema()
	{	
		RecordDefSis = Ext.data.Record.create
		([
			{name: 'codsis'}, 
			{name: 'nomsis'}
		]);
		
		var DatosNuevo = {'raiz':[{'codsis':'','nomsis':''}]};	
		dsSistema =  new Ext.data.Store({
		proxy: new Ext.data.MemoryProxy(DatosNuevo),
		reader: new Ext.data.JsonReader({
			root: 'raiz',               
			id: 'id'   
			},
				  RecordDefSis
			),
			data: DatosNuevo
			});
		
		gridSist = new Ext.grid.GridPanel({
				width:500,
				autoScroll:true,
				border:true,
				ds: dsSistema,
				cm: new Ext.grid.ColumnModel([
					{header: 'C�digo', width: 100, sortable: true,   dataIndex: 'codsis'},
					{header: 'Nombre', width: 200, sortable: true, dataIndex: 'nomsis'}
				]),
				viewConfig: {
								forceFit:true
							},
				autoHeight:true,
				stripeRows: true
		});
		gridSist.render('grid-sistema');
	}
	
		
/***********************************************************************************
* @Funci�n para confirmar eliminar un registro de la grid de sistemas.
* @parametros: 
* @retorno: 
* @fecha de creaci�n: 30/11/2011. 
* @autor: Ing. Yesenia Moreno. 
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/
	function irQuitar()
	{
		var claveseleccionada = gridSist.selModel.selections.keys;
		if(claveseleccionada.length > 0)
		{
			Ext.Msg.confirm('Alerta!','Realmente desea eliminar el registro?', borrarRegistro);
		} 
		else 
		{
			Ext.Msg.alert('Alerta!','Seleccione un registro para eliminar');
		}
	}
	
	
/***********************************************************************************
* @Funci�n para eliminar un registro de la grid de usuarios.
* @parametros: 
* @retorno: 
* @fecha de creaci�n: 30/11/2011. 
* @autor: Ing. Yesenia Moreno. 
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/	
	function borrarRegistro(btn) 
	{
		if (btn=='yes') 
		{
			var filaseleccionada = gridSist.getSelectionModel().getSelected();
			if (filaseleccionada)
			{
				gridSist.store.remove(filaseleccionada);
				Ext.Msg.alert('Exito','Registro eliminado');				
			}
		} 
	}
	
	
/***********************************************************************************
* @Limpiar campos del formulario
* @parametros: 
* @retorno: 
* @fecha de creaci�n: 30/11/2011. 
* @autor: Ing. Yesenia Moreno. 
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/		
	function limpiarCampos() 
	{		 
		for (i=0; i<=arrSistema.length; i++)
		{
			arrSistema.pop();			
		}
	}


/***********************************************************************************
* @Funci�n que limpia los campos 
* @parametros: 
* @retorno: 
* @fecha de creaci�n: 24/10/2008. 
* @autor: Ing. Yesenia Moreno. 
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/
	function irCancelar()
	{
		limpiarCampos();
		gridSist.store.removeAll();
		gridSist.store.loadData(datosNuevo);
		gridSist.store.commitChanges();
		cambiar = false;
	}

/***********************************************************************************
* @Funci�n que guarda o actualiza los datos del proceso de asignaci�n.
* @parametros: 
* @retorno: 
* @fecha de creaci�n: 30/11/2011. 
* @autor: Ing. Yesenia Moreno. 
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/
	function irProcesar()
	{
		valido=true;
		obtenerMensaje('procesar','','Procesando Datos');
		var cadenaJson = "{'oper': 'procesar','sistema': sistema,'vista': vista";				
			arrSistema = gridSist.store.getModifiedRecords();
			cadenaJson=cadenaJson+ ",datosSistema:[";
			total = arrSistema.length;
			if (total>0)
			{	
				for (i=0; i < total; i++)
				{
					if (i==0)
					{
						cadenaJson = cadenaJson +"{'codsis':'"+ arrSistema[i].get('codsis')+ "'}";
					}
					else
					{
						cadenaJson = cadenaJson +",{'codsis':'"+ arrSistema[i].get('codsis')+ "'}";
					}
				}			
			}
			cadenaJson = cadenaJson + ']}';
			objdata= eval('(' + cadenaJson + ')');	
			objdata=JSON.stringify(objdata);
			parametros = 'objdata='+objdata; 
			Ext.Ajax.request({
			url : ruta,
			params : parametros,
			method: 'POST',
			success: function (resultado, request)
			{ 
				datos = resultado.responseText;
				Ext.Msg.hide();
				var datajson = eval('(' + datos + ')');
				if (datajson.raiz.valido==true)
				{	
					Ext.MessageBox.alert('Mensaje', datajson.raiz.mensaje);
					irCancelar();  
				}
				else
				{
					Ext.MessageBox.alert('Error', datajson.raiz.mensaje);
				}
			},
			failure: function (resultado,request) 
			{ 
				Ext.Msg.hide();
				Ext.MessageBox.alert('Error', 'Error al procesar la Informaci�n'); 
			}					
			});
	}