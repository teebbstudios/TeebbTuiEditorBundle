<?php


namespace Teebb\TuiEditorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        if (\method_exists(TreeBuilder::class, 'getRootNode')) {
            $treeBuilder = new TreeBuilder('teebb_tui_editor');
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('teebb_tui_editor');
        }

        $rootNode
            ->children()
                ->booleanNode('enable')->defaultTrue()->end()
                ->booleanNode('jquery')->defaultTrue()->info("If you want use jquery.js set true.")->end()
                ->scalarNode('base_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/')->end()
                ->scalarNode('editor_js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/tui-editor/dist/tui-editor-Editor.min.js')->end()
                ->scalarNode('viewer_js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/tui-editor/dist/tui-editor-Viewer.min.js')->end()
                ->scalarNode('editor_css_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/tui-editor/dist/tui-editor.min.css')->end()
                ->scalarNode('editor_contents_css_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/tui-editor/dist/tui-editor-contents.min.css')->end()
                ->scalarNode('jquery_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/lib/jquery/dist/jquery.min.js')->end()
                ->scalarNode('default_config')->defaultValue(null)->end()
                ->append($this->createExtensions())
                ->append($this->createDependencies())
                ->append($this->createConfigsNode())
            ->end();

        return $treeBuilder;
    }

    private function createExtensions()
    {
        return $this->createNode('extensions')
            ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('scrollSync')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('extScrollSync_js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/tui-editor/dist/tui-editor-extScrollSync.min.js')->end()
                            ->scalarNode('css_path')->defaultValue(null)->end()
                        ->end()
                    ->end()

                    ->arrayNode('colorSyntax')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('tui_color_picker_js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/lib/tui-color-picker/dist/tui-color-picker.min.js')->end()
                            ->scalarNode('extColorSyntax_js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/tui-editor/dist/tui-editor-extColorSyntax.min.js')->end()
                            ->scalarNode('tui_color_picker_css_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/lib/tui-color-picker/dist/tui-color-picker.min.css')->end()
                        ->end()
                    ->end()

                    ->arrayNode('uml')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('plantuml_encoder_js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/lib/plantuml-encoder/dist/plantuml-encoder.min.js')->end()
                            ->scalarNode('extUML_js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/tui-editor/dist/tui-editor-extUML.min.js')->end()
                            ->scalarNode('css_path')->defaultValue(null)->end()
                        ->end()
                    ->end()

                    ->arrayNode('chart')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('raphael_js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/lib/raphael/raphael.min.js')->end()
                            ->scalarNode('tui_chart_js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/lib/tui-chart/dist/tui-chart.min.js')->end()
                            ->scalarNode('extChart_js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/tui-editor/dist/tui-editor-extChart.min.js')->end()
                            ->scalarNode('tui_chart_css_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/lib/tui-chart/dist/tui-chart.min.css')->end()
                        ->end()
                    ->end()

                    ->arrayNode('table')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('extTable_js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/tui-editor/dist/tui-editor-extTable.min.js')->end()
                            ->scalarNode('css_path')->defaultValue(null)->end()
                        ->end()
                    ->end()

                    ->arrayNode('mark')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('js_path')->defaultValue(null)->end()
                            ->scalarNode('css_path')->defaultValue(null)->end()
                        ->end()
                    ->end()



                ->end();
    }

    private function createDependencies()
    {
        return $this->createNode('dependencies')
            ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('markdown-it')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/lib/markdown-it/dist/markdown-it.min.js')->end()
                            ->scalarNode('css_path')->defaultValue(null)->end()
                        ->end()
                    ->end()

                    ->arrayNode('tui-code-snippet')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/lib/tui-code-snippet/dist/tui-code-snippet.min.js')->end()
                            ->scalarNode('css_path')->defaultValue(null)->end()
                        ->end()
                    ->end()
                    ->arrayNode('codemirror')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/lib/codemirror/lib/codemirror.js')->end()
                            ->scalarNode('css_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/lib/codemirror/lib/codemirror.css')->end()
                        ->end()
                    ->end()
                    ->arrayNode('highlight')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/lib/highlight/highlight.pack.js')->end()
                            ->scalarNode('css_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/lib/highlight/styles/github.css')->end()
                        ->end()
                    ->end()
                    ->arrayNode('squire-rte')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/lib/squire-rte/build/squire-raw.js')->end()
                            ->scalarNode('css_path')->defaultValue(null)->end()
                        ->end()
                    ->end()

                    ->arrayNode('to-mark')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/lib/to-mark/dist/to-mark.min.js')->end()
                            ->scalarNode('css_path')->defaultValue(null)->end()
                        ->end()
                    ->end()

                ->end();
    }

    private function createConfigsNode(): ArrayNodeDefinition
    {
        return $this->createPrototypeNode('configs')
            ->arrayPrototype()
                ->normalizeKeys(false)
                ->useAttributeAsKey('name')
                ->variablePrototype()->end()
            ->end();
    }

    private function createPrototypeNode(string $name): ArrayNodeDefinition
    {
        return $this->createNode($name)
            ->normalizeKeys(false)
            ->useAttributeAsKey('name');
    }

    private function createNode(string $name): ArrayNodeDefinition
    {
        if (\method_exists(TreeBuilder::class, 'getRootNode')) {
            $treeBuilder = new TreeBuilder($name);
            $node = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $treeBuilder = new TreeBuilder();
            $node = $treeBuilder->root($name);
        }

        \assert($node instanceof ArrayNodeDefinition);

        return $node;
    }
}
