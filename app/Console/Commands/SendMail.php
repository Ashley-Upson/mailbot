<?php

namespace App\Console\Commands;

use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:send {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command that will send a single message from the queue.';

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
        $message = Message::where('id', $this->argument('id'))->first();

        if($message->application == null) {
            Log::error('Failed to send message ' . $this->argument('id') . ': no application with that ID');
            return false;
        }

        if($message->application->account == null) {
            Log::error('Failed to send message ' . $message->id . ': no account with that ID');

            $message->update([
                'status' => 2,
            ]);

            return false;
        }

        $transport = Mail::getSwiftMailer()->getTransport();

        Mail::alwaysFrom($message->application->account->email, $message->application->account->name);

        $transport->setUsername($message->application->account->email);

        $transport->setPassword(decrypt($message->application->account->password));

        try {
            Mail::raw($message->body, function($mail) use ($message) {
                $mail->to($message->to);
                $mail->from($message->application->account->email);
                $mail->subject($message->subject);
                $mail->priority($message->priority ?? $message->application->default_priority);
            });

            $message->update([
                'status' => 1,
                'sent_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            Log::alert('Failed o send message ' . $message->id . ': an error occurred', [$e]);

            $message->update([
                'status' => 2,
            ]);

            return false;
        }
    }
}
