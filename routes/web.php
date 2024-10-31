<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

/* User */
Volt::route('dashboard', 'dashboard')->middleware(['auth'])->name('dashboard');
Volt::route('info', 'info')->middleware(['auth'])->name('info');

/* Admin */
Volt::route('usuarios', 'admin.user.index')->middleware(['auth', 'permission:mostrar usuarios'])->name('usuarios');
Volt::route('roles', 'admin.role.index')->middleware(['auth', 'permission:mostrar roles'])->name('roles');
Volt::route('permisos', 'admin.permission.index')->middleware(['auth', 'permission:mostrar permisos'])->name('permisos');
Volt::route('productos', 'admin.producto.index')->middleware(['auth', 'permission:mostrar productos'])->name('productos');
Volt::route('categorias', 'admin.categoria.index')->middleware(['auth', 'permission:mostrar categorias'])->name('categorias');
Volt::route('ventas', 'admin.venta.index')->middleware(['auth', 'permission:mostrar ventas'])->name('ventas');

/* Woo */
Volt::route('/', 'pages.woo.inicio')->name('woo.inicio');
Volt::route('compras', 'pages.woo.compras')->name('woo.compras');
Volt::route('check-list', 'pages.woo.check-list')->name('woo.checklist');

require __DIR__ . '/auth.php';
