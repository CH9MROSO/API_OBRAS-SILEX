<?php

/* email_notify_registration.html.twig */
class __TwigTemplate_44f242c5b179cc5d4184fe17f345a8e89ca9984b16c8d3cbc6e7652575b28ce7 extends Twig_Template
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
        echo "<div style=\"background:#f4f3f1\">
\t<center style=\"background:#f4f3f1;padding:30px 0\">
\t\t<table style=\"color:#555;font:16px/22px Arial,sans-serif\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"600\">
\t    \t<tbody>
\t    \t\t<tr>
    \t\t\t\t<td style=\"background:#55585d;color:#fff;line-height:1;font-size:18px;font-weight:bold;text-align:center;padding:30px\">Confirmación de registro</td>
\t\t\t\t</tr>

\t\t\t\t<tr>
    \t\t\t\t<td style=\"background:#fff;padding:40px 100px;text-align:center\">
        \t\t\t\tEsta es una notificación para confirmarle su registro en la aplicación Psixport.
        \t\t\t\t<div style=\"padding-top:30px\">
          \t\t\t\t    <img src=\"";
        // line 13
        echo twig_escape_filter($this->env, (isset($context["image_src"]) ? $context["image_src"] : $this->getContext($context, "image_src")), "html", null, true);
        echo "\" alt=\"Psixport\" style=\"display:inline-block\">
        \t\t\t\t</div>
    \t\t\t\t</td>
\t\t\t\t</tr>

\t\t\t\t<tr>
    \t\t\t\t<td style=\"background:#fff;padding:0 40px 40px;text-align:center\">
        \t\t\t\t<div style=\"padding:10px;background:#fafafa\">
            \t\t\t\t<div style=\"padding-top:20px 0 40px\">
            \t\t\t\t\t<p>Datos de registro:</p>
                \t\t\t\t<div style=\"font-size:14px;font-weight:bold;color:#888;letter-spacing:-.5px\">
                    \t\t\t\t";
        // line 24
        echo twig_escape_filter($this->env, (isset($context["email_date"]) ? $context["email_date"] : $this->getContext($context, "email_date")), "html", null, true);
        echo "
                    \t\t\t</div>
            \t\t\t\t</div>
            \t\t\t\t<div style=\"padding:40px 30px; text-align: left;\">
            \t\t\t\t\t<p>Nombre: <span style=\"font-size:14px;font-weight:bold;color:#888;letter-spacing:-.5px\">";
        // line 28
        echo twig_escape_filter($this->env, (isset($context["name"]) ? $context["name"] : $this->getContext($context, "name")), "html", null, true);
        echo "</span></p>
                                <p>Contraseña: <span style=\"font-size:14px;font-weight:bold;color:#888;letter-spacing:-.5px\">";
        // line 29
        echo twig_escape_filter($this->env, (isset($context["password"]) ? $context["password"] : $this->getContext($context, "password")), "html", null, true);
        echo "</span></p>
            \t\t\t\t\t<p>Fecha de nacimiento: <span style=\"font-size:14px;font-weight:bold;color:#888;letter-spacing:-.5px\">";
        // line 30
        echo twig_escape_filter($this->env, (isset($context["birthday"]) ? $context["birthday"] : $this->getContext($context, "birthday")), "html", null, true);
        echo "</span></p>
\t\t\t\t\t\t\t\t<p>Sexo: <span style=\"font-size:14px;font-weight:bold;color:#888;letter-spacing:-.5px\">";
        // line 31
        echo twig_escape_filter($this->env, (isset($context["gender"]) ? $context["gender"] : $this->getContext($context, "gender")), "html", null, true);
        echo "</span></p>
\t\t\t\t\t\t\t\t<p>Deporte: <span style=\"font-size:14px;font-weight:bold;color:#888;letter-spacing:-.5px\">";
        // line 32
        echo twig_escape_filter($this->env, (isset($context["sport"]) ? $context["sport"] : $this->getContext($context, "sport")), "html", null, true);
        echo "</span></p>
                                <p>Club: <span style=\"font-size:14px;font-weight:bold;color:#888;letter-spacing:-.5px\">";
        // line 33
        echo twig_escape_filter($this->env, (isset($context["club"]) ? $context["club"] : $this->getContext($context, "club")), "html", null, true);
        echo "</span></p>
                                <p>Categoría: <span style=\"font-size:14px;font-weight:bold;color:#888;letter-spacing:-.5px\">";
        // line 34
        echo twig_escape_filter($this->env, (isset($context["category"]) ? $context["category"] : $this->getContext($context, "category")), "html", null, true);
        echo "</span></p>
                                <p>Competición: <span style=\"font-size:14px;font-weight:bold;color:#888;letter-spacing:-.5px\">";
        // line 35
        echo twig_escape_filter($this->env, (isset($context["competition"]) ? $context["competition"] : $this->getContext($context, "competition")), "html", null, true);
        echo "</span></p>
                                <p>Fecha de inicio: <span style=\"font-size:14px;font-weight:bold;color:#888;letter-spacing:-.5px\">";
        // line 36
        echo twig_escape_filter($this->env, (isset($context["app_respondent_begin_date"]) ? $context["app_respondent_begin_date"] : $this->getContext($context, "app_respondent_begin_date")), "html", null, true);
        echo "</span></p>
                                <p>Franja horaria: <span style=\"font-size:14px;font-weight:bold;color:#888;letter-spacing:-.5px\">";
        // line 37
        echo twig_escape_filter($this->env, (isset($context["app_respondent_period"]) ? $context["app_respondent_period"] : $this->getContext($context, "app_respondent_period")), "html", null, true);
        echo "</span></p>
            \t\t\t\t</div>
        \t\t\t\t</div>
    \t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t</tbody>
