<?php

/**
 * @name    loop
 * @author  Mardix
 * @since   Feb 12, 2012,2:34:46 PM
 * @desc    
 */


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


$SPL  = new Simplate(__DIR__."/templates");

$SPL->addFile("LoopPage","loop.tpl");

$SPL->assign("Site","Yahoo.com");

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
    
// Let's prepare some data for the <spl-ineach>
    $innerD2 = array();
    for($j=0;$j<5;$j++){
        $innerD2[] = array(
            "GrandKids"=>"Grand Child $j",
            "MyParent"=>$innerD
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
                  "InnerLoop"=>$innerD
            );
   

}
/**
 * All data is aggreagate, we assign them at one time
 */
$SPL->each("Loop",$data);


/**
 * To render the template.
 * print it by using print()
 */
 print $SPL->render("LoopPage");