<div>
    <button wire:click="remove"
        class="bg-red-100 text-red-500 flex items-center cursor-pointer rounded-lg shadow-sm py-2 px-2"
        wire:loading.attr="disabled">
        Hapus
        <div wire:loading
            class="ml-2 animate-spin inline-block size-4 border-3 border-current border-t-transparent text-blue-500 rounded-full dark:text-blue-500"
            role="status" aria-label="loading">
            <span class="sr-only">Loading...</span>
        </div>
    </button>
</div>
