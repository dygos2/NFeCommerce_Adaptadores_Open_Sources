<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="-1">
<meta http-equiv="Cache-control" content="no-store">
<title>:: Selecionando Cidade e Estado ::</title>
<script type="text/javascript" language="javascript">

function altera_est(){

	document.getElementById("if_mun").src="uf_ajax.asp?cp1=<%=request.QueryString("cp1")%>&cp2=<%=request.QueryString("cp2")%>&cp3=<%=request.QueryString("cp3")%>&cp4=<%=request.QueryString("cp4")%>&uf=" + document.getElementById("estado").value + "&mun=" + document.getElementById("municipio").value
	
};

</script>

<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
.style1 {
	font-family: Arial, Helvetica, sans-serif;
	color: #FFFFFF;
	font-weight: bold;
}
.style2 {font-family: Arial, Helvetica, sans-serif}
-->
</style></head>
<body>
<table width="100%" border="0">
  <tr>
    <td height="36" colspan="2"><img src="http://208.109.236.57:8013/imgs/logo.jpg" width="186" height="56" /></td>
  </tr>
  <tr>
    <td height="36" colspan="2" bgcolor="#333333"><span class="style1">&nbsp;Sele&ccedil;&atilde;o de cidade e estado</span></td>
  </tr>
  <tr>
    <td width="25%" height="36" align="left" valign="top">
	
	<table width="100%" border="0">
        <tr>
          <td colspan="2" bgcolor="#333333"><span class="style1">Consulta por:</span></td>
        </tr>
        <tr>
          <td width="25%"><span class="style2">Estado:</span></td>
          <td width="75%">
<select name="estado" id="estado" onchange="altera_est();">
				<option value="" selected="selected">Busca por estado</option>
                <option value="12">Acre</option>
                <option value="27">Alagoas</option>
                <option value="16">Amap&aacute;</option>
                <option value="13">Amazonas</option>
                <option value="29">Bahia</option>
                <option value="23">Cear&aacute;</option>
                <option value="53">Distrito Federal</option>
                <option value="32">Esp&iacute;rito Santo</option>
                <option value="52">Goi&aacute;s</option>
                <option value="21">Maranh&atilde;o</option>
                <option value="51">Mato Grosso</option>
                <option value="50">Mato Grosso do Sul</option>
                <option value="31">Minas Gerais</option>
                <option value="25">Para&iacute;ba</option>
                <option value="41">Paran&aacute;</option>
                <option value="15">Par&aacute;</option>
                <option value="26">Pernambuco</option>
                <option value="22">Piau&iacute;</option>
                <option value="33">Rio de Janeiro</option>
                <option value="24">Rio Grande do Norte</option>
                <option value="43">Rio Grande do Sul</option>
                <option value="11">Rond&ocirc;nia</option>
                <option value="14">Roraima</option>
                <option value="42">Santa Catarina</option>
                <option value="35">S&atilde;o Paulo</option>
                <option value="28">Sergipe</option>
                <option value="17">Tocantins</option>
            </select>
		  </td>
        </tr>
        <tr>
          <td><span class="style2">Munic&iacute;pio:</span></td>
          <td><span class="style2">
            <input name="municipio" onkeyup="altera_est();" type="text" id="municipio" />
          </span></td>
        </tr>
      </table>
	
	</td>

    <td width="75%" align="left" valign="top">
	<iframe src="uf_ajax.asp?cp1=<%=request.QueryString("cp1")%>&cp2=<%=request.QueryString("cp2")%>&cp3=<%=request.QueryString("cp3")%>&cp4=<%=request.QueryString("cp4")%>" id="if_mun" name="if_mun" width="100%" height="300" frameborder="0"></iframe>	
    </td>

</tr>
</table>
</body>
</html>