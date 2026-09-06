<!-- FLOATING TOAST NOTIFICATION -->
        <div 
            x-show="toast.show" 
            x-transition
            :class="toast.type === 'error' ? 'bg-red-600' : 'bg-zinc-950'"
            class="fixed top-4 left-1/2 -translate-x-1/2 z-50 text-white text-xs font-semibold px-4 py-2 rounded-lg shadow-lg flex items-center gap-2 border border-white/10">
            <span x-text="toast.message"></span>
        </div>
