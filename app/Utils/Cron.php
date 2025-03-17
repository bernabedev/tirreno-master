<?php

/**
 * Tirreno ~ Open source user analytics
 * Copyright (c) Tirreno Technologies Sàrl (https://www.tirreno.com)
 *
 * Licensed under GNU Affero General Public License version 3 of the or any later version.
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Tirreno Technologies Sàrl (https://www.tirreno.com)
 * @license       https://opensource.org/licenses/AGPL-3.0 AGPL License
 * @link          https://www.tirreno.com Tirreno(tm)
 */

namespace Utils;

// can accept time params as `* * * * 0,1,2`, `0-15 * * 1 3`, but
// not step values like `23/4 10/2 * * *`
// comma-expressions should be wrapped in quotes like "0-10 0,12 * * *"
class Cron extends \Prefab {
    public const HANDLER = 0;
    public const EXPRESSION = 1;
    public const RANGES = [
        ['min' => 0, 'max' => 59], // minute
        ['min' => 0, 'max' => 23], // hour
        ['min' => 1, 'max' => 31], // day of month
        ['min' => 1, 'max' => 12], // month
        ['min' => 0, 'max' => 6],  // day of week (0 = Sunday)
    ];
    public const PATTERN = '/^(\*|\d+)(?:-(\d+))?(?:\/(\d+))?$/';

    protected $f3;
    protected array $jobs = [];
    protected array $forceRun = [];
    protected bool $runForcedOnly = false;

    public function __construct() {
        $this->f3 = \Base::instance();
        $this->f3->route('GET /cron', function (): void {
            $this->route();
        });
    }

    public static function parseExpression(string $expression): false|array {
        $parts = [];
        $expressionParts = preg_split('/\s+/', trim($expression), -1, PREG_SPLIT_NO_EMPTY);

        if (count($expressionParts) !== 5) {
            return false;
        }

        foreach ($expressionParts as $i => $field) {
            $values = [];
            // handle lists
            $fieldParts = explode(',', $field);

            foreach ($fieldParts as $part) {
                if (!preg_match(self::PATTERN, $part, $matches)) {
                    return false;
                }

                $start = $matches[1];
                $end = $matches[2] ?? null;
                $step = $matches[3] ?? 1;

                // Convert '*' to start and end values
                if ($start === '*') {
                    $start = self::RANGES[$i]['min'];
                    $end = self::RANGES[$i]['max'];
                } else {
                    $start = (int) $start;
                    $end = $end !== null ? (int) $end : $start;
                }
                $step = (int) $step;

                if ($start > $end || $start < self::RANGES[$i]['min'] || $end > self::RANGES[$i]['max'] || $step < 1) {
                    return false;
                }

                $range = range($start, $end, $step);
                $values = array_merge($values, $range);
            }

            $parts[$i] = array_unique($values);
            sort($parts[$i]);
        }

        return $parts;
    }

    public static function parseTimestamp(\DateTime $time): array {
        return [
            (int) $time->format('i'), // minute
            (int) $time->format('H'), // hour
            (int) $time->format('d'), // day of month
            (int) $time->format('m'), // month
            (int) $time->format('w'), // day of week
        ];
    }

    public function addJob(string $jobName, string $handler, string $expression): void {
        if (!preg_match('/^[\w\-]+$/', $jobName)) {
            throw new \Exception('Invalid job name.');
        }

        $this->jobs[$jobName] = [$handler, $expression];
    }

    public function run(\DateTime|null $time = null): void {
        if (!$time) {
            $time = new \DateTime();
        }

        $toRun = $this->getJobsToRun($time);
        if (!count($toRun)) {
            echo sprintf('No jobs to run at %s%s', $time->format('Y-m-d H:i:s'), PHP_EOL);
            exit;
        }

        foreach ($toRun as $jobName) {
            $this->execute($jobName);
        }
    }

    private function route(): void {
        if (PHP_SAPI !== 'cli') {
            $this->f3->error(404);

            return;
        }

        $this->f3->set('ONERROR', \Utils\ErrorHandler::getCronErrorHandler());

        while (ob_get_level()) {
            ob_end_flush();
        }
        ob_implicit_flush(1);

        $this->readArguments();
        $this->loadCrons();
        $this->validateForcedJobs();
        $this->run();
    }

    private function readArguments(): void {
        $argv = $GLOBALS['argv'];

        foreach ($argv as $position => $argument) {
            if ($argument === '--force') {
                if (array_key_exists($position + 1, $argv)) {
                    $this->forceRun[] = $argv[$position + 1];
                } else {
                    echo 'No job specified to force. Ignoring flag.' . PHP_EOL;
                }
            } elseif ($argument === '--force-only') {
                $this->runForcedOnly = true;
            }
        }
    }

    private function loadCrons(): void {
        $this->f3->config('config/crons.ini');

        $crons = (array) $this->f3->get('crons');
        foreach (array_keys($crons) as $jobName) {
            if (substr($jobName, 0, 1) !== '#') {
                $cron = $crons[$jobName];
                $this->addJob($jobName, $cron[self::HANDLER], $cron[self::EXPRESSION]);
            }
        }
    }

    private function validateForcedJobs(): void {
        $notFound = array_diff($this->forceRun, array_keys($this->jobs));
        foreach ($notFound as $flagArgument) {
            echo sprintf('Job not found. Ignoring --force %s flag.%s', $flagArgument, PHP_EOL);
        }

        $this->forceRun = array_diff($this->forceRun, $notFound);
    }

    public function execute(string $jobName): void {
        if (!isset($this->jobs[$jobName])) {
            throw new \Exception('Job does not exist.');
        }

        $job = $this->jobs[$jobName];
        $handler = $job[self::HANDLER];
        if (is_string($handler)) {
            $handler = $this->f3->grab($handler);
        }
        if (!is_callable($handler)) {
            throw new \Exception('Invalid job handler.');
        }

        call_user_func_array($handler, [$this->f3]);
    }

    private function isDue(\DateTime $time, string $expression): bool {
        $parts = self::parseExpression($expression);
        if (!$parts) {
            return false;
        }

        foreach (self::parseTimestamp($time) as $i => $k) {
            if (!in_array($k, $parts[$i])) {
                return false;
            }
        }

        return true;
    }

    private function getJobsToRun(\DateTime $time): array {
        if ($this->runForcedOnly) {
            return $this->forceRun;
        }

        $toRun = array_keys($this->jobs);
        $toRun = array_filter($toRun, function ($jobName) use ($time) {
            return $this->isDue($time, $this->jobs[$jobName][self::EXPRESSION]);
        });

        return array_unique(array_merge($toRun, $this->forceRun));
    }
}
