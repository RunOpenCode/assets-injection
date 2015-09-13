<?php

/* test */
class __TwigTemplate_a25ad329047ce0791effc24f616bbea34e31e6c3afb6a4b034199622438f306e extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'pero' => array($this, 'block_pero'),
            'djuro' => array($this, 'block_djuro'),
            'janko' => array($this, 'block_janko'),
            'cmarko' => array($this, 'block_cmarko'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $context[strtolower(get_class($this)) . 'assets_injection_buffer'] = new \RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Buff\OutputBuffer();        
        ob_start();        
        // line 1
        echo "
                ";
        // line 2
        $this->displayBlock('pero', $context, $blocks);
        // line 6
        echo "
                ";
        $context[strtolower(get_class($this)) . 'assets_injection_buffer'][] = ob_get_clean();        
        $context[strtolower(get_class($this)) . 'assets_injection_buffer'][] = Closure::bind(function($context, $blocks) {         
        // line 7
        $this->env->getExtension('assets_injection')->getManager()->render('css', null        , array()        );        
        }, $this);        
        ob_start();        
        // line 8
        echo "


                ";
        $context[strtolower(get_class($this)) . 'assets_injection_buffer'][] = ob_get_clean();        
        $context[strtolower(get_class($this)) . 'assets_injection_buffer'][] = Closure::bind(function($context, $blocks) {         
        // line 11
        $this->displayBlock('djuro', $context, $blocks);
        }, $this);        
        ob_start();        
        // line 25
        echo "
                ";
        // line 26
        $this->env->getExtension('assets_injection')->getManager()        
            ->inject('jquery')        
        ;        
        // line 27
        echo "
            ";
        $context[strtolower(get_class($this)) . 'assets_injection_buffer'][] = ob_get_clean();        
        $context[strtolower(get_class($this)) . 'assets_injection_buffer']->display($context, $blocks);        
    }

    // line 2
    public function block_pero($context, array $blocks = array())
    {
        // line 3
        echo "

                ";
    }

    // line 11
    public function block_djuro($context, array $blocks = array())
    {
        // line 12
        echo "
                    ";
        // line 13
        $this->displayBlock('janko', $context, $blocks);
        // line 22
        echo "

                ";
    }

    // line 13
    public function block_janko($context, array $blocks = array())
    {
        // line 14
        echo "
                        ";
        // line 15
        $this->displayBlock('cmarko', $context, $blocks);
        // line 20
        echo "
                    ";
    }

    // line 15
    public function block_cmarko($context, array $blocks = array())
    {
        // line 16
        echo "
                            ";
        // line 17
        $this->env->getExtension('assets_injection')->getManager()->render('js', null        , array()        );        
        // line 18
        echo "
                        ";
    }

    public function getTemplateName()
    {
        return "test";
    }

    public function getDebugInfo()
    {
        return array (  108 => 18,  106 => 17,  103 => 16,  100 => 15,  95 => 20,  93 => 15,  90 => 14,  87 => 13,  81 => 22,  79 => 13,  76 => 12,  73 => 11,  67 => 3,  64 => 2,  57 => 27,  53 => 26,  50 => 25,  46 => 11,  39 => 8,  35 => 7,  30 => 6,  28 => 2,  25 => 1,);
    }
}
/* */
/*                 {% block pero %}*/
/* */
/* */
/*                 {% endblock %}*/
/* */
/*                 {% css %}*/
/* */
/* */
/* */
/*                 {% block djuro %}*/
/* */
/*                     {% block janko %}*/
/* */
/*                         {% block cmarko %}*/
/* */
/*                             {% js %}*/
/* */
/*                         {% endblock %}*/
/* */
/*                     {% endblock %}*/
/* */
/* */
/*                 {% endblock %}*/
/* */
/*                 {% inject "jquery" %}*/
/* */
/*             */
