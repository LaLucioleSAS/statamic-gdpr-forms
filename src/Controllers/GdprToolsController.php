<?php

namespace Laluciole\GdprForms\Controllers;
use Statamic\CP\Column;
use Statamic\Facades\Form;
use Illuminate\Http\Request;
use Statamic\Facades\Action;
use League\Csv\Writer;
use League\Csv\Reader;
use SplTempFileObject;
use Statamic\Facades\File;

class GdprToolsController 
{

    public function index()
    {
        return view('gdpr-forms::gdpr/index', [
            'submissions' => [],
            'email' => '',
            'columns' => [],
            'messages' => []
        ]);
    }

    public function search(Request $request){
        $messages = [];
        $email = $request->email;
        $submissions = collect([]);
        $forms = Form::all();

        foreach($forms as $form){
            if($form->data()->get('gdpr')){
                $submissions = $submissions->merge($form->submissions()->filter(function($sub) use ($email) {
                    return str_contains(json_encode($sub->data()), $email);
                })->map(function ($submission) {
                    return [
                        'item' => $submission,
                        'datestamp' => date('d/m/Y H:i:s', $submission->id()),
                        'form' => $submission->form()->title(),
                        'id' => $submission->id(),
                        'actions' => Action::for($submission),
                        'data' => $submission->data(),
                        'show_url' => cp_route(
                            'forms.submissions.show', 
                            ['form' => $submission->form()->handle(), 'submission' => $submission->id()]
                        ),
                    ];
                }));
            }
        }
        
        if($request->action == 'delete'){
            foreach($submissions as $s){
                $s['item']->delete();
            }
            $submissions = [];
            $messages[] = "Informations supprimées.";
        } else if($request->action == 'export'){
            $messages[] = "Données exportées avec succès.";
            return $this->export($submissions, $email, 'csv');
        }

        return view('gdpr-forms::gdpr/index', [
            'submissions' => $submissions,
            'email' => $email,
            'columns' => [
                Column::make('datestamp')->label(__('Date')),
                Column::make('form')->label(__('general.form')),
                Column::make('data')->label(__('Data')),
            ],
            'messages' => $messages,
        ]); 
    }

    public function export($submissions, $email, $type)
    {
        $writer = Writer::createFromFileObject(new SplTempFileObject);
        $writer->setDelimiter(',');
        $headers = ['date', 'contexte', 'donnees'];
        $writer->insertOne($headers);

        $data = collect($submissions)->map(function ($value) {
            return [
                $value['datestamp'], $value['form'], implode(',', ($value['data'])->toArray())
            ];
        })->all();

        $writer->setOutputBOM(Reader::BOM_UTF8);
        $writer->insertAll($data);

        $path = storage_path('statamic/tmp/gdpr/' . $email . '-' . time() . '.' . $type);
        File::put($path, $writer);
        return response()->download($path)->deleteFileAfterSend();
    }

    public static function cpSection()
    {
        return 'Tools';
    }
}
