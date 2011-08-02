<?php
/**
 * SIMPLATE EXAMPLE
 * 
 * This is an example file showing how easy Simplate is
 * You can run this in the command line or in the browser. 
 * If run in the browser, please check the source code of the result, for a proper display
 * 
 */


/**
 * include the Simplate class
 */
include("../Simplate.php");


/**
 * Create custom filters that will be applied to variables
 */
Simplate::setFilter("toMardix",function($var){
    return "Mardix owns {$var}";
});


Simplate::setFilter("toRahel",function($var){
    return "Rahel Loves {$var}";
});


$SPL  = new Simplate("./");

$SPL->allowMacros();

$SPL->addFile("Default","page.tpl") // Set the first page as the default page. Automtically the first page will be set asDefault
    ->addFile("Page2", "page2.tpl") // add another template
    ->addTemplate("Myoplex","This is a Myoplex to {@Name} ") // Add a template from strings
    ->assign("Name","Mardix") // Set variable
    ->assign("Site","http://www.givemebeats.net")
      // Set vars via arrays
    ->assign(array("Number"=>7,
                    "Age"=>29,
                    "Amount"=>2975.2,
                    "LongText"=>"{@Name} is a long text we are going to truncate and see what it gives us...",
                    "CurrentTime"=>time(),
                ));


// Let's prepare some data for the <spl-ineach>
    $innerD = array();
    for($j=0;$j<5;$j++){
        $innerD[] = array(
             "Game"=>"$j",
             "Team"=>"Mavericks {$j}",
             "Foto"=>"La foto se me borro $j",
             "Site"=>"www.{$j}.child.com",
             "Time"=>time(),
        );
    }
    
/**
 * Iterations
 * Create an iteration that will be used by <spl-each>
 * The name of of it is Counto
 * To make the loop:
 *  <spl-each Counto >
 *      {@Site}
 *  </spl-each>
 * 
 * To limit the loop
 * 
 *  <spl-each Counto limit="5" >
 *      {@Site}
 *  </spl-each>
 * 
 * 
 * <spl-each Counto >
 *      {@Site}
 * 
 *      <spl-ineach Inner >
 *          {@Game}
 *      </spl-ineach>
 * 
 *  </spl-each>
 */
for($i=0;$i<10;$i++){

    $data[] =  array(
                  "Site"=>"www.{$i}.parent.com",
                  "Counter"=>$i,
                  "Playlist"=>"playlist magic number -{$i}",
                  "Name"=>"Hooligans- {$i}",
                  
                  
                  // This is an <spl-ineach>  with a name called Inner. <spl-ineach Inner >
                  "Inner"=>array_map(function($a)use($i){
                      return $a+array("Parent"=>$i);
                  },$innerD)
            );
   

}
/**
 * All data is aggreagate, we assign them at one time
 */
$SPL->each("Counto",$data);              

    

/**
 * Or you could assign them on the go
 */
for($i=0;$i<10;$i++){

    $data =  array(
                  "Site"=>"www.{$i}.parent.com",
                  "Counter"=>$i,
                  "Playlist"=>"playlist magic number -{$i}",
                  "Name"=>"Hooligans- {$i}",
                  
                  // This is an <spl-ineach>  with a name called Inner. <spl-ineach Inner >
                  "Inner"=>array_map(function($a)use($i){
                      return $a+array("Parent"=>$i);
                  },$innerD)
            );
   
     // Assign on the fly
     $SPL->each("TheOtherCounto",$data);
}


/**
 * To render the template.
 * print it by using print()
 */
 print $SPL->render("Default");


 // That's it on th PHP side