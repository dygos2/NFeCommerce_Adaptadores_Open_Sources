<?php 

 //pegando os dados do post e tirando do UTF8
 $numeros = (isset($_POST['nfe_ide_nnf'])  ? utf8_decode($_POST['nfe_ide_nnf'])	: '');
 $serie = (isset($_POST['serie'])  ? utf8_decode($_POST['serie'])	: '');
 $status = (isset($_POST['statusdanota'])  ? utf8_decode($_POST['statusdanota'])	: '');
 $tentativas = (isset($_POST['tentativasDeInclusao'])  ? utf8_decode($_POST['tentativasDeInclusao'])	: '');
 $chaves  = (isset($_POST['chave_nfe'])  ? utf8_decode($_POST['chave_nfe'])	: '');
 $cstats = (isset($_POST['retEnviNFe_cStat'])  ? utf8_decode($_POST['retEnviNFe_cStat'])	: '');
 $motivos = (isset($_POST['retEnviNFe_xMotivo'])  ? utf8_decode($_POST['retEnviNFe_xMotivo'])	: '');
 $urls = (isset($_POST['url_danfe'])  ? utf8_decode($_POST['url_danfe'])	: '');

 //verificando se o retorno é referente a mais de uma NF-e
 $numeros_arr = explode("§",$numeros);
 $serie_arr = explode("§",$serie);
 $status_arr = explode("§",$status);
 $tentativas_arr = explode("§",$tentativas);
 $chaves_arr = explode("§",$chaves);
 $cstats_arr = explode("§",$cstats);
 $motivos_arr = explode("§",$motivos);
 $urls_arr = explode("§",$urls);
 
 for($i = 0; $i < count($numeros_arr); $i++){
  
  //trazendo a NF-e N que retorna do servidor
  //$numeros_arr[$i] -> representará a primeira numeração da primeira NF-e retornada o servidor
  
  //fazer a rotina de gravação no servidor...
  
  //dando o print para testar a saída do arquivo
  	echo("Nota arr[" . $i  . "]= " . $numeros_arr[$i] . "<br>");
  	echo("Serie arr[" . $i  . "]= " . $serie_arr[$i] . "<br>");
  	echo("Status arr[" . $i  . "]= " . $status_arr[$i] . "<br>");
  	echo("Tentativas arr[" . $i  . "]= " . $tentativas_arr[$i] . "<br>");
  	echo("Chaves arr[" . $i  . "]= " . $chaves_arr[$i] . "<br>");
  	echo("Status Sefaz arr[" . $i  . "]= " . $cstats_arr[$i] . "<br>");
  	echo("Motivos arr[" . $i  . "]= " . $motivos_arr[$i] . "<br>");
  	echo("Urls arr[" . $i  . "]= " . $urls_arr[$i] . "<br>");

	//comando para achar o cnpj dentro da chave da Nota
	echo("CNPJ da Nota =" . substr($chaves_arr[$i],6,14) . "<br>");
	echo("------------------------<br>");
		//'==============================================================================================
		//'O cliente pode criar um histórico dos retornos, ou mesmo verificar se já tem o registro, a manter o último status da nota.
		//'==============================================================================================
	
 };
 
	$open = fopen("e:\\home\\results\\retorno2.txt","w");

	fwrite($open,$numeros);
	fclose($open);

?>