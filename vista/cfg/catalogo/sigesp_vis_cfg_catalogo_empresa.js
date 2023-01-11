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

var dataStoreEmpresa="";
var formularioBusquedaEmpresa="";
var gridEmpresa="";
var ventanaEmpresa="";

function crearDataStoreEmpresa()
{

	registroEmpresa = Ext.data.Record.create([
								{name:'codemp'},			  
								{name:'nombre'}, 
								{name:'titulo'}, 
								{name:'sigemp'}, 
								{name:'direccion'}, 
								{name:'telemp'}, 
								{name:'faxemp'}, 
								{name:'email'}, 
								{name:'website'}, 
								{name:'m01'}, 
								{name:'m02'}, 
								{name:'m03'}, 
								{name:'m04'}, 
								{name:'m05'}, 
								{name:'m06'}, 
								{name:'m07'}, 
								{name:'m08'}, 
								{name:'m09'}, 
								{name:'m10'}, 
								{name:'m11'}, 
								{name:'m12'}, 
								{name:'periodo'}, 
								{name:'vali_nivel'}, 
								{name:'esttipcont'}, 
								{name:'formpre'}, 
								{name:'formcont'}, 
								{name:'formplan'}, 
								{name:'formspi'}, 
								{name:'activo'}, 
								{name:'pasivo'}, 
								{name:'ingreso'}, 
								{name:'gasto'}, 
								{name:'resultado'}, 
								{name:'capital'}, 
								{name:'c_resultad'}, 
								{name:'c_resultan'}, 
								{name:'orden_d'}, 
								{name:'orden_h'}, 
								{name:'soc_gastos'}, 
								{name:'soc_servic'}, 
								{name:'activo_h'}, 
								{name:'pasivo_h'}, 
								{name:'resultado_h'}, 
								{name:'ingreso_f'}, 
								{name:'gasto_f'}, 
								{name:'ingreso_p'}, 
								{name:'gasto_p'}, 
								{name:'numniv'}, 
								{name:'nomestpro1'}, 
								{name:'nomestpro2'}, 
								{name:'nomestpro3'}, 
								{name:'nomestpro4'}, 
								{name:'nomestpro5'}, 
								{name:'rifemp'},
								{name:'nitemp'},
								{name:'estemp'}, 
								{name:'ciuemp'}, 
								{name:'zonpos'}, 
								{name:'estmodape'},  
								{name:'codorgsig'}, 
								{name:'socbieser'}, 
								{name:'estmodest'}, 
								{name:'salinipro'}, 
								{name:'salinieje'}, 
								{name:'numordcom'}, 
								{name:'numordser'}, 
								{name:'numsolpag'}, 
								{name:'nomorgads'}, 
								{name:'numlicemp'}, 
								{name:'concomiva'}, 
								{name:'estmodiva'}, 
								{name:'activo_t'}, 
								{name:'pasivo_t'}, 
								{name:'resultado_t'}, 
								{name:'c_financiera'}, 
								{name:'c_fiscal'}, 
								{name:'diacadche'}, 
								{name:'codasiona'}, 
								{name:'loncodestpro1'}, 
								{name:'loncodestpro2'},
								{name:'loncodestpro3'},
								{name:'loncodestpro4'},
								{name:'loncodestpro5'},  
								{name:'conrecdoc'}, 
								{name:'nroivss'}, 
								{name:'nomrep'}, 
								{name:'cedrep'}, 
								{name:'telfrep'}, 
								{name:'cargorep'},  
								{name:'clactacon'},
								{name:'estempcon'},
								{name:'codaltemp'}, 
								{name:'basdatcon'}, 
								{name:'estcamemp'}, 
								{name:'estparsindis'},
								{name:'basdatcmp'},
								{name:'confinstr'}, 
								{name:'estintcred'},
								{name:'estmodpartsep'}, 
								{name:'estmodpartsoc'}, 
								{name:'estmanant'}, 
								{name:'estpreing'},
								{name:'estretiva'}, 
								{name:'modageret'},
								{name:'concommun'}, 
								{name:'confiva'}, 
								{name:'casconmov'}, 
								{name:'estmodprog'}, 
								{name:'confi_ch'}, 
								{name:'ctaresact'},
								{name:'ctaresant'}, 
								{name:'dedconproben'}, 
								{name:'estaprsep'}, 
								{name:'sujpasesp'}, 
								{name:'bloanu'},
								{name:'contintmovban'},
								{name:'valinimovban'},
								{name:'estretmil'},
								{name:'concommil'},
								{name:'dirvirtual'},
								{name:'estceniva'},
								{name:'ctaejeprecie'},
								{name:'envcorsup'},
								{name:'capiva'},
								{name:'estciesem'},
								{name:'estspgdecimal'},
								{name:'nivapro'},
								{name:'estcencos'},
								{name:'inicencos'},
								{name:'fincencos'},
								{name:'cueproacu'},
								{name:'cuedepamo'},
								{name:'parcapiva'},
								{name:'estaprsoc'},
								{name:'valclacon'},
								{name:'valcomrd'},
								{name:'repcajchi'},
								{name:'estafenc'},
								{name:'cedben'},
								{name:'nomben'},
								{name:'scctaben'},
								{name:'estcomobr'},
								{name:'numrefcarord'},
								{name:'tiesesact'},
								{name:'blocon'},
								{name:'intblocon'},
								{name:'valestpre'},
								{name:'nivvalest'},
								{name:'estretislr'},
								{name:'estaprcxp'},
								{name:'estspidecimal'},
								{name:'estcommas'},
								{name:'valiniislr'},
								{name:'estcanret'},
								{name:'costo'},
								{name:'estconcom'},
								{name:'nroinicom'},
                                                                {name:'scforden_d'},
                                                                {name:'scforden_h'}
						]);							
	
		var objetoEmpresa={"raiz":[{"codemp":"","nombre":"","titulo":"","sigemp":"","direccion":"","telemp":"",
								   "faxemp":"","email":"","website":"","m01":"0","m02":"0","m03":"0","m04":"0",
								   "m05":"0","m06":"0","m07":"0","m08":"0","m09":"0","m10":"0","m11":"0","m12":"0",
								   "periodo":"","vali_nivel":"","esttipcont":"0","formpre":"",
								   "formcont":"","formplan":"","formspi":"",
								   "activo":"1","pasivo":"2","ingreso":"5","gasto":"6","resultado":"3","capital":"3",
								   "c_resultad":"","c_resultan":"","orden_d":"","orden_h":"","soc_gastos":"","soc_servic":"",
								   "gerente":"","jefe_compr":"","activo_h":"","pasivo_h":"","resultado_h":"","ingreso_f":"",
								   "gasto_f":"","ingreso_p":"","gasto_p":"","logo":"","numniv":"",
								   "nomestpro1":"","nomestpro2":"",
								   "nomestpro3":"","nomestpro4":"","nomestpro5":"","estvaltra":"0","rifemp":"",
								   "nitemp":"","estemp":"","ciuemp":"","zonpos":"","estmodape":"0","estdesiva":"0","estprecom":"0",
								   "estmodsepsoc":"0","codorgsig":"","socbieser":"1","estmodest":"1","salinipro":"0",
								   "salinieje":"0","numordcom":"0","numordser":"0","numsolpag":"0","nomorgads":"",
								   "numlicemp":"","modageret":"","nomres":"","concomiva":"","cedben":"",
								   "nomben":"","scctaben":"","estmodiva":"1","activo_t":"","pasivo_t":"","resultado_t":"",
								   "c_financiera":"","c_fiscal":"","saliniproaux":"0","saliniejeaux":"0","diacadche":"0",
								   "codasiona":"","loncodestpro1":"0","loncodestpro2":"0","loncodestpro3":"0","loncodestpro4":"0",
								   "loncodestpro5":"0","candeccon":"2","tipconmon":"0","redconmon":"2","conrecdoc":"0",
								   "estvaldis":"1","nroivss":"","nomrep":"","cedrep":"","telfrep":"","cargorep":"","estretiva":"",
								   "clactacon":"1","estempcon":"1","codaltemp":"","basdatcon":"","estcamemp":"0",
								   "estparsindis":"1","basdatcmp":"","estciespg":"0","estciespi":"0","confinstr":"N",
								   "estintcred":"1","estciescg":"0","estvalspg":"0","ctaspgrec":"","ctaspgced":"",
								   "estmodpartsep":"0","estmodpartsoc":"0","estmanant":"0","estpreing":"1","concommun":"",
								   "confiva":"","casconmov":"0","estmodprog":"0","confi_ch":"0","dirvirtual":"sigesp_apr",
								   "ctaresact":"","ctaresant":"","estvaldisfin":"","dedconproben":"1","estaprsep":"1",
								   "sujpasesp":"0","bloanu":"0","contintmovban":"0","valinimovban":"0","estretmil":"C","concommil":"0","costo":"","scforden_d":"","scforden_h":"","ctaejeprecie":""}]};
		
		dataStoreEmpresa =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objetoEmpresa),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
		    registroEmpresa  
			),
			data: objetoEmpresa
	  	})	
		
		var myJSONObject ={
			"oper": 'catalogo'
		}
		
		ObjSon=Ext.util.JSON.encode(myJSONObject);
		parametros = 'ObjSon='+ObjSon;
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_empresa.php',
		params : parametros,
		method: 'POST',
		success: function ( result, request) 
		{ 
			datos = result.responseText;
			var objetoEmpresa = eval('(' + datos + ')');
			if(objetoEmpresa!='')
			{
				dataStoreEmpresa.loadData(objetoEmpresa);
			}
		}	
	})
}

