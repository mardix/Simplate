
hello World
<spl-each Loop limit="3" >
    We are in SPL-EACH #{@Counter}
    
        <spl-if Counter.is(2) >
                IT'S 2
               
                <spl-else>
                    Not 2 but {@Counter}
        </spl-if>
    
        Local variable, with the name Site
            {@Site}
            
        Variable from Global scope, with the name Site
            {@:Site}
    
        --------
        Inner Loop, with limit 2
        
            <spl-each InnerLoop limit='2' >
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
