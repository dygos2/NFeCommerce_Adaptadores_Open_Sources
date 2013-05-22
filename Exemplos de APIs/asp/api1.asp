<%
response.ContentType="application/json; charset=utf-8"
response.Write request.QueryString("callback") & "([{'xOrderStatusID':'1','xStatus':'Novo pedido'},{'xOrderStatusID':'2','xStatus':'Aguardando pagamento'},{'xOrderStatusID':'3','xStatus':'Pagamento aprovado'}]);"
%>