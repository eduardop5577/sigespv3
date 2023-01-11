/***********************************************************************************
* @fecha de modificacion: 25/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

/*****************************************************************
  Funcion usada para ordenar las tablas mostradas en los catalogos
  por los distintos campos que se muestran en ella
******************************************************************/
function ue_ordenar(campo)
{
	f = document.form1;
	posicion = f.hidorden.value.indexOf(' ', 0);
    if (posicion != -1)
    {
		campo_aux = f.hidorden.value.substring(0,posicion);
		if (campo_aux == campo)
		{
			orden_aux = f.hidorden.value.substring(posicion+1,f.hidorden.value.length);
			if (orden_aux == 'ASC')
			{
				f.hidorden.value = campo+" DESC";
			}
			else
			{
				f.hidorden.value = campo+" ASC";
			}
		}
		else
		{
			f.hidorden.value = campo+" ASC";
		}
    }
	else
	{
		if (f.hidorden.value == campo)
		{
			f.hidorden.value = campo+" DESC";
		}
		else
		{
			f.hidorden.value = campo+" ASC";
		}
	}
	f.submit();
}
