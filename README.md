# Assets Injection

Assets injection library is missing assets management component that combines Dependency Injection and Assetic in
one library that helps full stack developers/front end developers to include necessary javascripts and stylesheets
into template without worrying about dependencies and order of inclusion.

If projects such as Bower or Components or some other will help you to pull assets from Github to your project directory,
this component will help you to include those assets into template.

## How? Learn by example...

In this example, we will presume that you use Twig. So in your template:

    <html>
        <head>
            <!-- ... your code ... -->
            {% css %}
            <!-- ... your code ... -->
        </head>
        <body>
        
            <!-- ... your code ... -->
            {% inject 'my-asset-library' %}
            <!-- ... your code ... -->
            
            {% js %}
        </body>
    </html>
    
And let's say that it is defined that your library `my-asset-library` depends on `jquery-ui` and `jquery-ui` needs `jquery`,
that would mean that you have to add that code to `HEAD`/`BODY` tag in correct order.

This library solves that issue, resolving all those dependencies, loading and injecting all required javascripts and stylesheets
based on definitions of libraries. So, your result might look like:

    <html>
        <head>
            <!-- ... your code ... -->
            <link rel="stylesheet" type="text/css" href="lib/jquery-ui/jquery-ui.min.css" />
            <link rel="stylesheet" type="text/css" href="app/my-app.css" />
            <!-- ... your code ... -->
        </head>
        <body>
        
            <!-- ... your code ... -->
            <!-- ... your code ... -->
            
            <script type="text/javascript" href="lib/jquery/jquery.min.css"></script>
            <script type="text/javascript" href="lib/jquery-ui/jquery-ui.min.css"></script>
            <script type="text/javascript" href="app/my-app.js"></script>
        </body>
    </html>
    
**One single `{% inject "library-name" %}` will save you from all that hassle...!**

And that is not all - you can configure library to combine and compress all those assets into one file, so your result 
might look like this in production enviroment:

    <html>
        <head>
            <!-- ... your code ... -->
            <link rel="stylesheet" type="text/css" href="cache/app.css" />
            <!-- ... your code ... -->
        </head>
        <body>
        
            <!-- ... your code ... -->
            <!-- ... your code ... -->
            
            <script type="text/javascript" href="cache/app.js"></script>
        </body>
    </html>


## In development 

Note that this library is still under heavy development, and first release is expected in October, 2015, as well as 
Symfony bundle.





