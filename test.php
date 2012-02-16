<?php

/**
 * @name    test
 * @author  Mardix
 * @since   Feb 13, 2012,3:11:27 AM
 * @desc    
 */

print(6%4);

exit;
class UTest{

    public $definedIterations = array();
    
    public function defineIterations($template){
    // Cactch all each
    $regexpP = '/<spl-each[^>]*>(?:(?:(?:(?!<\/?spl-each).)*|(?R))?)+<\/spl-each>/si';
    // Call all inner each
    $regexpR = '/<spl-each[^>]*>(?:(?:(?!<\/?spl-each).)*|(?R))?<\/spl-each>/si';
    // Read the current 
    $regexpS = "/<spl-each\s+([A-Z_]{1}.*?)\s+(.*?)>(.*?)<\/spl\-each>/is";
    
    
    preg_match_all($regexpP, $template,$matchP);
    
    if(count($matchP[0])){
        
        foreach($matchP[0] as $iP=>$P){
            
            ++$this->definedIterationsCount;
            
            $innerHolder = array();
            
                // Inner each
                preg_match_all($regexpR,$P,$matchR);

                if(count($matchR[0])){

                    foreach($matchR[0] as $iR=>$R){
                        
                        $replacementKey = "#_INNER_.{$iR}";
                        
                        $P = str_replace($R,$replacementKey,$P);
                        
                        preg_match($regexpS, $R,$matchSR);
                        
                            $childName = $matchSR[1];
                            $innerHolder[$childName] = array(
                                  "replacementKey"=>$replacementKey,
                                  //"attributes"=>$this->getAttributes($matchSR[2]),
                                  "innerContent"=>$matchSR[3],
                            ); 
                    }// R

                }
            
              preg_match($regexpS, $P,$matchSP); 

                $replacementKey = "_ITERATORREPLACEMENTHOLDER.{$this->definedIterationsCount}";
                $parentName = $matchSP[1];
                $innerContent = $matchSP[3];

                $this->definedIterations["_replacementKeys"][] = $replacementKey;

              if(count($innerHolder)){
                  foreach($innerHolder as $childName=>$childData){

                      $cName = "{$parentName}.__each__.{$childName}";
                      $rK = $childData["replacementKey"];
                      $repKey = $replacementKey.$rK;
                      $childData["eachIndex"] = $cName;
                      $childData["replacementKey"] = $repKey;

                      $this->definedIterations[$cName][] = $childData;
                      $this->definedIterations["_replacementKeys"][] = $repKey;
                      $innerContent = str_replace($rK,$repKey,$innerContent);
                  }
              }

            $this->definedIterations[$parentName][] = array(
                                                  "replacementKey"=>$replacementKey,
                                                  //"attributes"=>$this->getAttributes($matchSP[2]),
                                                  "innerContent"=>$innerContent,
                                                  "eachIndex"=>$parentName,
                                            );           
          
          $template = str_replace($matchP[0][$iP],$replacementKey,$template);
        }// P
        
    }
    
    return
        $template;
    }
    
}

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



<spl-each Loop11 >
    We are in SPL-EACH #{@Counter}
        
        Local variable, with the name Site
            {@Site}
            
        Variable from Global scope, with the name Site
            {@:Site}
    
        --------
        Inner Loop, with limit 2 1
        
            <spl-each Loop21 >
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
        Inner Loop, with limit 3 1
        
            <spl-each Loop31 >
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


Djakout Bon bagay
";


$test = new UTest;

$r = $test->defineIterations($template);

print_r($test->definedIterations);
exit;
$s = "           
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
            ";

/**
$xml = new SimpleXMLElement($template);
$result = $xml->xpath('/spl-each ');
while(list($k, $node) = each($result)) {
    echo "-> $k ",$node,"\n";
            
}
exit;
**/
$catchRecursive = '/<spl-each[^>]*>(?:(?:(?!<\/?spl-each).)*|(?R))?<\/spl-each>/si'; // index 0

$catchSingle = "/<spl-each\s+([A-Z_]{1}.*?)\s+(.*?)>(.*?)<\/spl\-each>/is";

$catchParent =  '/<spl-each[^>]*>(?:(?:(?:(?!<\/?spl-each).)*|(?R))?)+<\/spl-each>/si';

preg_match($catchSingle,$s,$matches);

print_r($matches);
exit;

preg_match_all($catchParent,$template,$matches);

print_r($matches);


exit;

foreach($matches[0] as $ineach){
    $template = str_replace($ineach,"INEACH",$template);
}

print($template);

