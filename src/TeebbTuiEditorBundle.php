<?php

namespace Teebb\TuiEditorBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Teebb\TuiEditorBundle\DependencyInjection\Compile\TwigFormThemeCompilePass;

class TeebbTuiEditorBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TwigFormThemeCompilePass());

        parent::build($container); // TODO: Change the autogenerated stub
    }

}