var version = 'v6.3.2';

self.addEventListener("install", function (event) {
	self.skipWaiting();
	event.waitUntil(
		caches.open(version + 'fundamentals')
			.then(function (cache) {
				return cache.addAll([
					'/',
					'manifest.json',
					'assets/js/releve-but.js',
					'assets/js/releve-dut.js'
				]);
			})
	);
});

self.addEventListener("fetch", function (event) {
	if (event.request.url.indexOf('http') === 0 && event.request.method == 'GET' && event.request.url.indexOf('-no-sw') != -1) {
		event.respondWith(
			caches
				.match(event.request)
				.then(function (cached) {
					var networked = fetch(event.request)
						.then(fetchedFromNetwork, unableToResolve)
						.catch(unableToResolve);
					return cached || networked;

					function fetchedFromNetwork(response) {
						var cacheCopy = response.clone();
						caches.open(version + 'pages')
							.then(function add(cache) {
								cache.put(event.request, cacheCopy);
							});
						return response;
					}

					function unableToResolve() {
						return new Response(`{"error": "Il semblerait que vous ne soyez pas connecté à Internet."}`, {
							status: 503,
							statusText: 'Service Unavailable',
							headers: new Headers({
								'Content-Type': 'text/html'
							})
						});
					}
				})
		);
	}
});

self.addEventListener("activate", function (event) {
	event.waitUntil(
		caches
			.keys()
			.then(function (keys) {
				return Promise.all(
					keys
						.filter(function (key) {
							return !key.startsWith(version);
						})
						.map(function (key) {
							return caches.delete(key);
						})
				);
			})
	);
});
