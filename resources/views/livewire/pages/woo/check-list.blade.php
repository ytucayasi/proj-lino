<?php

use App\Models\Categoria;
use App\Models\Venta;
use App\Models\VentaProducto;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new 
#[Layout('layouts.woo')]
class extends Component {
    public $cartItems = [];
    public $cartCount = 0;
    public $subtotal = 0;
    public $tax = 0;
    public $total = 0;
    public $categories;
    
    public $direccion;
    public $dni;
    public $celular;
    public $nombre_cliente;
    public $email;
    
    const TAX_RATE = 0.18;
    
    public function mount()
    {
        $this->categories = Categoria::all();
        $this->cartItems = session('cart', []);
        $this->updateCartSummary();
    }

    #[On('cart-updated')] 
    #[On('header-updated')]
    public function refreshCart()
    {
        $this->cartItems = session('cart', []);
        $this->updateCartSummary();
    }

    public function removeFromCart($productoId)
    {
        $cartItems = session('cart', []);
        unset($cartItems[$productoId]);
        session(['cart' => $cartItems]);
        $this->cartItems = $cartItems;
        $this->updateCartSummary();
        $this->dispatch('cart-updated');
    }

    public function updateQuantity($productoId, $change)
    {
        $cartItems = session('cart', []);
    
        if (!isset($cartItems[$productoId])) {
            return;
        }
    
        $newQuantity = $cartItems[$productoId]['quantity'] + $change;
        
        // Si el nuevo quantity es mayor que cantidad_base, no permitir el cambio
        if ($newQuantity > $cartItems[$productoId]['cantidad_base']) {
            $this->dispatch('notify', [
                'message' => 'No hay suficiente stock disponible',
                'type' => 'error'
            ]);
            return;
        }
    
        if ($newQuantity < 1) {
            unset($cartItems[$productoId]);
        } else {
            $cartItems[$productoId]['quantity'] = $newQuantity;
        }
    
        session(['cart' => $cartItems]);
        $this->cartItems = $cartItems;
        $this->updateCartSummary();
        $this->dispatch('cart-updated');
    }

    public function updated()
    {
        $this->dispatch('cart-updated');
    }

    public function procesarPago()
    {
        $this->validate([
            'direccion' => 'required|string|min:10',
            'dni' => 'required|string|size:8',
            'celular' => 'required|string|size:9',
            'nombre_cliente' => 'required|string|min:3',
            'email' => 'nullable|email'
        ]);

        try {
            $venta = Venta::create([
                'direccion' => $this->direccion,
                'dni' => $this->dni,
                'celular' => $this->celular,
                'nombre_cliente' => $this->nombre_cliente,
                'email' => $this->email,
                'estado' => '1',
                'subtotal' => $this->subtotal,
                'tax' => $this->tax,
                'total' => $this->total
            ]);

            foreach ($this->cartItems as $item) {
                VentaProducto::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $item['id'],
                    'cantidad' => $item['quantity'],
                    'precio_unitario' => $item['precio'],
                    'subtotal' => $item['precio'] * $item['quantity']
                ]);
            }

            session()->forget('cart');
            $this->cartItems = [];
            $this->updateCartSummary();
            $this->reset(['direccion', 'dni', 'celular', 'nombre_cliente', 'email']);
            
            // Dispatch evento para actualizar el header
            $this->dispatch('cart-updated');

            session()->flash('message', 'Compra procesada exitosamente');
            return redirect()->route('woo.compras');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al procesar la compra');
        }
    }

    private function updateCartSummary()
    {
        $this->cartCount = count($this->cartItems);
        $this->subtotal = collect($this->cartItems)->sum(function ($item) {
            return $item['precio'] * $item['quantity'];
        });
        $this->tax = round($this->subtotal * self::TAX_RATE, 2);
        $this->total = $this->subtotal + $this->tax;
    }
}; ?>

