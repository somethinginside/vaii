const staticCacheName = 's-app-v1'

const assetUrls = [
  'site.html',
  'care.html',
  'tombola.html',
  'app.js',
  'slider.js',
  'style.css'
]

self.addEventListener("install", event => {
  async function preCacheResources(){
  const cache = await caches.open(staticCacheName);
  await cache.addAll(assetUrls);
  }
  event.waitUntil(preCacheResources());
});

self.addEventListener("activate", event => {
  console.log("WORKER: activate event in progress.");
});

self.addEventListener("fetch", event => {
  async function preCacheResources(){
  const cache = await caches.open(staticCacheName);
  const cachedResponse = await cache.match(event.request.url);
  if (cachedResponse){
    return cachedResponse;
  }else {
    const fetchResponse = await fetch(event.request.url);
    cache.put(event.request.url, fetchResponse.clone());
    return fetchResponse
    }
  }
  event.respondWith(preCacheResources());
});

async function cacheFirst(request) {
  const cached = await caches.match(request)
  return cached ?? await fetch(request)
};