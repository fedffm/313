<?php
// Start by loading the database
require("dbConnector.php");
$db = loadDatabase();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Test</title>
</head>

<body>
	<form action="test.php" method="POST">
		<input type="text" id="searchinput" name="search"placeholder="Search"> <br/> <br/>
		<button type="submit" name="button">Submit</button>
	</form>

<?php
//Call the search method
search($db);

/******************************************************************************
* SEARCH
* This function uses the provided input to build a query
*******************************************************************************/
function search($db) {

	$query = buildQuery($db);

	if ($query)
		echo "The resulting query: $query<br/>";
	else
		echo "Returning false...<br/>";
}

/******************************************************************************
* BUILD QUERY
* This function uses the provided input to build a query
*******************************************************************************/
function buildQuery($db) 
{
	$query = "";     // This will be our MySQL query
	$found = FALSE;  // Whether or not the above query returns any rows

	if ($_SERVER['REQUEST_METHOD'] === 'POST') 
	{
		if (isset($_POST['button'])) 
		{
			$searchWord = $_POST['search'];

			// Break up the user's input into multiple words (keywords)
			$keywords = explode(" ", $searchWord);

			// This loop will query the database for each individual keyword
			for ($i = 0; $i < str_word_count($searchWord); $i++)
			{
				$searchQuery = $keywords[$i];

				// Insert an apostrophe, if the user forgot to put one 
				$newString = insertApostrophe($searchQuery);

				if ($newString)
					$searchQuery = $newString;

				$searchQuery = addslashes($searchQuery);
				$query 	  	 = determineQueryType($searchQuery);

				if (executeQuery($db, $query))
					return $query;
			}
			return FALSE;
		} 
	}
	else 
	{
		die();
	}	
}

/******************************************************************************
* INSERT APOSTROPHE
* This function gives an apostrophe to any word ending in 's'
*******************************************************************************/
function insertApostrophe($searchQuery)
{
	// Split the search query string into an array of characters
	$characterArray = str_split($searchQuery);

	// We're looking for 's' here...
	$length           = strlen($searchQuery);
	$lastCharPosition = strlen($searchQuery) - 1;
	$lastCharacter    = $characterArray[$lastCharPosition];

	// If the string ends in s but isn't preceded by an apostrophe, we'll add one
	if ($length > 2 && $lastCharacter == 's' && $characterArray[$lastCharPosition - 1] != '\'')
	{
		$newString  = substr($searchQuery, 0, $lastCharPosition) . '\'' . substr($searchQuery, $lastCharPosition);
		return $newString;
	}
	return FALSE;
}

/******************************************************************************
* DETERMINE QUERY TYPE
* This function determines which type of query we will use: LIKE or REGEXP
*******************************************************************************/
function determineQueryType($searchQuery)
{
	// Do LIKE if they keyword is large enough...
	if (strlen($searchQuery) >= 4)
		return "SELECT * FROM restaurants WHERE name LIKE '%$searchQuery%';";					
	else
		return "SELECT * FROM restaurants WHERE name REGEXP '[[:<:]]" . $searchQuery . "[[:>:]]'";
}

/******************************************************************************
* EXECUTE QUERY
* True if the query returns any rows, false if it comes up empty
*******************************************************************************/
function executeQuery($db, $query)
{
	// Prepare the query statement and execute it
	$statement = $db->prepare($query);
	$statement->execute();

	// Did the query return any rows?
	if ($statement->rowCount() != 0)
		return TRUE;
	else
		return FALSE;
}
?>

</body>
</html>