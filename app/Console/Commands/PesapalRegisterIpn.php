<?php

namespace App\Console\Commands;

use App\Services\PesapalService;
use Illuminate\Console\Command;

class PesapalRegisterIpn extends Command
{
    protected $signature   = 'pesapal:register-ipn';
    protected $description = 'Register the Pesapal IPN URL and output the IPN ID';

    public function handle(PesapalService $pesapal): int
    {
        $url = route('pesapal.ipn');

        $this->info("Registering IPN URL: {$url}");

        try {
            $ipnId = $pesapal->registerIPN($url);
            $this->line('');
            $this->info('IPN registered successfully!');
            $this->line('');
            $this->comment('Add the following to your .env file:');
            $this->line("PESAPAL_IPN_ID={$ipnId}");
            $this->line('');
        } catch (\Exception $e) {
            $this->error('Failed to register IPN: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
