<?php


namespace Teebb\TuiEditorBundle\Renderer;


use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;


final class TuiEditorRenderer implements TuiEditorRendererInterface
{
    /**
     * @var array
     */
    private $options;
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Packages
     */
    private $assetsPackages;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var null|string
     */
    private $locale;

    /**
     * TuiEditorRenderer constructor.
     * @param array $options The TeebbTuiEditorBundle all the configs is here.
     * @param RouterInterface $router
     * @param Packages $packages
     * @param RequestStack $requestStack
     * @param Environment $twig
     */
    public function __construct(
        array $options,
        RouterInterface $router,
        Packages $packages,
        RequestStack $requestStack,
        Environment $twig,
        ?string $locale
    )
    {
        $this->options = $options;
        $this->router = $router;
        $this->assetsPackages = $packages;
        $this->twig = $twig;
        $this->requestStack = $requestStack;
        $this->locale = $locale;
    }

    public function renderBasePath(string $basePath): string
    {
        return $this->fixPath($basePath);
    }

    public function renderEditorJsPath(string $editorJsPath = null): string
    {
        if ($editorJsPath === null) {
            return $this->fixPath($this->options['editor_js_path']);
        }
        return $this->fixPath($editorJsPath);
    }

    public function renderJqueryPath(string $jqueryPath = null): string
    {
        if ($jqueryPath === null) {
            return $this->fixPath($this->options['jquery_path']);
        }
        return $this->fixPath($jqueryPath);
    }

    public function renderEditorCssPath(string $editorCssPath = null): string
    {
        if ($editorCssPath === null) {
            return $this->fixPath($this->options['editor_css_path']);
        }
        return $this->fixPath($editorCssPath);
    }

    public function renderEditorContentsCssPath(string $editorContentsCssPath = null): string
    {
        if ($editorContentsCssPath === null) {
            return $this->fixPath($this->options['editor_contents_css_path']);
        }
        return $this->fixPath($editorContentsCssPath);
    }

    public function renderDependencies(array $dependencies = null): string
    {
        if ($dependencies === null) {
            $dependencies = $this->options['dependencies'];
        }

        $dependenciesJsHtml = "";
        $dependenciesCssHtml = "";

        if ($this->options['jquery']) {
            $dependenciesJsHtml .= sprintf('<script src="%s"></script>', $this->fixPath($this->options['jquery_path']));
        }
        foreach ($dependencies as $dependency) {
            if ($dependency['js_path'] !== null) {
                $dependenciesJsHtml .= sprintf('<script src="%s"></script>', $this->fixPath($dependency['js_path']));
            }
            if ($dependency['css_path'] !== null) {
                $dependenciesCssHtml .= sprintf('<link rel="stylesheet" href="%s">', $this->fixPath($dependency['css_path']));
            }
        }
        return $dependenciesJsHtml . $dependenciesCssHtml;
    }

    public function renderViewer(string $id, string $content, string $viewerJsPath = null): string
    {

        if (null === $viewerJsPath) {
            $viewerJsPath = $this->options['viewer_js_path'];
        }
        $extensions = $this->options['configs'][$this->options['default_config']]['exts'];

        $viewerJsCode = sprintf('<script src="%s"></script>', $this->fixPath($viewerJsPath));
        $viewerCssCode = sprintf('<link rel="stylesheet" href="%s" />', $this->fixPath($this->options['editor_contents_css_path']));

        $extsJsHtml = "";
        $extsCssHtml = "";

        if (null !== $extensions) {
            foreach ($extensions as $extKey => $extValue) {
                switch ($extValue) {
                    case 'colorSyntax':
                        $extsJsHtml .= sprintf('<script src="%s"></script>', $this->fixPath($this->options['extensions']['colorSyntax']['tui_color_picker_js_path']));
                        $extsJsHtml .= sprintf('<script src="%s"></script>', $this->fixPath($this->options['extensions']['colorSyntax']['extColorSyntax_js_path']));
                        $extsCssHtml .= sprintf('<link rel="stylesheet" href="%s" />', $this->fixPath($this->options['extensions']['colorSyntax']['tui_color_picker_css_path']));
                        break;
                    case 'uml':
                        $extsJsHtml .= sprintf('<script src="%s"></script>', $this->fixPath($this->options['extensions']['uml']['plantuml_encoder_js_path']));
                        $extsJsHtml .= sprintf('<script src="%s"></script>', $this->fixPath($this->options['extensions']['uml']['extUML_js_path']));
                        break;
                    case 'chart':
                        $extsJsHtml .= sprintf('<script src="%s"></script>', $this->fixPath($this->options['extensions']['chart']['raphael_js_path']));
                        $extsJsHtml .= sprintf('<script src="%s"></script>', $this->fixPath($this->options['extensions']['chart']['tui_chart_js_path']));
                        $extsJsHtml .= sprintf('<script src="%s"></script>', $this->fixPath($this->options['extensions']['chart']['extChart_js_path']));
                        $extsCssHtml .= sprintf('<link rel="stylesheet" href="%s" />', $this->fixPath($this->options['extensions']['chart']['tui_chart_css_path']));
                        break;
                    case 'table':
                        $extsJsHtml .= sprintf('<script src="%s"></script>', $this->fixPath($this->options['extensions']['table']['extTable_js_path']));
                        break;
                }
            }
        }

        $viewerJsScript = sprintf(
            '<script class="code-js">' .
            'var viewer = new tui.Editor({' .
            'el: document.querySelector("#%s"),' .
            'height: "%s",' .
            'initialValue: "%s",' .
            'exts: [%s]' .
            '});' .
            '</script>',
            $id,
            "300px",
            $content,
            $this->fixArrayToJs($extensions, "scrollSync")
        );

        return $viewerJsCode . $viewerCssCode . $extsJsHtml . $extsCssHtml . $viewerJsScript;
    }

