<?php

namespace App\Livewire\Forms\Admin;

use App\Models\Producto;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Form;
use Livewire\WithFileUploads;

class ProductoForm extends Form
{
    use WithFileUploads;
    public ?Producto $producto = null;
    #[Locked]
    public ?int $id = null;
    public ?int $categoria_id = null;
    public ?string $nombre = '';
    public ?string $descripcion = '';
    public $imagen;
    public ?string $marca = '';
    public ?int $cantidad_base = 0;
    public ?float $costo = 0.0;
    public ?float $precio = 0.0;
    public ?float $precio_oferta = 0.0;
    public function mount()
    {
        $this->producto = new Producto();
    }
    public function getProducto()
    {
        return $this->producto;
    }
    public function rules()
    {
        return [
            'categoria_id' => [
                'required',
                'integer',
                'exists:categorias,id',
            ],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'imagen' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:2048',
            ],
            'marca' => ['required', 'string', 'max:250'],
            'cantidad_base' => ['required', 'integer', 'min:1'],
            'costo' => ['required', 'numeric', 'min:1'],
            'precio' => ['required', 'numeric', 'min:1'],
            'precio_oferta' => ['nullable', 'numeric', 'min:1'],
        ];
    }
    public function setProducto(Producto $producto)
    {
        $this->producto = $producto;
        $this->id = $producto->id;
        $this->categoria_id = $producto->categoria_id;
        $this->nombre = $producto->nombre;
        $this->descripcion = $producto->descripcion;
        $this->imagen = $producto->imagen;
        $this->marca = $producto->marca;
        $this->cantidad_base = $producto->cantidad_base;
        $this->costo = $producto->costo;
        $this->precio = $producto->precio;
        $this->precio_oferta = $producto->precio_oferta;
    }
    public function store()
    {
        $producto = Producto::create($this->only([
            'categoria_id',
            'nombre',
            'descripcion',
            'imagen',
            'marca',
            'cantidad_base',
            'costo',
            'precio',
            'precio_oferta',
        ]));

        if ($this->imagen) {
            $imageName = $producto->id . '.' . $this->imagen->extension();
            $this->imagen->storeAs('productos', $imageName, 'public');
            $producto->update(['imagen' => 'productos/' . $imageName]);
        }
    }
    public function update()
    {
        // Verifica si se ha subido una nueva imagen
        if ($this->imagen) {
            // Elimina la imagen anterior si existe
            if ($this->producto->imagen && Storage::exists($this->producto->imagen)) {
                Storage::delete($this->producto->imagen);
            }

            // Genera un nuevo nombre para la imagen basado en el ID del producto
            $imageName = $this->producto->id . '.' . $this->imagen->extension();

            // Mueve la nueva imagen a la ubicación definitiva
            $imagePath = $this->imagen->storeAs('productos', $imageName, 'public');

            // Actualiza la ruta de la imagen en la base de datos
            $this->producto->update(['imagen' => $imagePath]);
        }
        $this->producto->update($this->only([
            'categoria_id',
            'nombre',
            'descripcion',
            'marca',
            'cantidad_base',
            'costo',
            'precio',
            'precio_oferta',
        ]));
    }
    public function delete()
    {
        $this->producto->delete();
    }
}
