

<spl-each Loop >
    We are in SPL-EACH #{@Counter}
        
        Local variable, with the name Site
            {@Site}
            
        Variable from Global scope, with the name Site
            {@:Site}
    
        --------
        Inner Loop, with limit 2
        
            <spl-ineach InnerLoop  >
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
            </spl-ineach>
    
</spl-each> 