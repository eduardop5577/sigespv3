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

var dataStoreTipoColocacion="";           // DataStore asociado al Grid que muestra los registros
var formularioBusquedaTipoColocacion="";  // Formulario que sirve como topbar del Grid, para la busqueda
var gridTipoColocacion="";                // Grid que muestra los datos
var ventanaTipoColocacion="";             // Ventana que contiene el Grid y el Formulario de Búsqueda 


function creardataStoreTipoColocacion()
{

	registroTipoColocacion = Ext.data.Record.create([
								{name:'codtipcol'},			  
								{name:'nomtipcol'} 
								
						]);							
	
		var objetoTipoColocacion={"raiz":[{"codtipcol":"","dentipcol":""}]};
		
		dataStoreTipoColocacion =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objetoTipoColocacion),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
		    registroTipoColocacion  
			),
			data: objetoTipoColocacion
	  	});	
		
		var myJSONObject ={
			"oper": 'catalogo'
		};
		
		ObjSon=Ext.util.JSON.encode(myJSONObject);
		parametros = 'ObjSon='+ObjSon;
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_scb_tipocolocacion.php',
		params : parametros,
		method: 'POST',
		success: function ( result, request) 
		{ 
			datos = result.responseText;
			var objetoTipoColocacion = eval('(' + datos + ')');
			if(objetoTipoColocacion!='')
			{
				dataStoreTipoColocacion.loadData(objetoTipoColocacion);
			}
		}	
	})
}

function actdataStoreTipoColocacion(criterio,cadena)
{
	dataStoreTipoColocacion.filter(criterio,cadena,true,false);
}


function crearFormularioBusqueda()
{
		formularioBusquedaTipoColocacion = new Ext.FormPanel({
        labelWidth: 75,
        frame:true,
        title: 'B&uacute;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 350,
		height:100,
        defaults: {width: 230},
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'C&#243;digo',
                name: 'codigo',
				id:'codigo',
				changeCheck: function(){
							var v = this.getValue();
							actdataStoreTipoColocacion('codtipcol',v);
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
			                fieldLabel: 'Denominaci&#243;n',
			                name: 'denominacion',
			                id:'denominacion',
							changeCheck: function()
							{
										var v = this.getValue();
										actdataStoreTipoColocacion('nomtipcol',v);
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


function creargridTipoColocacion()
{
	crearFormularioBusqueda();
	creardataStoreTipoColocacion();
		 
	 gridTipoColocacion = new Ext.grid.GridPanel({
	 width:770,
	 height:350,
	 tbar: formularioBusquedaTipoColocacion,
	 autoScroll:true,
     border:true,
     ds: dataStoreTipoColocacion,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codtipcol'},
          {header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'nomtipcol'}
       ]),
       	stripeRows: true,
      	viewConfig: {
      	forceFit:true
      },
      listeners:{'celldblclick' : function( grid, fila, columna, evento ){
    	  registro = grid.getSelectionModel().getSelected();
    	  limpiarCampos();
		  pasarDatosTipoColocacion(registro);
		  grid.destroy();
		  ventanaTipoColocacion.destroy();
      }}
      });            
}

function pasarDatosTipoColocacion(registro)
{
	Actualizar=true;
	Ext.getCmp('codtipcol').setValue(registro.get('codtipcol'));
	Ext.getCmp('nomtipcol').setValue(registro.get('nomtipcol'));
}

function mostrar_catalogo()
{
				   creargridTipoColocacion();
                   ventanaTipoColocacion = new Ext.Window(
                   {
                    title: 'Cat&#225;logo de Tipo de Colocaciones',
		    		autoScroll:true,
                    width:800,
                    height:450,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridTipoColocacion],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	registro = gridTipoColocacion.getSelectionModel().getSelected();
                    	limpiarCampos();
                    	pasarDatosTipoColocacion(registro);          
                    	gridTipoColocacion.destroy();
		      			ventanaTipoColocacion.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	gridTipoColocacion.destroy();
		      			ventanaTipoColocacion.destroy();
                     }
                    }]
                    
                   });
                  ventanaTipoColocacion.show();       
 

 }