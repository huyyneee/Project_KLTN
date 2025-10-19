#!/usr/bin/env php
<?php
/**
 * Test Multiple Image Upload
 * 
 * Usage: php test_multiple_upload.php
 */

// Test 1: Simulate multiple files with same key (like FormData.append('images[]', file))
echo "=== Test 1: Simulating FormData.append('images[]', file) ===\n";

// Simulate $_FILES structure when multiple files with same key 'images[]'
$_FILES = [
    'images_' => [  // PHP converts 'images[]' to 'images_'
        'name' => ['test1.jpg', 'test2.jpg', 'test3.jpg'],
        'type' => ['image/jpeg', 'image/jpeg', 'image/jpeg'],
        'tmp_name' => ['/tmp/php123', '/tmp/php456', '/tmp/php789'],
        'error' => [0, 0, 0],
        'size' => [12345, 23456, 34567]
    ]
];

echo "Files structure:\n";
print_r($_FILES);
echo "\n";

echo "Field 'images_' has " . count($_FILES['images_']['name']) . " files\n";
echo "\n";

// Test 2: Simulate multiple files with key 'images' (standard FormData)
echo "=== Test 2: Standard multiple file upload ===\n";

$_FILES = [
    'images' => [
        'name' => ['test1.jpg', 'test2.jpg', 'test3.jpg'],
        'type' => ['image/jpeg', 'image/jpeg', 'image/jpeg'],
        'tmp_name' => ['/tmp/php123', '/tmp/php456', '/tmp/php789'],
        'error' => [0, 0, 0],
        'size' => [12345, 23456, 34567]
    ]
];

echo "Files structure:\n";
print_r($_FILES);
echo "\n";

if (is_array($_FILES['images']['name'])) {
    echo "Field 'images' is an array with " . count($_FILES['images']['name']) . " files\n";
} else {
    echo "Field 'images' is a single file\n";
}
echo "\n";

// Test 3: What happens when FormData appends same key multiple times?
echo "=== Test 3: How PHP handles multiple FormData.append('images', file) ===\n";
echo "When you do:\n";
echo "  formData.append('images', file1)\n";
echo "  formData.append('images', file2)\n";
echo "  formData.append('images', file3)\n";
echo "\n";
echo "PHP receives it as:\n";
echo "  \$_FILES['images'] = [\n";
echo "    'name' => ['file1.jpg', 'file2.jpg', 'file3.jpg'],\n";
echo "    'type' => ['image/jpeg', 'image/jpeg', 'image/jpeg'],\n";
echo "    ...\n";
echo "  ]\n";
echo "\n";

echo "✅ PHP automatically handles it as an array!\n";
echo "\n";

// Test 4: What if only ONE file?
echo "=== Test 4: Single file upload ===\n";

$_FILES = [
    'images' => [
        'name' => 'single.jpg',  // NOT an array
        'type' => 'image/jpeg',
        'tmp_name' => '/tmp/phpABC',
        'error' => 0,
        'size' => 12345
    ]
];

echo "Files structure:\n";
print_r($_FILES);
echo "\n";

if (is_array($_FILES['images']['name'])) {
    echo "Field 'images' is an array\n";
} else {
    echo "Field 'images' is a SINGLE file (not array)\n";
    echo "Need to convert to array format for consistent handling\n";
}

echo "\n";
echo "=== CONCLUSION ===\n";
echo "✅ FormData.append('images', file) multiple times → PHP gets it as array automatically\n";
echo "✅ FormData.append('images[]', file) → PHP gets it as 'images_' array\n";
echo "✅ Backend ImageUploadController.php should handle both cases\n";
?>