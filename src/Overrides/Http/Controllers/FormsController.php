<?php

namespace Laluciole\GdprForms\Overrides\Http\Controllers;

use Statamic\Http\Controllers\CP\Forms\FormsController as StatamicFormsController;
use Statamic\Facades\Blueprint;

class FormsController extends StatamicFormsController
{
  protected function editFormBlueprint($form)
    {
        $initial_form = parent::editFormBlueprint($form);
        $contents = $initial_form->contents();

        if(!$initial_form->hasField('gdpr')){
            $contents['tabs']['main']['sections'][] = [
                "display" =>  __("GDPR"),
                "fields" => [
                    [
                        "handle" => "gdpr",
                        "field" => [
                            "display" => __("GDPR Form"),
                            "instructions" => __("Identify this form as a GDPR data form."),
                            "type" => "toggle"
                        ]
                    ]
                ]
            ];
            $initial_form->setContents($contents);
        }

        return Blueprint::make()->setContents($initial_form->contents());
    }
}