<!--incluir acesso ao bando de dados-->

<style type="text/css">
<!--
body {
	margin-top: 0px;
}
-->
</style>
<script type="text/javascript" language="javascript">
function SetaVal(cp1,cp2,cp3,cp4){

	 if (parent.parent.window.opener){
	   if (window.parent.parent.opener.document.Form_nfe4web){
	   
		 <%
		 	if request.QueryString("cp1") <> "" then
		 		response.Write("window.parent.parent.opener.document.Form_nfe4web." & request.QueryString("cp1") & ".value = cp1;")
		    end if
		  
		 	if request.QueryString("cp2") <> "" then
		 		response.Write("window.parent.parent.opener.document.Form_nfe4web." & request.QueryString("cp2") & ".value = cp2;")
		    end if
			
		 	if request.QueryString("cp3") <> "" then
		 		response.Write("window.parent.parent.opener.document.Form_nfe4web." & request.QueryString("cp3") & ".value = cp3;")
		    end if
			
		 	if request.QueryString("cp4") <> "" then
		 		response.Write("window.parent.parent.opener.document.Form_nfe4web." & request.QueryString("cp4") & ".value = cp4;")
		    end if
			%>
			   window.parent.close();
	  };
	};
}
</script>
<table width="100%" border="0">
  <tr>
    <td width="22%" bgcolor="#DFDFDF">Estado</td>
    <td width="10%" bgcolor="#DFDFDF">C&oacute;d. Estado </td>
    <td width="45%" bgcolor="#DFDFDF">Municipio</td>
    <td width="15%" bgcolor="#DFDFDF">C&oacute;d. Munic&iacute;pio </td>
    <%if request.QueryString("cp1") <> "" then%><td width="8%" bgcolor="#DFDFDF">&nbsp;</td><%end if%>
  </tr>
<%
dim rs10, sql, uf, mun

	uf = request.QueryString("uf")
	mun = request.QueryString("mun")

if uf = "" and mun = "" then
	uf = "xxxx"
	mun = "xxxxx"
end if

set rs10  = server.createobject("ADODB.recordset")
	sql = "select * from tb_ibge where cod_ibge like '" & uf & "%' and municipio like '%" & mun & "%'"
	rs10.open  sql, con,3,2
		do while not rs10.eof
%>
  <tr>
    <td width="22%"><%=rs10("estado")%></td>
    <td width="10%"><%=left(rs10("cod_ibge"),2)%></td>
    <td width="45%"><%=rs10("municipio")%></td> 
    <td width="15%"><%=rs10("cod_ibge")%></td>
    <%if request.QueryString("cp1") <> "" then%><td width="8%"><div align="center"><a href="#" onClick="SetaVal('<%=rs10("cod_ibge")%>','<%=replace(rs10("municipio"),"'","")%>','<%=rs10("uf")%>','<%=rs10("estado")%>')">selecionar</a></div></td><%end if%>
  </tr>
<%
		rs10.movenext
		loop
	rs10.close
set rs10 = nothing
%>
</table>
<!--fechando acesso ao banco de dados-->