    private function fixArrayToJs(array $array, ?string $exclude = null): string
    {
        if (null == $array) {
            return "";
        }
        $jsArray = "";
        foreach ($array as $key => $item) {
            if ($item == $exclude) continue;
            if ($key !== sizeof($array) - 1) {
                $jsArray .= "'" . $item . "',";
            } else {
                $jsArray .= "'" . $item . "'";
            }
        }

        return $jsArray;
    }

    private function fixContentToJs(string $content): string
    {
        if (null == $content) {
            return "";
        }
        $rows = explode("\r\n", $content);

        $jsArray = "";
        foreach ($rows as $index => $row) {
            if ($index !== sizeof($rows) - 1) {
                $jsArray .= "'" . $row . "',";
            } else {
                $jsArray .= "'" . $row . "'";
            }
        }
        return $jsArray;
    }

    private function fixPath(string $path): string
    {
        if (null === $this->assetsPackages) {
            return $path;
        }

        $url = $this->assetsPackages->getUrl($path);

        if ('/' === substr($path, -1) && false !== ($position = strpos($url, '?'))) {
            $url = substr($url, 0, (int)$position);
        }

        return $url;
    }

    private function fixConfigLanguage(array $config): array
    {
        if (!isset($config['locale']) && null !== ($language = $this->getLanguage())) {
            $config['locale'] = $language;
        }

        return $config;
    }

    private function getLanguage(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null !== $request) {
            $language = $request->getLocale();
            $language = substr($language, 0, 2) . strtoupper(substr(str_replace('-', '_', $language), 2));

            return $language;
        }

        return $this->locale;
    }

    public function renderEditor(string $id, array $config, string $content = null): string
    {
        $config = $this->fixConfigLanguage($config);
        $extensions = $config['exts'];

        $editorJsCode = sprintf('<script src="%s"></script>', $this->fixPath($this->options['editor_js_path']));
        $editorCssCode = sprintf('<link rel="stylesheet" href="%s" />', $this->fixPath($this->options['editor_css_path']));
        $editorContentsCssCode = sprintf('<link rel="stylesheet" href="%s" />', $this->fixPath($this->options['editor_contents_css_path']));

        $extsJsHtml = "";
        $extsCssHtml = "";

        if (null !== $extensions) {
            foreach ($extensions as $extKey => $extValue) {
                switch ($extValue) {
                    case 'scrollSync':
                        $extsJsHtml .= sprintf('<script src="%s"></script>', $this->fixPath($this->options['extensions']['scrollSync']['extScrollSync_js_path']));
                        break;
                    case 'colorSyntax':
                        $extsJsHtml .= sprintf('<script src="%s"></script>', $this->fixPath($this->options['extensions']['colorSyntax']['tui_color_picker_js_path']));
                        $extsJsHtml .= sprintf('<script src="%s"></script>', $this->fixPath($this->options['extensions']['colorSyntax']['extColorSyntax_js_path']));
                        $extsCssHtml .= sprintf('<link rel="stylesheet" href="%s" />', $this->fixPath($this->options['extensions']['colorSyntax']['tui_color_picker_css_path']));
                        break;
                    case 'uml':
                        $extsJsHtml .= sprintf('<script src="%s"></script>', $this->fixPath($this->options['extensions']['uml']['plantuml_encoder_js_path']));
                        $extsJsHtml .= sprintf('<script src="%s"></script>', $this->fixPath($this->options['extensions']['uml']['extUML_js_path']));
                        break;
                    case 'chart':
                        $extsJsHtml .= sprintf('<script src="%s"></script>', $this->fixPath($this->options['extensions']['chart']['raphael_js_path']));
                        $extsJsHtml .= sprintf('<script src="%s"></script>', $this->fixPath($this->options['extensions']['chart']['tui_chart_js_path']));
                        $extsJsHtml .= sprintf('<script src="%s"></script>', $this->fixPath($this->options['extensions']['chart']['extChart_js_path']));
                        $extsCssHtml .= sprintf('<link rel="stylesheet" href="%s" />', $this->fixPath($this->options['extensions']['chart']['tui_chart_css_path']));
                        break;
                    case 'table':
                        $extsJsHtml .= sprintf('<script src="%s"></script>', $this->fixPath($this->options['extensions']['table']['extTable_js_path']));
                        break;
                }
            }
        }

        $editorJsScript = sprintf(
            '<script class="code-js">' .
            'var content = [%s].join("\n");' .
            'var editor = new tui.Editor({' .
            'el: document.querySelector("#%s"),' .
            'initialEditType: "%s",' .
            'previewStyle: "%s",' .
            'height: "%s",' .
            'language: "%s",' .
            'initialValue: content,' .
            'exts: [%s]' .
            '});' .
            '</script>',
            $this->fixContentToJs($content),
            $id,
            array_key_exists('initialEditType', $config) ? $config['initialEditType'] : "markdown",
            array_key_exists('previewStyle', $config) ? $config['previewStyle'] : "vertical",
            array_key_exists('height', $config) ? $config['height'] : "300px",
            array_key_exists('language', $config) ? $config['language'] : $config['locale'],
            $this->fixArrayToJs($extensions)
        );

        return $editorJsCode . $editorCssCode . $editorContentsCssCode . $extsJsHtml . $extsCssHtml . $editorJsScript;
    }
}
