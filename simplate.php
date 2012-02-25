<?php
/**
 * -----------------------------------------------------------------------------
 * Simplate
 * -----------------------------------------------------------------------------
 * @author      Mardix [http://mardix.github.com] - You can also get at me on Twitter: @Mardix (Use twitter to contact me or get updates)
 * 
 * @desc        Simplate is a simple php template engine to separate application 
 *                  logic and content from its presentation. 
 *              Simplate is designed to be Developers and Designers friendly.
 *              For developers it uses PHP 5.3 (or later) and can be extended. 
 *                  It assigns variables, can include other templates, create loop 
 *                  and nested loops, etc
 *              For designers, it has a very low learning curve because it uses HTML-like syntax 
 *                  for example {@VarName} to show a var that was assigned, 
 *                  <spl-if Age.is()> to execute some script and more 
 *                  All variables become pseudo object and can be extended by built-in filters 
 *                  or filters you created yourself like {@VarName.toUpper()} or can be chained down like
 *                  {@VarName.replace(.com,.net).toUpper().truncate(15)} or in a <spl-if> statement like <spl-if @Text.length().gt(4)>
 * 
 * @link        http://github.com/mardix/Simplate
 * @github      http://mardix.github.com
 * @twitter     @mardix
 * @license     LGPL
 * @copyright   Copyright (c) 2011 - Mardix
 * @since       1.x May 1 2011,
 *              2.0 Mar 1 2012
 * 
 * @required    PHP 5.3 or later
 * 
 * @version     2.0
 * @LastUpdate  Mar 1 2012
 *              - Major update. Will break some 1.x features
 *              - All variables are called by @Var, @:Var or @#Var, either in {} or <spl-tag>
 *              - <spl-each> can be nested for loop
 *              - <spl-each> requires the parameter name to access the name of the var to loop. <spl-each
 *              - $this->addFile() : No exception is thrown when adding a new file to an existing key. It just replaces it.
 *              - <spl-if> condition can be placed inside of <spl-each>
 *              - new template filter: .calculate() to do some basic math operation
 *              - Refactoring
 *                        
 * 
 * @NowPlaying  "Amen" - Pusha T feat Kanye West & Young Jeezy 
 * 
 * -----------------------------------------------------------------------------
 *   
 * ---------------------------- SIMPLATE API -----------------------------------
 * 
 *+++++++++++++++++++++++++ FOR DESIGNERS (Template) +++++++++++++++++++++++++++
 * 
 * Tags to show, iterate and include data
 * 
 *** VARIABLES: {@Varname}
 *      Variable must be assigned from the PHP side, and can be called in the template page
 *      by using {@Varname}
 * 
 *      {@Varname}  : All assigned variables can be accessed this way
 *      {@Varname.toUpper()}    : Will uppercase the variable
 *      {@Varname.replace(www,ZZZ)} : Replace www by ZZZ in the VarName 
 *      {@Varname.replace(www,zZz).toUpper()} : Chain
 * 
 *  
 *** SCRIPT TAGS: <spl-$intruction > execution block to test for if, elseif, else, each, include, literal
 * 
 ****** Conditionals
 *      <spl-if> : Conditional Statement
 *      <spl-elseif> : Conditional statement if SPL-IF fails
 *      <spl-else>  : When if and elseif fail
 * 
 *              <spl-if @VarName.empty()>
 *                  Add content here
 * 
 *                      <spl-elseif @VarName.gt(8)>
 *                          Add content for else if
 * 
 *                      <spl-else>
 *                          Add content for else
 *              </spl-if>
 *      
 * 
 ****** Loops
 *      <spl-each> : Loop 
 *              <spl-each name="@eachname" >
 *          
 *              </spl-each>
 * 
 *      
 *      <spl-each> : Nested each
 *              <spl-each name="@eachname" >
 *          
 *                  <spl-each name="@innereachname" >
 *                      CONTENT HERE
 *                  </spl-each>
 * 
 *                  <spl-each name="@innereachname2" >
 *                      CONTENT HERE 2
 *                  </spl-each>
 *  
 *              </spl-each>
 * 
 ****** Include other templates in the template
 *      <spl-include> : To include file from source, or template already loaded
 *          
 *              <spl-include src="file.tpl" /> : Include a file directly in a template
 * 
 *              <spl-include src="@templateKey" /> : Include a template defined from php
 * 
 *    
 ***** Literal, to not parse the content
 *     <spl-literal>    : To put literal Simplate tags that will be returned as is, therefor will not be parsed and be left as is
 *                  <spl-literal>
 *                      {@TagName}
 *                      <spl-if @TagName.is(Jose) >
 *                              Hi Rihanna!
 *                      </spl-if>
 *                  </spl-literal>
 *          
 *            It will return it as
 *                      {@TagName}
 *                      <spl-if @TagName.is(Jose) >
 *                              Hi Rihanna!
 *                      </spl-if>                
 * 
 **** More on variable access
 *      Accessing variables. This can be used in {} or <spl-(if|elseif|each) >
 * 
 *      @         Accsess variable in the current scope
 *      @:        Access variable out of the current scope. Specially when in a loop and want to access variable outside of the loop 
 *      @#        To access the parent's variable in an inner loop <spl-ineach> 
 * 
 *      i.e
 *      {@Varname} access the local var within the current scope, which include each and nested each
 *      {@:Varname} to access the Varname in the global scope
 *      {@#Varname} when in a nested each and want to access the parent data
 * 
 *      Note:
 *      <spl-each> only access @Varname, no @:Varname or @#Varname
 *      <spl-each name="@Loop" >
 * 
 *** COMMENTS:
 *     There is no special tags for commenting. You can use the standard HTML commenting tag <!-- --> 
 *     If you want to remove all html comments, use <spl-macro cmd="stripComments" /> or Simplate::stripComments()
 * 
 * 
 *** DO MORE WITH MACROS
 *      If $this->setMacro is set, you will be able to execute some command to be execute from the PHP side
 * 
 *      - <spl-macro debug="vars" /> display the variables that were assigned
 *      - <spl-macro debug="errors" /> display all errors for unassigned vars, undefined filters etc
 *      - <spl-macro cmd="toJSON:$eachName" /> Will return the JSON value of the $eachName that was assigned during $this->each()
 *      - <spl-macro cmd="stripComments" /> Strip all HTML comments off the page
 * 
 * 
 **** ADVANCED
 * 
 *      ATTRIBUTES
 *          Each attributes are key=value
 *          <spl-include src="../../file.tpl" absolute='true' />
 * 
 *          <spl-each name="Tweets" limit="5" >
 * 
 *          </spl-each>
 * 
 *      MACROS
 *         Macros are instructions from the template that will execute some commands on the PHP side, such as show all debug errors
 *         Syntax:
 *          <spl-macro $macro="$options" />
 * 
 *              Available $macro:
 *                                debug: will display debug info on the page
 *                                     debug="$option" (errors | vars | vars:$eachName | vars:$eachName.$ineachName)
 * 
 *                                 cmd: allow to execute some command that will affect the page
 *                                      cmd="options" (stripComments | toJSON:$eachName)
 *                          
 *                                 To turn <spl-each>  into JSON you can use this command: <spl-macro cmd="toJSON:$eachName" />
 * 
 *------------------------------------------------------------------------------
 *  
 *+++++++++++++++++++++++++++ FOR DEVELOPERS (PHP) +++++++++++++++++++++++++++++
 * 
 * Simplate is pretty easy and will get you going right away. 
 * 
 * Note: One requirement when setting variable with $this->assign(), $this->each(), $this->addFile(), $this->addTemplate,
 * is that the key must start with an Uppercase Alpha non numeric, A-Z_ and underscore. i.e $this->assign("Varname","Hello"); $this->each("Loops",$Data);
 * 
 * Below are the public methods to assign variables, loop over data, include template file etc...
 * 
 *      setDir($dirPath)                    : set the root dir
 *      assign($key,$value)                 : assign variables. Previously set var can be concat by prefixing the $keyName with a dot: $this->assign(".KeyName","Value")
 *      addTemplate($tplName,$filename)     : add a template file. Can be called in the template: <spl-include src="@TemplateName" />
 *      addInlineTemplate($tplName,$Content): To add a content as template.
 *      each($name,$ArrayData)              : Create a loop. If there is an array inside of ArrayData, it will create an inner loop  
 *      render($tplName)                    : To render the template as a string. Use print to print it on the screen
 *      setLiteral($content)                : To leave Simplate tag as is in the content
 *      saveTo($tplName,$filePath)          : To save the rendered content into a file
 *      stripComments(bool)                 : To strip the HTML comments off the pages
 * 
 **** Remove / Clearing 
 *      removeTemplate($tplName)            : remove a template that was created with addFile or addTemplate
 *      clearVars()                         : To reset all vars
 *      clearAll                            : Unset everything
 * 
 **** ADVANCED
 *      Simplate::setFilter($name,\Closure function(){}) 
 *                                          Allow to create filter methods that will be applied on the variables in the template
 * 
 *      allowMacros(bool)                   : Set to true to allow macros in the template file
 *                                            When set, it can be called this way: <spl-macro $macro="$value" />
 * 
 **** Exception handling
 *    Upon an error, Simplate will throw an Excetion which gives details on the error
 * 
 *  
 * Min Requirement: PHP 5.3 and up
 */
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------

