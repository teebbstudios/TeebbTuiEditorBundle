<?php

namespace Teebb\TuiEditorBundle\Config;

use Teebb\TuiEditorBundle\Exception\ConfigException;

final class TuiEditorConfiguration implements TuiEditorConfigurationInterface
{

    /**
     * @var boolean
     */
    private $enable;

    /**
     * @var boolean
     */
    private $toHtml;

    /**
     * @var boolean
     */
    private $jquery;

    /**
     * @var string
     */
    private $basePath;

    /**
     * @var string
     */
    private $editorJsPath;

    /**
     * @var string
     */
    private $viewerJsPath;

    /**
     * @var string
     */
    private $editorCssPath;

    /**
     * @var string
     */
    private $editorContentsCssPath;

    /**
     * @var string
     */
    private $jqueryPath;

    /**
     * @var string|null
     */
    private $defaultConfig;

    /**
     * @var array
     */
    private $configs;

    /**
     * @var array
     */
    private $extensions;

    /**
     * @var array
     */
    private $dependencies;

    public function __construct(array $config)
    {
        if ($config['enable']) {
            $config = $this->resolveConfigs($config);
        }

        $this->enable = $config['enable'];
        $this->jquery = $config['jquery'];
        $this->basePath = $config['base_path'];
        $this->editorJsPath = $config['editor_js_path'];
        $this->viewerJsPath = $config['viewer_js_path'];
        $this->editorCssPath = $config['editor_css_path'];
        $this->editorContentsCssPath = $config['editor_contents_css_path'];
        $this->jqueryPath = $config['jquery_path'];
        $this->defaultConfig = $config['default_config'];
        $this->configs = $config['configs'];
        $this->extensions = $config['extensions'];
        $this->dependencies = $config['dependencies'];

    }

    private function resolveConfigs(array $config): array
    {
        if (empty($config['configs'])) {
            return $config;
        }

        if (!isset($config['default_config']) && !empty($config['configs'])) {
            reset($config['configs']);
            $config['default_config'] = key($config['configs']);
        }

        if (isset($config['default_config']) && !isset($config['configs'][$config['default_config']])) {
            throw ConfigException::invalidDefaultConfig($config['default_config']);
        }

        return $config;
    }

    public function isEnable(): bool
    {
        return $this->enable;
    }

    public function isToHtml(): bool
    {
        return $this->toHtml;
    }

    public function isJquery(): bool
    {
        return $this->jquery;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function getEditorJsPath(): string
    {
        return $this->editorJsPath;
    }

    public function getViewerJsPath(): string
    {
        return $this->viewerJsPath;
    }

    public function getEditorCssPath(): string
    {
        return $this->editorCssPath;
    }

    public function getEditorContentsCssPath(): string
    {
        return $this->editorContentsCssPath;
    }


    public function getJqueryPath(): string
    {
        return $this->jqueryPath;
    }

    public function getDefaultConfig(): ?string
    {
        return $this->defaultConfig;
    }

    public function getConfigs(): array
    {
        return $this->configs;
    }

    public function getExtensions(): array
    {
        return $this->extensions;
    }

    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function getConfig(string $name): array
    {
        if (!isset($this->configs[$name])) {
            throw ConfigException::configDoesNotExist($name);
        }

        return $this->configs[$name];
    }
}
