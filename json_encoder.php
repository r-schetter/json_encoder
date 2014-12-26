    function json_encoder ($a=false, $e=false, $max=99999)			#usage: $json = json_encoder($xml);
    {	# replacement for json_encode() by Roland Schetter, Dec2014
        static $strip = array(array('\\', '/', "\n", "\t", "\r", "\b", "\f", '"'), 
                              array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
        static $n = 0; if ($n++ >$max) return ''; # just a safety cut 
        if (is_scalar($a))
        {	if (!is_string($a)) return $a;
            else return '"' . str_replace($strip[0], $strip[1], $a) . '"';
        }
		if (!is_object($a)) return ''; # we do not expect anything else than an object from here
        $items = $done = array(); 
        # handle the attributes
		foreach ($a->attributes() as $k => $v) { $items[] = '"@'.$k.'":'.json_encoder((string)$v[0]); } 
		# handle pure textNodes
		if ($e && ($s=trim((string)$a[0]))) $items[] = '"'.$e.'":'.json_encoder($s); 
		# regular elements single or multiple instances
        foreach ($a as $k => $v) if (!$done[$k]) 
        {	$c = count($a->$k); # instances
           	if ($c==1) 	# single element
           	{	$items[] = '"'.$k.'":'.json_encoder($v,$k); 
           	} else 		# array for multiple elements 
           	{	$x= $a->xpath($k); $arr = array(); 
				foreach ($x as $v) $arr[] = json_encoder($v,$k);
	            $items[] = '"'.$k.'":['.join(',', $arr).']';
	            $done[$k]=$c; # mark array elements as 'done'
            } 
		}	
        return '{'.join(',',$items).'}'; # get united
    }
