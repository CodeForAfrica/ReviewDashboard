<?php

namespace App\Jobs;

use App\Form;
use App\Jobs\Job;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Google_Client;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;


class ImportResponses extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $form;
    protected $responses;

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
        echo "--- IMPORT RESPONSES | START: ".$this->attempts()." ---\n";
        echo "[".Carbon::now()."] Starting up.\n";

        $this->downloadFile();
        $this->processResponses();

        $this->form->import_status = 2;
        $this->form->save();

        echo "[".Carbon::now()."] Done.\n";
        echo "--- IMPORT RESPONSES | DONE: ".$this->attempts()." ---\n";
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
            'alt' => 'media' ))->getBody();

        // TODO: Save history of responses instead of replacing
        Storage::put(
            'responses/'.$this->form->id.'/latest.csv',
            $responses
        );

        $this->responses = Reader::createFromString($responses);

        echo "[".Carbon::now()."] Download complete.\n";
    }

    public function processResponses()
    {
        echo "[".Carbon::now()."] Processing responses.\n";

        $this->form->responses()->delete();

        $count = 0;

        foreach ($this->responses as $index => $response) {
            $response = json_encode($response);
            if ($index == 0){
                $this->form->responses_headers = $response;
                $this->form->save();
            } else {
                $this->form->responses()->create([
                    'data'     => $response
                ]);
            }
            $count = $index;
        }

        echo "[".Carbon::now()."] Processing complete [".$count." responses].\n";
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
        echo "--- IMPORT RESPONSES | FAILED: ".$this->attempts()." ---\n";
    }
}
