<?php


namespace Teebb\TuiEditorBundle\Config;


use Teebb\TuiEditorBundle\Exception\ConfigException;

interface TuiEditorConfigurationInterface
{
    public function isEnable(): bool;

    public function isToHtml(): bool;

    public function isJquery(): bool;

    public function getBasePath(): string;

    public function getEditorJsPath(): string;

    public function getViewerJsPath(): string;

    public function getEditorCssPath(): string;

    public function getEditorContentsCssPath(): string;

    public function getJqueryPath(): string;

    public function getDefaultConfig(): ?string;

    public function getConfigs(): array;

    public function getExtensions(): array;

    public function getDependencies(): array;

    public function getToolbars(): array;

    /**
     * @throws ConfigException
     */
    public function getConfig(string $name): array;
}
