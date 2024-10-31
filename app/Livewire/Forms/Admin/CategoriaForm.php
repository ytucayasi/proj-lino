<?php

namespace App\Livewire\Forms\Admin;

use App\Models\Categoria;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Form;

class CategoriaForm extends Form
{
    public ?Categoria $categoria = null;
    #[Locked]
    public ?int $id = null;
    public ?string $nombre = '';
    public ?string $estado = 'A';
    public ?string $descripcion = '';

    public function mount()
    {
        $this->categoria = new Categoria();
    }

    public function getCategoria()
    {
        return $this->categoria;
    }

    public function rules()
    {
        return [
            'nombre' => ['required', 'string', 'max:150'],
            'estado' => ['required', 'string', Rule::in(['A', 'I'])],
            'descripcion' => ['nullable', 'string'],
        ];
    }

    public function setCategoria(Categoria $categoria)
    {
        $this->categoria = $categoria;
        $this->id = $categoria->id;
        $this->nombre = $categoria->nombre;
        $this->estado = $categoria->estado;
        $this->descripcion = $categoria->descripcion;
    }

    public function store()
    {
        $this->validate();
        Categoria::create($this->only([
            'nombre',
            'estado',
            'descripcion'
        ]));
    }

    public function update()
    {
        $this->validate();
        $this->categoria->update($this->only([
            'nombre',
            'estado',
            'descripcion'
        ]));
    }

    public function delete()
    {
        $this->categoria->delete();
    }
}