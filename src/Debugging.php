<?php

namespace Bethropolis\DebugDoctor;

class Debugging
{
    public static function dump(...$args)
    {
        Logger::log(var_export($args, true));
    }

    public static function logBacktrace()
    {
        ob_start();
        debug_print_backtrace();
        $backtrace = ob_get_clean();
        Logger::log($backtrace);
        echo "<pre>$backtrace</pre>";
    }
}
