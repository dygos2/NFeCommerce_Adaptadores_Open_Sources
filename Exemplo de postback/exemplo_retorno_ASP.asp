<%
	dim insert_sql, numeros, status, tentativas, chaves, cstats, motivos, urls, i, tmp_cnpj, serie

 numeros = request.form("nfe_ide_nnf")'numeração da NFe (gerado automaticamente pelo sistema)
 serie = request.form("serie")'série da NFe (gerado automaticamente pelo sistema)
 status = request.form("statusdanota")
 tentativas = request.form("tentativasDeInclusao")
 chaves = request.form("chave_nfe")
 cstats = request.form("retEnviNFe_cStat")
 motivos = request.form("retEnviNFe_xMotivo")
 urls = request.form("url_danfe")
 pedido_num = request.form("pedido_num")
 tp_amb = request.form("tp_amb")

' Caso queira gravar o resultado, retire esta linha 
' dim fs,tfile
' set fs=server.createobject("scripting.filesystemobject")
' set tfile=fs.createtextfile("e:\retorno.txt")

'tfile.writeline(serie)
'tfile.writeline(numeros)
'tfile.writeline(status)
'tfile.writeline(tentativas)
'tfile.writeline(chaves)
'tfile.writeline(cstats)
'tfile.writeline(motivos)
'tfile.writeline(urls)
'tfile.writeline(tp_amb)
'tfile.writeline(pedido_num)

 serie = split(serie,"§")
 numeros = split(numeros,"§")
 status = split(status,"§")
 tentativas = split(tentativas,"§")
 chaves = split(chaves,"§")
 cstats = split(cstats,"§")
 motivos = split(motivos,"§")
 urls = split(urls ,"§")
 pedido_num = split(pedido_num ,"§")
   
 'tfile.writeline(ubound(numeros))
 
 'inicio da conexão com o banco
 
 for i = 0 to ubound(numeros)
 
		tmp_cnpj = right(chaves(i),38)
		tmp_cnpj = left(tmp_cnpj,14)'pegando o cnpj

		'==============================================================================================
		'O cliente pode criar um histórico dos retornos, ou mesmo verificar se já tem o registro, a manter o último status da nota.
		'==============================================================================================
		
		insert_sql = "INSERT INTO tabela_de_retorno (`nfe_ide_nnf`,`NFe_emit_cnpj`,`serie`,`statusdanota`,`tentativasdeinclusao`,`chave_nfe`,`retenvinfe_cstat`,`retenvinfe_xmotivo`,`url_danfe`)VALUES(" & numeros(i) & ",'"& tmp_cnpj &"',"& serie(i) &",'"& status(i) &"','"&tentativas(i)&"','"&chaves(i)&"','"&cstats(i)&"','"&motivos(i)&"','"&urls(i)&"');"
		'tfile.writeline(insert_sql)
		
		con.execute insert_sql
 Next
 'fim da conexão com o banco
  
' tfile.writeline("Erro: " & err.description)
' tfile.close
' set tfile=nothing
' set fs=nothing
 %>