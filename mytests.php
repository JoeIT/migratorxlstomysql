<?php

require_once('lib_functions.php');
require_once('query_manager.php');

echo 'Generando script SQL...</br>';

// Include PHPExcel
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';
require_once dirname(__FILE__) . '/Classes/PHPExcel/IOFactory.php';

$excelFileToRead = 'users_oficial.xlsx';
$scriptFile = 'intranet_script.sql';

try {
    $inputFileType = PHPExcel_IOFactory::identify($excelFileToRead);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($excelFileToRead);
} catch (Exception $e) {
    die('Error al cargar archivo: "' . pathinfo($excelFileToRead, PATHINFO_BASENAME) . '": ' . $e->getMessage());
}

$date = date('d-m-Y');
$timestamp = strtotime($date);

$functs = new LibFunctions();

//  Get worksheet dimensions
$sheet = $objPHPExcel->getSheet(0);
$maxRow = $sheet->getHighestRow();
$maxColumn = $sheet->getHighestColumn();

//$usersQuery = "INSERT INTO `users` (`iduser`, `code`, `firstname`, `lastname`, `email`, `username`, `password`, `salt`, `avatar`, `cover`, `gender`, `born`, `aboutme`, `codecountry`, `idregion`, `city`, `lat`, `lon`, `num_friends`, `num_followers`, `num_following`, `num_comments`, `num_albums`, `num_posts`, `num_posts_inwall`, `num_likes`, `privacy`, `validated`, `datevalidated`, `verified`, `active`, `registerdate`, `ipregister`, `previousaccess`, `ippreviousaccess`, `lastaccess`, `iplastaccess`, `lastclick`, `num_activities`, `num_notifications`, `num_chats`, `num_pages`, `num_groups`, `auth`, `auth_id`, `facebook`, `twitter`, `linkedin`, `gplus`, `coderecovery`, `who_write_on_my_wall`, `who_can_sendme_messages`, `is_network_admin`, `leveladmin`) VALUES \n";
//$friendsQuery = "\n\nINSERT INTO friends (`user1`, `user2`, `shipdate`, `accepted`) VALUES \n";
//$relationsQuery = "\n\nINSERT INTO relations (`leader`, `type_leader`, `subscriber`, `rltdate`) VALUES \n";

$usersQuery = new QueryManager('INSERT INTO `users` (`iduser`, `code`, `firstname`, `lastname`, `email`, `username`, `password`, `salt`, `avatar`, `cover`, `gender`, `born`, `aboutme`, `codecountry`, `idregion`, `city`, `lat`, `lon`, `num_friends`, `num_followers`, `num_following`, `num_comments`, `num_albums`, `num_posts`, `num_posts_inwall`, `num_likes`, `privacy`, `validated`, `datevalidated`, `verified`, `active`, `registerdate`, `ipregister`, `previousaccess`, `ippreviousaccess`, `lastaccess`, `iplastaccess`, `lastclick`, `num_activities`, `num_notifications`, `num_chats`, `num_pages`, `num_groups`, `auth`, `auth_id`, `facebook`, `twitter`, `linkedin`, `gplus`, `coderecovery`, `who_write_on_my_wall`, `who_can_sendme_messages`, `is_network_admin`, `leveladmin`) VALUES');
$friendsQuery = new QueryManager('INSERT INTO friends (`user1`, `user2`, `shipdate`, `accepted`) VALUES');
$relationsQuery = new QueryManager('INSERT INTO relations (`leader`, `type_leader`, `subscriber`, `rltdate`) VALUES');

