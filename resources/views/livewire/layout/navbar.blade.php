<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/');
    }
}; ?>
<nav class="max-w-7xl mx-auto flex justify-between items-center p-4 2xl:px-0 h-fit">

    <!-- Rigth Menu -->
    <div class="flex space-x-2 items-center h-fit">

        <!-- Brand -->
        <div class="rounded-full w-8 h-8 flex items-center justify-center">
            <span class="dark:text-white select-none">yt</span>
        </div>

        <!-- Manu Bar -->
        <ul class="flex space-x-2 items-center h-fit">
            @foreach (config('navbar') as $item)
                <li class="flex">
                    <a href="{{ $item['href'] }}" class=" p-2 rounded-md hover:text-primary-500 dark:hover:text-primary-300"
                        wire:navigate>
                        {{ $item['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Left Menu -->
    <div class="flex items-center space-x-2">

        <!-- Actions -->
        <div class="flex items-center">
            <button class="p-2 rounded-md">
                <x-icon name="sun" class="w-5 h-5 hover:text-secondary-500 dark:hover:text-yellow-500" solid mini
                    x-show="darkMode" @click="toggleTheme()" />
                <x-icon name="moon" class="w-5 h-5 hover:text-info-700" solid mini x-show="!darkMode"
                    @click="toggleTheme()" />
            </button>
        </div>

        <!-- Dropdown -->
        <x-dropdown position="bottom-end" class="fit">
            <x-slot name="trigger">
                <div
                    class="flex items-center space-x-1 h-fit select-none hover:text-primary-500 dark:hover:text-primary-300">
                    <div class="flex items-center">
                        <template x-if="!positionable.state">
                            <x-icon name="chevron-down" solid mini />
                        </template>
                        <template x-if="positionable.state">
                            <x-icon name="chevron-up" solid mini />
                        </template>
                    </div>
                    <span class="">{{ Auth::user()->name }}</span>
                </div>
            </x-slot>
            <x-dropdown.item icon="user" label="Mi Perfil" class="dark:hover:text-white" />
            <x-dropdown.item wire:click="logout" icon="power" label="Cerrar Sesión" class="dark:hover:text-white" />
        </x-dropdown>
    </div>
</nav>