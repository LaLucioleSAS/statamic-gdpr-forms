<?php

namespace Laluciole\GdprForms\Actions;

use Illuminate\Support\Facades\Date;
use Statamic\Actions\Action;
use Statamic\Contracts\Forms\Submission;

class MarkAsDone extends Action
{
    protected $dangerous = false;
    /**
     * The run method
     *
     * @return mixed
     */
    public function run($items, $values)
    {
        $items->each->set('done', true);
        $items->each->set('done_at', new \DateTime());
        $items->each->save();
        return trans_choice('Item marked as done.|:count items marked as done.', $items);
    }

   public static function title()
    {
        return __("Mark as done");
    }

 
    public function visibleTo($item)
    {
        return $item instanceof Submission && $item->form && $item->form->handle == 'contact';
    }

    public function authorize($user, $item)
    {
        return $user->can('view', $item);
    }
}
