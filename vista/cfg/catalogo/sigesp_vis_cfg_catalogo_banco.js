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

var dataStoreBanco="";
var formBusquedaBanco="";
var gridBanco="";
var ventanaBanco="";


function crearDataStoreBanco()
{
	registroBanco = Ext.data.Record.create([
	                        {name: 'codemp'},
							{name: 'codban'},    
							{name: 'nomban'},
							{name: 'dirban'},
							{name: 'gerban'},
							{name: 'telban'},
							{name: 'conban'},
							{name: 'movcon'},
							{name: 'esttesnac'},
							{name: 'codsudeban'},
							{name: 'codswift'}
						]);
	
	
	
	
	var objetoBanco={"raiz":[{ "codemp":'',
		                    "codban":'',   
							"nomban":'',
							"dirban":'',
							"gerban":'',
							"telban":'',
							"conban":'',
							"movcon":'',
							"esttesnac":'',
							"codsudeban":'',
							"codswift":''}]};
		
		dataStoreBanco =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objetoBanco),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
		    registroBanco  
			),
			data: objetoBanco
	  	})	
		
		var JSONObject ={
			"oper": 'catalogo'
		}
		ObjSon=JSON.stringify(JSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_scb_banco.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			datos = resultado.responseText;
			var objetoBanco = eval('(' + datos + ')');
			if(objetoBanco!='')
			{
				dataStoreBanco.loadData(objetoBanco);
			}
		}	
	})
}

function actualizarDataStoreBanco(criterio,cadena)
{
	dataStoreBanco.filter(criterio,cadena,true,false);
}


function crearFormularioBusquedaBanco()
{
		formBusquedaBanco = new Ext.FormPanel({
        labelWidth: 100,
        frame:true,
        title: 'B&#250;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 500,
		height:100,
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'C&#243;digo',
                name: 'codigo',
				id:'codigo',
				width: 50,
				changeCheck: function(){
							var v = this.getValue();
							actualizarDataStoreBanco('codban',v);
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
			    width: 350,
				changeCheck: function(){
							var v = this.getValue();
							actualizarDataStoreBanco('nomban',v);
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


function crearGridBanco()
{
	crearFormularioBusquedaBanco();
	crearDataStoreBanco();
		 
	 gridBanco = new Ext.grid.GridPanel({
	 width:770,
	 height:400,
	 tbar: formBusquedaBanco,
	 autoScroll:true,
     border:true,
     ds: dataStoreBanco,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codban'},
          {header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'nomban'}
       ]),
       	stripeRows: true,
      	viewConfig: {
      	forceFit:true
      }
      });            
}

function pasarDatosGridBanco(registro)
{
	
	Ext.getCmp('codban').setValue(registro.get('codban'));
	Ext.getCmp('nomban').setValue(registro.get('nomban'));
		
	//Actualizar=true;			
} 

function mostrar_catalogo()
{
				   crearGridBanco();
				   ObjetoFuente='definicion';
                   ventanaBanco = new Ext.Window(
                   {
                    title: 'Cat&#225;logo de Banco',
		    		autoScroll:true,
                    width:800,
                    height:480,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridBanco],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	registro = gridBanco.getSelectionModel().getSelected();
                    	switch(ObjetoFuente)   
                    	{
                    		case 'definicion':
                    			limpiarCampos();
	                    		PasDatosGridDef(registro);
								Actualizar=true;
	                    	break;
                    		case 'grid':
		                    	PasDatosGridGrid(registro);
	                    	break;
                    		case 'objeto':
	                    		PasDatosGridObj(registro);
	                    	break;
                    	}          
                    	gridBanco.destroy();
		      			ventanaBanco.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	gridBanco.destroy();
		      			ventanaBanco.destroy();
                     }
                    }]
                    
                   });
                  ventanaBanco.show();       
 

 }
 
 function catalogoBanco()
{
				   crearGridBanco();
				   ObjetoFuente='definicion';
                   ventanaBanco = new Ext.Window(
                   {
                    title: 'Cat&#225;logo de Banco',
		    		autoScroll:true,
                    width:800,
                    height:480,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridBanco],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	registro = gridBanco.getSelectionModel().getSelected();
                    	switch(ObjetoFuente)   
                    	{
                    		case 'definicion':
	                    		pasarDatosGridBanco(registro);
	                    	break;
                    		case 'grid':
		                    	pasarDatosGridBanco(registro);
	                    	break;
                    		case 'objeto':
	                    		pasarDatosGridBanco(registro);
	                    	break;
 
                    		
                    	}          
                    	gridBanco.destroy();
		      			ventanaBanco.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	gridBanco.destroy();
		      			ventanaBanco.destroy();
                     }
                    }]
                    
                   });
                  ventanaBanco.show();       
 }