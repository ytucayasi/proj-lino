<?php

namespace App\Livewire\Tables;

use Carbon\Carbon;
use App\Models\Categoria;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class CategoriaTable extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'CategoriaTable';
    public string $moduleName = 'categorias';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput()->includeViewOnTop('components.datatable.header-top'),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Categoria::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('nombre')
            ->add('estado')
            ->add('created_at_formatted', function ($categoria) {
                return Carbon::parse($categoria->created_at)->format('d/m/Y');
            });
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->sortable()
                ->searchable(),
            Column::make('Nombre', 'nombre')
                ->sortable()
                ->searchable(),
            Column::make('Estado', 'estado')
                ->sortable()
                ->searchable(),
            Column::make('Creado', 'created_at_formatted', 'created_at')
                ->sortable(),
            Column::action('Acciones')
        ];
    }

    public function filters(): array
    {
        return [
            // Puedes agregar filtros aquí si es necesario
        ];
    }

    public function deleteCategoria($id)
    {
        if (!Auth::user()->can('eliminar ' . $this->moduleName)) {
            return redirect()->route($this->moduleName);
        }
        $this->dispatch('deleteCategoria', ['categoria' => Categoria::findOrFail($id)]);
    }

    public function editCategoria($id)
    {
        if (!Auth::user()->can('editar ' . $this->moduleName)) {
            return redirect()->route($this->moduleName);
        }
        $this->dispatch('editCategoria', ['categoria' => Categoria::findOrFail($id)]);
    }

    public function showDetails($id)
    {
        $this->dispatch('showDetails', ['categoria' => Categoria::findOrFail($id)]);
    }

    public function open()
    {
        if (!Auth::user()->can('crear ' . $this->moduleName)) {
            return redirect()->route($this->moduleName);
        }
        $this->dispatch('createCategoria');
    }

    public function actions(Categoria $categoria): array
    {
        return [
            Button::add('edit')
                ->render(function ($categoria) {
                    return Blade::render(<<<HTML
                        @can('editar $this->moduleName')
                            <x-mini-button rounded icon="pencil" flat gray interaction="positive" wire:click="editCategoria('$categoria->id')" />
                        @endcan
                    HTML);
                }),
            Button::add('delete')
                ->render(function ($categoria) {
                    return Blade::render(<<<HTML
                        @can('eliminar $this->moduleName')
                            <x-mini-button rounded icon="trash" flat gray interaction="negative" wire:click="deleteCategoria('$categoria->id')" />
                        @endcan
                    HTML);
                })
        ];
    }

    public function actionRules($row): array
    {
        return [
            Rule::button('delete')
                ->when(fn($row) => $row->nombre == 'Especial')
                ->hide(),
        ];
    }
}
