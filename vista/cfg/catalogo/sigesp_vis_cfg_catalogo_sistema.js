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

var dataStoreSistema="";
var formBusquedaSistema="";
var gridSistema="";
var ventanaCatalogoSistema="";

function crearDataStoreSistema()
{

	registroSistema = Ext.data.Record.create([
							{name: 'codsis'},    
							{name: 'nomsis'}
						]);
	
	var objetoSistema={"raiz":[{"codsis":'',"nomsis":''}]};
		
		dataStoreSistema =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objetoSistema),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
		    registroSistema  
			),
			data: objetoSistema
	  	})	
		
		var JSONObject ={
			"operacion": 'catalogo'
		}
		ObjSon=JSON.stringify(JSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_sistema.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			datos = resultado.responseText;
			var objetoSistema = eval('(' + datos + ')');
			if(objetoSistema!='')
			{
				dataStoreSistema.loadData(objetoSistema);
			}
		}	
	})
}

function actDataStoreSistema(criterio,cadena)
{
	dataStoreSistema.filter(criterio,cadena,true,false);
}


function crearFormBusqueda()
{
		formBusquedaSistema = new Ext.FormPanel({
        labelWidth: 75, // label settings here cascade unless overridden
        url:'save-form.php',
        frame:true,
        title: 'B&uacute;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 350,
		height:100,
        defaults: {width: 230},
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'C&#243;digo',
                name: 'cod',
				id:'cod',
				changeCheck: function(){
							var v = this.getValue();
							actDataStoreSistema('codsis',v);
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
			                name: 'den',
			                id:'den',
							changeCheck: function()
							{
										var v = this.getValue();
										actDataStoreSistema('nomsis',v);
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


function crearGridSistema()
{
	crearFormBusqueda();
	crearDataStoreSistema();
		 
	 gridSistema = new Ext.grid.GridPanel({
	 width:770,
	 height:400,
	 tbar: formBusquedaSistema,
	 autoScroll:true,
     border:true,
     ds: dataStoreSistema,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codsis'},
          {header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'nomsis'}
       ]),
       	stripeRows: true,
      	viewConfig: {forceFit:true}
      });            
} 

function mostrarCatalogoSistema(objeto)
{
				   crearGridSistema();
				   ObjetoFuente='definicion';
                   ventanaCatalogoSistema = new Ext.Window(
                   {
                    //layout:'fit',
                    title: 'Cat&#225;logo c&#243;digos del sistema',
		    		autoScroll:true,
                    width:800,
                    height:400,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridSistema],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	Registro = gridSistema.getSelectionModel().getSelected();
                    	switch(ObjetoFuente)   
                    	{
                    		case 'definicion':
                    			objeto.setValue(Registro.get('codsis'));
	                    	break;
                    		case 'grid':
		                    	PasDatosGridGrid(Registro);
	                    	break;
                    		case 'objeto':
	                    		PasDatosGridObj(Registro);
	                    	break;
 
                    		
                    	}          
                    	gridSistema.destroy();
		      			ventanaCatalogoSistema.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	gridSistema.destroy();
		      			ventanaCatalogoSistema.destroy();
                     }
                    }]
                    
                   });
                  ventanaCatalogoSistema.show();       
 

 }