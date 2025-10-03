<?php
// public/view_debug.php - quick debug to verify view file existence and permissions
$view = __DIR__ . '/../app/Views/test.php';
echo "Checking view file: " . htmlspecialchars($view) . "<br>\n";
if (file_exists($view)) {
    echo "file_exists: yes<br>\n";
    echo "realpath: " . htmlspecialchars(realpath($view)) . "<br>\n";
    echo "is_readable: " . (is_readable($view) ? 'yes' : 'no') . "<br>\n";
    echo "<h3>First 40 lines:</h3>\n<pre>";
    $lines = @file($view);
    if ($lines !== false) {
        foreach (array_slice($lines, 0, 40) as $i => $line) {
            echo htmlspecialchars($line);
        }
    } else {
        echo "(cannot read file)";
    }
    echo "</pre>";
} else {
    echo "file_exists: NO\n";
}

echo "<p>Also show current working directory (for server): " . htmlspecialchars(getcwd()) . "</p>\n";
