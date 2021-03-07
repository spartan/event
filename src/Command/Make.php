<?php

namespace Spartan\Event\Command;

use Spartan\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Event Command
 *
 * @property string $name
 *
 * @package Spartan\Event
 * @author  Iulian N. <iulian@spartanphp.com>
 * @license LICENSE MIT
 */
class Make extends Command
{
    protected function configure(): void
    {
        $this->withSynopsis('make:event', 'Create an event with attached listeners')
             ->withArgument('name', 'Event name/listeners. Ex: Message.Received:Message.Queue,Message.Log')
             ->withOption('schema', 'Extend json schema event')
             ->withOption('no-suffix', 'Ignore suffix on class names for both event and listener')
             ->withOption('overwrite', 'Overwrite existing files');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        self::loadEnv();

        $cwd = getcwd();
        $app = getenv('APP_NAME');

        $listeners = (array)preg_split('/[:,]/', $this->name);
        $event     = (string)array_shift($listeners);

        /*
         * Event
         */

        $eventNamespace = implode(
            '\\',
            array_slice(explode('.', "{$app}.Event.{$event}"), 0, -1)
        );
        $eventPath      = implode(
            '/',
            array_slice(explode('.', "src.Event.{$event}"), 0, -1)
        );
        $eventName      = array_slice(explode('.', $event), -1, 1)[0];
        if (!$this->isOptionPresent('no-suffix')) {
            $eventName .= 'Event';
        }
        $eventFile = "{$cwd}/{$eventPath}/{$eventName}.php";

        if (file_exists($eventFile) && !$this->isOptionPresent('overwrite')) {
            $this->danger('Event already exists! Run with --overwrite to overwrite');

            return 1;
        } else {
            @mkdir("{$cwd}/{$eventPath}", 0777, true);

            if ($this->isOptionPresent('schema')) {
                $eventPhp = $this->schemaEventClass($eventName, $eventNamespace);
            } else {
                $eventPhp = $this->simpleEventClass($eventName, $eventNamespace);
            }
        }

        /*
         * Listeners
         */

        $listeners = array_filter($listeners);
        foreach ($listeners as &$listener) {
            $listenerNamespace = implode(
                '\\',
                array_slice(explode('.', "{$app}.Listener.{$listener}"), 0, -1)
            );
            $listenerPath      = implode(
                '/',
                array_slice(explode('.', "src.Listener.{$listener}"), 0, -1)
            );
            $listenerName      = array_slice(explode('.', $listener), -1, 1)[0];
            if (!$this->isOptionPresent('no-suffix')) {
                $listenerName .= 'Listener';
            }
            $listenerFile = "{$cwd}/{$listenerPath}/{$listenerName}.php";

            if (file_exists($listenerFile) && !$this->isOptionPresent('overwrite')) {
                $this->danger("Listener exists: {$listenerName}. Run with --overwrite to overwrite.");
            } else {
                @mkdir("{$cwd}/{$listenerPath}", 0777, true);

                if (file_exists($eventFile) && !$this->isOptionPresent('overwrite')) {
                    $this->danger('Event already exists! Run with --overwrite to overwrite');
                }

                $listenerPhp = $this->listenerClass($listenerName, $listenerNamespace, $event);

                $this->note('Writing listener to file...');
                file_put_contents($listenerFile, $listenerPhp);

                $listener = "\\{$listenerNamespace}\\{$listenerName}::class";
            }
        }

        $listenerList = implode(",\n        ", $listeners);

        if (count($listeners)) {
            $eventPhp = str_replace('return [];', "return [{$listenerList}\n];", $eventPhp);
        }

        $this->note('Writing event to file...');
        file_put_contents($eventFile, $eventPhp);

        return 0;
    }

    /**
     * @param string $class
     * @param string $namespace
     *
     * @return mixed[]
     */
    public function simpleEventClass(string $class, string $namespace): array
    {
        return [
            str_replace(
                [
                    'NAMESPACE_NAME',
                    'CLASS_NAME',
                ],
                [
                    $namespace,
                    $class,
                ],
                (string)file_get_contents(__DIR__ . '/../../data/stubs/SimpleEventStub.php')
            ),
        ];
    }

    /**
     * @param string $class
     * @param string $namespace
     *
     * @return mixed[]
     */
    public function schemaEventClass(string $class, string $namespace): array
    {
        return [
            str_replace(
                [
                    'NAMESPACE_NAME',
                    'CLASS_NAME',
                ],
                [
                    $namespace,
                    $class,
                ],
                (string)file_get_contents(__DIR__ . '/../../data/stubs/SchemaEventStub.php')
            ),
        ];
    }

    /**
     * @param string $class
     * @param string $namespace
     * @param string $eventClass
     *
     * @return mixed[]
     */
    public function listenerClass(string $class, string $namespace, string $eventClass): array
    {
        return [
            str_replace(
                [
                    'NAMESPACE_NAME',
                    'CLASS_NAME',
                    'EVENT_CLASS_NAME',
                    'EVENT_CLASS_SHORT_NAME',
                ],
                [
                    $namespace,
                    $class,
                    $eventClass,
                    '',
                ],
                (string)file_get_contents(__DIR__ . '/../../data/stubs/ListenerStub.php')
            ),
        ];
    }
}
