namespace Replace_plugin;

/*
	Replace plugin for MODx
	-----------------------

	Reads a preamble in the form:

	|replace|
		something => something else
	|/replace|

	...then searches the content for 'something' and replaces it with 'something else'

*/

function replace($string){
	// Get hash of things to replace
	$sectionscontent = sectionstohash($string, 'replace');

	// Replace and return
	return strtr($string, $sectionscontent);
}

// Returns a hash with all section content and removes the section(s) from the string
function sectionstohash(&$string, $sectionidentifier){
	// Get all |sectionidentifier| |/sectionidentifier| sections
	preg_match_all("/\|$sectionidentifier\|\s*(.*)\s*\|\/$sectionidentifier\|/uisU", $string, $sections);

	// For each section, add each get each of its key-value pairs and add to $returnhash
	$returnhash = array();
	foreach($sections[1] as $section){
		preg_match_all("/\s*(.*=>.*)\s*/u", $section, $entries);
		foreach($entries[1] as $entry){
			$keyvaluepair = (preg_split("/\s*=>\s*/u", $entry));
			$returnhash[chop($keyvaluepair[0])] = chop($keyvaluepair[1]);
		}
	}

	// Remove all |sectionidentifier| |/sectionidentifier| sections from the original string
	do{	
		$laststring = $string;
		$string = preg_replace("/(\|$sectionidentifier\|.*\|\/$sectionidentifier\|\s)/uisU", '', $string);
	}while($string !== $laststring);

	// Return hash
	return $returnhash;
}

$e = &$modx->Event;
switch ($e->name) {
	case "OnLoadWebDocument":
		$o = &$modx->documentObject['content'];
		$o = replace($o);
		break;
	default :
		return;
		break;
}
