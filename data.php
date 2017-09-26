<?php

    function logOutput($text){
        $logFile = fopen("serverLog.txt", "a") or die ("Unable to open file!");
        fwrite($logFile, $text);
        fclose($logFile);
    }


	// Make a function that creates and returns a new blank map array
	function makeMapArray()
	{
		// Make an array that will hold the map array data to be returned.
		$map = array();
		// Loop through the height and width as array positions
		for($y=0;$y<10;$y++)
		{
			for($x=0;$x<10;$x++)
			{
				// Set the value at this position to 0
				$map[$y][$x]=0;
			}
		}
		//Return the array
		return $map;
	}
	

	
	// Make a function that saves the map array $tileMapArray to an SQLite
	// table with the name as $tableName
	function saveMapArray($tableName, $tileMapArray)
	{

		// Open a PDO connection to the SQLite file called final.sqlite
		$db_file = new PDO('sqlite:final.sqlite');
        
		// Check if the table exists by doing a select on the SQLite 
		// table called 'sqlite_master' to check if the table name 
		// exists.  Remember to fetch the data out of the results
		$results = $db_file->query("SELECT count(name) from sqlite_master WHERE name = '" . $tableName . "'");
        $tableExist = $results->fetch(PDO::FETCH_NUM);
		
        // If the results are 0 the table does not exist, and you must 
		// create the SQLite table.
		if($tableExists[0]<1){
            $db_file->exec("CREATE TABLE `". $tableName . "` (`position_id` INTEGER PRIMARY KEY AUTOINCREMENT, `position_row` INTEGER, `position_col` INTEGER, `tile_id` INTEGER)");    
        }
		// Else if it does exist you must empty the SQLite table. (do not drop table)
		
        else {
            $db_file->exec("DELETE FROM" . $tableName);
            $db_file->exec("DELETE FROM sqlite_sequence WHERE name = '" . $tableName . "'");
        }
        
		// Generate one single SQLite query to insert all the $tileMapArray values to SQLite table 
		// by looping through the array called $tileMapArray.
		$query = "INSERT INTO '" . $tableName . "' ";
        
        for ($y = 0; $y<count($tileMapArray); $y++){
            for ($x = 0; $x<count($tileMapArray); $x++){
                if(isset($tileMapArray[$y][$x])){
                    if($x == 0 && $y == 0){
                        $query .= "SELECT NULL AS position_id, '$y' AS position_row, '$x' AS position_col, '" . $tileMapArray[$y][$x] . "' AS tile_id ";
                    } else {
                        $query .= "UNION SELECT NULL, '$y', '$x', '" . $tileMapArray[$y][$x] ."' ";
                    }
                }
            }
        }
		// Execute the query to insert the array data
		$db_file->exec($query);
		// Close connection to PDO object.
		$db_file = NULL;
	}
	
	
	
	// Make a function that loads data from the specified SQLite 
	// table as an array, and returns the array back to the application
	function loadMapArray($tableName)
	{
		
		//Make an empty array that will hold the map array data to be returned.
		$map = array();
		
		// Check if the file 'final.sqlite' exists on the server
		
        if(file_exists("final.sqlite")){
			
            // If the db file exists, open a link to it
            
			$db_file = new PDO('sqlite:final.sqlite');
			
            // Run a select query to return the whole table
			
            $results = $db_file->query("SELECT * FROM " . $tableName);
            
			// If the results are not empty, set the given array position to the value 'tile_id'.
			
            if(!empty($results)){
                while($result = $results->fetch(PDO::FETCH_ASSOC)){
                    $rowNum = $result["position_row"];
                    $colNum = $result["position_col"];
                    
                    $map[$rowNum][$colNum] = $result["tile_id"];
                    
                }
                
            }
            
			// Else, if the reults are empty, set the $map array equal to the return 
			// of the function makeMapArray(10,10,0)
			else {
                $map = makeMapArray();
                
            }
			// Close link to database
            $db_file = NULL;
        }
		// Else, if the SQLite file does not exist, set the $map array equal to the return
		// of the function makeMapArray(10,10,0) 
        else {
            $map = makeMapArray();
        }
		
		// Return the $map array
		return $map;
		
	}
	
	
	
	// Create a function that takes map array data and inserts it into a 
	// given MySQL table in a database called final
	function uploadMapArray($tableName, $tileMapArray)
	{

		// Connect to the database by creating a new PDO object
		
        $db_host = "localhost";
        $db_name = "finalMap";
        $db_user = "root";
        $db_password = "root";
     
        $db_link = new PDO("mysql:host=$db_host;dbname=$db_name",$db_user,$db_password);
		
        // Create a table IF NOT EXISTS for the given $tableName
		
        $db_link->exec("CREATE TABLE IF NOT EXISTS `" . $tableName . "` (
        `position_id` int(10) unsigned AUTO_INCREMENT PRIMARY KEY,
        `position_row` int(11) NOT NULL,
        `position_col` int(11) NOT NULL,
        `tile_id` int(11) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

		// Run a truncate query on the table to remove any data
        
        $db_link->exec("TRUNCATE TABLE " . $tableName);
        
		// Loop through the the $tileMapArray array to generate a single query to 
		// insert all the records from the $tileMapArray into the MySQL table.
		$query = "INSERT INTO " . $tableName . "(position_id,position_row,position_col,tile_id) VALUES ";
        for ($y = 0; $y<count($tileMapArray); $y++){
            for ($x = 0; $x<count($tileMapArray); $x++){
                if(isset($tileMapArray[$y][$x])){
                    
                    $query .="(0,". $y .',' .$x .','. $tileMapArray[$y][$x]."),";
                
                }
            }
        }
        $query=substr($query,0,-1);
        $query.=";";
        
		// Execute the insert query on the MySQL table
		
        $db_link->exec($query);
        
		// Close the PDO link to the database
		$db_link = NULL;
        
	}
	
	
	
	// Create a function that selects the map data from the MySQL table 
	// and returns it as an array to the application.
	function downloadMapArray($tableName)
	{
        
		//Make an empty array that will hold the map array data to be returned.
		$map = array();
		
		// Connect to the database by creating a new PDO object
        
		$db_host = "localhost";
        $db_name = "finalMap";
        $db_user = "root";
        $db_password = "root";
     
        $db_link = new PDO("mysql:host=$db_host;dbname=$db_name",$db_user,$db_password);
        
		// Use a select query to get all the records from the specified table
		
        $table = $db_link->query("SELECT * FROM " . $tableName);
        
		// If the results are not empty, set the given array position to the value 'tile_id'.
		// Each row in the table has the 'position_row' and 'position_col' 
		// stored telling you what array position to fill.
        if(!empty($table)){
            
            while($tb = $table->fetch(PDO::FETCH_ASSOC)){
                $row = $tb["position_row"];
                $col = $tb["position_col"];
            
                $map[$row][$col] = $tb["tile_id"];

            }
        }
        
		
		// Else if the results are empty, then set the $map array equal to the return
		// value of the function call makeMapArray(10,10,0)
		else {
            $map = makeMapArray();
        }
		// Close the PDO link to the MySQL database
		
        $db_link = NULL;
        
		// Return the $map array
		return $map;
	}
	
	
	
	// Create a function to export the given array $tileMapArray to an XML file.  
	// The root node of this document should be named with the value in $tableName.
	function exportMapArray($tableName, $tileMapArray)
	{
		// Create a string variable formated with the header of a valid XML document.
        
        $xmlString = "<?xml version='1.0' standalone='yes'?>\n";
        
        // Concatenate the root node named with the $tableName value
        $xmlString .= "<$tableName>\n";
        
		// Loop through the $tileMapArray, each row of the array being a set of 10 tiles,
		// and and each value of a given row being a specific tile.
		$rows = 1;
        foreach($tileMapArray as $row) {
            $xmlString .=   "<Row_" . $rows .">\n\n";

			// Loop through each record to concatinate each value inside the <col> node
            foreach( $row as $col => $value){
                $xmlString .=   "<col>"   . $col .   "</col>\n";
                $xmlString .=   "<tile>"   . $value .   "</tile>\n \n";
            }
            $xmlString .=  "</Row_" . $rows .">\n\n";
            $rows = $rows+1;
        }
		// Close the root node to end the XML structure
		
        $xmlString .= "</$tableName>\n";
        
		// Use the string variable to generate a SimpleXMLElement
		
        $mapXml = new SimpleXMLElement($xmlString);
        
		// Save the SimpleXMLElement to a file with the value of $tableName as the name
        
        $mapXml->asXML("$tableName.xml");
                
	}
	
	
?>

