<x-filament-panels::page>
    {{ $this->form }}

    <div class="flex items-center gap-3">
        <x-filament::button
            type="button"
            color="primary"
            icon="heroicon-o-arrow-up-tray"
            wire:click="import"
            wire:confirm="Seluruh data categories & api_docs yang ada akan di-truncate lalu diganti dengan isi file. Lanjutkan?"
        >
            Import
        </x-filament::button>

        <x-filament::button
            type="button"
            color="gray"
            icon="heroicon-o-arrow-down-tray"
            wire:click="export"
        >
            Export SQL
        </x-filament::button>
    </div>
</x-filament-panels::page>