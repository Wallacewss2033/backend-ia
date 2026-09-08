<?php
// $app = require __DIR__.'/bootstrap/app.php';
// $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
// $kernel->bootstrap();

$user = App\Models\User::factory()->create([
    'password' => bcrypt('12345678')
]);
echo $user->email . "\n" . $user->password . "\n";
