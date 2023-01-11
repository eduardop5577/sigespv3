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

var dscodigo_denominacion="";
var form_busqueda_cod_denominacion="";
var grid_cod_denominacion="";
var ventana_cat_cod_denominacion="";


function crear_data_store_cod_denominacion()
{

	registro_cod_denominacion = Ext.data.Record.create([
							{name: 'codmil'},    
							{name: 'denmil'}
						]);
	
	var objeto_cod_denominacion={"raiz":[{"codmil":'',"denmil":''}]};
		
		dscodigo_denominacion =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objeto_cod_denominacion),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
		    registro_cod_denominacion  
			),
			data: objeto_cod_denominacion
	  	})	
		
		var JSONObject ={
			"oper": 'catalogoMilco'
		}
		ObjSon=JSON.stringify(JSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_catalogomilco.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			datos = resultado.responseText;
			var objeto_cod_denominacion = eval('(' + datos + ')');
			if(objeto_cod_denominacion!='')
			{
				dscodigo_denominacion.loadData(objeto_cod_denominacion);
			}
		}	
	})
}

function act_data_store_cod_denominacion(criterio,cadena)
{
	dscodigo_denominacion.filter(criterio,cadena,true,false);
}


function crearFormBusqueda()
{
		form_busqueda_cod_denominacion = new Ext.FormPanel({
        labelWidth: 90, // label settings here cascade unless overridden
        url:'save-form.php',
        frame:true,
        title: 'B&#250;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 400,
		height:100,
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'C&#243;digo',
                name: 'cod',
				id:'cod',
				width:100,
				changeCheck: function(){
							var v = this.getValue();
							act_data_store_cod_denominacion('codmil',v);
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
			                width:230,
							changeCheck: function()
							{
										var v = this.getValue();
										act_data_store_cod_denominacion('denmil',v);
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


function crear_grid_catalogo()
{
	crearFormBusqueda();
	crear_data_store_cod_denominacion();
		 
	 grid_cod_denominacion = new Ext.grid.GridPanel({
	 width:770,
	 height:400,
	 tbar: form_busqueda_cod_denominacion,
	 autoScroll:true,
     border:true,
     ds: dscodigo_denominacion,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codmil'},
          {header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denmil'}
       ]),
       	stripeRows: true,
      	viewConfig: {forceFit:true}     
      });            
} 

function pasarDatosGridMilco(registro)
{
	Ext.getCmp('codmil').setValue(registro.get('codmil'));
	Ext.getCmp('denmil').setValue(registro.get('denmil'));
		
	Actualizar=true;			
}



function mostrarCatalogoMilco()
{
				   crear_grid_catalogo();
				   ObjetoFuente='definicion';
                   ventana_cat_cod_denominacion = new Ext.Window(
                   {
                    //layout:'fit',
                    title: 'Cat&#225;logo Milco',
		    		autoScroll:true,
                    width:800,
                    height:400,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[grid_cod_denominacion],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	registro = grid_cod_denominacion.getSelectionModel().getSelected();
                    	switch(ObjetoFuente)   
                    	{
                    		case 'definicion':
                    			pasarDatosGridMilco(registro);
	                    	break;
                    		case 'grid':
		                    	PasDatosGridGrid(registro);
	                    	break;
                    		case 'objeto':
	                    		PasDatosGridObj(registro);
	                    	break;
 
                    		
                    	}          
                    	grid_cod_denominacion.destroy();
		      			ventana_cat_cod_denominacion.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	grid_cod_denominacion.destroy();
		      			ventana_cat_cod_denominacion.destroy();
                     }
                    }]
                    
                   });
                  ventana_cat_cod_denominacion.show();       
 

 }