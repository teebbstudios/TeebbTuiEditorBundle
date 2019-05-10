<?php


namespace Teebb\TuiEditorBundle\Renderer;


interface TuiEditorRendererInterface
{
    public function renderBasePath(string $basePath): string;

    public function renderJqueryPath(string $jqueryPath = null): string;

    public function renderEditorJsPath(string $editorJsPath = null): string;

    public function renderViewer(string $id, string $content, string $viewerJsPath = null): string;

    public function renderEditor(string $id, array $config, string $content = null): string;

    public function renderEditorCssPath(string $editorCssPath = null): string;

    public function renderEditorContentsCssPath(string $editorContentsCssPath = null): string;

    public function renderDependencies(array $dependencies = null): string;

}
