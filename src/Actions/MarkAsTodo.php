<?php

namespace Laluciole\GdprForms\Actions;

use Statamic\Actions\Action;
use Statamic\Contracts\Forms\Submission;

class MarkAsTodo extends Action
{
    protected $dangerous = false;
    /**
     * The run method
     *
     * @return mixed
     */
    public function run($items, $values)
    {
        $items->each->set('done', false);
        $items->each->set('done_at', null);
        $items->each->save();
        return trans_choice('Item marked as todo.|:count items marked as todo.', $items);
    }

   public static function title()
    {
        return __("Mark as todo");
    }

 
    public function visibleTo($item)
    {
        return $item instanceof Submission && $item->form && $item->form->handle == 'contact' && $item->done;
    }

    public function authorize($user, $item)
    {
        return $user->can('view', $item);
    }
}
