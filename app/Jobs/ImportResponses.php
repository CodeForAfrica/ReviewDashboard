<?php

namespace App\Jobs;

use App\Form;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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
        //

        if ($this->attempts() > 3) {
            $this->form->import_status = 3;
            $this->form->save();
            $this->release(10);
        }

        $this->form->import_status = 2;
        $this->form->save();
    }
}
