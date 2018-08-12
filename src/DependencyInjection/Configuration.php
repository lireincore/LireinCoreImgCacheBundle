<?php

namespace LireinCore\ImgCacheBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use LireinCore\Image\ImageHelper;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('imgcache');

        $rootNode
            ->fixXmlConfig('postprocessor', 'postprocessors')
            ->children()
                ->enumNode('driver')->values(ImageHelper::supportedDrivers())->end()
                ->scalarNode('image_class')->end()
                ->scalarNode('srcdir')->end()
                ->scalarNode('destdir')->isRequired()->end()
                ->scalarNode('webdir')->end()
                ->scalarNode('baseurl')->end()
                ->integerNode('jpeg_quality')->min(0)->max(100)->end()
                ->integerNode('png_compression_level')->min(0)->max(9)->end()
                ->integerNode('png_compression_filter')->min(0)->max(9)->end()
                ->append($this->plugNode())
                ->append($this->convertMapNode())
                ->arrayNode('effects_map')
                    ->useAttributeAsKey('name')
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('postprocessors_map')
                    ->useAttributeAsKey('name')
                    ->scalarPrototype()->end()
                ->end()
                ->append($this->postprocessorsNode())
                ->append($this->presetsNode())
            ->end();

        return $treeBuilder;
    }

    protected function presetsNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('presets');

        $node
            ->useAttributeAsKey('name')
            ->arrayPrototype()
                ->fixXmlConfig('effect', 'effects')
                ->fixXmlConfig('postprocessor', 'postprocessors')
                ->children()
                    ->enumNode('driver')->values(ImageHelper::supportedDrivers())->end()
                    ->scalarNode('image_class')->end()
                    ->scalarNode('srcdir')->end()
                    ->scalarNode('destdir')->end()
                    ->scalarNode('webdir')->end()
                    ->scalarNode('baseurl')->end()
                    ->integerNode('jpeg_quality')->min(0)->max(100)->end()
                    ->integerNode('png_compression_level')->min(0)->max(9)->end()
                    ->integerNode('png_compression_filter')->min(0)->max(9)->end()
                    ->append($this->plugNode())
                    ->append($this->convertMapNode())
                    ->append($this->effectsNode())
                    ->append($this->postprocessorsNode())
                ->end()
            ->end();

        return $node;
    }

    protected function plugNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('plug');

        $node
            ->children()
                ->scalarNode('path')->end()
                ->scalarNode('url')->end()
                ->booleanNode('process')->end()
            ->end();

        return $node;
    }

    protected function convertMapNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('convert_map');

        $node
            ->useAttributeAsKey('name')
            ->enumPrototype()->values(ImageHelper::supportedDestinationFormats())->end();

        return $node;
    }

    protected function effectsNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('effects');

        return $this->configureEffectsOrPostprocessorsNode($node);
    }

    protected function postprocessorsNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('postprocessors');

        return $this->configureEffectsOrPostprocessorsNode($node);
    }

    protected function configureEffectsOrPostprocessorsNode(ArrayNodeDefinition $node)
    {
        $node
            ->performNoDeepMerging()
            ->arrayPrototype()
                ->beforeNormalization()
                    ->ifString()
                    ->then(function ($v) { return ['type' => $v]; })
                ->end()
                ->children()
                    ->scalarNode('type')->isRequired()->end()
                    ->arrayNode('params')
                        ->useAttributeAsKey('name')
                        ->variablePrototype()->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}