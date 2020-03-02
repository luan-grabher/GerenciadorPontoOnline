<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class ImportPagarmeBO extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:pagarme:bo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa os recebimentos no periodo fornecido.';

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
     * @return mixed
     */
    public function handle()
    {
        $newName = "robertinho";

        //sleep(5);
        $user = User::find(4);
        $user->name = $newName ;
        $user->save();
    }
}
