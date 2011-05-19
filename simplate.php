<?php
/**
 * -----------------------------------------------------------------------------
 * Simplate
 * -----------------------------------------------------------------------------
 * @author      Mardix [http://twitter.com/mardix] (Use twitter to contact me or get updates)
 * @desc        A simple php template engine to separate php and html content
 * @link        http://mardix.wordpress.com/simplate/
 * @github      http://github.com/mardix/Simplate 
 * @license     MIT
 * @copyright   Copyright (c) 2011 - Mardix - http://twitter.com/mardix
 * @since       May 1 2011
 * 
 * @version     1.04
 * @last update May 18 2011
 * -----------------------------------------------------------------------------
 *   
 * API:
 * 
 * TEMPLATE:
 * 
 * Tags to show, iterate and include data
 * 
 *      VARIABLES
 *          %Varname%     
 * 
 *      CONDITIONAL STATEMENT
 *          <spl-if: condition>
 *              <spl-elseif: condition>
 *              <spl-else>
 *          </spl-endif>
 * 
 * 
 *      ITERATION
 *          <spl-each: eachname >
 *          
 *          </spl-endeach>
 * 
 * 
 *      INCLUDE
 *          Include a file directly in a template
 *          <spl-include: file.tpl />
 * 
 *          Include a template defined from php
 *          <spl-template: templateKey />
 *      
 *      ATTRIBUTES
 *          Each attributes are key=value
 *          <spl-include: ../../file.tpl absolute='true' />
 * 
 *          <spl-each: tweets limit="5" >
 *          </spl-endeach>
 * 
 * PHP:
 * 
 * Methods to add, iterate, and include data
 * 
 *      setRootDir($dirPath)        : set the root dir
 *      set($key,$value)            : set variables
 *      addTemplate($key,$filename) : add a template
 *      render()                    : To render the template
 *      iterator($key,$ArrayData)   : Create an iteration
 *      setDefault($templateKey)    : To set a template as the default one to be rendered
 *      saveTo($fileName,$templateK): To save the rendered content into a file
 *      debug()                     : To show all unassigned variables. By default unassigned vars will be removed 
 *  
 */
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------

