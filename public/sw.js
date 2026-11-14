/*
 * Service Worker Project Tracker (PWA)
 * Strategi:
 *  - Aset statis (css/js/svg/png/font) : cache-first + simpan ke cache runtime.
 *  - Navigasi halaman                  : network-first; gagal/offline -> offline.html.
 *  - AJAX/JSON & POST                  : TIDAK di-intercept (selalu jaringan) agar data board selalu segar.
 */
"use strict";

const VERSION = "v4";
const PRECACHE = "tracker-precache-" + VERSION;
const RUNTIME = "tracker-runtime-" + VERSION;
const OFFLINE_URL = "/offline.html";

// hanya path dengan ekstensi statis yang boleh di-cache
const STATIC_PATTERN = /\.(css|js|svg|png|jpe?g|gif|ico|woff2?|ttf)$/i;
// host font (cross-origin, aman di-cache sebagai opaque/cors response)
const FONT_HOSTS = ["fonts.googleapis.com", "fonts.gstatic.com"];

const PRECACHE_URLS = [
	OFFLINE_URL,
	"/assets/img/logo.svg",
	"/assets/img/icon-192.png",
	"/assets/img/icon-512.png",
	"/assets/img/icon-maskable-512.png",
	"/cdn/lib/jquery/jquery.min.js",
	"/cdn/lib/bootstrap/bootstrap.bundle.min.js",
	"/cdn/lib/bootstrap/bootstrap.min.css",
	"/cdn/lib/bootbox/bootbox.all.min.js",
	"/cdn/lib/toastr/toastr.min.js",
	"/cdn/lib/toastr/toastr.min.css",
	"/cdn/lib/datatables/jquery.dataTables.min.js",
	"/cdn/lib/datatables/jquery.dataTables.min.css",
	"/cdn/lib/sortablejs/Sortable.min.js"
];

// aset milik app (app.css, logo, ikon) sering berubah saat development ->
// network-first agar perubahan langsung sinkron tanpa hard refresh
const APP_ASSET_PATTERN = /^\/assets\//;

self.addEventListener("install", function (event) {
	event.waitUntil(
		caches.open(PRECACHE)
			.then(function (cache) {
				return cache.addAll(PRECACHE_URLS);
			})
			.then(function () {
				return self.skipWaiting();
			})
	);
});

self.addEventListener("activate", function (event) {
	event.waitUntil(
		caches.keys()
			.then(function (keys) {
				return Promise.all(
					keys
						.filter(function (key) {
							return key !== PRECACHE && key !== RUNTIME;
						})
						.map(function (key) {
							return caches.delete(key);
						})
				);
			})
			.then(function () {
				return self.clients.claim();
			})
	);
});

self.addEventListener("fetch", function (event) {
	const request = event.request;

	// hanya GET yang dipertimbangkan; selain itu lewat ke jaringan apa adanya
	if (request.method !== "GET") {
		return;
	}

	const url = new URL(request.url);
	const isSameOrigin = url.origin === self.location.origin;
	const isFont = FONT_HOSTS.indexOf(url.hostname) !== -1;
	const isStatic = isSameOrigin && STATIC_PATTERN.test(url.pathname);

	// navigasi halaman: network-first dengan fallback offline
	// (cek ini HARUS sebelum gate statis di bawah, karena "/" tidak match pola statis)
	if (request.mode === "navigate") {
		event.respondWith(
			fetch(request)
				.then(function (response) {
					return response;
				})
				.catch(function () {
					return caches.match(OFFLINE_URL);
				})
		);
		return;
	}

	// aset milik app (/assets/...): network-first -> selalu versi terbaru saat
	// online, fallback cache saat offline. Update cache dengan versi terbaru.
	if (isSameOrigin && APP_ASSET_PATTERN.test(url.pathname)) {
		event.respondWith(
			fetch(request)
				.then(function (response) {
					if (response && response.ok) {
						const salinan = response.clone();
						caches.open(RUNTIME).then(function (cache) {
							cache.put(request, salinan);
						});
					}
					return response;
				})
				.catch(function () {
					return caches.match(request);
				})
		);
		return;
	}

	// AJAX/JSON (progress_daftar, group_daftar, dst) & halaman CI -> tidak di-cache
	if (!isStatic && !isFont) {
		return;
	}

	// aset statis & font: cache-first, isi cache runtime saat pertama kali
	event.respondWith(
		caches.match(request).then(function (cached) {
			if (cached) {
				return cached;
			}
			return fetch(request).then(function (response) {
				const bolehCache = response && (response.ok || response.type === "opaque");
				if (bolehCache) {
					const salinan = response.clone();
					caches.open(RUNTIME).then(function (cache) {
						cache.put(request, salinan);
					});
				}
				return response;
			});
		})
	);
});
