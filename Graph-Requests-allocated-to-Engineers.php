<?

// HelpSpot to StatusBoard: shows a graph of tickets allocated to engineers 
// when loaded into Panic StatusBoard as a graph.

// Author: Daniel Dainty of Kamazoy, 05/03/16

// Instructions:
// Modify the variables below to reflect your installation. Then upload this to somewhere and point your
// Panic Statusboard 2.0 installation to the URL as a graph.


// URL to your helpspot installation. No trailing slash.
define(HelpSpotURL,"http://.../helpspot");	
// Email address of any user who has access to HelpSpot
define(HelpSpotUsername,"api@...");
// Password of the above user
define(HelpSpotPassword,"password...");				
// How often do you want StatusBoard to refresh the values?
define(RefreshInSeconds,60);				

//////////////////////////////////////////////////////////////////////////////////////
// You shouldn't need to edit anything below here...


// Load all open requests into $xml
$all = get("private.request.search","fOpen=1");
$xml=simplexml_load_string($all) or die("Error: Cannot create object");

$assignedArray = array();

// Create a simple array that stores just the operator's name, so we can array_count_values it later
foreach($xml as $request){	
	$req = $request->xPersonAssignedTo->__toString();
	array_push($assignedArray, $req);
}

// Make a new array that stores the counts of the names, above
$assignedCounts = array_count_values($assignedArray);

// initialise empty arrays so we can push values onto them without warnings
$datasequence = array(); $datasequences = array();

// now put the values into something that StatusBoard will understand
foreach($assignedCounts as $key=>$value) {
	$datapoints = array();
	if($key==""){$key="Unallocated";}    // unallocated requests? Put the word "Unallocated" otherwise it's blank
	$datapoint = array("title"=>"Allocated","value"=>$value);
	array_push($datapoints,$datapoint);

	$datasequence = array(
		"title"=>$key,
		"datapoints"=>$datapoints,
	);

	// store each datapoint as a unique data sequence, so we get the unique colours per engineer
	$datasequence = array(
		"title"=>"$key",
		"datapoints"=>$datapoints
	);

	// push the finished $datasequence array onto the $datasequences array
	array_push($datasequences,$datasequence);
}



$graph = array(
	"title" => "Requests allocated to engineers",
	"refreshEveryNSeconds" => RefreshInSeconds,
	"datasequences" => $datasequences);

$output = array("graph" => $graph);

//echo("<pre>");die(print_r($output));
// uncomment line above for some debugging

// all finished - output the array as json
echo json_encode($output);









// cURL function
function get($method,$query) {
	$ch = curl_init(HelpSpotURL . "/api/index.php?method=". $method ."&". $query);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_USERPWD, HelpSpotUsername.":".HelpSpotPassword);

	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

?>

					
					
