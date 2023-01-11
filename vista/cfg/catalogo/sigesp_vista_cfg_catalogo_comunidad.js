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

var dataStoreComunidad=""; //datastore del catalogo
var formBusquedaComunidad=""; // formulario de busqueda
var gridComunidad=""; // grid del catalogo
var ventanaComunidad=""; //ventana donde se ubica el catalogo


function crearDataStoreComunidad()
{

	registroComunidad = Ext.data.Record.create([
							{name: 'codcom'},
							{name: 'nomcom'}
						]);
	
	var objetoComunidad={"raiz":[{"codcom":'',"nomcom":''}]};
		
		dataStoreComunidad =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objetoComunidad),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
		    registroComunidad  
			),
			data: objetoComunidad
	  	})	
		
		var JSONObject ={
			"oper": 'catalogo',
			"codpai":codpai,
			"codest":codest,
			"codmun":codmun,
			"codpar":codpar
		}
		ObjSon=JSON.stringify(JSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_comunidad.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			datos = resultado.responseText;
			var objetoComunidad = eval('(' + datos + ')');
			if(objetoComunidad.raiz == null)
			{
				obtenerMensaje('informacion',ventanaComunidad.destroy(),'Mensaje','No se encontro informacion, con los parametros de busqueda seleccionados')	
			}
			else
			{
			 if(objetoComunidad!='')
			 {
				dataStoreComunidad.loadData(objetoComunidad);
			 }
			}
		}	
	})
}

function actualizarDataStoreComunidad(criterio,cadena)
{
	dataStoreComunidad.filter(criterio,cadena,true,false);
}

function pasarDatosComunidad(comunidad)
{
	Ext.getCmp('codcom').setValue(comunidad.get('codcom'));
	Ext.getCmp('nomcom').setValue(comunidad.get('nomcom'));
	Ext.getCmp('codpai').disable();
	Ext.getCmp('codest').disable();
	Ext.getCmp('codmun').disable();
	Ext.getCmp('codpar').disable();
	Ext.getCmp('codcom').disable();
	Actualizar=true;
}


function crearFormBusqueda()
{
		formBusquedaComunidad = new Ext.FormPanel({
        labelWidth: 80, // label settings here cascade unless overridden
        frame:true,
        title: 'B&uacute;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 350,
		height:100,
        defaults: {width: 230},
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'C&#243;digo',
                name: 'descripcion',
				id:'descripcion',
				width: 100,
				changeCheck: function(){
							var v = this.getValue();
							actualizarDataStoreComunidad('codcom',v);
							if(String(v) !== String(this.startValue))
							{
								this.fireEvent('change', this, v, this.startValue);
							} 
							},							 
							initEvents : function()
							{
								AgregarKeyPress(this);
							}               
      			},{
			                fieldLabel: 'Comunidad',
			                name: 'den',
			                id:'den',
							changeCheck: function()
							{
										var v = this.getValue();
										actualizarDataStoreComunidad('nomcom',v);
										if(String(v) !== String(this.startValue))
										{
											this.fireEvent('change', this, v, this.startValue);
										} 
										},							 
										initEvents : function()
										{
											AgregarKeyPress(this);
										}
			            }]
					});				  

}


function crearGridCatalogoComunidad()
{
	crearFormBusqueda();
	crearDataStoreComunidad();
		 
	 gridComunidad = new Ext.grid.GridPanel({
	 width:770,
	 height:400,
	 tbar: formBusquedaComunidad,
	 autoScroll:true,
     border:true,
     ds: dataStoreComunidad,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codcom'},
          {header: "Comunidad", width: 30, sortable: true,   dataIndex: 'nomcom'}
       ]),
       	stripeRows: true,
      	viewConfig: {
      	forceFit:true
      }
      });            
} 

function irBuscar()
{
   if((Ext.getCmp('codpai')==null)||(Ext.getCmp('codpai')=='')||(Ext.getCmp('codest')==null)||(Ext.getCmp('codest')=='')||
	  (Ext.getCmp('codmun')==null)||(Ext.getCmp('codmun')=='')||(Ext.getCmp('codpar')==null)||(Ext.getCmp('codpar')==''))
   {
		Ext.Msg.show({
		title:'Mensaje',
		msg: 'Debe seleccionar un pais, un estado, un municipio y una parroquia, verifique por favor!!',
		buttons: Ext.Msg.OK,
		animEl: 'elId',
		icon: Ext.MessageBox.INFO
		});
   }
   else
   { 
	   crearGridCatalogoComunidad();
	   ObjetoFuente='definicion';
	   ventanaComunidad = new Ext.Window(
	   {
		title: 'Cat&#225;logo de comunidades',
		autoScroll:true,
		width:800,
		height:400,
		modal: true,
		closable:false,
		plain: false,
		items:[gridComunidad],
		buttons: [{
		text:'Aceptar',  
		handler: function()
		{ 
			registro = gridComunidad.getSelectionModel().getSelected();
			pasarDatosComunidad(registro);          
			gridComunidad.destroy();
			ventanaComunidad.destroy();                      
		}
		}
		,
		{
		 text: 'Salir',
		 handler: function()
		 {
			
			gridComunidad.destroy();
			ventanaComunidad.destroy();
		 }
		}]
		
	   });
	  ventanaComunidad.show(); 
   }
}