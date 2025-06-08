<?php
// Debug div balance in enhanced template
$file = 'public/partials/budgex-public-enhanced-budget-page.php';
$content = file_get_contents($file);
$lines = explode("\n", $content);

$div_balance = 0;
$line_number = 0;

echo "Analyzing div balance line by line:\n\n";

foreach ($lines as $line) {
    $line_number++;
    
    // Count opening divs in this line
    $opens = substr_count($line, '<div');
    $closes = substr_count($line, '</div>');
    
    if ($opens > 0 || $closes > 0) {
        $div_balance += $opens - $closes;
        echo "Line $line_number: +$opens -$closes (balance: $div_balance)\n";
        if ($opens > 0 || $closes > 0) {
            echo "  Content: " . trim($line) . "\n";
        }
        
        if ($div_balance < 0) {
            echo "  ⚠️  NEGATIVE BALANCE - extra closing tags!\n";
        }
    }
}

echo "\nFinal balance: $div_balance\n";
if ($div_balance === 0) {
    echo "✅ Divs are balanced!\n";
} else if ($div_balance > 0) {
    echo "❌ Missing $div_balance closing div(s)\n";
} else {
    echo "❌ " . abs($div_balance) . " extra closing div(s)\n";
}
?>
