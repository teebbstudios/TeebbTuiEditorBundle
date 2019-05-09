<?php


namespace Teebb\TuiEditorBundle\Installer;


use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Teebb\TuiEditorBundle\Exception\BadProxyUrlException;

final class TuiEditorInstaller
{

    const CLEAR_DROP = 'drop';

    const CLEAR_KEEP = 'keep';

    const CLEAR_SKIP = 'skip';

    const NOTIFY_CLEAR = 'clear';

    const NOTIFY_CLEAR_ARCHIVE = 'clear-archive';

    const NOTIFY_CLEAR_COMPLETE = 'clear-complete';

    const NOTIFY_CLEAR_PROGRESS = 'clear-progress';

    const NOTIFY_CLEAR_QUESTION = 'clear-question';

    const NOTIFY_CLEAR_SIZE = 'clear-size';

    const NOTIFY_DOWNLOAD = 'download';

    const NOTIFY_GET_DOWNLOAD_URL = 'get-download-url';

    const NOTIFY_DOWNLOAD_URL = 'download-url';

    const NOTIFY_DOWNLOAD_COMPLETE = 'download-complete';

    const NOTIFY_DOWNLOAD_PROGRESS = 'download-progress';

    const NOTIFY_DOWNLOAD_SIZE = 'download-size';

    const NOTIFY_EXTRACT = 'extract';

    const NOTIFY_EXTRACT_COMPLETE = 'extract-complete';

    const NOTIFY_EXTRACT_PROGRESS = 'extract-progress';

    const NOTIFY_EXTRACT_SIZE = 'extract-size';

    /**
     *  Get the latest tui.editor version
     *  %s is github repository, example: nhn/tui.editor
     */
    private static $archiveApi = 'https://api.github.com/repos/%s/releases/latest';

    /**
     *  Generate the archive download url;
     *  the first %s is github repository Name, example: nhn/tui.editor
     *  the second %s is the lastest repo version, example: v1.4.0
     */
    private static $archive = 'https://github.com/%s/archive/%s.zip';

    private $repository = 'teebbstudios/tui.editor-bundles';

    private $version;

    /**
     * @var OptionsResolver
     */
    private $resolver;


    public function __construct(array $options = [])
    {
        $this->resolver = (new OptionsResolver())
            ->setDefaults(array_merge([
                'clear' => null,
                'notifier' => null,
                'path' => dirname(__DIR__) . '/Resources/public',
            ], $options))
            ->setAllowedTypes('notifier', ['null', 'callable'])
            ->setAllowedTypes('path', 'string')
            ->setAllowedValues('clear', [self::CLEAR_DROP, self::CLEAR_KEEP, self::CLEAR_SKIP, null])
            ->setNormalizer('path', function (Options $options, $path) {
                return rtrim($path, '/');
            });
    }

    public function install(array $options = []): bool
    {
        $options = $this->resolver->resolve($options);

        if (self::CLEAR_SKIP === $this->clear($options)) {
            return false;
        }

        $path = $this->download($options);

        $this->extract($path, $options);

        return true;
    }

    private function clear(array $options): string
    {

        if (!file_exists($options['path'].'/tui.editor-bundles/tui-editor/dist/tui-editor-Editor.js')) {
            return self::CLEAR_DROP;
        }

        if (null === $options['clear'] && null !== $options['notifier']) {
            $options['clear'] = $this->notify(self::NOTIFY_CLEAR, $options['path'], $options['notifier']);
        }

        if (null === $options['clear']) {
            $options['clear'] = self::CLEAR_SKIP;
        }

        if (self::CLEAR_DROP === $options['clear']) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($options['path'], \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            $this->notify(self::NOTIFY_CLEAR_SIZE, iterator_count($files), $options['notifier']);

            foreach ($files as $file) {
                $filePath = $file->getRealPath();
                $this->notify(self::NOTIFY_CLEAR_PROGRESS, $filePath, $options['notifier']);

                if ($dir = $file->isDir()) {
                    $success = @rmdir($filePath);
                } else {
                    $success = @unlink($filePath);
                }

                if (!$success) {
                    throw $this->createException(sprintf(
                        'Unable to remove the %s "%s".',
                        $dir ? 'directory' : 'file',
                        $filePath
                    ));
                }
            }

            $this->notify(self::NOTIFY_CLEAR_COMPLETE, null, $options['notifier']);
        }

        return $options['clear'];
    }

    private function download(array $options): string
    {

        $url = $this->getDownloadUrl($options);

        $this->notify(self::NOTIFY_DOWNLOAD, $url, $options['notifier']);

        $zip = @file_get_contents($url, false, $this->createStreamContext($options['notifier']));

        if (false === $zip) {
            throw $this->createException(sprintf('Unable to download %s ZIP archive from "%s".', $url));
        }

        $path = (string)tempnam(sys_get_temp_dir(), 'tui.editor-bundles.zip');

        if (!@file_put_contents($path, $zip)) {
            throw $this->createException(sprintf('Unable to write tui.editor-bundles ZIP archive to "%s".', $path));
        }

        $this->notify(self::NOTIFY_DOWNLOAD_COMPLETE, $path, $options['notifier']);

        return $path;
    }

