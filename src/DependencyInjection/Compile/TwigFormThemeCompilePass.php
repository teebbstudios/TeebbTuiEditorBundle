<?php


namespace Teebb\TuiEditorBundle\DependencyInjection\Compile;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TwigFormThemeCompilePass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        //add tuieditor_widget.html.twig to form_theme
        $form_theme_old = $container->getParameter('twig.form.resources');
        $form_theme = array_merge($form_theme_old, ['@TeebbTuiEditor/Form/tuieditor_widget.html.Twig']);

        $container->getDefinition('twig.form.engine')->replaceArgument(0, $form_theme);
    }
}