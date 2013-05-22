<%
response.ContentType="application/json; charset=utf-8"
response.Write request.QueryString("callback") & "([{'xOrderID':'8888','xProcDate':'2010-12-30 18:59:59','xCustName':'Nome do cliente','xQtyItems':'3','xOrderStatusID':'2'},{'xOrderID':'9999','xProcDate':'2011-12-30 18:59:59','xCustName':'Nome do cliente2','xQtyItems':'2','xOrderStatusID':'1'}]);"
%>