# TeebbTuiEditorBundle
This bundle integration tui.editor for your symfony project. The code for this bundle was modified from [FOSCKEditorBundle](https://github.com/FriendsOfSymfony/FOSCKEditorBundle).
Thanks FOSCKEditorBundle author:[Eric Geleon](geloen.eric@gmail.com) and [FriendsOfSymfony Community](https://github.com/FriendsOfSymfony/FOSCKEditorBundle/graphs/contributors) , your code is cool. Thanks MIT License.

Installation
============

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require teebbstudios/tuieditor-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require teebbstudios/tuieditor-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...
            new Teebb\TuiEditorBundle\TeebbTuiEditorBundle(),
        ];

        // ...
    }

    // ...
}
```

### Step 3: Download the Bundle resources

Download the latest [tui.editor-bundles](https://github.com/teebbstudios/tui.editor-bundles) in your project. 

```console 
$ php bin/console tuieditor:install
```

This will download the tui.editor all resources to TeebbTuiEditorBundle `src/Resources/public` folder. Then:


```console
$ php bin/console assets:install --symlink
```
### Step 4: Config the Bundle

You can add a config file in `config/packages` folder.（Just a simple config, But you can use the following configuration completely）:
```yaml
#config/packages/teebb_tuieditor.yaml
teebb_tui_editor:
    #enable: true                           # Whether to enable tui.editor.
    #jquery: true                           # Whether to enable jquery in dependencies.
    #jquery_path: ~                         # Custom jquery path.
    #editor_js_path: ~                      # Custom tui.editor js path.
    # ...                                   # more config options, you can see: bin/console debug:config teebb_tui_editor 
    
    default_config: basic_config

    configs:
        basic_config:
            to_html: false                  # Save to database use html syntax?
            #previewStyle: 'vertical'       # Markdown editor's preview style (tab, vertical)
            #height: '400px'                # Editor's height style value. Height is applied as border-box ex) '300px', '100%', 'auto'
            #initialEditType: 'markdown'    # Initial editor type (markdown, wysiwyg)
            exts:                           # exts must defined as array
                - scrollSync
                - colorSyntax
                - uml
                - chart
                - mark
                - table


twig:
    form_themes:
        - '@TeebbTuiEditor/Form/tuieditor_widget.html.twig'

```

### Step 5: Use the Bundle

Add the tui.editor dependencies in your page top. For example:

```twig
{{ tuieditor_dependencies() }}
```
This will add the tui.editor dependencies JS and CSS libs like:

```html

<script src="/bundles/teebbtuieditor/tui.editor-bundles/lib/jquery/dist/jquery.min.js"></script>
<script src="/bundles/teebbtuieditor/tui.editor-bundles/lib/markdown-it/dist/markdown-it.min.js"></script>
<script src="/bundles/teebbtuieditor/tui.editor-bundles/lib/tui-code-snippet/dist/tui-code-snippet.min.js"></script>
<script src="/bundles/teebbtuieditor/tui.editor-bundles/lib/codemirror/lib/codemirror.js"></script>
<script src="/bundles/teebbtuieditor/tui.editor-bundles/lib/highlight/highlight.pack.js"></script>
<script src="/bundles/teebbtuieditor/tui.editor-bundles/lib/squire-rte/build/squire-raw.js"></script>
<script src="/bundles/teebbtuieditor/tui.editor-bundles/lib/to-mark/dist/to-mark.min.js"></script>
<link rel="stylesheet" href="/bundles/teebbtuieditor/tui.editor-bundles/lib/codemirror/lib/codemirror.css">
<link rel="stylesheet" href="/bundles/teebbtuieditor/tui.editor-bundles/lib/highlight/styles/github.css">

```

Second, use the `TuiEditorType` in your form field:

```php
class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ...
            ->add('body', TuiEditorType::class)
        ;
    }

    // ...
} 
```

### Step 6: Render Markdown syntax content

If you were saved markdown syntax in the database. Then you can use the twig function `tuieditor_viewer_widget` to render the markdown syntax content. 
The first parameter id:  div DOM id.
The second parameter content: twig variable, the markdown syntax content.

Tips: Don't forget render the dependencies in the page top！

```twig
<div id="id"></div>
{{ tuieditor_viewer_widget("id", content) }}
```

### Step 7: Done!
Yeah! Good Job! The tui.editor will use in your page. Now you can use your inspiration to create.