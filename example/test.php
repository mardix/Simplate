<?php
/**
 * This is an example file showing how easy Simplate is
 */

include("../Simplate.php");

$Tpl = new Simplate("./");


$Tpl->addTemplate("home","page.tpl")->setDefault() // Set the first page as the default page. Automtically the first page will be set asDefault
    ->addTemplate("page2", "page2.tpl") // add another template
    ->set("Name","Mardix") // Set variable
    ->set("Site","http://www.givemebeats.net")
    ->set("Number",7)
    ->set("Age",29);


/**
 * Iterations
 */
for($i=0;$i<10;$i++){
    
    $Tpl->iterator("counto",
            array("Counter"=>$i,"Playlist"=>"PL-{$i}")
    );
}


/**
 * Print the rendered data
 */
 print $Tpl->render();
