#{@Simplate}

---


### What is Simplate?

Simplate (Simple Template) is a simple PHP template engine to seprate application logic and content from its presentation.



#### Why another PHP template engine?

Well, I created Simplate for my own project, but decided to share with everyone. 

I was also tired of all those bloated PHP template system, with steep learning curve that create another language on top of PHP. Really?!

So this one is smaller, it consists of one PHP file. It is not bloated and contains some useful methods and tricks. And above all, it's damn simple.


#### And who created it?
Me. Mardix. You can follow me  [@mardix](http://twitter.com/mardix), or check out my github: [mardix.github.com](http://mardix.github.com/) to check out some of my other code that you may want to fork.

So Holla!

---

## {@Simplate for Developers}

Simplate use PHP 5.3 or later, and can be extended for your needs. But by default it comes with everything you need.

On the PHP side, developer can assign variables, include templates, create loops, create embeded loops, create filters etc...


#### Public methods

>***setDir($dirPath)*** : to set the root dir

>***assign($key,$value)*** : to assign variables. 

>***assignJSON($key,$Arrayvalue)*** : to assign and array that will be passed as JSON

>***addFile($tplName,$filename)***: To add a template file that will be include with the tag `<spl-include src="@tplName" />`

>***addTemplate($tplName,$Content)***: to add a string or content as template that will be included with the tag `<spl-include src="@tplName" />`

>***render($tplName)***: to render the template as string. Use print() to print the template content

>***each($name,$ArrayData)***: to create a loop that can be included with the tag `<spl-each eachName > </spl-each>`

>***stripComments(bool)***: to strip all HTML comments from the page. You can also use `<spl-macro cmd="stripComments" />` in the template

>***saveTo($tplName)***: to save the content of the template to a file

>***allowMacros(bool)***: To allow macros in the template, like `<spl-macro $macro="$value" />`

>***removeTemplate($tplName)***: to remove a template by its name when it was set

>***clearVars()***: to reset all vars

>***clearAll()*** : reset everything

#### Advanced

#### Filters

Filters are custom methods to be applied on the variables on the template side.

`{@VarName}` is a normal variable. Now in `{@VarName.toUpper()}` `.toUpper()` is a filter. 

Filters are also chainable `{@VarName.replace(a,b).toUpper()}`

#### Create your own filters

You can create your own filter by using the static method `Simplate::setFilter`

Let's create a custom filter that will calculate the length of a string


`Simplate::setFilter("strlen",function($var){

        return strlen($var);

});`

Now in your template you can use it like this:

`{@VarName.strlen()}`

Now for a full example, please go the example folder to see more examples

So {@Simplate}!


---

## {@Simplate for Designers}

Simplate was designed with designers in mind. Therefor it has a low learning curve. It uses HTML-like syntax
to display data and execute piece of code. And it's easy with your favorite HTML editor

### Variables {@VarName}

In Simplate, variables are marked with {@ and }, like `{@VarName}`

Because I'm also a designer, I know how it is to have the same syntax in all of your codes. Therefor, I made sure that all variable become pseudo-object
 , something like javascript, where you can apply filters on them, and even chain all them like so

` {@VarName.replace(.com,.net).toUpper().truncate(15)} `

So what will this do? Pretty simple, you read it and you got it. It will replace .com with .net, then uppercase it, then truncate it to 15 characters

So {@Simplate}! 


#### Built-in filters

Simplate comes with some built-in filters to apply on variables. PHP developers can set their own filter by using the static method Simplate::setFilter

>***.toUpper()*** : to upper case 

>***.toLower()*** : to lower case 

>***.capitalize()*** : to capitalize

>***.truncate(start,end)*** : to truncate from start to end. If only the start point is available, it will start at 0 and end at start

>***.length()*** : get the length of the variable

>***.toNumber(decimal)*** : to format the string to a number with decimal.

>***.replace(pattern,replacement)*** : to replace pattern with replacement

>***.trim()*** : to remove left and right blank space

>***.escapeHTML()*** : to escape the HTML

>***.stripTags()*** : to strip all HTML or PHP tags

>***.toDate(format)***: to format a date. Use PHP letter to format the date



### Markup Tags `<spl-tag />`

 Simplate allow the use of the Simplate markup  tags `<spl-tag>` to do conditional statements, loops, include, macros and more.

To keep Simplate markup tags consisitent with HTML, Simplate use opening and close tags like the following

`<spl-tag> </spl-tag>`  or `<spl-tag />`


### Include `<spl-include src=""/>`

`<spl-include src=""/>`  allow to include a template in the current template file, where `src` is the source of the template.

The source can be a file itself with extension or the name of the template when it was created on the PHP  side. 

 > ` <spl-include src="myTemplate.tpl" /> ` : this will include the template file myTemplate.tpl in the current template. By default, it's including the template file from the directory that was set in PHP with Simplate::setDir()

> `<spl-include src="../myownPath/myTemplate.tpl" absolute="true"/>`: this will include the template file myTemplate.tpl in the current template. But this time it will call the file from another location. This is done by settinge absolute to true. 

> `<spl-include src="@ContactPage" />` : To include a file that was loaded from the PHP with `Simplate::addFile("ContactPage","contact.tpl")`. In src, you add `@TemplateName`


### Conditional Statement `<spl-[if|elseif|else]  VarName.test() >` `</spl-if>`

Conditional statement allow to make some test before executing some piece of code. It can be use to include, show parts of data

Let's say we assign a variable called Number as 7 -> `Simplate::assign("Number",7);`


`<spl-if Number.odd() >`

> `Will execute this part if Number is an odd number`

>`<spl-elseif Number.is(4) >`

>`Will execute if Number == 4`

>`<spl-else>`

>`All failed show this piece`

`</spl-if>`


#### Built-in conditional test

The built in condition return true when test pass.

>***.is(number|string)*** or ***equals(number|string)*** : To test the equality

>***.not(number|string)*** : inequality

>***.null()*** or ***empty()*** : Test if null

>***.startsWith(val)*** : if starts with val

>***.endsWith(val)*** : if ends with val

>***.match(val)*** : if contains val

>***.even()*** : if a number is even

>***.odd()*** : if a number is odd

>***.gt(number)*** if it's greater than number

>***.gte(number)*** if it's greater than or equals number

>***.lt(number)*** if it's lesser than number

>***.lte(number)*** if it's lesser than or equals number


But you can force a conditional statement to be negative by adding ! in front of it. 

Let's say the Number is 7, so it's an odd number. We can negate a test like the following

>`<spl-if !Number.odd() >`

Which means, test if Number is not odd. 


### Each loop `<spl-each eachName >`

It allows to loop over an array set on the PHP side with `Simplate::each("eachName",Array)`

`<spl-each eachName>`

>`{@Name}` 

`</spl-each>`


### InEach loop `<spl-ineach ineachName >`

It allows to loop over an array set on the PHP side with `Simplate::each("eachName",Array)`, where there is a key that was itself and each with the key name ineachName

`<spl-each eachName>`

>`{@Name}` 


>`<spl-each ineachName>`

>>`{@Name}` 

>`</spl-ineach>`


`</spl-each>`

@TODO: more doc


#### Macros

Macros are instruction from the template that will execute some commands on the PHP such as show errors, return data as JSON

@TODO: more doc


Please refer to the example directory for examples



 [@mardix](http://twitter.com/mardix)













