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
	return $string;
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