function actDataStoreEmpresa(criterio,cadena)
{
	dataStoreEmpresa.filter(criterio,cadena,true,false);
}


function crearFormularioBusqueda()
{
		formularioBusquedaEmpresa = new Ext.FormPanel({
        labelWidth: 75,
        frame:true,
        title: 'B&#250;squeda',
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
							actDataStoreEmpresa('codemp',v);
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
										actDataStoreEmpresa('nombre',v);
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


function crearGridEmpresa()
{
	crearFormularioBusqueda();
	crearDataStoreEmpresa();
		 
	 gridEmpresa = new Ext.grid.GridPanel({
	 width:770,
	 height:400,
	 tbar: formularioBusquedaEmpresa,
	 autoScroll:true,
     border:true,
     ds: dataStoreEmpresa,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codemp'},
          {header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'nombre'}
       ]),
       	stripeRows: true,
      	viewConfig: {
      	forceFit:true
      },
      listeners:{'celldblclick' : function( grid, fila, columna, evento ){
    	  registro = grid.getSelectionModel().getSelected();
    	  limpiarCampos();
		  pasarDatosEmpresa(registro);
		  validarIvaConfigurado(registro.get('codemp'));
		  validarFormatoCuentasIngreso(registro.get('codemp'));
		  validarFormatoCuentasGasto(registro.get('codemp'));
		  validarFormatoCuentasContables(registro.get('codemp'));
		  validarEstructuras(registro.get('codemp'));
		  validarApertura(registro.get('codemp'));
		  var formcont   = registro.get('formcont');
	      var formcont_1 = formcont.substring(0,15);
	      var formcont_2 = formcont.substring(15,formcont.length);
	      Ext.getCmp('formcont_1').setValue(formcont_1);
	      Ext.getCmp('formcont_2').setValue(formcont_2);
		  Ext.getCmp('tabempresa').activate('tabdefempresa');
		  grid.destroy();
		  ventanaEmpresa.destroy();
      }}
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
	url : '../../controlador/cfg/sigesp_ctr_cfg_empresa.php',
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

function pasarDatosEmpresa(Registro)
{
	for(i=0;i<Campos.length;i++)
	{
		var valor = Registro.get(Campos[i][0]);
		if(Registro.get(Campos[i][0])!='' && Registro.get(Campos[i][0]))
		{
			if(Ext.getCmp(Campos[i][0]).isXType("radiogroup"))
			{
				for( var j=0; j < Ext.getCmp(Campos[i][0]).items.length; j++ ) 
				{
					if(valor==Ext.getCmp(Campos[i][0]).items.items[j].inputValue)
					{
						Ext.getCmp(Campos[i][0]).items.items[j].setValue(true);
						break;
					}
				}
			}
			else if(Ext.getCmp(Campos[i][0]).isXType("checkbox"))
			{
				if(valor==Ext.getCmp(Campos[i][0]).inputValue)
				{	
					Ext.getCmp(Campos[i][0]).setValue(true);
				}
			}
			else if(Ext.getCmp(Campos[i][0]).isXType("combo"))
			{
				Ext.getCmp(Campos[i][0]).setValue(valor);	
			}
			else if(Ext.getCmp(Campos[i][0]).isXType("datefield"))
			{
				Ext.getCmp(Campos[i][0]).setValue(Registro.get(Campos[i][0]));
			}
			else
			{
			  Ext.get(Campos[i][0]).dom.value =valor;	
			}
		}
	}
	dirvirtual = Registro.get('dirvirtual');
	Actualizar = true;
	bloquearCamposPrimarios();
}

function irBuscar() {
	crearGridEmpresa();
	ventanaEmpresa = new Ext.Window({
    	title: 'Cat&#225;logo de Empresas',
		autoScroll:true,
        width:820,
        height:400,
        modal: true,
        closable:false,
        plain: false,
        items:[gridEmpresa],
        buttons: [{
	        text:'Aceptar',  
	        handler: function() {
	        	limpiarFormulario(formempresa);
	        	registro = gridEmpresa.getSelectionModel().getSelected();
	        	pasarDatosEmpresa(registro);
	        	validarIvaConfigurado(registro.get('codemp'));
	        	validarFormatoCuentasIngreso(registro.get('codemp'));
	        	validarFormatoCuentasGasto(registro.get('codemp'));
	        	validarFormatoCuentasContables(registro.get('codemp'));
	        	validarEstructuras(registro.get('codemp'));
	        	validarApertura(registro.get('codemp'));
	        	var formcont   = registro.get('formcont');
	        	var formcont_1 = formcont.substring(0,15);
	        	var formcont_2 = formcont.substring(15,formcont.length);
	        	Ext.getCmp('formcont_1').setValue(formcont_1);
	        	Ext.getCmp('formcont_2').setValue(formcont_2);
	            gridEmpresa.destroy();
			    ventanaEmpresa.destroy();                      
			}
       	},
		{
			text: 'Salir',
         	handler: function() {
         		gridEmpresa.destroy();
  				ventanaEmpresa.destroy();
         	}
		}]
	});
    ventanaEmpresa.show();       
}