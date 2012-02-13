<?php

/**
 * @name    test
 * @author  Mardix
 * @since   Feb 13, 2012,3:11:27 AM
 * @desc    
 */


$template="
<spl-each Loop1 >
    We are in SPL-EACH #{@Counter}
        
        Local variable, with the name Site
            {@Site}
            
        Variable from Global scope, with the name Site
            {@:Site}
    
        --------
        Inner Loop, with limit 2
        
            <spl-each Loop2 >
                --- INNER LOOP --
                
                Chain
                    {@Time.toDate().toRahel().toMardix().toUpper()}
                    
                Global scope with filters, with the name Site
                    {@:Site.replace(.net,.net/).toUpper()} 
                    
                Parent Scope, with the name Site
                    {@#Site} 
                
                Local variable, with the name Site
                    {@Site.replace(.com,.org).capitalize()}
                    
                    
                {@#Counter}  / {@Team.toMardix().toUpperCase()} Won #{@Game} in Foto: {@Foto} in PL: {@#Name} 
            </spl-each>
 

        --------
        Inner Loop, with limit 3
        
            <spl-each Loop3 >
                --- INNER LOOP --
                
                Chain
                    {@Time.toDate().toRahel().toMardix().toUpper()}
                    
                Global scope with filters, with the name Site
                    {@:Site.replace(.net,.net/).toUpper()} 
                    
                Parent Scope, with the name Site
                    {@#Site} 
                
                Local variable, with the name Site
                    {@Site.replace(.com,.org).capitalize()}
                    
                    
                {@#Counter}  / {@Team.toMardix().toUpperCase()} Won #{@Game} in Foto: {@Foto} in PL: {@#Name} 
                
            </spl-each>
            
</spl-each>     


";

/**
$xml = new SimpleXMLElement($template);
$result = $xml->xpath('/spl-each ');
while(list($k, $node) = each($result)) {
    echo "-> $k ",$node,"\n";
            
}
exit;
**/
$innerEach = '#<spl-each[^>]*>(?:(?:(?!</?spl-each).)*|(?R))?</spl-each>#si'; // index 0

//$regexp = "/<spl-each\s+([A-Z_]{1}.*?)\s+(.*?)>(.*?)<\/spl\-each>/is";

$regexp =  '#<spl-each[^>]*>(((?!</?spl-each).)*|(?R))?</spl-each>#si';
preg_match_all($regexp,$template,$matches);

print_r($matches);

foreach($matches[0] as $ineach){
    $template = str_replace($ineach,"INEACH",$template);
}

print($template);