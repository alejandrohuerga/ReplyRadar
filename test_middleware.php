<?php
$web = app('router')->getMiddlewareGroups()['web'] ?? [];
echo "Web middleware count: " . count($web) . "\n";
foreach ($web as $i => $m) {
    $name = is_string($m) ? $m : (is_object($m) ? get_class($m) : 'Closure');
    echo "$i: $name\n";
}
