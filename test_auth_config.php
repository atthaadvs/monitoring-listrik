<?php

// Simple test untuk auth guard admin
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Auth Configuration\n";
echo "==========================\n\n";

try {
    // Test auth config
    $guards = config('auth.guards');
    echo "Available Guards:\n";
    print_r($guards);
    
    echo "\nProviders:\n";
    $providers = config('auth.providers');
    print_r($providers);
    
    // Test guard creation
    echo "\nTesting Admin Guard:\n";
    $adminGuard = auth()->guard('admin');
    echo "Admin guard created successfully: " . get_class($adminGuard) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}