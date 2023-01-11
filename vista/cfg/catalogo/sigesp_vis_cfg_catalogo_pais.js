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

var dataStorePais="";
var formBusquedaPais="";
var gridPais="";
var ventanaPais="";


function crearDataStorePais()
{

	registroPais = Ext.data.Record.create([
							{name: 'codpai'},    
							{name: 'despai'}
						]);
	
	var objetoPais={"raiz":[{"codpai":'',"despai":''}]};
		
		dataStorePais =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objetoPais),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
		    registroPais  
			),
			data: objetoPais
	  	})	
		
		var JSONObject ={
			"oper": 'catalogo'
		}
		ObjSon=JSON.stringify(JSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_pais.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			datos = resultado.responseText;
			var objetoPais = eval('(' + datos + ')');
			 if(objetoPais!='')
			 {
				dataStorePais.loadData(objetoPais);
			 }
		}	
	})
}

function actDataStorePais(criterio,cadena)
{
	dataStorePais.filter(criterio,cadena,true,false);
}


function crearFormBusqueda()
{
		formBusquedaPais = new Ext.FormPanel({
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
							actDataStorePais('codpai',v);
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
										actDataStorePais('despai',v);
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


function crearGridPais()
{
	 crearFormBusqueda();
	 crearDataStorePais();
	 gridPais = new Ext.grid.GridPanel({
	 width:770,
	 height:400,
	 tbar: formBusquedaPais,
	 autoScroll:true,
     border:true,
     ds: dataStorePais,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codpai'},
          {header: "Pa&#237;ses", width: 50, sortable: true, dataIndex: 'despai'}
       ]),
       	stripeRows: true,
      	viewConfig: {
      	forceFit:true
      }
	 });            
} 

function mostrar_catalogo()
{
				   crearGridPais();
				   ObjetoFuente='definicion';
                   ventanaPais = new Ext.Window(
                   {
                    title: 'Cat&#225;logo de pa&#237;ses',
		    		autoScroll:true,
		    		resizable:false,
                    width:800,
                    height:475,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridPais],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	Registro = gridPais.getSelectionModel().getSelected();
								alert('catalogo obj'+ObjetoFuente);
                    	switch(ObjetoFuente)   
                    	{
							case 'definicion':
                    			limpiarCampos();
	                    		Actualizar=1;
	                    		pasarDatosPais(Registro);
	                    		Ext.getCmp('codpai').disable();
	                    	break;
                    		case 'grid':
		                    	PasDatosGridGrid(Registro);
	                    	break;
                    		case 'objeto':
	                    		PasDatosGridObj(Registro);
	                    	break;
 
                    		
                    	}          
                    	gridPais.destroy();
		      			ventanaPais.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	gridPais.destroy();
		      			ventanaPais.destroy();
                     }
                    }]
                    
                   });
                  ventanaPais.show();
 }

function pasarDatosPais(registro)
{
	for(i=0;i<Campos.length;i++)
	{
		if(registro.get('codpai')!='' && registro.get('codpai'))
		{
			var valor = registro.get('codpai');
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
		if(registro.get('despai')!='' && registro.get('despai'))
		{
			valor = registro.get('despai');
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


function mostrarCatalogoPais(operacion,codpais,denpais)
{
   crearGridPais(operacion);
   ventanaPais = new Ext.Window(
                   {
                    title: 'Cat&#225;logo de paises',
		    		autoScroll:true,
		    		resizable:false,
                    width:800,
                    height:475,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridPais],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function(){ 
					                    var registro = gridPais.getSelectionModel().getSelected();
										switch(operacion)   
										{
											case 'definicion':
												pasarDatosPais(registro);   
												Actualizar=1;										
												gridPais.destroy();
												ventanaPais.destroy();
											break;
											case 'grid':
												PasDatosGridGrid(Registro);
											break;
											case 'catalogo':
												codpais.setValue(registro.get('codpai'));
												denpais.setText(registro.get('despai'));
											break;
										}					                    
										gridPais.destroy();
										ventanaPais.destroy();
                    				   }
                    
                    },
                   {
                     text: 'Salir',
                     handler: function()
                     {
                    	gridPais.destroy();
                      	ventanaPais.destroy();
                     }
                   }]
                    
                   });
				   ventanaPais.show();       
 }