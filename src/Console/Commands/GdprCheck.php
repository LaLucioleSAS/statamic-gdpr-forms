<?php

namespace Laluciole\GdprForms\Console\Commands;

use Illuminate\Console\Command;
use Statamic\Facades\Form;
use Statamic\Facades\FormSubmission;

class GdprCheck extends Command
{
    protected $delay = 2;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gdpr:gdpr-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks GDPR entries and deletes achived ones older than 2 years.';

    /**
     * Execute the console command.
     */
    public function handle()
    {   
        // Delete archived form input after 2 years
        $forms = Form::all();
        $targetYear = date("Y") - $this->delay;

        foreach($forms as $form){
            if($form->data()->get('gdpr')){
                $this->info($form->handle());
                $submissions = FormSubmission::query()
                    ->where('form', $form->handle())
                    ->where('done', true)
                    ->get();

                foreach($submissions as $sub){
                    if($sub->get('done_at')){
                        $year = date("Y", $sub->get('done_at'));
                        if($year <= $targetYear){
                            $sub->delete();
                            $this->info($sub->id() . " was deleted.");
                        }
                    }
                }

            }
        }
    }
}
