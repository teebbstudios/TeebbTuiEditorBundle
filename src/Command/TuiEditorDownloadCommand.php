<?php

namespace Teebb\TuiEditorBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Teebb\TuiEditorBundle\Installer\TuiEditorInstaller;

class TuiEditorDownloadCommand extends Command
{
    protected static $defaultName = 'tuieditor:install';

    private $installer;

    public function __construct(TuiEditorInstaller $installer)
    {
        parent::__construct();

        $this->installer = $installer;
    }

    protected function configure()
    {
        $this
            ->setDescription('Download the lastest tui.editor with all the required libraries.')
            ->addArgument('path', InputArgument::OPTIONAL, 'Where to install tui.editor')
            ->addOption('clear', null, InputOption::VALUE_OPTIONAL, 'How to clear previous tui.editor installation (drop, keep or skip)')
            ->addOption('no-progress-bar', 'nobar', InputOption::VALUE_NONE,'Hide the progress bars?')
            ->setHelp(
<<<'EOF'
The <info>%command.name%</info> command install tui.editor in your application:

  <info>php %command.full_name%</info>
  
You can install it at a specific path (absolute):

  <info>php %command.full_name% path</info>
  
If there is a previous tui.editor installation detected, 
you can control how it should be handled in non-interactive mode:

  <info>php %command.full_name% --clear=drop</info>
  <info>php %command.full_name% --clear=keep</info>
  <info>php %command.full_name% --clear=skip</info>

EOF
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->title($output);

        $options = $this->createOptions($input,$output);

        $success = $this->installer->install($options);

        if ($success) {
            $this->success('tui.editor has been successfully installed...', $output);
        } else {
            $this->info('tui.editor installation has been skipped...', $output);
        }

    }

    private function createOptions(InputInterface $input, OutputInterface $output): array
    {
        $options = ['notifier' => $this->createNotifier($input, $output)];

        if ($input->hasArgument('path')) {
            $options['path'] = $input->getArgument('path');
        }

        if ($input->hasOption('clear')) {
            $options['clear'] = $input->getOption('clear');
        }

        return array_filter($options);
    }

    private function createNotifier(InputInterface $input, OutputInterface $output): \Closure
    {
        $barOutput = $input->getOption('no-progress-bar') ? new NullOutput() : $output;

        $clear = new ProgressBar($barOutput);
        $download = new ProgressBar($barOutput);
        $extract = new ProgressBar($barOutput);

        return function ($type, $data) use ($input, $output, $barOutput, $clear, $download, $extract) {
            switch ($type) {
                case TuiEditorInstaller::NOTIFY_CLEAR:

                    $result = $this->choice(
                        [
                            sprintf('tui.editor is already installed in "%s"...', $data),
                            '',
                            'What do you want to do?',
                        ],
                        $choices = [
                            TuiEditorInstaller::CLEAR_DROP => 'Drop the directory & reinstall tui.editor',
                            TuiEditorInstaller::CLEAR_KEEP => 'Keep the directory & reinstall tui.editor by overriding files',
                            TuiEditorInstaller::CLEAR_SKIP => 'Skip installation',
                        ],
                        TuiEditorInstaller::CLEAR_DROP,
                        $input,
                        $output
                    );

                    if (false !== ($key = array_search($result, $choices, true))) {
                        $result = $key;
                    }

                    if (TuiEditorInstaller::CLEAR_DROP === $result) {
                        $this->comment(sprintf('Dropping tui.editor-bundles from "%s"', $data), $output);
                    }

                    return $result;

                case TuiEditorInstaller::NOTIFY_CLEAR_ARCHIVE:
                    $this->comment(sprintf('Dropping tui.editor-bundles ZIP archive "%s"', $data), $output);

                    break;

                case TuiEditorInstaller::NOTIFY_CLEAR_COMPLETE:
                    $this->finishProgressBar($clear, $barOutput);

                    break;

                case TuiEditorInstaller::NOTIFY_CLEAR_PROGRESS:
                    $clear->advance();

                    break;

                case TuiEditorInstaller::NOTIFY_CLEAR_SIZE:
                    $clear->start($data);

                    break;

                case TuiEditorInstaller::NOTIFY_GET_DOWNLOAD_URL:
                    $this->comment(sprintf('Get tui.editor-bundles ZIP archive download url'), $output);

                    break;

                case TuiEditorInstaller::NOTIFY_DOWNLOAD_URL:
                    $this->comment(sprintf('tui.editor-bundles ZIP archive download url is: "%s" ', $data), $output);

                    break;

                case TuiEditorInstaller::NOTIFY_DOWNLOAD:
                    $this->comment(sprintf('Downloading tui.editor-bundles ZIP archive from "%s"',  $data), $output);

                    break;

                case TuiEditorInstaller::NOTIFY_DOWNLOAD_COMPLETE:
                    $this->finishProgressBar($download, $barOutput);

                    break;

                case TuiEditorInstaller::NOTIFY_DOWNLOAD_PROGRESS:
                    $download->setProgress($data);

                    break;

                case TuiEditorInstaller::NOTIFY_DOWNLOAD_SIZE:
                    $download->start($data);

                    break;

                case TuiEditorInstaller::NOTIFY_EXTRACT:
                    $this->comment(sprintf('Extracting tui.editor-bundles ZIP archive to "%s"', $data), $output);

                    break;

                case TuiEditorInstaller::NOTIFY_EXTRACT_COMPLETE:
                    $this->finishProgressBar($extract, $barOutput);

                    break;

                case TuiEditorInstaller::NOTIFY_EXTRACT_PROGRESS:
                    $extract->advance();

                    break;

                case TuiEditorInstaller::NOTIFY_EXTRACT_SIZE:
                    $extract->start($data);

                    break;
            }
            return null;
        };
    }

    private function title(OutputInterface $output): void
    {
        $output->writeln(
            [
                '----------------------',
                '| TuiEditor Download |',
                '----------------------',
                '',
            ]
        );
    }

    /**
     * @param array $question
     * @param array $choices
     * @param string $default
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return string|null
     */
    private function choice(
        array $question,
        array $choices,
        string $default,
        InputInterface $input,
        OutputInterface $output
    ): ?string {
        $helper = new QuestionHelper();

        if (is_array($question)) {
            $question = implode("\n", $question);
        }

        $result = $helper->ask(
            $input,
            $output,
            new ChoiceQuestion($question, $choices, $default)
        );

        $output->writeln('');

        return $result;
    }

    private function finishProgressBar(ProgressBar $progress, OutputInterface $output): void
    {
        $progress->finish();
        $output->writeln(['', '']);
    }

    private function block(
        string $message,
        OutputInterface $output,
        string $background = null,
        string $font = null
    ): void {
        $options = [];

        if (null !== $background) {
            $options[] = 'bg='.$background;
        }

        if (null !== $font) {
            $options[] = 'fg='.$font;
        }

        $pattern = ' %s ';

        if (!empty($options)) {
            $pattern = '<'.implode(';', $options).'>'.$pattern.'</>';
        }

        $output->writeln($block = sprintf($pattern, str_repeat(' ', strlen($message))));
        $output->writeln(sprintf($pattern, $message));
        $output->writeln($block);
    }

    private function comment(string $message, OutputInterface $output): void
    {
        $output->writeln(' // '.$message);
        $output->writeln('');
    }

    private function success(string $message, OutputInterface $output): void
    {
        $this->block('[OK] - '.$message, $output, 'green', 'black');
    }

    private function info(string $message, OutputInterface $output): void
    {
        $this->block('[INFO] - '.$message, $output, 'yellow', 'black');
    }

    private function warn(string $message, OutputInterface $output): void
    {
        $this->block('[WARNNING] - '.$message, $output, 'red', 'black');
    }
}
