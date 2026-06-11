@extends('statamic::layout')
@section('title', __('RGPD'))

@section('content')

    <div class="max-w-5xl mx-auto">
    <ui-header icon="checkmark" title="Gestion RGPD"></ui-header>

    <form method="POST" action={{ cp_route('statamic.cp.gdpr.search') }}>
        @csrf

        <div class="flex items-center gap-2 mb-8">
            <ui-input class="flex w-full" type="email" name="email" autofocus="autofocus" placeholder="theo@test.test" value={{ $email }}/>
            <ui-button type="submit" name="action" value="search" variant="default" text="{{ __('Rechercher') }}" />
        </div>

        @if ($messages && sizeof($messages) > 0)
            @foreach ($messages as $m)
                <div class="bg-blue-100 py-2 px-4 border mt-4 mb-4 border-blue-400 rounded-lg text-blue-400 font-bold" role="alert">{{ $m }}</div>
            @endforeach
        @endif


        @if ($email and $submissions)

            @if (sizeof($submissions) == 0)
                Aucun résultat pour cette adresse e-mail.
            @else

                <ui-listing :allow-search="false" :allow-customizing-columns="false"
                    :items="{{ json_encode($submissions) }}"
                    :columns="[
                        { field: 'datestamp', label: 'Date', sortable: true },
                        { field: 'form', label: 'Formulaire', sortable: false },
                        { field: 'data', label: 'Données', sortable: false },
                    ]">

                    <template #row-actions="{ row: entry }">
                        <ui-dropdown-item :text="__('Show')" :redirect="submission.show_url" />
                    </template>
                </ui-listing>

                <div class="flex align-middle justify-center space-x-4 mb-3 mt-3">
                    <ui-button-group>
                        <ui-button icon="download" text="Prepend" type="submit" name="action" value="export">Exporter l'ensemble</ui-button>
                        <ui-button icon="delete" text="Prepend" variant="danger" onclick="return confirm('Confirmer la suppression de toutes les données pour cet email ?');" type="submit" name="action" value="delete">Supprimer les données</ui-button>
                    </ui-button-group>
                </div>

            @endif
        @endif


    </form>



</div>

@endsection
