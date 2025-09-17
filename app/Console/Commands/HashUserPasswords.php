<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class HashUserPasswords extends Command
{
    protected $signature = 'users:hash-passwords';
    protected $description = 'Hash les mots de passe en clair des utilisateurs';

    public function handle()
    {
        $users = User::all();
        $count = 0;

        foreach ($users as $user) {
            if (!Hash::needsRehash($user->password)) {
                $this->info("Mot de passe déjà hashé pour {$user->email}");
                continue;
            }

            $user->password = Hash::make($user->password);
            $user->save();
            $this->info("Mot de passe hashé pour {$user->email}");
            $count++;
        }

        $this->info("✅ {$count} mot(s) de passe mis à jour.");
        return Command::SUCCESS;
    }
}