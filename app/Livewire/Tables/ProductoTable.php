<?php

namespace App\Livewire\Tables;

use Carbon\Carbon;
use App\Models\Producto;
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

final class ProductoTable extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'ProductoTable';
    public string $moduleName = 'productos';

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
        return Producto::query();
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
            ->add('imagen')
            ->add('marca')
            ->add('cantidad_base')
            ->add('costo', function ($producto) {
                return 'S/ ' . $producto->costo;
            })
            ->add('precio', function ($producto) {
                return 'S/ ' . $producto->precio;
            })
            ->add('precio_oferta', function ($producto) {
                return 'S/ ' . $producto->precio_oferta;
            })
            ->add('ganancia', function ($producto) {
                return 'S/ ' . $producto->precio - $producto->costo;
            })
            ->add('created_at_formatted', function ($producto) {
                return Carbon::parse($producto->created_at)->format('d/m/Y');
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
            Column::make('Marca', 'marca')
                ->sortable()
                ->searchable(),
            Column::make('Cantidad Base', 'cantidad_base')
                ->sortable()
                ->searchable(),
            Column::make('Precio Compra', 'costo')
                ->sortable()
                ->searchable(),
            Column::make('Precio Venta', 'precio')
                ->sortable()
                ->searchable(),
            Column::make('Ganancia', 'ganancia')
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

    public function deleteProducto($id)
    {
        if (!Auth::user()->can('eliminar ' . $this->moduleName)) {
            return redirect()->route($this->moduleName);
        }
        $this->dispatch('deleteProducto', ['producto' => Producto::findOrFail($id)]);
    }

    public function editProducto($id)
    {
        if (!Auth::user()->can('editar ' . $this->moduleName)) {
            return redirect()->route($this->moduleName);
        }
        $this->dispatch('editProducto', ['producto' => Producto::findOrFail($id)]);
    }

    public function showDetails($id)
    {
        $this->dispatch('showDetails', ['producto' => Producto::findOrFail($id)]);
    }

    public function open()
    {
        if (!Auth::user()->can('crear ' . $this->moduleName)) {
            return redirect()->route($this->moduleName);
        }
        $this->dispatch('createProducto');
    }

    public function actions(Producto $producto): array
    {
        return [
            Button::add('edit')
                ->render(function ($producto) {
                    return Blade::render(<<<HTML
                        @can('editar $this->moduleName')
                            <x-mini-button rounded icon="pencil" flat gray interaction="positive" wire:click="editProducto('$producto->id')" />
                        @endcan
                    HTML);
                }),
            Button::add('delete')
                ->render(function ($producto) {
                    return Blade::render(<<<HTML
                        @can('eliminar $this->moduleName')
                            <x-mini-button rounded icon="trash" flat gray interaction="negative" wire:click="deleteProducto('$producto->id')" />
                        @endcan
                    HTML);
                })
        ];
    }

    public function actionRules($row): array
    {
        return [
            Rule::button('delete')
                ->when(fn($row) => $row->cantidad_base <= 0)
                ->hide(),
        ];
    }
}