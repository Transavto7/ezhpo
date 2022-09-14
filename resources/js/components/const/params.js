export function addParams(params) {
    const url = new URL(window.location.href);

    for (const p of url.searchParams) {
        url.searchParams.delete(p[0]);
    }

    for (const key in params) {
        if (params[key] !== null && params[key] !== undefined) {
            url.searchParams.set(key, params[key]);
        }
    }

    if (history.pushState) {
        history.pushState(null, null, url.toString());
    }
}

export function getParams() {
    const url = new URL(window.location.href);
    const params = {};
    for (const p of url.searchParams) {
        if (p[1] === 'false') {
            p[1] = false;
        } else if (p[1] === 'true') {
            p[1] = true;
        }
        params[p[0]] = p[1];
    }

    return params;
}