\t\t</table>
\t</center>
\t<div class=\"yj6qo\"></div>
\t<div class=\"adL\"></div>
</div>";
    }

    public function getTemplateName()
    {
        return "email_notify_registration.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  90 => 37,  86 => 36,  82 => 35,  78 => 34,  74 => 33,  70 => 32,  66 => 31,  62 => 30,  58 => 29,  54 => 28,  47 => 24,  33 => 13,  19 => 1,);
    }
}
/* <div style="background:#f4f3f1">*/
/* 	<center style="background:#f4f3f1;padding:30px 0">*/
/* 		<table style="color:#555;font:16px/22px Arial,sans-serif" cellspacing="0" cellpadding="0" border="0" width="600">*/
/* 	    	<tbody>*/
/* 	    		<tr>*/
/*     				<td style="background:#55585d;color:#fff;line-height:1;font-size:18px;font-weight:bold;text-align:center;padding:30px">Confirmación de registro</td>*/
/* 				</tr>*/
/* */
/* 				<tr>*/
/*     				<td style="background:#fff;padding:40px 100px;text-align:center">*/
/*         				Esta es una notificación para confirmarle su registro en la aplicación Psixport.*/
/*         				<div style="padding-top:30px">*/
/*           				    <img src="{{image_src}}" alt="Psixport" style="display:inline-block">*/
/*         				</div>*/
/*     				</td>*/
/* 				</tr>*/
/* */
/* 				<tr>*/
/*     				<td style="background:#fff;padding:0 40px 40px;text-align:center">*/
/*         				<div style="padding:10px;background:#fafafa">*/
/*             				<div style="padding-top:20px 0 40px">*/
/*             					<p>Datos de registro:</p>*/
/*                 				<div style="font-size:14px;font-weight:bold;color:#888;letter-spacing:-.5px">*/
/*                     				{{email_date}}*/
/*                     			</div>*/
/*             				</div>*/
/*             				<div style="padding:40px 30px; text-align: left;">*/
/*             					<p>Nombre: <span style="font-size:14px;font-weight:bold;color:#888;letter-spacing:-.5px">{{name}}</span></p>*/
/*                                 <p>Contraseña: <span style="font-size:14px;font-weight:bold;color:#888;letter-spacing:-.5px">{{password}}</span></p>*/
/*             					<p>Fecha de nacimiento: <span style="font-size:14px;font-weight:bold;color:#888;letter-spacing:-.5px">{{birthday}}</span></p>*/
/* 								<p>Sexo: <span style="font-size:14px;font-weight:bold;color:#888;letter-spacing:-.5px">{{gender}}</span></p>*/
/* 								<p>Deporte: <span style="font-size:14px;font-weight:bold;color:#888;letter-spacing:-.5px">{{sport}}</span></p>*/
/*                                 <p>Club: <span style="font-size:14px;font-weight:bold;color:#888;letter-spacing:-.5px">{{club}}</span></p>*/
/*                                 <p>Categoría: <span style="font-size:14px;font-weight:bold;color:#888;letter-spacing:-.5px">{{category}}</span></p>*/
/*                                 <p>Competición: <span style="font-size:14px;font-weight:bold;color:#888;letter-spacing:-.5px">{{competition}}</span></p>*/
/*                                 <p>Fecha de inicio: <span style="font-size:14px;font-weight:bold;color:#888;letter-spacing:-.5px">{{app_respondent_begin_date}}</span></p>*/
/*                                 <p>Franja horaria: <span style="font-size:14px;font-weight:bold;color:#888;letter-spacing:-.5px">{{app_respondent_period}}</span></p>*/
/*             				</div>*/
/*         				</div>*/
/*     				</td>*/
/* 				</tr>*/
/* 			</tbody>*/
/* 		</table>*/
/* 	</center>*/
/* 	<div class="yj6qo"></div>*/
/* 	<div class="adL"></div>*/
/* </div>*/
