<?php

namespace Teebb\TuiEditorBundle\Twig;

use Teebb\TuiEditorBundle\Renderer\TuiEditorRendererInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;


final class TuiEditorExtension extends AbstractExtension implements TuiEditorRendererInterface
{
    /**
     * @var TuiEditorRendererInterface
     */
    private $renderer;

    public function __construct(TuiEditorRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function getFunctions(): array
    {
        $options = ['is_safe' => ['html']];

        return [
            new TwigFunction('tuieditor_base_path', [$this, 'renderBasePath'], $options),
            new TwigFunction('tuieditor_jquery_path', [$this, 'renderJqueryPath'], $options),
            new TwigFunction('tuieditor_editor_js_path', [$this, 'renderEditorJsPath'], $options),
            new TwigFunction('tuieditor_viewer_widget', [$this, 'renderViewer'], $options),
            new TwigFunction('tuieditor_editor_widget', [$this, 'renderEditor'], $options),
            new TwigFunction('tuieditor_editor_css_path', [$this, 'renderEditorCssPath'], $options),
            new TwigFunction('tuieditor_editor_contents_csss_path', [$this, 'renderEditorContentsCssPath'], $options),
            new TwigFunction('tuieditor_dependencies', [$this, 'renderDependencies'], $options),
        ];
    }

    public function renderEditorCssPath(string $editorCssPath = null): string
    {
        return $this->renderer->renderEditorCssPath($editorCssPath);
    }

    public function renderEditorContentsCssPath(string $editorContentsCssPath = null): string
    {
        return $this->renderer->renderEditorContentsCssPath($editorContentsCssPath);
    }

    public function renderBasePath(string $basePath): string
    {
        return $this->renderer->renderBasePath($basePath);
    }

    public function renderEditorJsPath(string $editorJsPath = null): string
    {
        return $this->renderer->renderEditorJsPath($editorJsPath);
    }

    public function renderViewer(string $id, string $content, string $viewerJsPath = null): string
    {
        return $this->renderer->renderViewer($id, $content, $viewerJsPath);
    }

    public function renderJqueryPath(string $jqueryPath = null): string
    {
        return $this->renderer->renderJqueryPath($jqueryPath);
    }

    public function renderDependencies(array $dependencies = null): string
    {
        return $this->renderer->renderDependencies($dependencies);
    }

    public function renderEditor(string $id, array $config, string $content = null): string
    {
        return $this->renderer->renderEditor($id, $config, $content);
    }
}
