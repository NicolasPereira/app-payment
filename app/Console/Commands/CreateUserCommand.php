<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create {--t|type=: users profile type ((C) for client or (S) for shopkeeper)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $user = User::factory()->create();

        $profileType = $this->option('type');

        if($profileType === 's') {
            $user->profile = 'shopkeeper';
        } else {
            $user->profile = 'client';
        }
        $user->save();

        $this->info("Usuário criado: {$user->email}!");
        $this->info("Usuário tipo: {$user->profile}!");
    }
}
