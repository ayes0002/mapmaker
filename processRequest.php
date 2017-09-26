<?php
	///////////////////////////////////////////////////////////////////
	// File: processRequest.php
	// Author: Sebastian Lenczewski
	// Copyright: 2013, Sebastian Lenczewski, Algonquin College
	///////////////////////////////////////////////////////////////////

	require("data.php");
	
	$newMap = makeMapArray();
	$response = "";
	$mapName = "map1";
	
	if(isset($_GET['jsonMapData'])){
		$newMap = json_decode($_GET['jsonMapData']);
	}
	
	// Check $_GET values from buttons and call correct function
	if(isset($_GET['action'])){
		if($_GET['action'] == 'Upload'){
			uploadMapArray($mapName, $newMap);
		}
		else if($_GET['action'] == 'Download'){
			$newMap = downloadMapArray($mapName);
		}
		else if($_GET['action'] == 'Save'){
			saveMapArray($mapName, $newMap);
		}
		else if($_GET['action'] == 'Load'){
			$newMap = loadMapArray($mapName);
		}
		else if($_GET['action'] == 'Export'){
			exportMapArray($mapName, $newMap);
		
		}else if($_GET['action'] == 'New'){
			$newMap = makeMapArray();
		}
		
		
		
		if($_GET['action'] == 'Load' || $_GET['action'] == 'Download' || $_GET['action'] == 'New'){
			echo json_encode($newMap);
			
		}
		
		
		
	}
	
?>

