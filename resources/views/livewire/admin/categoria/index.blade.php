<?php

use App\Livewire\Forms\Admin\CategoriaForm;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use App\Models\Categoria;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;
    public CategoriaForm $categoriaForm;
    public string $modelName = 'Categoría';
    public bool $modal = false;
    public bool $modalDelete = false;
    public bool $modalDetails = false;

    #[On('createCategoria')]
    public function open()
    {
        $this->resetForm();
        $this->modal = true;
    }

    #[On('editCategoria')]
    public function setCategoria(Categoria $categoria)
    {
        $this->resetForm();
        $this->categoriaForm->setCategoria($categoria);
        $this->modal = true;
    }

    #[On('deleteCategoria')]
    public function removeCategoria(Categoria $categoria)
    {
        $this->categoriaForm->setCategoria($categoria);
        $this->modalDelete = true;
    }

    public function delete()
    {
        $this->categoriaForm->delete();
        $this->dispatch('pg:eventRefresh-CategoriaTable');
        $this->modalDelete = false;
    }

    public function clear()
    {
        $this->resetForm();
    }

    public function save()
    {
        $this->categoriaForm->id
            ? $this->update()
            : $this->store();
    }

    public function validateForm()
    {
        $this->categoriaForm->validate();
    }

    public function store()
    {
        $this->validateForm();
        $this->categoriaForm->store();
        $this->resetForm();
        $this->dispatch('pg:eventRefresh-CategoriaTable');
        $this->modal = false;
    }

    public function update()
    {
        $this->validateForm();
        $this->categoriaForm->update();
        $this->dispatch('pg:eventRefresh-CategoriaTable');
        $this->modal = false;
    }

    public function resetForm()
    {
        $this->categoriaForm->resetValidation();
        $this->categoriaForm->reset();
    }

    public function check()
    {
        if (!Auth::user()->can('mostrar categorias')) {
            return redirect()->route('info');
        }
    }
};

?>

<div wire:poll="check">
    <livewire:tables.categoria-table />

    <!-- Modal para eliminar categoría -->
    <x-modal wire:model="modalDelete" width="sm">
        <x-card>
            <div class="flex flex-col justify-center items-center gap-4">
                <div class="bg-warning-400 dark:border-4 dark rounded-full p-4">
                    <x-phosphor.icons::regular.warning class="text-white w-16 h-16" />
                </div>
                <span class="text-center font-semibold text-xl">¿Desea eliminar la categoría?</span>
                <span class="text-center">Recuerde que se eliminará definitivamente</span>
                <div class="flex gap-2">
                    <x-button flat label="Cancelar" x-on:click="close" />
                    <x-button flat negative label="Eliminar" wire:click="delete" />
                </div>
            </div>
        </x-card>
    </x-modal>

    <!-- Modal para crear/editar categoría -->
    <x-modal-card title="{{($categoriaForm->id ? 'Editar' : 'Registrar') . ' ' . $modelName}}" wire:model="modal"
        width="sm">
        <div class="grid grid-cols-1 gap-4">

            <!-- Nombre de la Categoría -->
            <x-input label="Nombre" placeholder="Ingresar nombre" wire:model="categoriaForm.nombre" />

            <!-- Estado de la Categoría -->
            <x-select label="Estado" placeholder="Seleccionar estado" :options="[['id' => 'A', 'name' => 'Activo'], ['id' => 'I', 'name' => 'Inactivo']]" option-label="name" option-value="id"
                wire:model="categoriaForm.estado" />

            <!-- Descripción de la Categoría -->
            <x-textarea label="Descripción" placeholder="Ingresar descripción" wire:model="categoriaForm.descripcion" />
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