<div>
    <!-- start page title -->
    <section class="page-title-center-alignment cover-background top-space-padding"
        style="background-image: url(images/demo-decor-store-title-bg.jpg)">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center position-relative page-title-extra-large">
                    <h1 class="alt-font d-inline-block fw-700 ls-minus-05px text-base-color mb-10px mt-3 md-mt-50px">
                        Carrito de Compras</h1>
                </div>
                <div class="col-12 breadcrumb breadcrumb-style-01 d-flex justify-content-center">
                    <ul>
                        <li><a href="{{ route('woo.inicio') }}">Inicio</a></li>
                        <li>Carrito</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!-- end page title -->
    
    <!-- start section -->
    <section>
        <div class="container">
            <div class="row align-items-start">
                <div class="col-lg-8 pe-50px md-pe-15px md-mb-50px xs-mb-35px">
                    <div class="row align-items-center">
                        <div class="col-12">
                            <table class="table cart-products">
                                <thead>
                                    <tr>
                                        <th scope="col"></th>
                                        <th scope="col" class="alt-font fw-600">Producto</th>
                                        <th scope="col"></th>
                                        <th scope="col" class="alt-font fw-600">Precio</th>
                                        <th scope="col" class="alt-font fw-600">Cantidad</th>
                                        <th scope="col" class="alt-font fw-600">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($cartItems as $item)
                                        <tr wire:key="cart-row-{{ $item['id'] }}">
                                            <td class="product-remove">
                                                <a href="#" wire:click.prevent="removeFromCart({{ $item['id'] }})" class="fs-20 fw-500">×</a>
                                            </td>
                                            <td class="product-thumbnail">
                                                <a href="#">
                                                    <img class="cart-product-image" src="{{ asset('storage/' . $item['imagen']) }}" alt="{{ $item['nombre'] }}">
                                                </a>
                                            </td>
                                            <td class="product-name">
                                                <a href="#" class="text-dark-gray fw-500 d-block lh-initial">{{ $item['nombre'] }}</a>
                                            </td>
                                            <td class="product-price" data-title="Precio">S/{{ number_format($item['precio'], 2) }}</td>
                                            <td class="product-quantity" data-title="Cantidad">
                                                <div class="quantity">
                                                    <button type="button" class="qty-minus" wire:click="updateQuantity({{ $item['id'] }}, -1)">-</button>
                                                    <input class="qty-text" type="text" value="{{ $item['quantity'] }}">
                                                    <button type="button" class="qty-plus" wire:click="updateQuantity({{ $item['id'] }}, 1)">+</button>
                                                </div>
                                            </td>
                                            <td class="product-subtotal" data-title="Total">S/{{ number_format($item['precio'] * $item['quantity'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <p>Tu carrito está vacío</p>
                                                <a href="{{ route('woo.compras') }}" class="btn btn-base-color btn-medium">Ir a comprar</a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="bg-very-light-gray border-radius-6px p-50px xl-p-30px lg-p-25px">
                        <span class="fs-26 alt-font fw-600 text-dark-gray mb-5px d-block">Total del Carrito</span>
                        <form wire:submit.prevent="procesarPago">
                            <table class="w-100 total-price-table">
                                <tbody>
                                    <tr>
                                        <th class="w-45 fw-600 text-dark-gray alt-font">Subtotal</th>
                                        <td class="text-dark-gray fw-600">S/{{ number_format($subtotal, 2) }}</td>
                                    </tr>
                                    <tr class="calculate-shipping">
                                        <th colspan="2" class="fw-500">
                                            <div class="address-block">
                                                <div class="mt-15px">
                                                    <input type="text" 
                                                        wire:model="nombre_cliente"
                                                        class="input-small border-radius-4px mb-15px @error('nombre_cliente') border-danger @enderror" 
                                                        placeholder="Nombre completo">
                                                    @error('nombre_cliente')
                                                        <div class="text-danger fs-12">{{ $message }}</div>
                                                    @enderror

                                                    <input type="email" 
                                                        wire:model="email"
                                                        class="input-small border-radius-4px mb-15px @error('email') border-danger @enderror" 
                                                        placeholder="Email (opcional)">
                                                    @error('email')
                                                        <div class="text-danger fs-12">{{ $message }}</div>
                                                    @enderror

                                                    <input type="text" 
                                                        wire:model="direccion"
                                                        class="input-small border-radius-4px mb-15px @error('direccion') border-danger @enderror" 
                                                        placeholder="Dirección">
                                                    @error('direccion')
                                                        <div class="text-danger fs-12">{{ $message }}</div>
                                                    @enderror

                                                    <input type="text" 
                                                        wire:model="dni"
                                                        class="input-small border-radius-4px mb-15px @error('dni') border-danger @enderror" 
                                                        placeholder="DNI"
                                                        maxlength="8">
                                                    @error('dni')
                                                        <div class="text-danger fs-12">{{ $message }}</div>
                                                    @enderror

                                                    <input type="text" 
                                                        wire:model="celular"
                                                        class="input-small border-radius-4px mb-15px @error('celular') border-danger @enderror" 
                                                        placeholder="Número de Celular"
                                                        maxlength="9">
                                                    @error('celular')
                                                        <div class="text-danger fs-12">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </th>
                                    </tr>
                                    <tr class="total-amount">
                                        <th class="fw-600 text-dark-gray alt-font pb-0">Total</th>
                                        <td class="pb-0" data-title="Total">
                                            <h6 class="d-block fw-700 mb-0 text-dark-gray alt-font">S/{{ number_format($subtotal, 2) }}</h6>
                                            <!-- <h6 class="d-block fw-700 mb-0 text-dark-gray alt-font">S/{{ number_format($total, 2) }}</h6> -->
                                            <!-- <span class="fs-14">(Incluye IGV S/{{ number_format($tax, 2) }})</span> -->
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            @if(count($cartItems) > 0)
                                <button type="submit" class="btn btn-base-color btn-extra-large btn-switch-text btn-round-edge btn-box-shadow w-100 text-transform-none mt-25px">
                                    <span>
                                        <span class="btn-double-text" data-text="Proceder al pago">Proceder al pago</span>
                                    </span>
                                </button>
                            @endif

                            <!-- Mensajes de éxito o error -->
                            @if (session()->has('message'))
                                <div class="alert alert-success text-center mt-3">
                                    {{ session('message') }}
                                </div>
                            @endif

                            @if (session()->has('error'))
                                <div class="alert alert-danger text-center mt-3">
                                    {{ session('error') }}
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end section -->
</div>