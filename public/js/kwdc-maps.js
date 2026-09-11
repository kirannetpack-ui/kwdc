(function () {
    const cache = new Map();
    let queue = Promise.resolve();
    let lastRequest = 0;

    async function request(path, payload) {
        const key = path + JSON.stringify(payload || null);
        if (cache.has(key)) return cache.get(key);
        const task = queue.catch(() => {}).then(async () => {
            const delay = Math.max(0, 1100 - (Date.now() - lastRequest));
            if (delay) await new Promise(resolve => setTimeout(resolve, delay));
            lastRequest = Date.now();
            const response = await fetch(path, {
                method: payload ? 'POST' : 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                ...(payload ? { body: JSON.stringify(payload) } : {}),
                signal: AbortSignal.timeout(15000),
            });
            if (!response.ok) throw new Error('Map service is unavailable. Please retry.');
            return response.json();
        });
        queue = task;
        cache.set(key, task);
        task.catch(() => cache.delete(key));
        return task;
    }

    window.KwdcMaps = {
        async search(address) {
            const matches = await request('/maps/search?q=' + encodeURIComponent(address.trim()));
            if (!matches.length) return null;
            return {lat: Number(matches[0].lat), lng: Number(matches[0].lon), label: matches[0].display_name};
        },
        async roadDistance(addresses) {
            const points = [];
            for (const address of addresses) {
                const point = await this.search(address);
                if (!point) throw new Error('Location not found: ' + address);
                points.push({lat: point.lat, lng: point.lng});
            }
            const route = await request('/maps/route', {points});
            return route.distance_km;
        },
    };
})();
