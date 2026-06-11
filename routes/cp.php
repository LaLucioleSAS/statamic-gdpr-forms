<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laluciole\GdprForms\Actions\MarkAsDone;
use Laluciole\GdprForms\Controllers\GdprToolsController;
use Statamic\Facades\FormSubmission;
use Statamic\Facades\CP\Toast;

Route::prefix('gdpr')->name('statamic.cp.gdpr.')->middleware(['statamic.cp', 'statamic.cp.authenticated'])->group(function () {
    Route::get('/', [GdprToolsController::class, 'index'])->name('index');
    Route::post('/search', [GdprToolsController::class, 'search'])->name('search');
});

Route::get('/forms/submissions/{id}/done', function(Request $request, string $id){
    $sub = FormSubmission::find($id);
    if($sub && $request->user()->can('view', $sub)){
        $response = (new MarkAsDone())->run(collect([$sub]), null);
        Toast::success($response);
    }
})->name('gdpr.mark_sub_done');
