<?php
header('content-type: application/json; charset=utf-8');

 echo $_GET['callback'] . '([{"xOrderStatusID":"1","xStatus":"Novo pedido"},{"xOrderStatusID":"2","xStatus":"Aguardando pagamento"},{"xOrderStatusID":"3","xStatus":"Pagamento aprovado"}]);';
 ?>