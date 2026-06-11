<?php

namespace Laluciole\GdprForms\Widgets;

use Statamic\Widgets\Widget;
use Statamic\Facades\Entry as EntryAPI;
use Statamic\Facades\Form as FormFacade;
use Statamic\Facades\User;

use Illuminate\Support\Facades\Schedule;
use Laluciole\GdprForms\Jobs\GdprChecker;

class Formsubmissions extends Widget
{
    /**
     * The HTML that should be shown in the widget.
     *
     * @return string|\Illuminate\View\View
     */
    public function html()
    {
        $formId = $this->config('form');
        $form = FormFacade::find($formId);

        if(!$form){
            return;
        }

        $hasAccess = User::current()->can('view', $form);
        if (!$hasAccess) {
            return;
        }

        return view('gdpr-forms::widgets.formsubs', [
            'title' => $form->title,
            'handle' => $formId,
            'editLink' => "/cp/forms/" . $formId,
            'canUpdate' => $hasAccess,
        ]);
    }
}
