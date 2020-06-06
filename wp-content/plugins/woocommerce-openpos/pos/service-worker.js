var version = 'v1';
var staticCache = 'openpos-static-' + version;
var cacheFiles = [
];
self.addEventListener('install', function(event) {
    console.log('[ServiceWorker] Install');
    event.waitUntil(
        caches.open(staticCache).then(function(cache) {
            
            return cache.addAll(cacheFiles);
        }).then(function() {
            self.skipWaiting();
        })
    );
});

self.addEventListener('fetch', function(event) {
  
  		if (event.request.method != 'GET')
  		{
        	return;
        }
        let _newUrl = event.request.url;

        event.respondWith(getPosCacheData(event.request, staticCache));

});

function getPosCacheData(request, cacheName) {

    var storageUrl = request.url;

    var checkResponse = navigator.onLine;
    
    return caches.open(cacheName).then(function(cache) {
        if (checkResponse == true) {
            return fetch(request).then(function(networkResponse) {
                if (networkResponse.ok == true) {
                    cache.put(storageUrl, networkResponse.clone());
                }

                return networkResponse;
            }).catch(function(error) {
                return cache.match(storageUrl).then(function(response) {
                    if (response)
                        return formFilter(response);
                    else
                        return fallPosBackResponse('live');
                });
            });
        } else {
            return cache.match(storageUrl).then(function(response) {
                if (response) {
                    return response;
                } else {
                    return fetch(request).then(function(networkResponse) {
                        if(networkResponse.ok == true){
                            cache.put(storageUrl, networkResponse.clone());
                        }

                        return networkResponse;
                    }).catch(function(error) {
                        return fallPosBackResponse('css');
                    });
                }
            });
        }
    });
}

function fallPosBackResponse(type) {
    switch (type) {
        case 'post':
            var headers = {
                "Cache-Control": "no-cache",
                "Connection": "Keep-Alive",
                "Content-Length": "7960",
                "Content-Type": "application/json"
            }

            var body = {
                success: 'You are offline right now, we stored your data and will sync it as soon as you will get online.'
            };

            return new Response(JSON.stringify(body), { "headers": headers });

        case 'live':
            var headers = {
                "Cache-Control": "no-cache",
                "Connection": "Keep-Alive",
                "Content-Length": "7960",
                "Content-Type": "text/html"
            }

            var body = {
                offline: true
            };

            body = '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width, initial-scale=1"><title>OpenPOS Offline Page</title></head><body><div style="text-align:center;padding: 0 5px;left: 0;right: 0;top: 0;bottom: 0;margin: auto;position: fixed;height: 300px;"><svg width="160px" height="160px" viewBox="0 0 160 160" version="1.1" xmlns="//www.w3.org/2000/svg" xmlns:xlink="//www.w3.org/1999/xlink"><title>Offline</title><desc>Offline</desc><defs></defs><g id="icon/cloud" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g transform="translate(20.000000, 20.000000)" id="Shape"><polygon points="0 0 120 0 120 120 0 120"></polygon><path d="M96.75,50.2 C93.35,32.95 78.2,20 60,20 C52.6,20 45.75,22.15 39.95,25.85 L47.25,33.15 C51.05,31.15 55.4,30 60,30 C75.2,30 87.5,42.3 87.5,57.5 L87.5,60 L95,60 C103.3,60 110,66.7 110,75 C110,80.65 106.8,85.55 102.2,88.1 L109.45,95.35 C115.8,90.8 120,83.4 120,75 C120,61.8 109.75,51.1 96.75,50.2 Z M15,26.35 L28.75,40.05 C12.8,40.75 0,53.85 0,70 C0,86.55 13.45,100 30,100 L88.65,100 L98.65,110 L105,103.65 L21.35,20 L15,26.35 Z M38.65,50 L78.65,90 L30,90 C18.95,90 10,81.05 10,70 C10,58.95 18.95,50 30,50 L38.65,50 Z" fill="#000000" fill-rule="nonzero"></path></g></g></svg><p style="font-size:18px;font-family:Montserrat,sans-serif;margin:0;text-align:center;letter-spacing:0;line-height:21px;color:rgba(0,0,0,0.34);">You are currently offline, content will load once you get online.</p><button onclick="location.reload();" style="margin: 0 auto;height: auto;padding: 12px 15px;border: none;font-weight: 700;border-radius: 0;background-color: #00b6ffab;color: #fff;font-family:Montserrat,sans-serif;margin-top:15px;font-size:18px;">TRY AGAIN</button><div></body></html>';

            return new Response(body, { "headers": headers });

        case 'css':

        default:
            var headers = {
                "Cache-Control": "no-cache",
                "Connection": "Keep-Alive",
                "Content-Length": "7960",
                "Content-Type": "application/json"
            }
            var body = {};
            return new Response(JSON.stringify(body), { "headers": headers });
    }
}

function formFilter(response) {
    var headers = {
        "Cache-Control": "no-cache",
        "Connection": "Keep-Alive",
        "Content-Encoding": "gzip",
        "Content-Length": "7960",
        "Content-Type": "text/html; charset=UTF-8"
    }
    return response.text().then(function(body) {
        return new Response(body, { "headers": headers });
    });
}