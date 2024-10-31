<?php

use App\Models\Categoria;
use App\Models\Producto;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new 
#[Layout('layouts.woo')]
class extends Component {
    use WithPagination;
    

    public $categorias;
    protected $paginationTheme = 'custom-pagination';

    public function mount() {

        $this->categorias = Categoria::all();
    }

    public function with(): array
    {
        return [
            'productos' => Producto::paginate(10)
        ];
    }

    public function addToCart($productoId)
    {
        $producto = Producto::find($productoId);
        
        if (!$producto) return;

        $cartItems = session('cart', []);

        if (isset($cartItems[$productoId])) {
            $cartItems[$productoId]['quantity']++;
        } else {
            $cartItems[$productoId] = [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio' => $producto->precio_oferta ?: $producto->precio,
                'imagen' => $producto->imagen,
                'quantity' => 1,
                'cantidad_base' => $producto->cantidad_base,
            ];
        }

        session(['cart' => $cartItems]);
        return redirect(request()->header('Referer'));
    }
}; ?>

<div>
    <!-- start page title -->
    <section class="page-title-center-alignment cover-background top-space-padding" style="background-image: url(images/demo-decor-store-title-bg.jpg)">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center position-relative page-title-extra-large">
                    <h1 class="alt-font d-inline-block fw-700 ls-minus-05px text-base-color mb-10px mt-3 md-mt-50px">Productos</h1>
                </div>
                <div class="col-12 breadcrumb breadcrumb-style-01 d-flex justify-content-center">
                    <ul>
                        <li><a href="demo-decor-store.html">Hogar</a></li> 
                        <li>productos</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!-- end page title -->
    <!-- start section -->
    <section wire:ignore.self class="ps-6 pe-6 lg-ps-3 lg-pe-3 sm-ps-0 sm-pe-0">
        <div class="container-fluid">
            <div class="row flex-row-reverse"> 
                <div class="col-xxl-10 col-lg-9 ps-5 md-ps-15px md-mb-60px">
                    <div wire:ignore class="toolbar-wrapper border-bottom border-color-extra-medium-gray d-flex flex-column flex-sm-row align-items-center w-100 mb-40px md-mb-30px pb-15px" data-anime='{ "translateY": [0, 0], "opacity": [0,1], "duration": 600, "delay":50, "staggervalue": 150, "easing": "easeOutQuad" }'>
                        <div class="ms-20px xs-ms-0">Showing 1–12 of 48 results</div>
                        <div class="mx-auto me-sm-0">
                            <select class="form-select border-0 background-position-right" aria-label="Default sorting">
                                <option selected>Default sorting</option>
                                <option value="1">Sort by popularity</option>
                                <option value="2">Sort by average rating</option>
                                <option value="3">Sort by latest</option>
                                <option value="4">Sort by price: low to high</option>
                                <option value="5">Sort by price: high to low</option>
                            </select>
                        </div>
                    </div>
                    <ul wire:ignore.self class="shop-boxed shop-wrapper grid-loading grid grid-4col xxl-grid-4col xl-grid-3col lg-grid-3col md-grid-2col sm-grid-2col xs-grid-1col gutter-large text-center" data-anime='{ "el": "childs", "translateY": [50, 0], "opacity": [0,1], "duration": 600, "delay":100, "staggervalue": 150, "easing": "easeOutQuad" }'>
                        <li class="grid-sizer"></li>
                        <!-- start shop item -->
                        @forelse($productos as $producto)
                            <li class="grid-item" wire:key="producto-{{ $producto->id }}">
                                <div class="shop-box pb-25px">
                                    <div class="shop-image">
                                        <a href="demo-decor-store-single-product.html">
                                            <img src="{{ asset('storage/' . $producto->imagen) }}" alt="Normal" />
                                            <span class="lable new">New</span>
                                            <div class="product-overlay bg-gradient-extra-midium-gray-transparent"></div> 
                                        </a>
                                        <div class="shop-hover d-flex justify-content-center">
                                            <a wire:click="addToCart({{ $producto->id }})" href="#" class="bg-white w-45px h-45px text-dark-gray d-flex flex-column align-items-center justify-content-center rounded-circle ms-5px me-5px box-shadow-medium-bottom" data-bs-toggle="tooltip" data-bs-placement="top" title="Añadir a carrito"><i class="feather icon-feather-shopping-bag fs-15"></i></a>
                                            <a href="#" class="bg-white w-45px h-45px text-dark-gray d-flex flex-column align-items-center justify-content-center rounded-circle ms-5px me-5px box-shadow-medium-bottom" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver"><i class="feather icon-feather-eye fs-15"></i></a>
                                        </div>
                                    </div>
                                    <div class="shop-footer text-center pt-20px">
                                        <a href="demo-decor-store-single-product.html" class="text-dark-gray fs-17 alt-font fw-600">{{ $producto->nombre }}</a>
                                        <div class="fw-500 fs-15 lh-normal">
                                        @if($producto->precio_oferta)
                                            <del>{{ $producto->precio }} Soles</del>
                                            {{ $producto->precio_oferta }} Soles
                                        @else
                                            {{ $producto->precio }} Soles
                                        @endif
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li>Aún Nada</li>
                        @endforelse
                        <!-- end shop item -->
                    </ul>
                    <div wire:ignore.self>
                        {{ $productos->links() }}
                    </div>
                </div>
                <div class="col-xxl-2 col-lg-3 shop-sidebar">
                    <div class="mb-30px">
                        <span class="alt-font fw-600 fs-17 text-dark-gray d-block mb-10px">Filtrar por categoría</span>
                        <ul class="fs-15 shop-filter category-filter">
                            @forelse($categorias as $categoria)
                                <li><a href="#"><span class="product-cb product-category-cb"></span>{{ $categoria->nombre }}</a><span class="item-qty">{{ $categoria->productos->count() }}</span></li>
                            @empty
                                <li>Aún Nada</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end section -->
</div>
<script>
    document.addEventListener('livewire:init', () => {
        // Reinicializar animaciones después de actualizaciones de Livewire
        Livewire.on('productsUpdated', () => {
            if (typeof anime !== 'undefined') {
                // Reinicializar las animaciones
                anime.timeline({}).add({
                    targets: '.grid-item',
                    translateY: [50, 0],
                    opacity: [0, 1],
                    duration: 600,
                    delay: anime.stagger(150),
                    easing: 'easeOutQuad'
                });
            }
        });
    });

    // Ocultar loader cuando la página está completamente cargada
    window.addEventListener('load', () => {
        document.querySelector('.grid-loading').classList.remove('grid-loading');
    });
</script>