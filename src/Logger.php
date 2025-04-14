<?php

namespace Bethropolis\DebugDoctor;

class Logger
{
    private static function logToFile($logMessage)
    {
        if (!file_exists(DEBUG_DIRECTORY)) {
            mkdir(DEBUG_DIRECTORY, 0777, true);
        }

        file_put_contents(DEBUG_DIRECTORY . DEBUG_FILE, $logMessage, FILE_APPEND);
    }

    public static function log(...$args)
    {
        if (!self::isDebugEnabled()) return;

        $formattedLog = "[" . date("Y-m-d H:i:s") . "] " . implode(" ", $args);
        $logMessage = $formattedLog . "\n";

        self::logToFile($logMessage);
        self::displayLogEntry($formattedLog);
    }

    private static function isDebugEnabled()
    {
        return DEBUG_ENABLED == "true";
    }

    private static function displayLogEntry($logMessage)
    {
        $callerInfo = self::getCallingLocation();
        echo "<div class='log-entry'>";
        echo "<div class='log-data'><pre>$logMessage</pre></div>";
        echo "<details>";
        echo "<summary>$callerInfo</summary>";
        echo "<pre><code>";
        self::displayCodeSnippet($callerInfo);
        echo "</code></pre>";
        echo "</details></div>";
    }

    private static function getCallingLocation()
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,4);
        if (isset($trace[1]['file']) && isset($trace[1]['line'])) {
            $callingScript = $trace[1]['file'];
            $callingLine = $trace[1]['line'];
            return "$callingScript:$callingLine";
        }
        return 'Unknown';
    }



    private static function displayCodeSnippet($callerInfo)
    {
        if (preg_match('/^(.*):(\d+)$/', $callerInfo, $matches)) {
            $callingScript = $matches[1];
            $callingLine = intval($matches[2]);

            $lines = file($callingScript);
            $startLine = max(1, $callingLine - 5);
            $endLine = min(count($lines), $callingLine + 5);

            for ($i = $startLine - 1; $i < $endLine; $i++) {
                $lineNumber = $i + 1;
                $lineContent = htmlspecialchars($lines[$i]);
                if ($lineNumber == $callingLine) {
                    echo "<b>{$lineNumber}: $lineContent</b>";
                } else {
                    echo "$lineNumber: $lineContent";
                }
            }
        }
    }
}
