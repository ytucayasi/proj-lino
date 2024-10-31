<?php

use App\Models\Venta;
use Livewire\Attributes\On;
use Livewire\Volt\Compilers\Mount;
use Livewire\Volt\Component;

new class extends Component {
    public $modalDelete = false;
    public $modalEstado = false;
    public $ventaId = null;
    public $estado = null;
    public $ventaActual = null;
    public $productos = [];
    public $modalProductos = false;

    #[On('editarVenta')]
    public function editEstado(Venta $venta)
    {
        $this->ventaId = $venta->id;
        $this->estado = $venta->estado;
        $this->modalEstado = true;
    }

    public function getEstadosOptionsProperty()
    {
        return [
            ['name' => 'Pendiente', 'id' => 1],
            ['name' => 'Pagado', 'id' => 2],
            ['name' => 'Enviado', 'id' => 3],
            ['name' => 'Entregado', 'id' => 4],
            ['name' => 'Cancelado', 'id' => 5],
        ];
    }

    #[On('eliminarVenta')]
    public function showDelete(Venta $venta)
    {
        $this->ventaId = $venta->id;
        $this->modalDelete = true;
    }

    public function updateEstado()
    {
        $venta = Venta::find($this->ventaId);
        $venta->update(['estado' => $this->estado]);

        $this->modalEstado = false;
        $this->dispatch('pg:eventRefresh-VentaTable');
        $this->dispatch('notify', [
            'status' => 'success',
            'message' => 'Estado actualizado correctamente'
        ]);
    }

    public function delete()
    {
        Venta::destroy($this->ventaId);
        $this->modalDelete = false;
        $this->dispatch('pg:eventRefresh-VentaTable');
        $this->dispatch('notify', [
            'status' => 'success',
            'message' => 'Venta eliminada correctamente'
        ]);
    }

    #[On('show-productos')]
    public function showProductos(Venta $venta)
    {
        $this->ventaActual = Venta::with('productos.producto')->find($venta->id);
        $this->productos = $this->ventaActual->productos;
        $this->modalProductos = true;
    }
}; ?>

<div>
    <div class="mb-4">
        <livewire:tables.venta-table />
    </div>

    {{-- Modal Eliminar --}}
    <x-modal wire:model="modalDelete">
        <x-card>
            <div class="flex flex-col justify-center items-center gap-4">
                <div class="bg-red-100 rounded-full p-4">
                    <x-phosphor.icons::regular.warning class="w-16 h-16 text-red-500" />
                </div>
                <span class="text-center font-semibold text-xl">¿Desea eliminar la venta?</span>
                <span class="text-center text-gray-500">Esta acción no se puede deshacer</span>
                <div class="flex gap-2">
                    <x-button flat label="Cancelar" wire:click="$set('modalDelete', false)" />
                    <x-button negative label="Eliminar" wire:click="delete" />
                </div>
            </div>
        </x-card>
    </x-modal>

    {{-- Modal Estado --}}
    <x-modal wire:model="modalEstado">
        <x-card title="Actualizar Estado">
            <div class="mb-4">
                <x-select label="Estado" wire:model="estado" :options="[['name' => 'Pendiente', 'id' => '1'], ['name' => 'Pagado', 'id' => '2'], ['name' => 'Enviado', 'id' => '3'], ['name' => 'Entregado', 'id' => '4'], ['name' => 'Cancelado', 'id' => '5']]" option-label="name" option-value="id" />
            </div>

            <x-slot name="footer">
                <div class="flex justify-end gap-2">
                    <x-button flat label="Cancelar" wire:click="$set('modalEstado', false)" />
                    <x-button primary label="Actualizar" wire:click="updateEstado" />
                </div>
            </x-slot>
        </x-card>
    </x-modal>

    <x-modal wire:model="modalProductos" max-width="2xl">
        <x-card title="Detalle de Productos">
            @if($ventaActual)
                <div class="mb-4">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-600">Cliente</p>
                            <p>{{ $ventaActual->nombre_cliente }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-600">DNI</p>
                            <p>{{ $ventaActual->dni }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-600">Fecha</p>
                            <p>{{ $ventaActual->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-600">Estado</p>
                            <p>{{ $this->estadosOptions[($ventaActual->estado - 1)]['name'] }}</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Producto
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cantidad
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Precio Unit.
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Subtotal
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($productos as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full object-cover"
                                                        src="{{ asset('storage/' . $item->producto->imagen) }}"
                                                        alt="{{ $item->producto->nombre }}">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $item->producto->nombre }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $item->cantidad }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">S/ {{ number_format($item->precio_unitario, 2) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">S/ {{ number_format($item->subtotal, 2) }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right font-medium">Subtotal:</td>
                                    <td class="px-6 py-4">S/ {{ number_format($ventaActual->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right font-medium">IGV (18%):</td>
                                    <td class="px-6 py-4">S/ {{ number_format($ventaActual->tax, 2) }}</td>
                                </tr>
                                <tr class="font-bold">
                                    <td colspan="3" class="px-6 py-4 text-right">Total:</td>
                                    <td class="px-6 py-4">S/ {{ number_format($ventaActual->total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif

            <x-slot name="footer">
                <div class="flex justify-end">
                    <x-button flat label="Cerrar" wire:click="$set('modalProductos', false)" />
                </div>
            </x-slot>
        </x-card>
    </x-modal>

    @script
    <script>
        Livewire.on('notify', ({ status, message }) => {
            window.$wireui.notify({
                title: status === 'success' ? 'Éxito' : 'Error',
                description: message,
                icon: status === 'success' ? 'success' : 'error'
            });
        });
    </script>
    @endscript
</div>