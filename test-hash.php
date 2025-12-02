<?php
$plainToken = '7875f98ee3d545640350d95922e2edb5bb2014c2f9a689c2b411ae27ab952adb';
$hashedToken = hash('sha256', $plainToken);
echo "Plain token: " . $plainToken . "\n\n";
echo "Hashed token: " . $hashedToken . "\n\n";
echo "DB token: b3da05770f4be3c62e1a7ff93b111c1b527ea11ffc916254289ecd793f0e6de8\n\n";
echo "Match? " . ($hashedToken ? 'YES' : 'NO');