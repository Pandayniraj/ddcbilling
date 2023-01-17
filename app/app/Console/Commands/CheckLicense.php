<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Auth\Events\Authenticated;

class CheckLicense extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:license';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check License and Send email before it expires';

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
public function handle(Authenticated $event)
    {
        $message = 'Expired licence';
        if(auth()->user()->hasRole('admins')){
            $licenceFin = Carbon::createFromFormat('d/m/Y', config('config_app.licenceEnd'));
            if(Carbon::now('Asia/Kathmandu') == $licenceEnd->subDays(15)){
                $event->user->notify(new licence_soon_expired($message));
            }
            if(Carbon::now('Asia/Kathmandu') == $licenceEnd->subMonth()){
                $event->user->notify(new licence_soon_expired($message));
            }
            if(Carbon::now('Asia/Kathmandu') >= $licenceEnd){
                $errors = new MessageBag();
                $errors->add('licence_expired', $message);
                auth()->logout();
                return back()->withErrors($errors);
            }
        }
    }
}
