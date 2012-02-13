
            --------------------------------
            THIS IS PAGE #2
            --------------------------------

            Hey {@Name}, you are on page #2, it has been defined in the PHP 
            and was include with <SPL-TEMPLATE>

            Loop inside of an included page
            <spl-each Counto limit="2" >
                We are winning - {@Counter} - Playlist: {@Playlist} {@Site.toUpperCase()}
            </spl-each>

            {@Site}

            {@MarcoPolo}
            
            INLCUDE A PAGE INSIDE OF AN INCLUDED PAGE

            Include a page from an included page
            
            <spl-include src="page4.tpl" />