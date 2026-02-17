const CACHE_NAME = 'dtrecord-v2';
const STATIC_ASSETS = [
    '/css/app.css',
    '/js/app.js',
    '/js/offline-db.js',
    '/images/favicon.png',
    '/fonts/vendor/bootstrap-icons/bootstrap-icons.woff2',
    'https://code.jquery.com/jquery-3.7.0.min.js',
    'https://unpkg.com/dexie@latest/dist/dexie.js'
];

// Install Event
self.addEventListener('install', event => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            console.log('Pre-caching static assets');
            return cache.addAll(STATIC_ASSETS);
        })
    );
});

// Activate Event
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(keys
                .filter(key => key !== CACHE_NAME)
                .map(key => caches.delete(key))
            );
        })
    );
    self.clients.claim();
});

// Fetch Event
self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);

    // Skip non-GET and API requests (API handled by offline-db.js)
    if (event.request.method !== 'GET' || url.pathname.startsWith('/api/')) return;

    // Strategy: CacheFirst for static assets
    if (STATIC_ASSETS.includes(url.pathname) || url.hostname === 'unpkg.com' || url.hostname === 'code.jquery.com') {
        event.respondWith(
            caches.match(event.request).then(response => {
                return response || fetch(event.request).then(fetchRes => {
                    return caches.open(CACHE_NAME).then(cache => {
                        cache.put(event.request, fetchRes.clone());
                        return fetchRes;
                    });
                });
            })
        );
        return;
    }

    // Strategy: NetworkFirst for everything else (pages)
    event.respondWith(
        fetch(event.request).then(networkResponse => {
            return caches.open(CACHE_NAME).then(cache => {
                cache.put(event.request, networkResponse.clone());
                return networkResponse;
            });
        }).catch(() => {
            return caches.match(event.request).then(cachedResponse => {
                if (cachedResponse) return cachedResponse;

                // Fallback for navigation
                if (event.request.mode === 'navigate') {
                    return caches.match('/home');
                }
            });
        })
    );
});
