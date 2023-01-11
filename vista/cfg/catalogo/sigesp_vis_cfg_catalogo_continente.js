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

var dataStoreContinente="";
var formBusquedaContinente="";
var gridContinente="";
var ventanaContinente="";

function crearDataStoreContinente()
{

	registroContinente = Ext.data.Record.create([
							{name: 'codcont'},    
							{name: 'dencont'}
						]);
	
	var objetoContinente={"raiz":[{"codcont":'',"dencont":''}]};
		
		dataStoreContinente =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objetoContinente),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
		    registroContinente  
			),
			data: objetoContinente
	  	})	
		
		var JSONObject ={
			"oper": 'catalogo'
		}
		ObjSon=JSON.stringify(JSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_continente.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			datos = resultado.responseText;
			var objetoContinente = eval('(' + datos + ')');
			 if(objetoContinente!='')
			 {
				dataStoreContinente.loadData(objetoContinente);
			 }
		}	
	})
}

function actDataStoreContinente(criterio,cadena)
{
	dataStoreContinente.filter(criterio,cadena,true,false);
}


function crearFormBusqueda()
{
		formBusquedaContinente = new Ext.FormPanel({
        labelWidth: 80,
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
				width: 100,
				changeCheck: function(){
							var v = this.getValue();
							actDataStoreContinente('codcont',v);
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
			                fieldLabel: 'Pa&#237;s',
			                name: 'den',
			                id:'den',
							changeCheck: function()
							{
										var v = this.getValue();
										actDataStoreContinente('dencont',v);
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


function crearGridContinente()
{
	 crearFormBusqueda();
	 crearDataStoreContinente();
	 gridContinente = new Ext.grid.GridPanel({
	 width:770,
	 height:400,
	 tbar: formBusquedaContinente,
	 autoScroll:true,
     border:true,
     ds: dataStoreContinente,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codcont'},
          {header: "Pa&#237;ses", width: 50, sortable: true, dataIndex: 'dencont'}
       ]),
       	stripeRows: true,
      	viewConfig: {
      	forceFit:true
      }
	 });            
} 

function mostrar_catalogo()
{
				   crearGridContinente();
				   ObjetoFuente='definicion';
                   ventanaContinente = new Ext.Window(
                   {
                    title: 'Cat&#225;logo de pa&#237;ses',
		    		autoScroll:true,
		    		resizable:false,
                    width:800,
                    height:475,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridContinente],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	Registro = gridContinente.getSelectionModel().getSelected();
								alert('catalogo obj'+ObjetoFuente);
                    	switch(ObjetoFuente)   
                    	{
							case 'definicion':
                    			limpiarCampos();
	                    		Actualizar=1;
	                    		pasarDatosContinente(Registro);
	                    		Ext.getCmp('codcont').disable();
	                    	break;
                    		case 'grid':
		                    	PasDatosGridGrid(Registro);
	                    	break;
                    		case 'objeto':
	                    		PasDatosGridObj(Registro);
	                    	break;
 
                    		
                    	}          
                    	gridContinente.destroy();
		      			ventanaContinente.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	gridContinente.destroy();
		      			ventanaContinente.destroy();
                     }
                    }]
                    
                   });
                  ventanaContinente.show();
 }

function pasarDatosContinente(registro)
{
	for(i=0;i<Campos.length;i++)
	{
		if(registro.get('codcont')!='' && registro.get('codcont'))
		{
			var valor = registro.get('codcont');
			valor = valor.replace('|@@@|','+');
			cadena='';
			for(j=0;j<valor.length;j++)
			{
				letra = valor.substr(j,1);
				if(letra=='|')
				{
					letra = unescape('%0A');
				}
				cadena=cadena+letra;	
			}
			Ext.getCmp(Campos[0][0]).setValue(cadena);	
		}
		if(registro.get('dencont')!='' && registro.get('dencont'))
		{
			valor = registro.get('dencont');
			valor = valor.replace('|@@@|','+');
			cadena='';
			for(j=0;j<valor.length;j++)
			{
				letra = valor.substr(j,1);
				if(letra=='|')
				{
					letra = unescape('%0A');
				}
				cadena=cadena+letra;	
			}
			if(Ext.getCmp(Campos[1][0]).isXType('label'))
			{
				Ext.getCmp(Campos[1][0]).setText(cadena);	
			}
			else
			{
				Ext.getCmp(Campos[1][0]).setValue(cadena);	
			}
		}
	}
}


function mostrarCatalogoContinente(operacion,codconts,denContinente)
{
   crearGridContinente(operacion);
   ventanaContinente = new Ext.Window(
                   {
                    title: 'Cat&#225;logo de Continentes',
		    		autoScroll:true,
		    		resizable:false,
                    width:800,
                    height:475,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridContinente],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function(){ 
					                    var registro = gridContinente.getSelectionModel().getSelected();
										switch(operacion)   
										{
											case 'definicion':
												pasarDatosContinente(registro);   
												Actualizar=1;										
												gridContinente.destroy();
												ventanaContinente.destroy();
											break;
											case 'grid':
												PasDatosGridGrid(Registro);
											break;
											case 'catalogo':
												codconts.setValue(registro.get('codcont'));
												denContinente.setText(registro.get('dencont'));
											break;
										}					                    
										gridContinente.destroy();
										ventanaContinente.destroy();
                    				   }
                    
                    },
                   {
                     text: 'Salir',
                     handler: function()
                     {
                    	gridContinente.destroy();
                      	ventanaContinente.destroy();
                     }
                   }]
                    
                   });
				   ventanaContinente.show();       
 }