<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class DecryptPrograms extends Command
{
    protected $signature = 'programs:decrypt';
    protected $description = 'Decrypt program column in users table';

    public function handle()
    {
        $this->info('Starting program decryption...');
        
        $users = User::all();
        $success = 0;
        $failed = 0;
        
        DB::beginTransaction();
        
        try {
            foreach ($users as $user) {
                $originalValue = $user->getRawOriginal('program');
                
                if ($originalValue && is_string($originalValue)) {
                    try {
                        $decrypted = Crypt::decryptString($originalValue);
                        $user->program = $decrypted;
                        $user->save();
                        $success++;
                    } catch (\Exception $e) {
                        \Log::warning("Failed to decrypt program for user {$user->id}: " . $e->getMessage());
                        $failed++;
                    }
                }
            }
            
            DB::commit();
            
            $this->info("Successfully decrypted: {$success} users");
            $this->warn("Failed to decrypt: {$failed} users");
            
            if ($failed > 0) {
                $this->error('Some users failed to decrypt. Check the logs.');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Decryption failed: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}