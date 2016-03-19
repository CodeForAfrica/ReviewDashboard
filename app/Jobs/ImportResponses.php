<?php

namespace App\Jobs;

use App\Form;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Google_Client;
use Illuminate\Support\Facades\Storage;


class ImportResponses extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $form;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Form $form)
    {
        //
        $this->form = $form;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        echo "---\n";
        echo $this->attempts().". Starting up.\n";

        $this->downloadFile();

        $this->form->import_status = 2;
        $this->form->save();

        echo $this->attempts()."Done.\n---\n";
    }


    public function downloadFile()
    {
        $client = new Google_Client();

        $client->setApplicationName('CfA_Review_Dashboard');
        $client->setDeveloperKey(env('GOOGLE_API_KEY'));
        $client->setAccessToken($this->form->users[0]->google_token);

        $fileId = parse_url($this->form->responses_url, PHP_URL_PATH);
        $fileId = str_replace(array("/spreadsheets/d/","/edit","/"), "", $fileId);

        $driveService = new \Google_Service_Drive($client);
        $responses = $driveService->files->export($fileId, 'text/csv', array(
            'alt' => 'media' ));

        // TODO: Save history of responses instead of replacing
        Storage::put(
            'responses/'.$this->form->id.'/latest.csv',
            $responses->getBody()
        );

        echo "  - Download complete\n";
    }


    /**
     * Handle a job failure.
     *
     * @return void
     */
    public function failed()
    {
        $this->form->import_status = 3;
        $this->form->save();

        echo "\n";
        echo "! ! ! ! ! ! ! ! ! ! ! ! ! ! ! !\n";
        echo "! ! ! We have failed you. ! ! !\n";
        echo "! ! ! ! ! ! ! ! ! ! ! ! ! ! ! !\n";
        echo "\n";
    }
}
