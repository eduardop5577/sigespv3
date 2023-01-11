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

var dsunidadadministradora="";
var grid_unidadadministradora="";
var form_busqueda_unidadadministradora="";
var registro_unidadadministradora="";
var ventana_cat_unidadadministradora="";

function crear_data_store_unidadadministradora()
{
	registro_unidadadministradora = Ext.data.Record.create([
						{name: 'coduac'},    
						{name: 'denuac'},
						{name: 'resuac'},
						{name: 'tipuac'}
					]);
	
	var objeto_unidadadministradora={"raiz":[{"coduac":'',"denuac":'',"resuac":'',"tipuac":''}]};
		
		dsunidadadministradora =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objeto_unidadadministradora),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
			registro_unidadadministradora
			),
			data: objeto_unidadadministradora
	  	})	
		

		var myJSONObject ={
		"oper": 'catalogo'
		}
		ObjSon=JSON.stringify(myJSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_spg_unidadadministradora.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			datos = resultado.responseText;
			var objeto_unidadadministradora = eval('(' + datos + ')');
			if(objeto_unidadadministradora!='')
			{
				dsunidadadministradora.loadData(objeto_unidadadministradora);
			}
		}	
	})
}

function act_data_store_unidadadministradora(criterio,cadena)
{
	dsunidadadministradora.filter(criterio,cadena,true,false);
}


function crear_form_busqueda_unidadadministradora()
{
		form_busqueda_unidadadministradora = new Ext.FormPanel({
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
                name: 'cod_uniadm',
				id:'cod_uniadm',
				changeCheck: function(){
							var v = this.getValue();
							act_data_store_unidadadministradora('coduac',v);
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


function CrearGrid()
{
		crear_form_busqueda_unidadadministradora();
		crear_data_store_unidadadministradora();
		 
	 grid_unidadadministradora = new Ext.grid.GridPanel({
	 width:770,
	 height:400,
	 tbar: form_busqueda_unidadadministradora,
	 autoScroll:true,
     border:true,
     ds: dsunidadadministradora,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'coduac'},
          {header: "Descripcion", width: 50, sortable: true, dataIndex: 'denuac'}
        ]),
       stripeRows: true,
      viewConfig: {
      	forceFit:true
      }
      });            
} 

function mostrar_catalogo()
{
	
				   CrearGrid();
				   ObjetoFuente='definicion';
                   ventana_cat_unidadadministradora = new Ext.Window(
                   {
                    //layout:'fit',
                    title: 'Cat&#225;logo Unidades Administadoras',
		    		autoScroll:true,
                    width:800,
                    height:400,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[grid_unidadadministradora],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	Registro = grid_unidadadministradora.getSelectionModel().getSelected();
                    	switch(ObjetoFuente)   
                    	{
                    		case 'definicion':
                    			limpiarCampos();
	                    		PasDatosGridDef(Registro);
	                    	break;
                    		case 'grid':
		                    	PasDatosGridGrid(Registro);
	                    	break;
                    		case 'objeto':
	                    		PasDatosGridObj(Registro);
	                    	break;
 
                    		
                    	}          
                    	grid_unidadadministradora.destroy();
		      			ventana_cat_unidadadministradora.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	grid_unidadadministradora.destroy();
		      			ventana_cat_unidadadministradora.destroy();
                     }
                    }]
                    
                   });
                  ventana_cat_unidadadministradora.show();       
 }