<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* modules/custom/helper/templates/list-entity.html.twig */
class __TwigTemplate_646d7ac742b65054a6557dab2cb96c1a extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        $this->displayBlock('content', $context, $blocks);
    }

    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 2
        echo "  <!-- Start of the content block for reviews -->
  <div id=\"reviews\">
    <!-- Container for individual reviews -->
    <div class=\"container-reviews\">
      <!-- Loop through each review in the 'reviews' list -->
      ";
        // line 7
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["items"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
            // line 8
            echo "        <!-- Individual review item with a unique ID based on the review's ID -->
        <div class=\"review-item\" id=\"review-";
            // line 9
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "id", [], "any", false, false, true, 9), 9, $this->source), "html", null, true);
            echo "\">
          <!-- Section for the author's information -->
          <div class=\"review-author-info\">
            <!-- Avatar of the user -->
            <div class=\"avatar\">";
            // line 13
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "avatar", [], "any", false, false, true, 13), 13, $this->source), "html", null, true);
            echo "</div>
            <!-- User's name -->
            <div class=\"user-name\">";
            // line 15
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "user_name", [], "any", false, false, true, 15), 15, $this->source), "html", null, true);
            echo "</div>
            <!-- Review creation time -->
            <div class=\"review-created-time\">";
            // line 17
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "created", [], "any", false, false, true, 17), 17, $this->source), "html", null, true);
            echo "</div>
          </div>

          <!-- Section for the review discussion -->
          <div class=\"review-disc\">
            <!-- Image associated with the review -->
            <div class=\"review-disc-image\">";
            // line 23
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "review_image", [], "any", false, false, true, 23), 23, $this->source), "html", null, true);
            echo "</div>
            <!-- Text content of the review -->
            <div class=\"review-disc-text\">
              <p>";
            // line 26
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "review", [], "any", false, false, true, 26), 26, $this->source), "html", null, true);
            echo "</p>
            </div>
          </div>

          <!-- Section for the author's contact information -->
          <div class=\"review-authors-contacts\">
            <!-- User's email -->
            <div class=\"authors-email\">Email: ";
            // line 33
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "user_email", [], "any", false, false, true, 33), 33, $this->source), "html", null, true);
            echo "</div>

            <!-- User's phone number -->
            <div class=\"authors-phone\">Tel: +";
            // line 36
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "user_phone", [], "any", false, false, true, 36), 36, $this->source), "html", null, true);
            echo "</div>
          </div>

          <!-- Admin actions section (visible only if the user is an admin) -->
          ";
            // line 40
            if (($context["is_admin"] ?? null)) {
                // line 41
                echo "            <div class=\"admin-actions\">
              <!-- Edit action button -->
              <div class=\"edit\">";
                // line 43
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "edit", [], "any", false, false, true, 43), 43, $this->source), "html", null, true);
                echo "</div>
              <!-- Delete action button -->
              <div class=\"delete\">";
                // line 45
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "delete", [], "any", false, false, true, 45), 45, $this->source), "html", null, true);
                echo "</div>
            </div>
          ";
            }
            // line 48
            echo "        </div>
      ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 50
        echo "
    </div>
  </div>
  <!-- End of the content block for reviews -->
";
    }

    public function getTemplateName()
    {
        return "modules/custom/helper/templates/list-entity.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  140 => 50,  133 => 48,  127 => 45,  122 => 43,  118 => 41,  116 => 40,  109 => 36,  103 => 33,  93 => 26,  87 => 23,  78 => 17,  73 => 15,  68 => 13,  61 => 9,  58 => 8,  54 => 7,  47 => 2,  40 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "modules/custom/helper/templates/list-entity.html.twig", "/var/www/web/modules/custom/helper/templates/list-entity.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("block" => 1, "for" => 7, "if" => 40);
        static $filters = array("escape" => 9);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['block', 'for', 'if'],
                ['escape'],
                []
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