    private function getDownloadUrl(array $options): string
    {

        $this->notify(self::NOTIFY_GET_DOWNLOAD_URL, null, $options['notifier']);

        $repoApiUrl = sprintf(self::$archiveApi, $this->repository);

        $apiResponseJson = json_decode(file_get_contents($repoApiUrl, false, $this->createHttpGetContext()), true);

        $this->version = $apiResponseJson['tag_name'];

        $downloadUrl = sprintf(self::$archive, $this->repository, $this->version);

        $this->notify(self::NOTIFY_DOWNLOAD_URL, $downloadUrl, $options['notifier']);

        return $downloadUrl;
    }

    private function createHttpGetContext()
    {
        $options = array(
            'http' => array(
                'method' => "GET",
                'header' => "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.131 Safari/537.36"
            )
        );
        return stream_context_create($options);
    }

    /**
     * @return resource
     */
    private function createStreamContext(callable $notifier = null)
    {
        $context = [];
        $proxy = getenv('https_proxy') ?: getenv('http_proxy');

        if ($proxy) {
            $proxyUrl = parse_url($proxy);

            if (false === $proxyUrl || !isset($proxyUrl['host']) || !isset($proxyUrl['port'])) {
                throw BadProxyUrlException::fromEnvUrl($proxy);
            }

            $context['http'] = [
                'proxy' => 'tcp://' . $proxyUrl['host'] . ':' . $proxyUrl['port'],
                'request_fulluri' => (bool)getenv('https_proxy_request_fulluri') ?:
                    getenv('http_proxy_request_fulluri'),
            ];
        }

        return stream_context_create($context, [
            'notification' => function (
                $code,
                $severity,
                $message,
                $messageCode,
                $transferred,
                $size
            ) use ($notifier) {
                if (null === $notifier) {
                    return;
                }

                switch ($code) {
                    case STREAM_NOTIFY_FILE_SIZE_IS:
                        $this->notify(self::NOTIFY_DOWNLOAD_SIZE, $size, $notifier);

                        break;

                    case STREAM_NOTIFY_PROGRESS:
                        $this->notify(self::NOTIFY_DOWNLOAD_PROGRESS, $transferred, $notifier);

                        break;
                }
            },
        ]);
    }

    private function extract(string $path, array $options): void
    {
        $this->notify(self::NOTIFY_EXTRACT, $options['path'], $options['notifier']);

        $zip = new \ZipArchive();
        $zip->open($path);

        $this->notify(self::NOTIFY_EXTRACT_SIZE, $zip->numFiles, $options['notifier']);

        for ($i = 0; $i < $zip->numFiles; ++$i) {
            $filename = $zip->getNameIndex($i);
            $isDirectory = ('/' === substr($filename, -1, 1));

            if (!$isDirectory) {
                $this->extractFile($filename, $filename, $path, $options);
            }
        }

        $zip->close();

        $originExtractFolderName = $options['path'] . '/tui.editor-bundles-' . substr($this->version, 1);

        rename($originExtractFolderName, $options['path'] . '/tui.editor-bundles');

        $this->notify(self::NOTIFY_EXTRACT_COMPLETE, null, $options['notifier']);

        $this->notify(self::NOTIFY_CLEAR_ARCHIVE, $path, $options['notifier']);

        if (!@unlink($path)) {
            throw $this->createException(sprintf('Unable to remove the tui.editor-bundles ZIP archive "%s".', $path));
        }
    }

    private function extractFile(string $file, string $rewrite, string $origin, array $options): void
    {
        $this->notify(self::NOTIFY_EXTRACT_PROGRESS, $rewrite, $options['notifier']);

        $from = 'zip://' . $origin . '#' . $file;
        $to = $options['path'] . '/' . $rewrite;

        $targetDirectory = dirname($to);

        if (!is_dir($targetDirectory) && !@mkdir($targetDirectory, 0777, true)) {
            throw $this->createException(sprintf('Unable to create the directory "%s".', $targetDirectory));
        }

        if (!@copy($from, $to)) {
            throw $this->createException(sprintf('Unable to extract the file "%s" to "%s".', $file, $to));
        }

    }


    private function notify(string $type, $data = null, callable $notifier = null)
    {
        if (null !== $notifier) {
            return $notifier($type, $data);
        }
    }

    private function createException(string $message): \RuntimeException
    {
        $error = error_get_last();

        if (isset($error['message'])) {
            $message .= sprintf(' (%s)', $error['message']);
        }

        return new \RuntimeException($message);
    }


}