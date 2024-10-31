<?php

use App\Livewire\Forms\Admin\ProductoForm;
use App\Models\Categoria;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use App\Models\Producto;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;
    public ProductoForm $productoForm;
    public string $modelName = 'Producto';
    public bool $modal = false;
    public bool $modalDelete = false;
    public bool $modalDetails = false;
    public $categorias = [];
    #[Validate('image|max:1024')]
    public $imagen_prev;

    public function mount()
    {
        $this->categorias = Categoria::all()->map(function ($categoria) {
            return [
                'id' => $categoria->id,
                'name' => $categoria->nombre,
            ];
        });
    }

    #[On('createProducto')]
    public function open()
    {
        $this->resetForm();
        $this->modal = true;
    }

    #[On('editProducto')]
    public function setProducto(Producto $producto)
    {
        $this->resetForm();
        $this->productoForm->setProducto($producto);
        $this->modal = true;
    }

    #[On('deleteProducto')]
    public function removeProducto(Producto $producto)
    {
        $this->productoForm->setProducto($producto);
        $this->modalDelete = true;
    }

    public function delete()
    {
        $this->productoForm->delete();
        $this->dispatch('pg:eventRefresh-ProductoTable');
        $this->modalDelete = false;
    }

    public function clear()
    {
        $this->resetForm();
    }

    public function save()
    {
        $this->productoForm->id
            ? $this->update()
            : $this->store();
    }

    public function validateForm()
    {
        $this->productoForm->validate();
    }

    public function store()
    {
        $this->validateForm();
        $this->productoForm->store();
        $this->resetForm();
        $this->dispatch('pg:eventRefresh-ProductoTable');
        $this->modal = false;
    }

    public function update()
    {
        $this->validateForm();
        $this->productoForm->update();
        $this->dispatch('pg:eventRefresh-ProductoTable');
        $this->modal = false;
    }

    public function resetForm()
    {
        $this->productoForm->resetValidation();
        $this->productoForm->reset();
    }

    public function check()
    {
        if (!Auth::user()->can('mostrar productos')) {
            return redirect()->route('info');
        }
    }
};

?>

<div wire:poll="check">
    <livewire:tables.producto-table />

    <!-- Modal para eliminar producto -->
    <x-modal wire:model="modalDelete" width="sm">
        <x-card>
            <div class="flex flex-col justify-center items-center gap-4">
                <div class="bg-warning-400 dark:border-4 dark rounded-full p-4">
                    <x-phosphor.icons::regular.warning class="text-white w-16 h-16" />
                </div>
                <span class="text-center font-semibold text-xl">¿Desea eliminar el producto?</span>
                <span class="text-center">Recuerde que se eliminará definitivamente</span>
                <div class="flex gap-2">
                    <x-button flat label="Cancelar" x-on:click="close" />
                    <x-button flat negative label="Eliminar" wire:click="delete" />
                </div>
            </div>
        </x-card>
    </x-modal>

    <!-- Modal para crear/editar producto -->
    <x-modal-card title="{{($productoForm->id ? 'Editar' : 'Registrar') . ' ' . $modelName}}" wire:model="modal"
        width="lg">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Nombre del Producto -->
            <x-input label="Nombre" placeholder="Ingresar nombre" wire:model="productoForm.nombre" class="w-10" />

            <!-- Marca del Producto -->
            <x-input label="Marca" placeholder="Ingresar marca" wire:model="productoForm.marca" />

            <!-- Cantidad Base del Producto -->
            <x-input label="Cantidad Base" type="number" placeholder="Ingresar cantidad base"
                wire:model="productoForm.cantidad_base" />

            <!-- Costo del Producto -->
            <x-input label="Precio Compra" type="number" step="0.01" placeholder="Ingresar costo"
                wire:model="productoForm.costo" />

            <!-- Precio del Producto -->
            <x-input label="Precio Venta" type="number" step="0.01" placeholder="Ingresar precio"
                wire:model="productoForm.precio" />

            <!-- Precio de Oferta del Producto -->
            <x-input label="Precio de Descuento" type="number" step="0.01" placeholder="Ingresar precio de oferta"
                wire:model="productoForm.precio_oferta" />

            <!-- Categoria -->
            <x-select class="col-span-2" label="Categoría" placeholder="Seleccionar" :options="$this->categorias"
                option-label="name" option-value="id" wire:model="productoForm.categoria_id" />

            <x-input class="col-span-2" type="file" label="Imagen" placeholder="Ingresar"
                wire:model="productoForm.imagen" />

            <!-- Verificación de imagen existente o temporal -->
            @if(is_string($productoForm->imagen))
                <div class="col-span-2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Imagen Actual:</label>
                    <img src="{{ asset('storage/' . $productoForm->imagen) }}" alt="Imagen del Producto"
                        class="w-32 h-32 object-cover">
                </div>
            @endif

            <!-- Descripción del Producto -->
            <x-textarea class="col-span-2" label="Descripción" placeholder="Ingresar descripción"
                wire:model="productoForm.descripcion" />
        </div>
        <x-slot name="footer" class="flex justify-between items-center gap-x-4">

            <!-- Botón de Limpiar -->
            <x-mini-button flat negative rounded icon="trash" wire:click="clear" />
            <div class="flex gap-x-2">

                <!-- Botón de Cancelar -->
                <x-button flat label="Cancelar" x-on:click="close" />

                <!-- Botón de Guardar -->
                <x-button flat positive label="Guardar" wire:click="save" />
            </div>
        </x-slot>
    </x-modal-card>

    @script
    <script>
        Livewire.on('refresh-page', () => {
            location.reload();
        });
    </script>
    @endscript
</div>