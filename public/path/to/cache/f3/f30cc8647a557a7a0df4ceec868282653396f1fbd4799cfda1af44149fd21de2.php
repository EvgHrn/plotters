<?php

/* index.html */
class __TwigTemplate_1b268dd772a29aa21cafe84f7f3880c273ec71c4ad96ac02b46abdc7e3ccfd7c extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html>
    <head>
        <meta charset=\"utf-8\"/>
        <title>Plotters</title>        <link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css\" integrity=\"sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ\" crossorigin=\"anonymous\">
    </head>
    <body>
        <h1>PLOTTERS</h1>
        <div>a microframework for PHP</div>

        <form>
            <div class=\"form-group\">
                <label for=\"exampleInputEmail1\">Email address</label>
                <input type=\"email\" class=\"form-control\" id=\"exampleInputEmail1\" placeholder=\"Email\">
            </div>
            <div class=\"form-group\">
                <label for=\"exampleInputPassword1\">Password</label>
                <input type=\"password\" class=\"form-control\" id=\"exampleInputPassword1\" placeholder=\"Password\">
            </div>
            <div class=\"form-group\">
                <label for=\"exampleInputFile\">File input</label>
                <input type=\"file\" id=\"exampleInputFile\">
                <p class=\"help-block\">Example block-level help text here.</p>
            </div>
            <div class=\"checkbox\">
                <label>
                <input type=\"checkbox\"> Check me out
                </label>
            </div>
            <button type=\"submit\" class=\"btn btn-default\">Submit</button>
        </form>

    </body>
</html>
";
    }

    public function getTemplateName()
    {
        return "index.html";
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "index.html", "C:\\xampp\\htdocs\\plotters\\templates\\index.html");
    }
}
