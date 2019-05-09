<?php


namespace Teebb\TuiEditorBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Teebb\TuiEditorBundle\Config\TuiEditorConfigurationInterface;


final class TuiEditorType extends AbstractType
{
    /**
     * @var TuiEditorConfigurationInterface
     */
    private $configuration;

    /**
     * @var string|null
     */
    private $locale;

    public function __construct(TuiEditorConfigurationInterface $configuration, ?string $locale)
    {
        $this->configuration = $configuration;
        $this->locale = $locale;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder->setAttribute('enable', $options['enable']);

        if (!$options['enable']) {
            return;
        }

        $builder->setAttribute('jquery', $options['jquery']);
        $builder->setAttribute('base_path', $options['base_path']);
        $builder->setAttribute('locale', $options['locale']);
        $builder->setAttribute('editor_js_path', $options['editor_js_path']);
        $builder->setAttribute('viewer_js_path', $options['viewer_js_path']);
        $builder->setAttribute('editor_css_path', $options['editor_css_path']);
        $builder->setAttribute('editor_contents_css_path', $options['editor_contents_css_path']);
        $builder->setAttribute('jquery_path', $options['jquery_path']);
        $builder->setAttribute('config', $this->resolveConfig($options));
        $builder->setAttribute('config_name', $options['config_name']);
        $builder->setAttribute('extensions', $options['extensions']);
        $builder->setAttribute('toolbars', $options['toolbars']);
        $builder->setAttribute('dependencies', $options['dependencies']);
    }

    private function resolveConfig(array $options): array
    {
        $config = $options['config'];

        if (null === $options['config_name']) {
            $options['config_name'] = uniqid('teebb', true);
        } else {
            $config = array_merge($this->configuration->getConfig($options['config_name']), $config);
        }

        return $config;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $config = $form->getConfig();
        $view->vars['enable'] = $config->getAttribute('enable');

        if (!$view->vars['enable']) {
            return;
        }

        $view->vars['jquery'] = $config->getAttribute('jquery');
        $view->vars['locale'] = $config->getAttribute('locale');
        $view->vars['base_path'] = $config->getAttribute('base_path');
        $view->vars['editor_js_path'] = $config->getAttribute('editor_js_path');
        $view->vars['viewer_js_path'] = $config->getAttribute('viewer_js_path');
        $view->vars['editor_css_path'] = $config->getAttribute('editor_css_path');
        $view->vars['editor_contents_css_path'] = $config->getAttribute('editor_contents_css_path');
        $view->vars['jquery_path'] = $config->getAttribute('jquery_path');
        $view->vars['config'] = $config->getAttribute('config');
        $view->vars['extensions'] = $config->getAttribute('extensions');
        $view->vars['toolbars'] = $config->getAttribute('toolbars');
        $view->vars['dependencies'] = $config->getAttribute('dependencies');
        $view->vars['config_name'] = $config->getAttribute('config_name');

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'enable' => $this->configuration->isEnable(),
                'jquery' => $this->configuration->isJquery(),
                'locale' => $this->locale,
                'base_path' => $this->configuration->getBasePath(),
                'editor_js_path' => $this->configuration->getEditorJsPath(),
                'viewer_js_path' => $this->configuration->getViewerJsPath(),
                'editor_css_path' => $this->configuration->getEditorCssPath(),
                'editor_contents_css_path' => $this->configuration->getEditorContentsCssPath(),
                'jquery_path' => $this->configuration->getJqueryPath(),
                'config_name' => $this->configuration->getDefaultConfig(),
                'config' => $this->configuration->getConfigs(),
                'extensions' => $this->configuration->getExtensions(),
                'toolbars' => $this->configuration->getToolbars(),
                'dependencies' => $this->configuration->getDependencies(),
            ])
            ->addAllowedTypes('enable', 'bool')
            ->addAllowedTypes('jquery', 'bool')
            ->addAllowedTypes('locale', ['string', 'null'])
            ->addAllowedTypes('config_name', ['string', 'null'])
            ->addAllowedTypes('base_path', 'string')
            ->addAllowedTypes('editor_js_path', 'string')
            ->addAllowedTypes('viewer_js_path', 'string')
            ->addAllowedTypes('editor_css_path', 'string')
            ->addAllowedTypes('editor_contents_css_path', 'string')
            ->addAllowedTypes('jquery_path', 'string')
            ->addAllowedTypes('config', 'array')
            ->addAllowedTypes('extensions', 'array')
            ->addAllowedTypes('dependencies', 'array')
            ->setNormalizer('base_path', function (Options $options, $value) {
                if ('/' !== substr($value, -1)) {
                    $value .= '/';
                }

                return $value;
            });
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'tuieditor';
    }
}