Class Simplate {

    CONST NAME = "Simplate";
    CONST VERSION = "2.0";
   
    /**
     * The directory holding all the templates
     * @var String
     */
    protected $tplDir  = "";

    /**
     * Hold all the variables set
     * @var Array
     */
    private $Vars = array();

    /**
     * To allow <spl-macro /> to be executed from the template
     * For performance set to false.
     * Best way to use it it's when developement phase 
     * @var bool 
     */
    protected $allowMacros = false;
    
    
    /**
     * To strip HTML comments
     * @var bool 
     */
    protected $stripHTMLComments = false;
    
    
    /**
     * Hold all the templates filename that was loaded with: Simplate::addFile()
     * @var Array 
     */
    protected $templateFiles = array();
    
    /**
     * Hold all the template content that was loaded via: Simplate::addTemplate()
     * @var Array
     */
    protected $templateStrings = array();
    
    /**
     * Holds file content that have been called via <spl-include src="filename" >
     * @var Array 
     */
    protected $templateInlines = array();
    
    
    /**
     * Holds all templates content when parsing
     * @var type 
     */
    private $templates = array();


    /**
     * Bool to see if templates have been parsed
     * @var bool
     */
    private $templatesParsed = false;
    
    
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
    
    /**
     * Count total literals
     * @var int
     */
    private $definedLiteralsCount = 0;
    
    
    /**
     * Holds the defined literals.
     * @var Array 
     */
    private $definedLiterals = array();
    
    /**
     * To clean unassigned vars
     * @var bool
     */
    private $clearUnassigned = true;
        
    
 
    /*
     * Most hard core regexp used. 
     * I broke my head over the wall to get most of these regex to work... finally got them.
     * If you find a better solution, go for it, and please share :)
     * @var Array
     */
    private $REGEXP = array(
        /**
         * Validate all variable names
         * Must start with letter or underscore. First letter must be capitalize. may contains only letters and numbers
         */
        "vars"=>"/^[A-Z_]{1}\w+$/",
        
        /**
         * Extract Name.Method() from 
         *  <SPL-IF @Name.Method() >
         *  <SPL-IF @Age.Method().Chained().Test() >
         *  <SPL-IF @#Age.Method().Chained().Test() >
         *  <SPL-IF @:Age.Method().Chained().Test() >
         */
        "splIfMethods"=>"/<spl-(if|elseif)\s+@(?:([:#\w]*)?\.)+((?:[\w]+)(?:\((?:.*?)\))+|(?R))\s*>/i",
       
        
        /**
         * Match variables in the following format. The the fiter will be applied to the var name
         *      {@VarName.filter()}
         *          {@Alpha.replace(jones,picko)}
         *          {@:Bravo.toLower()}  // in loop
         *          {@#Charlie.toUpper()} // in inner loop
         *          {@Name.capitalize().stripTags().length()} // Chainable
         *          
         *      
         */
        "varsFilters"=>"/{@(?:([:#\w]*)?\.)+((?:[\w]+)(?:\((.*?)\))+|(?R))}/",
        
        /**
         * Get the chained methods
         *          .replace(x,y) => [1]=>replace, [2]=>x,y
         */
        "chainedMethods"=>"/([\w]+)\((.*?)\)(?:\.*)/",
        
        
        /**
         * To access vars from other scropes, specially when it's in an each loop
         * Match 
         *      {@:Varname} - From the global scope
         *      {@#Varname} - Access the parent varname in an spl-ineach loop
         * Not
         *      {@A} 
         *      {@A.fn()}    
         */
        "varsOuterScope"=>"/{@([:#]+[\.\w]+)}/",
        
        /**
         * Jones {@Name.Filter().SubFilter()} {@:Name} {@#Name}  {@:Name.Filter()}
         * Will Get
         *      {@Name.Filter().SubFilter()}
         *      {@:Name}
         *      {@#Name}
         *      {@:Name.Filter()}
         */
        "varsMod"=>"/{@(?:[:#\w]+)*(?:\.)*(?:[\w#]+)?(?:.*?)}/",
        
        
        /**
         * Get the attributes in a tag
         * <spl-include src='file.tpl' />
         * 
         *  will extract src='file.tpl'
         */
        "attributes"=>"(\w+)\s*=\s*[\"'](.*?)[\"']",
        
        
        /**
         * To strip html comments. 
         * But will leave conditionals comments such as <!-- [if IE 7]><![endif]-->
         */
        "stripHTMLComments"=> "/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/Uis",
        
        
        /**
         * To extract inside literal tag
         */
        "literal"=>"/<spl\-literal>(.*?)<\/spl\-literal>/si",
        
        
        /**
         * To extract include tag
         */
        "include"=>'/<spl\-include\s+(.*?)\s*\/>/i',
        
        
        
        /**
         * Regexp to catch <SPL-EACH>
         */
        "each"=>array(
            "catchAll"=>"/<spl-each[^>]*>(?:(?:(?:(?!<\/?spl-each).)*|(?R))?)+<\/spl-each>/si",
            
            "catchInner"=>"/<spl-each[^>]*>(?:(?:(?!<\/?spl-each).)*|(?R))?<\/spl-each>/si",
            
            "catchSingle"=>"/<spl-each\s+(.*?)>(.*?)<\/spl\-each>/si" // <spl-each name='@eachName'></spl-each>
        )
    );
 
    
    /**
     * Save statically customed filters to be applied on variable
     * i.e: Simplate::setFilter("toUpper",function($n){ return strtoupper($n);});
     * @var Array of Closures 
     */
    private static $CustomFilters = array();
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------

    
    /**
     * Constructor
     * @param string $templateDir - The template directory
     */
    public function __construct ($templateDir = ""){
        
        if($templateDir)
                $this->setDir($templateDir);

        /**
         * Set up the meta entry
         */
        $this->iterators["__meta__"] = array();

    }

    
    /**
     * Destructor
     */
    public function __destruct(){

        unset(  
                $this->tplDir,
                $this->Vars,
                $this->templateFiles,
                $this->templateStrings,
                $this->templateInlines,
                $this->templates,
                $this->iterators,
                $this->definedIterations,
                $this->definedLiterals
            );
    }

//------------------------------------------------------------------------------


    /**
     * Set the dir of the templates
     * @param string $dir
     * @return Simplate 
     */
    public function setDir ($dir){

        $this->tplDir = preg_match("!/$!",$dir) ? $dir : "{$dir}/";

        return $this;
    }

    
    
    /**
    * To assign variables
    * @param mixed $keys - If it's a string, it will assign value to it. if a string, it must start with a letter and contain alphanum chars. Multi dim array, will map
    * @param String|Numeric $value - The value to assign to $keys
    * @param bool $formatVar - if false it will leave the tag as is
    * @return Simplate
    * 
    * @HTMLTAG:
    *           {@Name178_}
    *
    * * Advance
    *       Concat: 
    *           $this->assign(".KeyName","Value")
    *           Add a dot(.) in front of the key name to concat it to previously assign value. 
    */
    public function assign($keys, $value="",$formatVar=true){
        
        if(is_array($keys)){
            foreach($keys as $tplK=>$tplV){
                $this->assign($tplK,$tplV);
            }
        }
        
        else{

            /**
             * Invalid variable name
             * Making sure concatenated var (.VarName) pass this test
             */
            if(!preg_match("/^\./",$keys) && !preg_match($this->REGEXP["vars"],$keys))
                throw new \Exception("Simplate Exception in ".__METHOD__." - Invalid variable name: '$keys'. Variable must start with a letter or underscore. First letter must be capitalized. The rest of the var may contain alpha numeric and underscore ");
            
            /**
             * Concat: $this->assign(".KeyName","Value")
             */
            $concatKey = preg_match("/^\./",$keys) ;
            if($concatKey)
                $keys = preg_replace("/^\./","",$keys);
            
            $kName = ($formatVar) ? $this->formatVar($keys) : $keys;
            
            $this->Vars[$kName] = ($concatKey ? ($this->Vars[$kName]) : "").$value;
            
        }

        return $this;
    }

    
    /**
     * To add a template file. This template can also be included in the template with: 
     * @param string $key - A unique key that identifies this page. It can be used to include this piece of code in the template examle <spl-include src="@$KEY" />
     * @param String $file - The file url to include
     * @param bool $absolutePath - When true it will not get the file from root but from the absolute path
     * @return Simplate 
     * 
     * @HTMLTAG
     *      <spl-include src="@KEY" />
     */
    public function addTemplate($key,$file,$absolutePath=false){
        $this->templateFiles[$key] = array(
            "src"=>$file,
            "absolutePath"=>$absolutePath,
            "mustExist"=>true
        );

        return $this;

    }

    
    /**
     * To add an inline template 
     * @param String $key A unique key that identifies this page. It can be used to include this piece of code in the template examle <spl-include src="@$KEY" />
     * @param String $Content - The content of the template
     * @return Simplate 
     * 
     * @HTMLTAG
     *      <spl-include src="@KEY" />
     */
    public function addInlineTemplate($key,$Content){

        $this->templateStrings[$key] = $Content;
        
        return $this;
    }
    
    /**
     * To remove a template from the list
     * @param type $key 
     * @return Simplate
     */
    public function removeTemplate($key){
        
        if(isset($this->templateFiles[$key]))
          unset($this->templateFiles[$key]);
        
        if(isset($this->templateStrings[$key]))
          unset($this->templateStrings[$key]);

        return $this;
    }
    
    
    
    /**
     * Parse the templates and render it
     * @param String - The key of the template to render
     * @return String - The content to be rendered 
     */
    public function render($templateKey){
        return
            $this->getContent($templateKey);
    }
   
    
    
    /**
     * To save the template to file
     * @param type $templateKey - The key to render
     * @param string $filename - The file to save the template to
     * @return bool
     */
    public function saveTo($templateKey,$fileName){
        
        $content = $this->getContent($templateKey);
        
        if(!file_put_contents($fileName,$content)) 
           throw new Exception("Unable to save template key '{$templateKey}' to: {$fileName}");
        
        return
            true;

    }
    
    
    
    /**
     * To create a loop iterator that will be called in the template by <spl-each>
     * @param string $name - The name of the loop. Must be unique
     * @param array $data - The data to be inserted
     * @return Simplate 
     * 
     * @HTMLTAG
     *      <spl-each @{$name}>
     *          CONTENT HERE
     *      </spl-each>
     * 
     *      For nested loop
     *      <spl-each @{$name}>
               <spl-each @{$innername}>
                   CONTENT HERE
               </spl-each>
     *      </spl-each>  
     */

     public function each($name,Array $data){


        /**
         * Invalid variable name
         * Making sure concatenated var (.VarName) pass this test
         */
        if((preg_match("/\./",$name)) || (!preg_match("/\./",$name) && !preg_match($this->REGEXP["vars"],$name)))
            throw new \Exception("Simplate Exception in ".__METHOD__." - Invalid each variable name: '$name'. Variable must start with a letter and contain alpha numeric and underscore ");

            
        /**
         * Test if it's a bulk each where we dump a massive array into each, or if this each in a loop itself building the data
         */
        $isBulk = (isset($data[0]) && is_array($data[0])) ? true : false;

        // Format the keys
        $newData = array();

        foreach($data as $K=>$V){

            if(is_array($V)){
                
                if($isBulk){
                   foreach($V as $Vk=>$Vv){
                    
                       /**
                        * <spl-ineach >
                        */
                       if(is_array($Vv))
                           $newData[$K]["__each__"][$Vk] = $this->nestedEach($Vv,"{$name}.{$K}");

                       else
                           $newData[$K][$this->formatVar($Vk)] = $Vv;
                   }
                }
                
                /**
                 * <spl-ineach >
                 */
                else{
                    $ln = $this->iterators["__meta__"][$name]["count"]?:0;
                    $newData[$name]["__each__"][$K] = $this->nestedEach($V,"{$name}.{$ln}");;
                }
            }

            else{
                    $newData[$this->formatVar($K)] = $V;
            }

        }

        /**
         * New data in the iterator
         */
        if(!isset($this->iterators[$name])){
           $this->iterators[$name] = (!isset($newData[0])) ? array($newData) : $newData;
           
           $this->iterators["__meta__"][$name]["count"] =  1;
           
            /**
             * We'll save the children from the 
             */
            if(strpos($name,".")){
                
              $pName = current(explode(".",$name));
              $this->iterators["__meta__"][$pName]["children"][$name] = 1;  
            }
                    
        }
        
        // Data is in a loop, we'll reset and reassign old data
        else{

          ++$this->iterators["__meta__"][$name]["count"];
          
              /**
               * Entering the second loop so we'll add this data in the first index
               */
              if($this->iterators["__meta__"][$name]["count"] == 2)
                  $this->iterators[$name] = (!isset($this->iterators[$name][0])) 
                                                ? array($this->iterators[$name]) : $this->iterators[$name];
              
          
          $this->iterators[$name][] =  $newData;
        }

        unset($newData);
        
        return $this;
    }   


    /**
     * Literals are data that has simplate markup but we dont want to parse them but leave as is
     * by putting the data between the tags: <spl-literal> and </spl-literal>
     * Programmatically you can use $this->setLiteral($content)
     * @param type $content
     * @return string 
     */ 
    public function setLiteral($content=""){
        return
            $this->defineLiterals("<spl-literal>".$content."</spl-literal>");
    }
    
    
    /**
     * Allow to use macros from the templae
     * macros execute certain php instructions that will affect the page
     * Some macros:
     *          debug -> will display debugging info on the page 
     *          cmd -> execute some commands on the php side
     * @param bool $allow
     * @return Simplate 
     */
    public function allowMacros($allow=true){
        
        $this->allowMacros = $allow;
        
        return $this;
    }
    
    /**
     * To strip html comments off
     * @param bool $stripHTMLComments
     * @return Simplate 
     */
    public function stripComments($stripHTMLComments = true){
        
        $this->stripHTMLComments = $stripHTMLComments;
        
        return $this;
    }
    
    
    /**
     * To remove all unassigned vars
     * @param bool $clearUnassigned
     * @return Simplate 
     */
    public function clearUnassigned($clearUnassigned = true){
        
        $this->clearUnassigned = $clearUnassigned;
        
        return $this;
    }
    
    
    /**
     * To clear all variables
     * @return Simplate 
     */
    public function clearVars(){
        
        $this->Vars = array();
        
        return $this;
    }
    
    
    /**
     * To clear everything
     * @return Simplate
     */
    public function clearAll(){
        
        $this->Vars = array();
        $this->templateFiles = array();
        $this->templateStrings = array();
        $this->templateInlines = array();
        $this->templates = array();
        $this->iterators = array();
        $this->definedIterations = array();

        return $this;
    }
    
    
    
    /**
     * To set custom filters to be applied on the variables 
     * @param type $name - The name of the filter
     * @param \Closure $filterName - The function to apply to it
     * @return Simplate 
     */
    public static function setFilter($name,\Closure $filterName){
        self::$CustomFilters[strtolower($name)] = $filterName;
    }    
    
    
    
    
    
//------------------------------------------------------------------------------    
//-- PROTECTED / PRIVATE METHODS -----------------------------------------------
//------------------------------------------------------------------------------

    /**
     * Rteurn the content
     * @param type $templateKey
     * @return type 
     */
    protected function getContent ($templateKey){

       $this->parseAll();
       
            if(isset($this->templates[$templateKey]))
                   return $this->templates[$templateKey];
            else
                throw new \Exception("Simplate Exception in ".__METHOD__." - Can't get content for template key: '{$templateKey}' because it doesn't exist");
    }
    
    
    /**
     * To process nested each
     * @param array $data
     * @param type $parent
     * @return array 
     */
    private function nestedEach(Array $data,$parent){
        
           $nD = array();

           foreach($data as $i=>$entries){
               $nD[$i][$this->formatVar('#')] = $parent;
               
               foreach($entries as $k=>$v){
                  if(is_array($v))
                      $nD["__each__"][$k][] = $this->nestedEach($v,$parent.".$k.{$i}");
                      
                  else
                    $nD[$i][$this->formatVar($k)] = $v; 
               }
           }
           return
            $nD;        
    }    
    
    /**
     * Get the template's content
     * @param string $filename - The filename relative to root. If $absolutePath is true, it will get it from path
     * @param bool $mustExist - If file must exits for content, if it doesnt exist it will throw an excption
     * @param bool $absolutePath - Specified if we get the file from absolutePath, or relative to root
     * @return string 
     */
    protected function getTemplate ($filename,$mustExist=true,$absolutePath=false){

        $filename = ($absolutePath==true) ? $filename : $this->tplDir.$filename;

        if($mustExist && !file_exists($filename))
            throw new \Exception("Simplate Exception in ".__METHOD__." - File: '{$filename}' doesn't exist");

        if(file_exists($filename))
          return 
            $this->defineIterators(file_get_contents($filename));  
        
        else
            return "";
    } 

    /**
     * Get the attributes out of a string, ie: absolute="true"
     * @param string $tagString
     * @return Array - containg key/value of tag/value -> array("absolute"=>true)
     */
    private function getAttributes($tagString){
        
        preg_match_all("/".$this->REGEXP["attributes"]."/",$tagString,$attributes_);
        
        return 
            (count($attributes_[1]) && count($attributes_[2])) ? array_combine($attributes_[1],$attributes_[2]) : array();
    }
    
    
    /**
     * Start defining iterators for SPL-FOREACH
     * @param string $template - The content to parse the iterator through
     * @return string 
     */
    private function defineIterators($template){
        // Cactch all each
        $regexpP = $this->REGEXP["each"]["catchAll"];
        // Call all inner each
        $regexpR = $this->REGEXP["each"]["catchInner"];
        // Read the current 
        $regexpS = $this->REGEXP["each"]["catchSingle"];


        preg_match_all($regexpP, $template,$matchP);

        if(count($matchP[0])){

            foreach($matchP[0] as $iP=>$P){

                ++$this->definedIterationsCount;

                $innerHolder = array();

                    // nested each
                    preg_match_all($regexpR,$P,$matchR);

                    
                    $c = count($matchR[0]);
                    /**
                     * To make sure that single each without nested each is not interpreted as nested each 
                     */
                    if(($c > 1) || ($c==1 && $matchR[0][0]!=$P)){

                        foreach($matchR[0] as $iR=>$R){

                            $replacementKey = "_{$iR}__{c#}_";

                            $P = str_replace($R,$replacementKey,$P);

                            preg_match($regexpS, $R,$matchSR);
                            
                                $cAttributes = $this->getAttributes($matchSR[1]);
                                $childName = $this->varName($cAttributes["name"]);
                                
                                $innerHolder[$childName] = array(
                                      "replacementKey"=>$replacementKey,
                                      "attributes"=>$cAttributes,
                                      "innerContent"=>$matchSR[2],
                                ); 
                        }// R

                    }

                  preg_match($regexpS, $P,$matchSP); 

                    $replacementKey = "_ITERATORS.PARENT_{$this->definedIterationsCount}";

                    $attributes = $this->getAttributes($matchSP[1]);
                    $parentName = $this->varName($attributes["name"]);
                    $innerContent = $matchSP[2];
                   
                    $this->definedIterations["_replacementKeys"][] = $replacementKey;

                  if(count($innerHolder)){
                      foreach($innerHolder as $childName=>$childData){

                          $cName = "{$parentName}.__each__.{$childName}";
                          $rK = $childData["replacementKey"];
                          $repKey = "_ITERATORS.CHILD_{$this->definedIterationsCount}".$rK;
                          $childData["eachIndex"] = $cName;
                          $childData["replacementKey"] = $repKey;
                          $childData["parentLimit"] = isset($attributes["limit"]) ? $attributes["limit"] : 0;

                          $this->definedIterations[$cName][] = $childData;
                          $this->definedIterations["_replacementKeys"][] = $repKey;
                          $innerContent = str_replace($rK,$repKey,$innerContent);
                      }

                      unset($innerHolder);
                  }

                $this->definedIterations[$parentName][] = array(
                                                      "replacementKey"=>$replacementKey,
                                                      "attributes"=>$attributes,
                                                      "innerContent"=>$innerContent,
                                                      "eachIndex"=>$parentName,
                                                );           

              $template = str_replace($matchP[0][$iP],$replacementKey,$template);
            }// P

        }

        return
            $template;
    }

    
    /**
     * Literals are data that has simplate markup but we dont want to parse them but leave as is
     * by putting the data between the tags: <spl-literal> and </spl-literal>
     * Programmatically you can use $this->setLiteral($content)
     * @param type $content
     * @return type 
     */
    private function defineLiterals($content=""){
        
        $regexp = $this->REGEXP["literal"];

        preg_match_all($regexp,$content,$matches);

        $totalMatches = count($matches[0]);        
        
         if($totalMatches){
            
            for($i=0;$i<$totalMatches;$i++){
                
                ++$this->definedLiteralsCount;
                
                $name = "_DEFINEDLITERALS_{$this->definedLiteralsCount}";
                
                $this->definedLiterals[$name] = $matches[1][$i];
                
                $content = str_replace($matches[0][$i],$name,$content);

            }   
            
         }
        
         return
                $content;
    }
    
    
    /**
     * To return the original literals
     * @param type $content
     * @return type 
     */
    private function parseLiterals($content){
        return
            str_replace(array_keys($this->definedLiterals),array_values($this->definedLiterals),$content);
        
    }
    

    /**
     * Parse the iterators and create the loop 
     * @return Array - containing the replacement  
     */
    private function parseIterators(){

        $replacements = array();

        foreach($this->definedIterations as $itName=>$defIt){

            if($itName!="_replacementKeys" && is_array($defIt)){

              foreach($defIt as $eachDefKey=>$eachDefVal){

                   $limit = isset($eachDefVal["attributes"]["limit"]) ? $eachDefVal["attributes"]["limit"] : 0 ;
                   $replacementKey = $eachDefVal["replacementKey"];
                   $innerContent = $eachDefVal["innerContent"];
                   
                   // Nested each
                   if(preg_match("/__each__/",$itName)){

                       list($parent,$child) = explode(".__each__.",$itName,2);

                       $parentLimit = $eachDefVal["parentLimit"];
                       $pIt = $this->iterators[$parent];

                       foreach($pIt as $pItK=>$pItV){
                           $_replacementKey = str_replace("_{c#}_",$pItK,$replacementKey);

                           $itrtr = $this->dot2Array($pItV,"__each__.{$child}");

                              if(is_array($itrtr) && count($itrtr)){

                                  // Single Item
                                  if(count($itrtr) == count($itrtr,COUNT_RECURSIVE)){
                                        $replacements[$_replacementKey] .= $this->parseTemplate($innerContent,$itrtr);  
                                  }

                                  else{
                                      foreach($itrtr as $itI=>$itData){
                                        
                                        if(!isset($replacements[$_replacementKey]))
                                            $replacements[$_replacementKey] = "";
                                        
                                        $replacements[$_replacementKey] .= $this->parseTemplate($innerContent,$itData);  

                                        if($limit && $itI>=$limit-1)
                                          break;
                                     }
                                  }
                                  
                              }
                              
                              if($parentLimit && $pItK >= $parentLimit-1)
                                 break;
                       }
                       
                   }

                   else{

                      $itrtr = $this->dot2Array($this->iterators,$itName);

                          if(is_array($itrtr) && count($itrtr)){

                            $replacements[$replacementKey] = "";
                            
                              // Single Item
                              if(count($itrtr) == count($itrtr,COUNT_RECURSIVE)){
                                    $_innerContent = str_replace("_{c#}_",0,$innerContent);
                                    unset($itrtr["__each__"]);
                                    $replacements[$replacementKey] = $this->parseTemplate($_innerContent,$itrtr);  
                              }

                              else{
                                  foreach($itrtr as $itI=>$itData){
                                      
                                    unset($itData["__each__"]);
                                    $_innerContent = str_replace("_{c#}_",$itI,$innerContent);
                                    $replacements[$replacementKey] .= $this->parseTemplate($_innerContent,$itData);  

                                    if($limit && $itI>=$limit-1)
                                      break;
                                 }
                              }
                          }
                    }
              }
              
            }
            
        }

        return $replacements;
        
    }


    /**
     * Parse variables, which 
     * @param String $template
     * @param array $Scope, the current array scope to look for the variable, espcially in iterations where data are within their scope
     * @return type 
     */
    private function parseVars($template,Array $Scope = array()){

       $Vars = (count($Scope)) ? $Scope : $this->Vars;

        if(preg_match_all($this->REGEXP["varsMod"],$template,$matches)){
      
            foreach($matches[0] as $v){

                /**
                 * Variable that use the filter methods
                 * i.e: {@Name.toUpper()}
                 *      {@Name.toUpper().replace(.com,.net).escapeHTML()} chain
                 */
                if(preg_match($this->REGEXP["varsFilters"],$v,$mA)){
                    
                    $var = $this->getVar($mA[1],$Scope);
                    
                    if(preg_match_all($this->REGEXP["chainedMethods"],$mA[2],$filters)){
                        foreach($filters[1] as $fK=>$filter)
                            $var = $this->applyFilter($var,$filter,explode(",",$filters[2][$fK]));
                    }

                    $Vars[$mA[0]] = $var;
                }
                
                /**
                 * Variable without filters
                 */
                else if(preg_match($this->REGEXP["varsOuterScope"],$v,$mA))
                    $Vars[$mA[0]] = $this->getVar($mA[1],$Scope);
                
            }

        }

       /**
        * Making sure there is at least no multi-dim array.
        * $Scope pass the whole array, and it returns a NOTICE. So to prevent this we check it
        */
       if(!isset($Vars[0]))
           return 
                str_replace(array_keys($Vars),array_values($Vars),$template);
       else
           return $template;
        
    }
    
     
    /**
     * To strip HTML Coments
     * @param String $content
     * @return String 
     */
    protected function stripHTMLComments($content){
        return 
            preg_replace($this->REGEXP["stripHTMLComments"],"",$content);
    }
    
    
     /**
     * Parse template and start the replacement
     * @param String $template - The template content
     * @param array $Scope - the scope of the current template. Which is data that can be used for this piece of code
     * @return string 
     */
    private function parseTemplate($template,Array $Scope = array()) {

        $template = $this->defineLiterals($template);
        
        /**
         * Parse the condition statements
         */
        $template = $this->parseCondStmts ($template,$Scope);

        /**
         * <SPL-INCLUDE src="$FILENAME" />
         * Recursively include file in the template from the template
         * 
         * INCLUDES
         * Format: <spl-include src="filename.tpl" absolute="true"  /> 
         * Will include the filename.tpl in the current file. Can also add other include inside of includes
         * @todo The regexp to get the filename can't get file with "../.."
         */
        $matches = array();
        if (preg_match_all ($this->REGEXP["include"],$template,$matches)) {      
            
            foreach($matches[1] as $mk=>$att){
                
                $attributes = $this->getAttributes($att);
                
                if(isset($attributes["src"]) && !preg_match("/^@/",$attributes["src"])){
                    
                    $incFile = $attributes["src"];

                    if(!isset($this->templateInlines[$incFile])){

                        $absolutePath = (isset($attributes["absolute"]) && preg_match("/^true|yes|y|1$/i",$attributes["absolute"])) ? true : false;

                        $this->templateInlines[$incFile] = $this->getTemplate($incFile,false,$absolutePath);
                    }                    

                    $tpl = $this->templateInlines[$incFile];

                    $template = $this->parseTemplate(str_replace($matches[0][$mk],$tpl,$template),$Scope);
                }  
            }
        } 

        return 
            $this->parseVars($template,$Scope);
           
    } 

        
        
    /**
     * To parse  condition statements in the template
     * @param string $template - The template content
     * @param array $Scope - the scope of the current template. Which is data that can be used for this piece of code
     * @return string 
     * 
     * @example
     *      <spl-if @Age.lt(18)>
     *          You can't join because you are under age
     * 
     *          <spl-elseif @Age.lt(21)>
     *              You can't drink beer because you are in USA
     * 
     *          <spl-else>
     *              Welcome to the gentlemen club
     *      </spl-if>
     */
    private function parseCondStmts($template,Array $Scope = array()) {
            $lines = explode ("\n",$template );
            $newTemplate = "";
            $level = 0;
            $condStmt["defined"][$level] = false;
            $condStmt["parse"][$level] = true;
            $condStmt["break"][$level] = true;

            foreach($lines as $line) {

                  if ((!$condStmt["defined"][$level] || $condStmt["parse"][$level])  &&  !preg_match("/(<spl-if|<spl-elseif|<spl-else|<\/spl-if)/i",strtolower($line)))
                     $newTemplate .= $line."\n";

                /**
                 * <SPL-IF KEY.METHOD(VALUE)>
                 * <SPL-ELSEIF KEY.METHOD(VALUE)>
                 * Conditional can be chained with filters, as long as the last chain is a conditional
                 * 
                 * e.g: Number.odd()
                 *      Name.match(mynameis)
                 *      Field.gt(5)
                 * or chained
                 *      Age.calculate(+1).is(19)
                 *      Name.length().calculate(+6).gte(18)
                 * 
                 * <spl-if Age.gte(18) > // Age >= 18      
                 */

                if (preg_match($this->REGEXP["splIfMethods"],$line,$regs)) {
                   
                    $methodEvaled = false;
                    $var = $this->getVar($regs[2],$Scope);

                    if(preg_match_all($this->REGEXP["chainedMethods"],$regs[3],$filters)){
                       
                       
                        $totalFilters = count($filters[1]);
                        
                        // One method, must be a test
                        if($totalFilters==1){
                           $methodEvaled = $this->conditionalTestMethods($regs[2],$filters[1][0],$filters[2][0],$Scope); 
                        }
                        
                        // Chained
                        else{
                            for($i=0;$i<$totalFilters;$i++){

                                // Filter must be applied 
                                if($i+1 < $totalFilters){
                                    $var = $this->applyFilter($var,$filters[1][$i],explode(",",$filters[2][$i]));
                                }

                                // The last method must be conditional to test 
                                else{
                                  // After all set and done, we'll make a new scope with the var
                                  $nV = "__Var";
                                  $Scope = array($this->formatVar("__Var")=>$var);
                                  $methodEvaled = $this->conditionalTestMethods($nV,$filters[1][$i],$filters[2][$i],$Scope); 
                                 
                                }
                            }
                        }
                    }

                    
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
                 * </SPL-IF>
                 */
                else if (preg_match("!</spl-if\s*>!i",$line) && $condStmt ["defined"][$level]) {
                    $condStmt ["defined"][$level]  = false;
                    $condStmt["break"][$level] = false;
                }
            }

            return $newTemplate;
    }        



    /**
     * To format variable with the proper opening and cclosed tags
     * @param string $varName - The variable name
     * @return string 
     */
    protected function formatVar($varName){
        return "{@{$varName}}";
    }

    
    /**
     * To return the raw var name of a formatted varname. I guess it's nicely said... lol
     * @param type $formattedVarName
     * @return string 
     */
    protected function varName($formattedVarName){
        return
            str_replace(array("@","{","}"),"",$formattedVarName);
    }
    
    
    /**
     * Get a variable data. Can access data in iterations and global scope
     * @param string $Var - The variable key without @
     * @return mixed  
     */
    protected function getVar($Var,Array $Scope = array()){

        /**
         * {@#Name}
         * # indicate a parent entry in a <spl-each> 
         * It will get the @# tag that was built and concat it to var name to go look for it
         */
        if(preg_match("/^#/",$Var)){
            
            $key = $this->formatVar(str_replace("#","",$Var));
            
            $parent = $this->dot2Array($Scope,$this->formatVar("#"));
            
            if($parent){
                // concat values to go down the array
                $Var = $parent.".".$key;
                return
                    $this->dot2Array($this->iterators,$Var);
            }
        }        
        
        /**
         * {@:Name}
         * Global scope var
         */
        if(preg_match("/^:/",$Var)){
            $Var = str_replace(":","",$Var);  
            $Scope = array();
        }
        
        $key = $this->formatVar($Var);
        
        return 
            $this->dot2Array(count($Scope) ? $Scope : $this->Vars,$key);

    }
                
                
    /**
     * To test a value in a if statement
     * @param string $key - The variable name set by $this->assign()
     * @param string $testName - The name of the method to use, like: odd,even,not,is...
     * @param  mixed $value - The value to compare $key with
     * @return bool 
     * 
     * @example 
     *          Key.Method(Value)
     *          <spl-if Age.is(18)> or <spl-if !Age.is(18) >
     *              Age = $key
     *              is = $filterName
     *              18 = $value
     */
    private function conditionalTestMethods($key,$testName,$value,Array $Scope = array()){

        // negate the keys=> !Key
        $neg = preg_match("/^!/",$key) ? true : false;
        
        $key = str_replace("!","",$key);
        $keyVal = $this->getVar($key,$Scope);

        
        $testName = strtolower($testName);
        switch($testName){
            // unknown value should always return false
            default :
                $this->__debugger("{$testName}({$value}) : test method is undefined");
                return false;
            break;

            // .is(string) or .equals(number) - comparison
            case "is":
            case "equals":
                $res = ($keyVal == $value);
            break;

            // .not(string|number) - comparison
            case "not":
                $res = ($keyVal != $value);
            break;
        
            // .null() or .empty() - null or empty val
            case "null":
            case "empty":
                $res = !$keyVal;
            break;
        
            // .startsWith(keyVal) - starts with keyVal
            case "startswith":
               $res = preg_match("~^{$value}~",$keyVal); 
            break;
        
            // .endsWith(keyVal) - ends with keyval
            case "endswith":
               $res = preg_match("~{$value}$~",$keyVal) ; 
            break;

        
            // .match(keyVal) - or .contains(keyVal) contain keyval
            case "match":
            case "contains":
                $res = preg_match("~{$value}~",$keyVal);
            break;                       

            // .even() - even numbers
            case "even":
                $res = ($keyVal % 2 == 0);
            break;       

            // .odd() - odd numbers
            case "odd":
                $res = ($keyVal % 2 != 0);
            break;   

            // .gt(number) - greater than number
            case "gt":
                $res = ($keyVal > $value);
            break;                     

            // .gte(number) - greater or equal to 
            case "gte":
                $res = ($keyVal >= $value);
            break; 

            // .lt(number) - lesser than
            case "lt":
                $res = ($keyVal < $value);
            break; 

            // .lte(number) - lesser or equal to
            case "lte":
                $res = ($keyVal <= $value);
            break;                     
        }

           return 
                 ($neg) ? !$res : $res;

    }

    
    /**
     * To apply filters to variable in the template. ie {@VarName().toUpperCase()}
     * To create custom filters, use Simplate::setFilter($name,\Closure);
     * @param String | int $val - The value, which is the var itself
     * @param String $filterName - The filter name to apply
     * @param Array $args - Argument to be passed
     * @return mixed 
     */
    private function applyFilter($val,$filterName,Array $args=array()){
        
        // Always lowercase the filterName here, so we make sure it gets the exact section
        $filterName = strtolower($filterName);
        switch($filterName){
            
            
            default :

                /** CUSTOM FILTERS, created with Simplate::setFilter() **/
                if(isset(self::$CustomFilters[$filterName])){
                    $customFilter = self::$CustomFilters[$filterName];
                    return 
                        $customFilter($val);
                }
                
                // unknown value should always return the value
                else{
                    $this->__debugger("{$filterName}() : filter undefined ");
                    return $val;
                }
                
            break;
        
            /** BUILT-IN FILTERS **/
            
            // .toUpper()
            case "toupper":
                return strtoupper($val);
            break;
        
            // .toLower() 
            case "tolower":
                return strtolower($val);
            break;
        
            // .capitalize()
            case "capitalize":
                return ucwords($val);
            break;
        
            // .truncate(0,4), or truncate(7) (which will truncate to the 7th char)
            case "truncate":
                // Second argument is empty, so it will start at 0 
                if($args[0] && !isset($args[1])){
                    $start = 0;
                    $length = $args[0];
                }
                else{
                    $start = $args[0] ?: 0;
                    $length = $args[1] ?: strlen($val);
                }
                
                return substr($val,$start,$length);
            break;
        
            // .length() get the size of the fiedl
            case "length":
                return strlen($val);
            break;
        
            // .toNumber(2)
            case "tonumber":
                $decimal = ($args[0]) ?: 0;
                return number_format($val,$decimal);
            break;      
        
            // .replace(pattern,replacement)
            case "replace":
                $pattern = ($args[0]) ?: "";
                $replacement = ($args[1]) ?: "";
                return ($pattern && $replacement) ? str_replace($pattern,$replacement,$val) : $val;
            break;
        
            // .trim()
            case "trim":
                return trim($val);
            break;
        
            // .escapeHTML()
            case "escapehtml":
                return htmlspecialchars($val);
            break;
            
            // .stripTags()
            case "striptags":
                return strip_tags($val);
            break;
        
        
            // .toDate(format) - to format a date. Can use a pre-made mask or your own format
            case "todate":
              $datetime = $val;
              $format = implode("",$args) ?: "date";

              if(!$datetime || $datetime == "0000-00-00 00:00:00")
                    return "";
              
                    // Some pre-made format
                    $mask = array(
                      "date"=>"D, M d Y",
                      "dateTime"=>"D, M d Y @ g:i a",
                      "dateTimeSecond"=>"D, M d Y @ g:i:s a",
                      "time"=>"g:i a",
                      "timeSecond"=>"g:i:s a",
                      "mysql"=>"Y-m-d H:i:s",
                    );

                $toTime = (is_numeric($datetime)) ? $datetime : strtotime($datetime);

                return date(isset($mask[$format]) ? $mask[$format] : $format ,$toTime);  
                
            break;
            
            // .calculate(inst), where instr can be +-*/% . ie: .calculate(+1) or multiple, .calulcate(+1,*5,...)
            
            case "calculate":
                $oVal = $val;
                foreach($args as $arg){
                  if(preg_match("/^(\+|\-|\/|\*|%)([0-9]+)$/",$arg,$match)){
                      eval('$oVal = $oVal '.$match[0].';');
                  } 
                }
                  return $oVal;

            break;
        
        
        }
        
        
    }
    
    
    /**
     * To log error that doesn't affect the exceution of the template
     * @param type $message
     * @param type $type 
     * @todo  log errors
     */
    protected function __debugger($message){
        $this->debugger[] = $message;
        return $this;
    }
    
    
    /**
     * Read data in array, based on dotNotationKeys
     * @param array $Data
     * @param String $dotNotationKeys - the dot notation, i.e, "key.subkey.subsubkey"
     * @param mixed $emptyValue - A value to return if dotNotArg doesnt find any match
     * @return Mixed: Array, String, Numeric
     * @example
     *  $A = array("location"=>array("City"=>"Charlotte","ZipCode"=>25168));
     *  dot2Array($A,"location.ZipCode") 
     *  -> 25168
     */
    protected function dot2Array(Array $Data, $dotNotationKeys = ".", $emptyValue = "") {

        // Eliminate the last dot
        $dotNotationKeys = preg_replace("/\.$/","",$dotNotationKeys);
        
        if(!$dotNotationKeys)
            return $Data;
 
        $dotKeys = explode(".",$dotNotationKeys);

        foreach ($dotKeys as $key) {
            
            if (!isset($Data[$key]))
                return $emptyValue;
            
            $Data = $Data[$key];
        } 
        return $Data;
    }    
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//----------- THE SAUCE --------------------------------------------------------
    
    
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
         * Get all the template files and parsed them
         */
        if(count($this->templateFiles))
            foreach($this->templateFiles as $key=>$file)
                $this->templates[$key] = $this->parseTemplate($this->getTemplate($file["src"],$file["mustExist"],$file["absolutePath"]));
        
        /**
         * Get all the templates loaded with  Simplate::addTemplate()
         */
        if(count($this->templateStrings))
            foreach($this->templateStrings as $key=>$content)
                $this->templates[$key] = $this->parseTemplate($content);
                      
       
        /**
         * Parse iterators for each and ineach
         */
        $iteratorsRep = $this->parseIterators();
        if(count($iteratorsRep))
            foreach($this->templates as $tK=>$tV)
                $this->templates[$tK] = $this->parseVars(str_replace(array_keys($iteratorsRep),array_values($iteratorsRep),$tV),$iteratorsRep);//str_replace($iKey,$iVal,$tV);


        /** 
         * Last call for alcohol before page is rendered
         */    

        foreach($this->templates as $ttK=>$ttV){

            $matches = array();

            /**
             * Include @template: <spl-include src="@keyName" />
             */
            if (preg_match_all ( '/<spl\-include\s+(.*?)\s*\/>/i',$ttV,$matches)) {      

                foreach($matches[1] as $mk=>$att){

                    $attributes = $this->getAttributes($att);

                    if(isset($attributes["src"]) && preg_match("/^@/",$attributes["src"])){

                        $tpl = str_replace("@","",$attributes["src"]);

                        if(isset($this->templates[$tpl]))
                            $this->templates[$ttK] = str_replace($matches[0][$mk],$this->templates[$tpl],$this->templates[$ttK]);
                        
                    }  
                }
            }
            
            /**
             * Strip HTML Comments, if it was instructed from PHP
             */
            if($this->stripHTMLComments == true)
                    $this->templates[$ttK] = $this->stripHTMLComments($this->templates[$ttK]);
            
            
			
            /**
             * Clear all unassigned vars
             */
            if($this->clearUnassigned){
                
                $this->templates[$ttK] = preg_replace("/{@\w+}/i","",$this->templates[$ttK]);
                
                $this->templates[$ttK] =  str_replace(array_values($this->definedIterations["_replacementKeys"]),"",$this->templates[$ttK]);
                
               
            }

			
			
            /**
             * <spl-macro $macro='$options' />
             * It allows to send special intructions to the PHP from the template
             * Can be used to show all errors, strip comments etc
             * 
             * <spl-macro debug="errors" />
             */
            if($this->allowMacros == true){
    
                if(preg_match_all("/<spl\-macro ".$this->REGEXP["attributes"]."+\s*\/>/i",$this->templates[$ttK],$Macros)){

                    foreach($Macros[1] as $mK=>$macro){
                        
                        $replacement = "";
                        @list($mInstruct,$mArgs) = explode(":",$Macros[2][$mK]);
                        $mInstruct = strtolower($mInstruct);
                        
                        switch($macro){

                            // DEBUG: to display debug on the screen
                            case "debug":
                              
                                switch($mInstruct){
                                    
                                    // SHOW ERRORS
                                    case "errors":
                                        
                                       if(preg_match_all("/{@\w+}/i",$this->templates[$ttK],$unassignedVars))
                                            foreach($unassignedVars[0] as $unV)
                                                $this->__debugger(" {$unV} : unparsed variable in template key: {$ttK}"); 

                                       $replacement = "{@__DEBUG:ERRORS__}";
                                       $showErrors = true;   
                                       
                                    break;
                                
                                
                                    // SHOW VARS
                                    case "vars":

                                         $vars = (!isset($mArgs)) 
                                                    ? $this->Vars 
                                                    : (isset($this->iterators[$mArgs]) 
                                                            ? $this->iterators[$mArgs] 
                                                            : array());

                                         if(isset($vars[0]) && is_array($vars[0])){

                                             $tV = array_reduce($vars,function($r,$Arr){
                                                // each
                                                if(!isset($Arr[0])){
                                                    foreach($Arr as $aK=>$aV){
                                                        $r .= "\t {$aK} => $aV\n";
                                                    }
                                                    $r .= "\t--\n";
                                                }

                                                //ineach
                                                else{

                                                   $r .= array_reduce($Arr,function($r1,$A1){

                                                        foreach($A1 as $aK=>$aV){
                                                            $r1 .= "\t {$aK} => $aV\n";
                                                        }
                                                        $r1 .= "\n";

                                                       return $r1;
                                                   }); 

                                                   $r .= "\t--\n";
                                                }

                                               return $r;  
                                             });
                                         }
                                         else{
                                             foreach($vars as $vK=>$vV){
                                                 $tV .= "\t{$vK} : {$vV} \n";
                                             }
                                         }
                                         $replacement = "<pre>\nSIMPLATE DEBUG VARS: {$mArgs}\n{$tV}</pre>";                                        

                                        
                                        
                                    break;
                                }
                                
                            break;
                            
                            
                            
                            // CMD: Execute commands
                            case "cmd":
                                
                                switch($mInstruct){
                                
                                    // Strip comments off the page
                                    case "stripcomments":
                                        $stripComments = true;
                                    break;
                                
                                
                                
                                
                                    /**
                                     * toJSON:$eachName
                                     * toJSON will return a data set with $this->each() into json
                                     * It will crunch up all children with the parent, and prepare them the way they were inserted
                                     * this macro is memory intensive, use it only when need it.
                                     * If you want Json data, it's preferable to use $this->assignJSON()
                                     * The reason why this was created it's because sometimes, the loop may exist buut you want to do more with  that dat
                                     * 
                                     */
                                    case "tojson":
                                       
                                           // Must be data from the iterators, set with $this->each()
                                           $vars = isset($this->iterators[$mArgs]) 
                                                                ? $this->iterators[$mArgs] 
                                                                : array();


                                              /**
                                               * Children are ineach data that were passed during the creation in $this->each()
                                               * It will grab up all children, prepare them, and make them available for the parent
                                               */
                                              $Children = array();
                                              if(isset($this->iterators["__meta__"][$mArgs]["children"])){

                                                $childrn = array_keys($this->iterators["__meta__"][$mArgs]["children"]);

                                                    foreach($childrn as $childd){

                                                      $cName = end(explode(".",$childd));

                                                      /**
                                                       * Will clean up the key by removing the spl tag {@}
                                                       */
                                                      $Children[$cName] = array_map(function($A){

                                                         return
                                                                array_map(function($AA){
                                                                    foreach($AA as $Ak=>$Av){
                                                                         $nAk = str_replace(array("{@","}"),"",$Ak);

                                                                         // Was included, during the parsing of the ineach, so we'll remove it
                                                                         if($nAk!= "#")
                                                                             $AA[$nAk] = $Av;
                                                                        // unset the original array
                                                                        unset($AA[$Ak]);
                                                                    }
                                                                    return 
                                                                        $AA;
                                                                 },$A);

                                                      },$this->iterators[$childd]); 
                                                    } 
                                                }


                                                 /**
                                                  * Clean the main iterators, by removing spl tag {@}
                                                  */
                                                 $vars = array_map(function($AA){
                                                            foreach($AA as $Ak=>$Av){
                                                                 $nAk = str_replace(array("{@","}"),"",$Ak);

                                                                 if($nAk!= "#")
                                                                     $AA[$nAk] = $Av;

                                                                unset($AA[$Ak]);
                                                            }
                                                            return 
                                                                $AA ;
                                                         },$vars);

                                                 /**
                                                  * Merge children to parents 
                                                  */
                                                 if(count($Children)){
                                                     $childrenNames = array_keys($Children);

                                                     foreach($childrenNames as $childName)
                                                        foreach($vars as $vvk=>$vvv)
                                                            $vars[$vvk] += array($childName=>$Children[$childName][$vvk]);
                                                 }    


                                          $replacement = json_encode($vars); 
                                          
                                    break;
                                }

                            break;
                        }
                        
                        $this->templates[$ttK] = str_replace($Macros[0][$mK],$replacement,$this->templates[$ttK]);
                    }

                }
            }
            

                // Remove all unparsed iterators and unaassigned vars
               // $this->templates[$ttK] = str_replace($definedIterationsRepVals,array(""),preg_replace("/{@\w+}/i","",$this->templates[$ttK]));

        }

        // FINAL Replacements based on allowCmds
        if($this->allowMacros == true){
            
            $R = array();
            
            // Show errors
            if(isset($showErrors) && $showErrors == true){
                    $debug = "";
                    foreach($this->debugger as $dV)
                        $debug .= "\t{$dV}\n";

                    $R = array(
                        "{@__DEBUG:ERRORS__}"=> "<pre>\nSIMPLATE DEBUG ERRORS :\n{$debug}\n</pre>"
                    );                
            }

            
            /**
             * Last going thru the templates to finish the work
             */    
            foreach($this->templates as $ttK=>$ttV){
                
                $this->templates[$ttK] = str_replace(array_keys($R),array_values($R),$this->templates[$ttK]);
                
               if(isset($stripComments) && $stripComments == true)
                 $this->templates[$ttK] = $this->stripHTMLComments($this->templates[$ttK]);
            
            }

        }
        

        
            /**
             * Last going thru the templates to finish the work
             */    
             if($this->definedLiteralsCount){
                foreach($this->templates as $ttK=>$ttV)
                    $this->templates[$ttK] = $this->parseLiterals($this->templates[$ttK]);
             }
            
        return $this;
    }
    
    
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
   
}
