<?php
use App\Models\Categoria;
use App\Models\Producto;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {
    public $cartItems = [];
    public $cartCount = 0;
    public $subtotal = 0;
    public $tax = 0;
    public $total = 0;
    public $categories;

    const TAX_RATE = 0.18;

    public function mount()
    {
        $this->categories = Categoria::all();
        $this->cartItems = session('cart', []);
        $this->updateCartSummary();
    }

    #[On('cart-updated')]
    public function refreshCart()
    {
        $this->cartItems = session('cart', []);
        $this->updateCartSummary();
        $this->dispatch('header-updated');
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

        if (!isset($cartItems[$productoId]))
            return;

        $newQuantity = $cartItems[$productoId]['quantity'] + $change;

        if ($cartItems[$productoId]['cantidad_base'] <= $newQuantity) {
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

<header class="header-with-topbar">

    <!-- start navigation -->
    <nav class="navbar navbar-expand-lg header-light bg-transparent disable-fixed" data-header-hover="light">
        <div class="container-fluid">
            <div class="col-auto">
                <a class="navbar-brand" href="demo-decor-store.html">
                    <img src="images/demo-decor-store-logo-black.png"
                        data-at2x="images/demo-decor-store-logo-black@2x.png" alt="" class="default-logo">
                    <img src="images/demo-decor-store-logo-black.png"
                        data-at2x="images/demo-decor-store-logo-black@2x.png" alt="" class="alt-logo">
                    <img src="images/demo-decor-store-logo-black.png"
                        data-at2x="images/demo-decor-store-logo-black@2x.png" alt="" class="mobile-logo">
                </a>
            </div>
            <div class="col-auto menu-order position-static xs-ps-0">
                <button class="navbar-toggler float-start" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNav" aria-controls="navbarNav" aria-label="Toggle navigation">
                    <span class="navbar-toggler-line"></span>
                    <span class="navbar-toggler-line"></span>
                    <span class="navbar-toggler-line"></span>
                    <span class="navbar-toggler-line"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                    <ul class="navbar-nav alt-font">
                        <li class="nav-item"><a href="{{ route('woo.inicio') }}" class="nav-link">Inicio</a></li>
                        <li class="nav-item dropdown simple-dropdown">
                            <a href="{{ route('woo.compras') }}" class="nav-link">Comprar</a>
                            <i class="fa-solid fa-angle-down dropdown-toggle" id="navbarDropdownMenuLink3" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false"></i>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink3">
                                @forelse ($categories as $categoria)
                                    <li><a href="#">{{ $categoria->nombre }}</a></li>
                                @empty
                                    <li>Sin Categorías</li>
                                @endforelse
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-auto ms-auto">
                <div class="header-icon">
                    <div class="header-search-icon icon">
                        <a href="javascript:void(0)" class="search-form-icon header-search-form"><i
                                class="feather icon-feather-search"></i></a>
                        <div class="search-form-wrapper">
                            <button title="Close" type="button" class="search-close alt-font">×</button>
                            <form id="search-form" role="search" method="get" class="search-form bg-white"
                                action="search-result.html">
                                <div class="search-form-box">
                                    <h2 class="text-dark-gray text-center mb-7 alt-font fw-700 ls-minus-1px">
                                        ¿Que quieres buscar?</h2>
                                    <input class="search-input alt-font" id="search-form-input5e219ef164995"
                                        placeholder="Ingrese el nombre del producto..." name="s" value="" type="text"
                                        autocomplete="off">
                                    <button type="submit" class="search-button">
                                        <i class="feather icon-feather-search" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="header-cart-icon icon">
                        <div class="header-cart dropdown w-100">
                            <a href="javascript:void(0);" wire:key="cart-icon">
                                <i class="feather icon-feather-shopping-bag"></i>
                                <span class="cart-count alt-font" wire:key="cart-count">{{ $cartCount }}</span>
                            </a>
                            <ul class="cart-item-list">
                                @forelse($cartItems as $item)
                                    <li class="cart-item align-items-center" wire:key="header-item-{{ $item['id'] }}">
                                        <a href="#" wire:click.prevent="removeFromCart({{ $item['id'] }})"
                                            class="alt-font close">×</a>
                                        <div class="product-image">
                                            <a href="#">
                                                <img src="{{ asset('storage/' . $item['imagen']) }}" class="cart-thumb"
                                                    alt="{{ $item['nombre'] }}">
                                            </a>
                                        </div>
                                        <div class="product-detail alt-font fw-600">
                                            <a href="#">{{ $item['nombre'] }}</a>
                                            <div class="quantity-controls">
                                                <button wire:click.prevent="updateQuantity({{ $item['id'] }}, -1)"
                                                    class="btn btn-sm">-</button>
                                                <span class="quantity"
                                                    wire:key="header-quantity-{{ $item['id'] }}">{{ $item['quantity'] }}</span>
                                                <button wire:click.prevent="updateQuantity({{ $item['id'] }}, 1)"
                                                    class="btn btn-sm">+</button>
                                            </div>
                                            <span class="item-ammount fw-500" wire:key="header-amount-{{ $item['id'] }}">
                                                {{ $item['quantity'] }} x S/{{ number_format($item['precio'], 2) }}
                                            </span>
                                        </div>
                                    </li>
                                @empty
                                    <li class="cart-item">
                                        <div class="text-center p-4">El carrito está vacío</div>
                                    </li>
                                @endforelse

                                @if($cartCount > 0)
                                    <li class="cart-total">
                                        <div class="alt-font mb-15px">
                                            <div class="d-flex justify-content-between mb-2" wire:key="header-subtotal">
                                                <span class="w-50 fw-500">Subtotal:</span>
                                                <span class="fw-700">S/ {{ number_format($subtotal, 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2" wire:key="header-tax">
                                                <span class="w-50 fw-500">IGV (18%):</span>
                                                <span class="fw-700">S/ {{ number_format($tax, 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between" wire:key="header-total">
                                                <span class="w-50 fw-600">Total:</span>
                                                <span class="fw-700">S/ {{ number_format($total, 2) }}</span>
                                            </div>
                                        </div>
                                        <a href="{{ route('woo.checklist') }}"
                                            class="btn btn-large btn-base-color btn-box-shadow btn-round-edge w-100">Pagar</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <div class="widget-text ms-25px md-ms-20px alt-font">
                        <a href="{{ route('login') }}" class="fs-17 fw-600"><i
                                class="feather icon-feather-user d-inline-block d-xl-none"></i><span
                                class="d-none d-xl-inline-block">Ingresar</span></a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>