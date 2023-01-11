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

var dsprocedencia="";
var grid_procedencia="";
var form_busqueda_procedencia="";
var registro_procedencia="";
var ventana_cat_procedencia="";

function crear_data_store_procedencia()
{
	registro_procedencia = Ext.data.Record.create([
						{name: 'procede'},    
						{name: 'codsis'},
						{name: 'opeproc'},
						{name: 'desproc'}
					]);
	
	var objeto_procedencia={"raiz":[{"procede":'',"codsis":'',"opeproc":'',"desproc":''}]};
		
		dsprocedencia =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objeto_procedencia),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
		    registro_procedencia  
			),
			data: objeto_procedencia
	  	})	
		

		var myJSONObject ={
		"oper": 'catalogo'
		}
		ObjSon=JSON.stringify(myJSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_procedencia.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			datos = resultado.responseText;
			var objeto_procedencia = eval('(' + datos + ')');
			if(objeto_procedencia!='')
			{
				dsprocedencia.loadData(objeto_procedencia);
			}
		}	
	})
}

function act_data_store_procedencia(criterio,cadena)
{
	dsprocedencia.filter(criterio,cadena,true,false);
}


function crear_form_busqueda_procedencia()
{
		form_busqueda_procedencia = new Ext.FormPanel({
        labelWidth: 75, // label settings here cascade unless overridden
        frame:true,
        title: 'B&uacute;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 350,
		height:100,
        defaults: {width: 230},
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'C&#243;digo',
                name: 'cod_procedencia',
				id:'cod_procedencia',
				changeCheck: function(){
							var v = this.getValue();
							act_data_store_procedencia('procede',v);
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

function bloquearCamposPrimarios()
{
	var myJSONObject ={
		"oper":"claveprimaria"
	};
	
	ObjSon=Ext.util.JSON.encode(myJSONObject);
	parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
	url : '../../controlador/cfg/sigesp_ctr_cfg_procedencia.php',
	params : parametros,
	method: 'POST',
	success: function ( result, request) 
	{ 
		datos = result.responseText;
		var pk = eval('(' + datos + ')');
		if(pk.length>0)
		{
			for(i=0; i < pk.length; i++)
			{
				Ext.getCmp(pk[i].toString()).setDisabled(true);
			}
		}
	}	
	})
}


function CrearGrid()
{
		crear_form_busqueda_procedencia();
		crear_data_store_procedencia();
		 
	 grid_procedencia = new Ext.grid.GridPanel({
	 width:770,
	 height:350,
	 tbar: form_busqueda_procedencia,
	 autoScroll:true,
     border:true,
     ds: dsprocedencia,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'procede'},
          {header: "Sistema", width: 50, sortable: true, dataIndex: 'codsis'},
          {header: "Operaci&#243;n", width: 50, sortable: true, dataIndex: 'opeproc'},
          {header: "Descripci&#243;n", width: 50, sortable: true, dataIndex: 'desproc'}
       ]),
       stripeRows: true,
      viewConfig: {
      	forceFit:true
      }
      ,
      });            
} 

function mostrar_catalogo()
{
	
				   CrearGrid();
				   ObjetoFuente='definicion';
                   ventana_cat_procedencia = new Ext.Window(
                   {
                    title: 'Cat&#225;logo c&#243;digos procedencia',
		    		autoScroll:true,
                    width:800,
                    height:450,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[grid_procedencia],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	Registro = grid_procedencia.getSelectionModel().getSelected();
                    	switch(ObjetoFuente)   
                    	{
                    		case 'definicion':
                    			limpiarCampos();
	                    		PasDatosGridDef(Registro);
	                    		bloquearCamposPrimarios();
	                    	break;
                    		case 'grid':
		                    	PasDatosGridGrid(Registro);
	                    	break;
                    		case 'objeto':
	                    		PasDatosGridObj(Registro);
	                    	break;
 
                    		
                    	}          
                    	grid_procedencia.destroy();
		      			ventana_cat_procedencia.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	grid_procedencia.destroy();
		      			ventana_cat_procedencia.destroy();
                     }
                    }]
                    
                   });
                  ventana_cat_procedencia.show();       
 }