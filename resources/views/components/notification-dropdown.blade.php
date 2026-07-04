<div id="notification-bell" class="relative" x-data="notificationBell()" x-init="load()">

    {{-- Bell Button --}}
    <button @click="toggle()" type="button"
            class="relative p-2 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none transition"
            aria-label="Notifications">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-5-5.917V4a1 1 0 10-2 0v1.083A6 6 0 006 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>

        {{-- Unread badge --}}
        <span x-show="unreadCount > 0" x-text="unreadCount > 99 ? '99+' : unreadCount"
              class="absolute -top-1 -right-1 min-w-[18px] h-[18px] bg-red-500 text-white text-[10px] font-bold leading-none
                     flex items-center justify-center rounded-full px-1 ring-2 ring-white"
              x-cloak></span>
    </button>

    {{-- Dropdown Panel --}}
    <div x-show="open" @click.away="open = false"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden"
         x-cloak>

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 bg-gray-50">
            <h3 class="text-sm font-semibold text-gray-800">Notifications</h3>
            <button x-show="unreadCount > 0" @click="markAllRead()" type="button"
                    class="text-xs text-indigo-600 hover:text-indigo-800 font-medium transition">
                Mark all read
            </button>
        </div>

        {{-- Notification List --}}
        <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">

            <template x-if="loading">
                <div class="py-8 flex items-center justify-center">
                    <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </div>
            </template>

            <template x-if="!loading && notifications.length === 0">
                <div class="py-10 text-center">
                    <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-5-5.917V4a1 1 0 10-2 0v1.083A6 6 0 006 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-400">No notifications yet</p>
                </div>
            </template>

            <template x-for="n in notifications" :key="n.id">
                <div class="flex gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer transition"
                     :class="{ 'bg-indigo-50/60': !n.read_at }"
                     @click="handleClick(n)">

                    {{-- Unread dot --}}
                    <div class="mt-1.5 flex-shrink-0">
                        <span x-show="!n.read_at" class="block w-2 h-2 rounded-full bg-indigo-500"></span>
                        <span x-show="n.read_at"  class="block w-2 h-2 rounded-full bg-transparent"></span>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 leading-snug" x-text="n.data.title"></p>
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2" x-text="n.data.body"></p>
                        <p class="text-[10px] text-gray-400 mt-1" x-text="timeAgo(n.created_at)"></p>
                    </div>

                    {{-- Delete button --}}
                    <button @click.stop="remove(n)"
                            class="flex-shrink-0 opacity-0 group-hover:opacity-100 text-gray-300 hover:text-red-400 transition mt-1"
                            title="Dismiss">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </template>
        </div>

        {{-- Footer --}}
        <div x-show="hasMore" class="border-t border-gray-100 px-4 py-2.5 text-center">
            <button @click="loadMore()" type="button"
                    class="text-xs text-indigo-600 hover:text-indigo-800 font-medium transition">
                Load more
            </button>
        </div>
    </div>
</div>

<script>
function notificationBell() {
    return {
        open: false,
        loading: false,
        notifications: [],
        unreadCount: 0,
        hasMore: false,
        page: 1,

        toggle() {
            this.open = !this.open;
            if (this.open && this.notifications.length === 0) this.load();
        },

        async load() {
            this.loading = true;
            this.page = 1;
            try {
                const res = await fetch(`/notifications?per_page=10&page=1`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const json = await res.json();
                this.notifications = json.data;
                this.unreadCount   = json.unread_count;
                this.hasMore       = json.has_more;
            } finally {
                this.loading = false;
            }
        },

        async loadMore() {
            this.page++;
            const res = await fetch(`/notifications?per_page=10&page=${this.page}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const json = await res.json();
            this.notifications.push(...json.data);
            this.hasMore = json.has_more;
        },

        async handleClick(n) {
            if (!n.read_at) await this.markRead(n);
            if (n.data?.url) window.location.href = n.data.url;
        },

        async markRead(n) {
            const res = await fetch(`/notifications/${n.id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            });
            const json = await res.json();
            n.read_at = new Date().toISOString();
            this.unreadCount = json.unread_count;
        },

        async markAllRead() {
            await fetch('/notifications/read-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            });
            this.notifications.forEach(n => n.read_at = new Date().toISOString());
            this.unreadCount = 0;
        },

        async remove(n) {
            await fetch(`/notifications/${n.id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            });
            this.notifications = this.notifications.filter(x => x.id !== n.id);
            if (!n.read_at) this.unreadCount = Math.max(0, this.unreadCount - 1);
        },

        timeAgo(dateStr) {
            const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000);
            if (diff < 60)   return 'Just now';
            if (diff < 3600) return Math.floor(diff/60) + 'm ago';
            if (diff < 86400) return Math.floor(diff/3600) + 'h ago';
            return Math.floor(diff/86400) + 'd ago';
        }
    }
}
</script>