//  Loop through each row of the worksheet in turn
for ($index = 2; $index <= $maxRow; $index++) {
    //  Read a index of data into an array
    $rowData = $sheet->rangeToArray('A' . $index . ':' . $maxColumn . $index, NULL, TRUE, FALSE);
    
	$code = $functs->uniqueCode(11, 1, 'users', 'code');
	$newpass_md5 = md5($rowData[0][6]);
	$salt = md5(uniqid(rand(),true));
	$hash = hash('sha512', $salt.$newpass_md5);
	$coderecovery = $functs->getCode(20, 0);
	
	$id = $index - 1;
	$query = "$id, "
	."'$code', " //code
	."'".$rowData[0][2]."', " //firstname
	."'".$rowData[0][3]."', " //lastname
	."'".$rowData[0][4]."', " //email
	."'".$rowData[0][5]."', " //user
	."'$hash', " //password
	."'$salt', " //salt
	."'".$rowData[0][8]."', " //avatar
	."'".$rowData[0][9]."', " //cover
	.$rowData[0][10].", " //gender
	."'".$rowData[0][11]."', " //born
	."'".$rowData[0][12]."', " //aboutme
	."'".$rowData[0][13]."', " //codecountry
	.$rowData[0][14].", " //idregion
	."'".$rowData[0][15]."', " //city
	."'".$rowData[0][16]."', " //lat
	."'".$rowData[0][17]."', " //lon
	.($maxRow - 2).", " //num_friends
	.($maxRow - 2).", " //num_followers
	.($maxRow - 2).", " //num_following
	.$rowData[0][21].", " //num_comments
	.$rowData[0][22].", " //num_albums
	.$rowData[0][23].", " //num_posts
	.$rowData[0][24].", " //num_posts_inwall
	.$rowData[0][25].", " //num_likes
	.$rowData[0][26].", " //privacy
	.$rowData[0][27].", " //validated
	.$rowData[0][28].", " //datevalidated
	.$rowData[0][29].", " //verified
	.$rowData[0][30].", " //active
	.$rowData[0][31].", " //registerdate
	.$rowData[0][32].", " //ipregister
	.$rowData[0][33].", " //previousaccess
	.$rowData[0][34].", " //ippreviousaccess
	.$rowData[0][35].", " //lastaccess
	.$rowData[0][36].", " //iplastaccess
	.$rowData[0][37].", " //lastclick
	.$rowData[0][38].", " //num_activities
	.$rowData[0][39].", " //num_notifications
	.$rowData[0][40].", " //num_chats
	.$rowData[0][41].", " //num_pages
	.$rowData[0][42].", " //num_groups
	."'".$rowData[0][43]."', " //auth
	."'".$rowData[0][44]."', " //auth_id
	."'".$rowData[0][45]."', " //facebook
	."'".$rowData[0][46]."', " //twitter
	."'".$rowData[0][47]."', " //linkedin
	."'".$rowData[0][48]."', " //gplus
	."'$coderecovery', " //coderecovery
	.$rowData[0][50].", " //who_write_on_my_wall
	.$rowData[0][51].", " //who_can_sendme_messages
	.$rowData[0][52].", " //is_network_admin
	.$rowData[0][53]; //leveladmin
	
	$usersQuery->addQueryValues($query);
	
	for($lasts_ids = 1; $lasts_ids < $id; $lasts_ids++)
	{
		// Friends data generation
		//'user1', 'user2', 'shipdate', 'accepted'
		$friendsQuery->addQueryValues("$id, $lasts_ids, $timestamp, $timestamp");
		
		// Relations data generation
		//'leader', 'type_leader', 'subscriber', 'rltdate'
		$relationsQuery->addQueryValues("$id, 0, $lasts_ids, $timestamp");
		$relationsQuery->addQueryValues("$lasts_ids, 0, $id, $timestamp");
	}
}

// Generation sql file
$script = fopen($scriptFile, "w") or die("Unable to open file!");
fwrite($script, $usersQuery->getQuery());
fwrite($script, "\n\n");
fwrite($script, $friendsQuery->getQuery());
fwrite($script, "\n\n");
fwrite($script, $relationsQuery->getQuery());
fclose($script);

echo "Archivo: $scriptFile generado</br>";
echo "Generación finalizada!!!</br></br>";
echo 'Empleados: '. ($maxRow - 1) .'</br>';

//echo $usersQuery->getHtmlQuery() .'</br>';
//echo $friendsQuery->getHtmlQuery() .'</br>';
//echo $relationsQuery->getHtmlQuery() .'</br>';

/*
$functs = new LibFunctions();
$code = $functs->uniqueCode(11, 1, 'users', 'code');

$newpass_md5 = md5('123456');
$salt = md5(uniqid(rand(),true));

$hash = hash('sha512', $salt.$newpass_md5);

$coderecovery = $functs->getCode(20, 0);
$date = date('d-m-Y');
$timestamp = strtotime($date);

echo "Generated code:			$code</br>";
echo "Generated password:		$hash</br>";
echo "Generated salt:			$salt</br>";
echo "Generated coderecovery:	$coderecovery</br>";
echo "Date: $date</br>";
echo "Generated timestamp:		$timestamp</br></br>";
*/