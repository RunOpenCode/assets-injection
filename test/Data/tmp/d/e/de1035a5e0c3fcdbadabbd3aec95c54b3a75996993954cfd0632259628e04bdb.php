<?php

/* test2 */
class __TwigTemplate_1e83fd748e3ad436aa2318cb57d442c4980ac50d5de373ed48436518e8df54b4 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 2
        $this->parent = $this->loadTemplate("test", "test2", 2);
        $this->blocks = array(
            'pero' => array($this, 'block_pero'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "test";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_pero($context, array $blocks = array())
    {
        // line 4
        echo "                    Nesto

                ";
    }

    public function getTemplateName()
    {
        return "test2";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  31 => 4,  28 => 3,  11 => 2,);
    }
}
/* */
/*                 {% extends "test" %}*/
/*                 {% block pero %}*/
/*                     Nesto*/
/* */
/*                 {% endblock %}*/
/* */
/*             */
