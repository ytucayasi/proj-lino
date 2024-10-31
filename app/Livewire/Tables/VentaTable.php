<?php
namespace App\Livewire\Tables;

use App\Models\Venta;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class VentaTable extends PowerGridComponent
{
    use WithExport;
    public string $tableName = 'VentaTable';
    public string $moduleName = 'ventas';

    // Estados de venta
    const ESTADOS = [
        1 => 'Pendiente',
        2 => 'Pagado',
        3 => 'Enviado',
        4 => 'Entregado',
        5 => 'Cancelado'
    ];

    public function setUp(): array
    {
        $this->showCheckBox();
        return [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput()->includeViewOnTop('components.total-ventas'),
            Footer::make()
                ->showPerPage()
                ->showRecordCount()
        ];
    }

    public function datasource(): Builder
    {
        return Venta::query();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('nombre_cliente')
            ->add('dni')
            ->add('total')
            ->add('estado', fn(Venta $venta) => self::ESTADOS[$venta->estado] ?? 'Desconocido')
            ->add('created_at_formatted', fn(Venta $venta) => Carbon::parse($venta->created_at)->format('d/m/Y H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()
                ->searchable(),

            Column::make('Cliente', 'nombre_cliente')
                ->sortable()
                ->searchable(),

            Column::make('DNI', 'dni')
                ->sortable()
                ->searchable(),

            Column::make('Total', 'total')
                ->sortable()
                ->searchable(),

            Column::make('Est   ado', 'estado')
                ->sortable()
                ->searchable(),

            Column::make('Fecha', 'created_at_formatted')
                ->sortable(),

            Column::action('Acciones')
        ];
    }

    public function getEstadisticasVentas()
    {
        return [
            'total_general' => $this->datasource()->sum('total'),
            'total_por_estado' => collect(self::ESTADOS)->mapWithKeys(function ($estado, $key) {
                return [$key => $this->datasource()->where('estado', $key)->sum('total')];
            }),
            'cantidad_ventas' => $this->datasource()->count(),
            'promedio_venta' => $this->datasource()->avg('total')
        ];
    }

    public function getTotalVentas()
    {
        // Calculamos el total solo de ventas con estado 2 (Pagado)
        $totalPagado = $this->datasource()
            ->where('estado', 2)
            ->sum('total') ?? 0;

        // Calculamos el 10% del total
        $porcentaje = $totalPagado * 0.10;

        return [
            'total' => number_format($totalPagado, 2),
            'porcentaje' => number_format($porcentaje, 2)
        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('estado', 'estado')
                ->dataSource(collect(self::ESTADOS)->map(fn($label, $value) => ['id' => $value, 'label' => $label]))
                ->optionValue('id')
                ->optionLabel('label')
        ];
    }

    public function editEstado($id)
    {
        if (!Auth::user()->can('editar ' . $this->moduleName)) {
            return redirect()->route($this->moduleName);
        }
        $this->dispatch('editarVenta', ['venta' => Venta::findOrFail($id)]);
    }
    public function verProductos($id)
    {
        if (!Auth::user()->can('mostrar ' . $this->moduleName)) {
            return redirect()->route($this->moduleName);
        }
        $this->dispatch('show-productos', ['venta' => Venta::findOrFail($id)]);
    }
    public function deleteVenta($id)
    {
        if (!Auth::user()->can('eliminar ' . $this->moduleName)) {
            return redirect()->route($this->moduleName);
        }
        $this->dispatch('eliminarVenta', ['venta' => Venta::findOrFail($id)]);
    }

    public function actions(Venta $row): array
    {
        return [
            Button::add('ver')
                ->render(function ($row) {
                    return Blade::render(<<<HTML
                    @can('mostrar $this->moduleName')
                        <x-mini-button rounded icon="eye" flat gray interaction="positive" wire:click="verProductos('$row->id')" />
                    @endcan
                HTML);
                }),
            Button::add('edit')
                ->render(function ($row) {
                    return Blade::render(<<<HTML
                        @can('editar $this->moduleName')
                            <x-mini-button rounded icon="pencil" flat gray interaction="positive" wire:click="editEstado('$row->id')" />
                        @endcan
                    HTML);
                }),
            Button::add('delete')
                ->render(function ($row) {
                    return Blade::render(<<<HTML
                        @can('eliminar $this->moduleName')
                            <x-mini-button rounded icon="trash" flat gray interaction="negative" wire:click="deleteVenta('$row->id')" />
                        @endcan
                    HTML);
                }),
        ];
    }
}