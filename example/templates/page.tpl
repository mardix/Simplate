--------------------------------------------------------------------------------

SIMLATE Example

--------------------------------------------------------------------------------

1. Show a variable
My name is {@Name}

2. Apply filter to a variable
My name is {@Name.toUpper()} 

3. Apply a custom filter to a variable
 {@Name.toRahel()}

4. Chain filter
{@Name.toRahel().toUpper()}

5. Some built-in filters: 

    - .toUpper()
       {@Name.toUpper()}
       
    - .toLower()
       {@Name.toLower()}

    - .toNumber()
        I got ${@Amount.toNumber(2)}
        
    - .replace()
       URL: {@Site.replace(http://www,WWW)}
       
    - .toDate()
      Date: {@CurrentTime.toDate()}

    - .length()
      My name contains {@Name.length()} characters

--------------------------------------------------------------------------------

Include a file on the fly
    <spl-include src="page3.tpl" />

--------------------------------------------------------------------------------

Include a template that was added from php with $SPL->addFile
    <spl-include src="@Page2" />


--------------------------------------------------------------------------------

Include a template that was added from php with $SPL->addTemplate
    <spl-include src="@Myoplex" />

    
--------------------------------------------------------------------------------

Conditional statement IF/ELSEIF/ELSE
    
The number is {@Number}.

<spl-if Number.odd() >
    Pass the IF, because it's odd

    <spl-elseif Number.equals(4) >
        Pass the ELSE IF because if equals 4
        
    <spl-else>
        IF & ELSEIF failed, this is ELSE
    
</spl-if> 

More stuff can be added below

--------------------------------------------------------------------------------

Creating loops with limit 3 items. To show all, just remove the limit

<spl-each Counto >
    We are in SPL-EACH #{@Counter}
        
        Local variable, with the name Site
            {@Site}
            
        Variable from Global scope, with the name Site
            {@:Site}
    
        --------
        Inner Loop, with limit 2
        
            <spl-ineach Inner >
                Chain
                    {@Time.toDate().toRahel().toMardix().toUpper()}
                    
                Global scope with filters, with the name Site
                    {@:Site.replace(.net,.net/).toUpper()} 
                    
                Parent Scope, with the name Site
                    {@#Site} 
                
                Local variable, with the name Site
                    {@Site.replace(.com,.org).capitalize()}
                    
                    
                {@#Counter}  / {@Team.toMardix().toUpperCase()} Won #{@Game} in Foto: {@Foto} in PL: {@#Name} 
            </spl-ineach>
    
</spl-each>  



--------------------------------------------------------------------------------

Apply Macros

    Strip all comments
        <spl-macro cmd="stripComments" />
        
        <!-- This is a comment, it will be stripped -->

     Show all vars
        <spl-macro debug="vars" />
        
     
     Show errors
        <spl-macro debug="errors" />
        
     

        
Thank You!

