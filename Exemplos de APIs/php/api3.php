<?php
header('content-type: application/json; charset=utf-8');

 echo $_GET['callback'] . '(
 	{
			"CNPJ":"99999999999999","CPF":"","xName":"Nome do cliente","xLgr":"Rua xyz","nro":"999","xCpl":"complemento end","xBairro":"xxx bairro","cMun":"999999","xMun":"XXX Municipio","UF":"sp","CEP":"99999999","fone":"99999999","IE":"999999999","email":"emaildocliente@email.com","indPag":"0","Item_Object":[{"cProd":"777","cEAN":"777","xProd":"Descricao do produto 1","NCM":"77","uCom":"unid","qCom":"2","subst":0,"vUnCom":"777.00","vFrete":0,"vDesc":0},{"cProd":"888","cEAN":"888","xProd":"Descricao do produto 2","NCM":"88","uCom":"unid","qCom":"3","subst":0,"vUnCom":"888.00","vFrete":0,"vDesc":0},{"cProd":"999","cEAN":"999","xProd":"Descricao do produto 3","NCM":"99","uCom":"unid","qCom":"4","subst":0,"vUnCom":"999.00","vFrete":0,"vDesc":0}],"transportador_Object":{"modFrete":"0","xNome":"Nome da transportadora","volume_Object":[{"qVol":"3","pesoL":"3.000","pesoB":"3.000"}]}});';
?>