Class Simplate {
    
    /**
     * The template system's name and version
     * @var String
     */
    public static $NAME = "Simplate";
    public static $VERSION = "1.04";

   
    /**
     * The directory holding all the templates
     * @var String
     */
    protected $rootDir  = "";

    /**
     * Hold all the variables set
     * @var Array
     */
    private $Vars = array();

    /**
     * To remove all unassigned variables and loop variables before rendering
     * @var Bool
     */
    private $parseSafe = true;
    
    
    /**
     * Hold all the templates list
     * @var Array 
     */
    protected $templateFiles = array();
    
    /**
     * Holds all templates content
     * @var type 
     */
    private $templates = array();

    /**
     * Holds file content that have been called via <spl-include: $filename >
     * @var Array 
     */
    protected $inlineTemplates = array();
    
    /**
     * Bool to see if templates have been parsed
     * @var bool
     */
    private $templatesParsed = false;
    
    /**
     * Holds the key of the added template
     * @var string
     */
    private $lastTplKey = "";
    
    /**
     * Hold the key of the default template to be rendered
     * @var type 
     */
    private $defaultTplKey = "";
    
    
    
    /**
     * Holds all the iterations in an associative array 
     * @var Array 
     */
    private $iterators = array();

    /**
     * Count the total defined iterations
     * @var Int 
     */
    private $definedIterationsCount = 0;

    /**
     * Holds the defined iterations.
     * @var Array 
     */
    private $definedIterations = array();
    

 
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------

    
    /**
     * Constructor
     * @param type $pathToTemplates 
     */
    public function __construct ($pathToTemplates = ""){
        
        if($pathToTemplates)
                $this->setRootDir($pathToTemplates);

        // Count iterators in a loop or not
        $this->iterators["__loopcount__"] = array();
    }

    
    /**
     * Destructor
     */
    public function __destruct(){
        
        unset(  $this->Vars,
                $this->rootDir,
                $this->iterators,
                $this->definedIterations,
                $this->templates,
                $this->inlineTemplates,
                $this->templateFiles
            );
    }

    
    
    /**
     * Set the root dir of the templates
     * @param string $dir
     * @return Simplate 
     */
    public function setRootDir ($dir){

        $this->rootDir = preg_match("!/$!",$dir) ? $dir : "{$dir}/";

        return $this;
    }

    
    
    /**
    * To set variables
    * @param <type> $tpl_array
    * @param <type> $trailer
    * @param bool $formatVar - if false it will leave the tag as is
    * @return Simplate
    */
    public function set($keys, $value="",$formatVar=true){
        
        if(is_array($keys)){
            
            foreach($keys as $tplK=>$tplV){
                
                $this->set($tplK,$tplV);
                
            }
        }
        
        else{
            
            $kName = ($formatVar) ? $this->formatVar($keys) : $keys;
            
            $this->Vars[$kName] = $value;
            
        }

        return $this;
    }


    
    /**
     * To add a template file
     * @param string $key - A unique key that identifies this page. It can be used to include this piece of code in the template examle <spl-embed: home > 
     * @param type $file
     * @param bool $absolutePath - When true it will not get the file from root but from the absolute path
     * @return Simplate 
     */
    public function addTemplate($key,$file,$absolutePath=false){

        $this->templateFiles[$key] = array(
            "src"=>$file,
            "absolutePath"=>$absolutePath,
            "mustExist"=>true
        );
        
        $this->lastTplKey = $key;
        
        // by default we'll set the first file as the main template. But can be changed later
        if(!$this->defaultTplKey)
                $this->setDefault($key);

        return $this;

    }

    /**
     * Set the default template to be rendered. if no template key is provided, the first file that was added with addTemplate will be set a default
     * @param type $templateKey
     * @return Simplate 
     */
    public function setDefault($templateKey=""){
        if(!$templateKey && $this->lastTplKey)
               $templateKey = $this->lastTplKey;
        
        if($templateKey)
            $this->defaultTplKey = $templateKey;
                
        return $this;
    }
    
    /**
     * Parse the templates and render it
     * @param String - The key of the template to render
     * @return String - The content to be rendered 
     */
    public function render($templateKey=""){
        return
            $this->getContent($templateKey);
    }
   
    
    
    /**
     * To save the template to file
     * @param string $filename - The file to save the template to
     * @param type $templateKey - The key to render
     * @return bool
     */
    public function saveTo($filename,$templateKey=""){
        
        $content = $this->getContent($templateKey);
        
        return 
            file_put_contents($filename,$content) ? true : false;

    }
    
    
    
    /**
     * To cerate a loop iterator that will be called in the template by <spl-each>
     * @param string $name - The name of the loop. Must be unique
     * @param array $data - The data to be inserted
     * @return Simplate 
     * 
     * TEMPLATE SYNTAX
     *      <spl-each: {$name}>
     *          CONTENT HERE
     *      </spl-endeach>
     */
    public function iterator($name,Array $data){

        
        // Format the keys
        $newData = array();
        foreach($data as $K=>$V){
            if(is_array($V))
                foreach($V as $Vk=>$Vv)
                   $newData[$K][$this->formatVar($Vk)] = $Vv;
            
            else
              $newData[$this->formatVar($K)] = $V;
        }

        
        // new data seems unique in the iterator
        if(!isset($this->iterators[$name])){
                $this->iterators[$name] = $newData;
                $this->iterators["__loopcount__"][$name] = 0;
        }
        
        // Data is in a loop, we'll reset and reassign old data
        else{
            
          ++$this->iterators["__loopcount__"][$name];  
            
          // Reset the old iterator
          if($this->iterators["__loopcount__"][$name] == 1){
              $lastData = $this->iterators[$name];
              $this->iterators[$name] = array();
              $this->iterators[$name][] = $lastData;
              unset($lastData);
          }
          
          $this->iterators[$name][] = $newData;
        }

        unset($newData);
        
        return $this;
    }

    
    /**
     * By default, all unassigned variables will be removed. Calling this method will display the unassigned variables upon rendering
     * @return Simplate 
     */
    public function debug(){
        
        $this->parseSafe = false;
        
        return $this;
        
    }
//------------------------------------------------------------------------------    
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
    
    /**
     * Rteurn the content
     * @param type $templateKey
     * @return type 
     */
    protected function getContent ($templateKey=""){

            $this->parseAll();
  
            if(!$templateKey)
                $templateKey = $this->defaultTplKey;
            
            if(isset($this->templates[$templateKey]))
                   return $this->templates[$templateKey];
            else
                throw new Exception("Can't getContent for template key: {$templateKey}");
    }
    
    
    /**
     * Get the template's content
     * @param string $filename - The filename relative to root. If $absolutePath is true, it will get it from path
     * @param bool $mustExist - If file must exits for content, if it doesnt exist it will throw an excption
     * @param bool $absolutePath - Specified if we get the file from absolutePath, or relative to root
     * @return string 
     */
    private function getTemplate ($filename,$mustExist=true,$absolutePath=false){

        $filename = ($absolutePath==true) ? $filename : $this->rootDir.$filename;

        if($mustExist && !file_exists($filename))
            throw new Exception("File '{$filename}' doesn't exist");

        if(file_exists($filename))
          return 
            $this->defineIterations(file_get_contents($filename));  
        
        else
            return "";
    } 


    /**
     * Called by getTemplate() to defined interations that will be parsed
     * @param string $template
     * @return string 
     */
    private function defineIterations($template){
        
        $regexp = "/<spl-each:\s+([A-Z]{1}.*?)\s+(.*?)>(.*?)<\/spl\-endeach>/is";

        preg_match_all($regexp,$template,$matches);

        $totalMatches = count($matches[0]);

        if($totalMatches){
            for($i=0;$i<$totalMatches;$i++){

                $this->definedIterationsCount++;

                $replacementKey = "__ITERATORREPLACEMENTHOLDER_{$this->definedIterationsCount}__";

                /**
                 * Save the replacement keys to remove them, if any, after parsing
                 */
                if(!isset($this->definedIterations["_replacementKeys"]))
                        $this->definedIterations["_replacementKeys"] = array();
                
                $this->definedIterations["_replacementKeys"][] = $replacementKey;
                
                

                $name = $matches[1][$i];

                if(!isset($this->definedIterations[$name]))
                        $this->definedIterations[$name] = array();
                
                $this->definedIterations[$name][] = array(
                      "replacementKey"=>$replacementKey,
                      "attributes"=>$this->getAttributes($matches[2][$i]),
                      "innerContent"=>$matches[3][$i],
                );

                $template = str_replace($matches[0][$i],$replacementKey,$template);
            }
        }
        
        return $template;
    }
    
    
    /**
     * Get the attributes out of a string, ie: absolute="true"
     * @param string $tagString
     * @return Array - containg key/value of tag/value -> array("absolute"=>true)
     */
    private function getAttributes($tagString){
        
        preg_match_all("/(\w+)\s*=\s*[\"'](.*?)[\"']/",$tagString,$attributes_);
        
        return 
            (count($attributes_[1]) && count($attributes_[1])) ? array_combine($attributes_[1],$attributes_[2]) : array();
    }
    
    
    /**
     * Parse the iterators in with their inner content
     * @return Array containing the replacement  
     */
    private function parseIterators(){

        $replacements = array();
        
        foreach($this->definedIterations as $itName=>$defIt){

            if(isset($this->iterators[$itName]) && is_array($this->iterators[$itName]) && is_array($defIt)){
                
                // Loop over each iterators once, even if there is more of that iterations on the page
                foreach($this->iterators[$itName] as $itData){

                    // Loop over the defined iteratore
                    foreach($defIt as $it){

                        if(!isset($replacements[$it["replacementKey"]]))
                            $replacements[$it["replacementKey"]] = "";

                        if(!isset($replacementIterationsCount[$it["replacementKey"]]))
                            $replacementIterationsCount[$it["replacementKey"]] = 0;

                         $limit = (isset($it["attributes"]["limit"]) && $it["attributes"]["limit"] <= $replacementIterationsCount[$it["replacementKey"]]) ? true : false;

                        ++$replacementIterationsCount[$it["replacementKey"]];


                        $replacements[$it["replacementKey"]] .= $limit ? "" : str_replace(array_keys($itData),array_values($itData),$it["innerContent"]); 
                    }
                }
            }

        }//
        
        return $replacements;
    }



     /**
     * Parse template and start the replacement
     * @param String $template - The template content
     * @return string 
     */
    private function parseTemplate($template) {
        
        /**
         * Parse the condition statements
         */
        $template = $this->parseCondStmts ($template);


        /**
         * <SPL-INCLUDE: $FILENAME />
         * Recursively include file in the template from the template
         * 
         * INCLUDES
         * Format: <spl-include: filename.tpl absolute="true"  /> 
         * Will include the filename.tpl in the current file. Can also add other include inside of includes
         * @todo The regexp to get the filename can't get file with "../.."
         */
        $matches = array();
        if (preg_match_all ( '/<spl\-include:\s+([\{\}a-zA-Z0-9_\.\-\/]+)\s*(.*?)\s*\/>/i',$template,$matches)) {      
            
            $incFile = $matches[1][0];
            
            if(file_exists($incFile)){
                
                if(!isset($this->inlineTemplates[$incFile]))
                    
                    $attributes = $this->getAttributes($matches[2][0]);
                        
                    $absolutePath = (isset($attributes["absolute"]) && preg_match("/^true|yes|y|1$/i",$attributes["absolute"])) ? true : false;
                
                    $this->inlineTemplates[$incFile] = $this->getTemplate($incFile,false,$absolutePath);
                
                $tpl = $this->inlineTemplates[$incFile];
                
                $template = $this->parseTemplate(str_replace($matches[0][0],$tpl,$template));
            }
        } 

        /**
         * Replacement everywhere
         */
        return 
            str_replace(array_keys($this->Vars),array_values($this->Vars),$template);
    } 

        
        
    /**
     * To parse  condition statements in the template
     * @param string $template - The template content
     * @return string 
     * 
     * @example
     *      <spl-if: Age.lt(18)>
     *          You can't join because you are under age
     * 
     *          <spl-elseif: Age.lt(21)>
     *              You can't drink beer because you are in USA
     * 
     *          <spl-else>
     *              Welcome to the gentlemen club
     *      </spl-endif>
     */
    private function parseCondStmts($template) {
            $lines = explode ("\n",$template );
            $newTemplate = "";
            $level = 0;
            $condStmt["defined"][$level] = false;
            $condStmt["parse"][$level] = true;
            $condStmt["break"][$level] = true;
            // regexp to match data in tag
            $matchRegexp = "\s+([a-zA-Z_!][a-zA-Z0-9_]+)(\.|\-\>)?([a-zA-Z_][a-zA-Z0-9_]+)?\(?(\s*\,?\".*\"\s*\,?|\s*\,?[a-z0-9\_]*\s*\,?)\)?\s*";


            foreach($lines as $line) {

                  if ((!$condStmt["defined"][$level] || $condStmt["parse"][$level])  &&  !preg_match("/(<spl-if:|<spl-elseif:|<spl-else|<\/spl-endif)/i",strtolower($line)))
                     $newTemplate .= $line."\n";

                /**
                 * <SPL-IF: KEY.METHOD(VALUE)>
                 * <SPL-ELSEIF: KEY.METHOD(VALUE)>
                 * 
                 * Format KEY.METHOD(VALUE)
                 * e.g: Number.odd()
                 *      Name.match(mynameis)
                 *      Field.gt(5)
                 * <spl-if: Age.gte(18) > // Age >= 18      
                 */

                if (preg_match("/<spl-(if|elseif):{$matchRegexp}>/i",$line,$regs)) {

                    $methodEvaled = $this->evalMethods($regs[2],$regs[4],$regs[5]);

                    // Open up with all if tags
                    if(strtoupper($regs[1])=="IF"){
                             $level++;   
                             $condStmt["defined"][$level] = true;  
                             $condStmt["break"][$level] = false;
                    }

                     if($methodEvaled && !$condStmt["break"][$level]){
                         $condStmt["parse"][$level] = true;
                         $condStmt["break"][$level] = true;
                     }

                     else{
                         $condStmt["parse"][$level] = false;
                     }

                } 


                /**
                 * <SPL-ELSE>
                 */
                else if (preg_match("/<spl-else\s*>/i",$line)) {
                   if($condStmt["defined"][$level] && $condStmt["break"][$level])
                        $condStmt["parse"][$level] = false;
                   else
                       $condStmt["parse"][$level] = true;
                }

                /**
                 * <SPL-ENDIF>
                 */
                else if (preg_match("!</spl-endif\s*>!i",$line) && $condStmt ["defined"][$level]) {
                    $condStmt ["defined"][$level]  = false;
                    $condStmt["break"][$level] = false;
                }
            }

            return $newTemplate;
    }        
                

    
    /**
     * This is where the magic happens
     * It parses all the templates and prepare them to be rendered
     * @return Simplate 
     */
    protected function parseAll(){

        if($this->templatesParsed)
           return $this;
        
        
        $this->templatesParsed = true;
        
        /**
         * Parse all templates
         */
        foreach($this->templateFiles as $key=>$file){
            $this->templates[$key] = $this->parseTemplate($this->getTemplate($file["src"],$file["mustExist"],$file["absolutePath"]));
        }
        
        /**
         * Assign everything
         */
        $iteratorsRep = $this->parseIterators();
        $iKey = array_keys($iteratorsRep);
        $iVal = array_values($iteratorsRep);

        foreach($this->templates as $tK=>$tV){
            
            $this->templates[$tK] = str_replace($iKey,$iVal,$tV);
            
            /**
             * Remove unassigned variable
             */
            if($this->parseSafe){
                
                $this->templates[$tK] = preg_replace("/%\w+%/i","",$this->templates[$tK]); 
                
                $this->templates[$tK] = str_replace(array_values($this->definedIterations["_replacementKeys"]),array(""),$this->templates[$tK]);
            }
            
        }

        /** 
         * Last call for alcohol before page is rendered
         * Include template page in page
         * <SPL-TEMPLATE: {} />
         */        
        foreach($this->templates as $ttK=>$ttV){

            if(preg_match_all ( '/<spl\-template:\s+([\{\}a-zA-Z0-9_\.\-\/]+)\s*\/>/i',$ttV,$matches)){
               $totalMatches = count($matches[0]);
               
               for($i=0;$i<$totalMatches;$i++){
                   if(isset($this->templates[$matches[1][$i]]))
                    $this->templates[$ttK] = str_replace($matches[0][$i],$this->templates[$matches[1][$i]],$ttV);
               }
            }
        }

        return $this;
    }    
    
    
    
    /**
     * To format variable with the proper opening and cclosed tags
     * @param string $varName - The variable name
     * @return string 
     */
    protected function formatVar($varName){
        return "%{$varName}%";
    }

    
    
    /**
     * Get a variable's name
     * @param string $key - The variable key
     * @return mixed  
     */
    protected function getVar($key){
        
        $key = $this->formatVar($key);

        return 
            $this->Vars[$key];
    }
                
                
    /**
     * To evaluate a method that will be in the tpl
     * @param string $key - The variable name set by $this->set()
     * @param string $fn - The name of the method to use, like: odd,even,not,is...
     * @param  mixed $value - The value to compare $key with
     * @return bool 
     * 
     * @example 
     *          Key.Method(Value)
     *          <spl-if: Age.is(18)>
     *              Age = $key
     *              is = $fn
     *              18 = $value
     */
    protected function evalMethods($key,$fn,$value){

        // negate the keys=> !Key
        $neg = preg_match("/^!/",$key) ? true : false;
        
        $key = str_replace("!","",$key);
        $keyVal = $this->getVar($key);

        
        
        switch(strtolower($fn)){
            // unknown value should always return false
            default :
                $this->__error("Undefined methods: {$fn}");
                return false;
            break;

            // IS:, EQUALS: equality
            case "is":
            case "equals":
                $res = ($keyVal == $value) ? true : false;
            break;

            // NOT: different
            case "not":
                $res = ($keyVal != $value) ? true : false;
            break;

            // EMPTY: check empty
            case "empty":
                $res = $keyVal ? true : false;
            break;

            // MATCH: Find at least one
            case "match":
                $res = preg_match("!{$value}!",$keyVal) ? true : false;
            break;                       

            // EVEN: Check even numbers
            case "even":
                $res = ($keyVal % 2 == 0) ? true : false;
            break;       

            // ODD: Check odd numbers
            case "odd":
                $res = ($keyVal % 2 != 0) ? true : false;
            break;   

            // GT: Greater than
            case "gt":
                $res = ($keyVal > $value) ? true : false;
            break;                     

            // GTE: Greater than or equal
            case "gte":
                $res = ($keyVal >= $value) ? true : false;
            break; 

            // LT: Lesser than
            case "lt":
                $res = ($keyVal < $value) ? true : false;
            break; 

            // LTE: Lesser than or Equal
            case "lte":
                $res = ($keyVal <= $value) ? true : false;
            break;                     
        }

           return 
                 ($neg) ? !$res : $res;

    }

    
    /**
     * To log error
     * @param type $message
     * @param type $type 
     * @todo  log errors
     */
    private function __error($message,$type=""){

    }
  
                
}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
