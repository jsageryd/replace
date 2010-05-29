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
	$replacepreamble = stripreplacepreamble($string);

	// Replace
	$s = $string;
	$padding = '§';
	foreach($replacepreamble as $k => $v){
		$s = preg_replace('/(?<!' . $padding .')' . preg_quote($k) . '(?!' . $padding . ')/uU', $padding . preg_quote($v) . $padding, $s);
	}

	// Remove all padding
	$s = preg_replace('/'.$padding.'/u', '', $s);

	return $s;
}

// Returns an hash with all replacements to be made
function stripreplacepreamble(&$string){
	// Get all |replace| |/replace| sections
	preg_match_all("/\|replace\|\s*?(.*)\s*?\|\/replace\|/uisU", $string, $sections);

	// For each section, add each get each of its key-value pairs and add to $replacepreamble
	$replacepreamble = array();
	foreach($sections[1] as $section){
		preg_match_all("/\s*(.*=>.*)\s*/u", $section, $entrysets);
		foreach($entrysets[1] as $entries){
			$entry = (preg_split("/\s*=>\s*/u", $entries));
			$replacepreamble[$entry[0]] = $entry[1];
		}
	}

	// Remove all |replace| |/replace| sections from the original string
	do{	
		$laststring = $string;
		$string = preg_replace("/(\|replace\|.*\|\/replace\|\s)/uisU", '', $string);
	}while($string !== $laststring);

	// Return hash
	return $replacepreamble;
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
