SIMPLATE
========
---

##### What is Simplate?

Simplate (Simple Template) is a simple PHP template engine that allows the separation of PHP code and HTML.

Simplate consists of only class that contains the necessary methods to add templates, assign variables and iterate over an array or list of object.

I created Simplate for my own project, but decided to share with everyone.


##### Why another PHP template engine?

Why not? Well this one is smaller comparing to what's out there and it gets the job done right. It is not bloated and contains some useful methods and tricks. And above all, no PHP will be found in your template. 

##### Twitter Mardix

Follow me on twitter: [http://twitter.com/mardix](http://twitter.com/mardix), or check out my blog: [http://mardix.wordpress.com/](http://mardix.wordpress.com/) for the latest update on Simplate.

Also browse my repo for some more libraries that you may want to use.

Peace!



---

###Public methods:###

>   **set()** : *set variables*

>   **addTemplate()** : *add a template*

>   **render()**  : *To render the template*

>   **iterator()**   : *Create an iteration*

>   **setDefault()**  : *To set a template as the default one to be rendered*

>   **saveTo()**: *To save the rendered content into a file*


---

###PHP Methods and Template Tags###

#### Set() and `%VariableName%` : To assign variable

> `Simplate::set($key,$value)` 

> `$Tpl->set("Location","Charlotte, NC");`

> `$Tpl->set(array("Name"=>"Mardix","Drink"=>"Grey Goose"));`

> In the template to assign variables Simplate uses the **%** sign

> `My name is %Name%, I live in %Location% and I like to drink %Drink%`

---

#### AddTemplate() and `<spl-template: {templateKey} />`  to insert a template


> `Simplate::addTemplate($key,$filename)`

> `$Tpl->addTemplate("home","home.tpl")`

>     ->addTemplate("page2","page2.tpl");`


home.tpl

> `This is some HTML`

> `<spl-template: page2 />`

> `Other HTML Code`


---

#### Iterator() and `<spl-each: {$key} ></spl-endeach>` to iterate over list of data

> `Simplate::iterator($key,$ArrayData)`

> `for($i=0;$i<10;$i++){`

>> `$Tpl->iterator("playlist",array("Artist"=>"Artist #{$i}","Album"=>"My new album #{$i}"));`

> `}`

home.tpl

> `This is some HTML`

> `Iterations start below`

>  `<spl-each: playlist >`

>> `%Artist% has album titled %Album%`

> `</spl-endeach>`

> `Other HTML Code`


---
### Other Methods

#### __Construct() to init and set the root dir of the template

> `$Tpl = new Simplate("/templates");`


#### setRootDir()

> `$Tpl->setRootDir("/templates");`


---

#### Render(): To render the page

> `Simplate::render()`

> `$Tpl->render()` 


or

> `$Tpl->render("home")`

Will render the templatekey->home


---

#### SaveTo(): to save the rendered content to a file

> `$Tpl->saveTo("/sources/mysite.html");`


---


#### SetDefault(): to set a template as the default page to be rendered

> `$Tpl->setDefault("home");`

Alternatively, right after addTemplate() is called, call setDefault() with no argument to set the last page as the default

> `$Tpl->addTemplate("page3","page3.tpl")->setDefault();`


---

### Other Template Tags ###

Simplate uses tags in the HTML template to assign variables. 

All tags use the opening Simplate tag: **`<spl-{name}: >`**
    
---


#### `<spl-if: {condition} > </spl-endif>` : Conditional statement

> **`<spl-if:`** Number.odd() >`

>> This part will be displayed if the variable named Number is odd

>> **`<spl-elseif`**: Number.gte(4) >`

>>> This part will be shown instead if Number is greater than 4

>> **`<spl-else>`**

>>> This part will be displayed only if none of if and else if is true

> **`</spl-endif>`**

##### Pre-made methods for conditional satetements

Simplate come with a built-in methods to validate conditions

The format is `{VariableName}.{MethodName}({Value})`

i.e: `Number.odd()` or `Number.gte(4)`

List of the built-in methods

>>> `X.is(value)         : X == value`

>>> `X.equals(value)     : X == value`

>>> `X.not(value)        : X != value`

>>> `X.empty()           : X == ""`

>>> `X.match(value)      : X contains value`

>>> `X.even()            : X is even ?`

>>> `X.odd()             : X is odd ?`

>>> `X.gt(value)         : X > value`

>>> `X.gte(value)        : X >= value`

>>> `X.lt(value)         : X < value`

>>> `X.lte(value)        : X <= value`

##### Negation

> To negate the condition, you can add **!** before the variableName

>> `!X.even() ` 

---

#### `<spl-include: file.tpl />` to include a file directly in the template without calling it from PHP

home.tpl

> `This is some HTML`

> `<spl-include: page2.tpl />`

> `Other HTML Code`


Upon parsing home.tpl, 'mypage.tpl' will be included. The included template can also include other templates, which can include other templates and so on. 

##### Difference between `<spl-include: page2.tpl />` and `<spl-template: page2 />` 

`<spl-include: page2.tpl />` : include the file *page2.tpl* while the parent is being parsed

`<spl-template: page2 />` : is already parsed by PHP and the content is just included there

---

### Attributes in Tags

Simplate allow some attributes in some tags. Attributes allow to modify the rendering behavior, such as a *limit* and *absolute path* for an included file.

#### LIMIT

Limit is implemented in the iterations to reduce the data to be displayed

home.tpl

>  `<spl-each: playlist limit="5" >`

>> `%Artist% has album titled %Album%`

> `</spl-endeach>`

This iteration will display only 5 entries


#### Absolute

By default when `<spl-include: page2.tpl />` the template will try to look in the setRootDir directory for such file, but when Absolute is implemented in `<spl-include: page2.tpl  absolute="true" />` it will force it to get the file from the src.

