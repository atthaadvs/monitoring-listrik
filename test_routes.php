<?php

// Test route functionality
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Route Configuration\n";
echo "===========================\n\n";

try {
    // Test route generation
    echo "Testing route('dashboard'): ";
    $dashboardUrl = route('dashboard');
    echo $dashboardUrl . "\n";
    
    echo "Testing route('admin.login'): ";
    $loginUrl = route('admin.login');
    echo $loginUrl . "\n";
    
    echo "\nAll routes working properly!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}