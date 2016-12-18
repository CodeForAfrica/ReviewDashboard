<?php

namespace App\Jobs;

use App\Form;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Google_Client;
use Illuminate\Support\Facades\Storage;


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
        echo "FORM_ID: " . $this->form->id . "\n";

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
        $client->setClientId(env('GOOGLE_ID'));
        $client->setClientSecret(env('GOOGLE_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT'));
        $client->setAccessToken($this->form->users[0]->google_token);

        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            $this->form->users[0]->google_token = $client->getAccessToken();
            $this->form->users[0]->save();
        }

        $fileId = parse_url($this->form->responses_url, PHP_URL_PATH);
        $fileId = str_replace(array("/spreadsheets/d/","/edit","/"), "", $fileId);

        $sheetService = new \Google_Service_Sheets($client);
        $spreadsheet = $sheetService->spreadsheets->get($fileId);

        $sheet_name = $spreadsheet->sheets[0]->properties->title;
        foreach ($spreadsheet->sheets as $sheet) {
            if ($sheet->properties->title == 'SHORTLIST'){
                $sheet_name = 'SHORTLIST';
            }
        }
        $range = "'" . $sheet_name . "'!A1:"
            . xl_rowcol_to_cell($spreadsheet->sheets[0]->properties->gridProperties->rowCount,
                $spreadsheet->sheets[0]->properties->gridProperties->columnCount);

        $responses = $sheetService->spreadsheets_values->get($fileId, $range)->getValues();

        $responses_csv = '';
        foreach ($responses as $response){
            foreach ($response as $field){
                $responses_csv .= json_encode($field) . ',';
            }
            $responses_csv = substr($responses_csv, 0, -1);
            $responses_csv .= "\n";
        }
        $responses_csv = substr($responses_csv, 0, -2);

        // TODO: Save history of responses instead of replacing
        Storage::put(
            'responses/'.$this->form->id.'/latest.csv',
            $responses_csv
        );

        $this->responses = $responses;

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
                    'data' => $response
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
