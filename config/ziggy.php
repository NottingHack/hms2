<?php

return [
    /*
    |----------------------------------------------------------------------------
    | Ziggy route filtering
    |----------------------------------------------------------------------------
    | Either `only` or `except` never both
    */
    // 'only' => ['home', 'api.*'],
    'except' => ['debugbar.*', 'horizon.*', 'telescope.*', 'pretty-routes.*', 'client.*'],
];
