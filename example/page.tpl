SIMLATE TEST

This is a test page show, iterate and include data in the template

--------------------------------
VARIABLE
My name is %Name%
Site: %Site%

--------------------------------
INCLUDE A FILE FROM FILE CALLED IN THE PHP. 
The template key is 'page2'

<spl-template: page2 />


--------------------------------
CONDITIONAL STATEMENT
<spl-if: Number.odd() >
    INCLUDE A FILE FROM TEMPLATE ITSELF AFTER A CONDITION HAS BEEN SATISFIED
    <spl-include: page3.tpl />

    <spl-elseif: Number.equals(4) >
        ELSEIF Number %Number% is 7 now
        
    <spl-else>
        ELSE Number %Number% is not even or 4
        But we can see waht we can do
        Add Image here
        Good styff
    
</spl-endif> 

More text right here under IF



--------------------------------

MAKE A LOOP. SHOW EVERYTHING
<spl-each: counto >
     It is so much better now to count %Counter% 
</spl-endeach>         
       

CREATE A LOOP OF ONLY 5 ITEMS
<spl-each: counto limit="5" >
    Each 3 %Counter% 
</spl-endeach>  
 