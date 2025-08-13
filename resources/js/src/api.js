import axios from 'axios';

// Make a new axios instance per app/request
export function createApi(getToken) {
    const api = axios.create({
        baseURL: import.meta.env.VITE_API_BASE_URL ?? '/api',
        headers: { Accept: 'application/json' },
        // do not set withCredentials for token mode
    });

    api.interceptors.request.use((config) => {
        const token = getToken?.();
        if (token) config.headers.Authorization = `Bearer ${token}`;
        else delete config.headers.Authorization;
        return config;
    });

    api.interceptors.response.use(
        (r) => r,
        (err) => {
            if (err?.response?.status === 401) {
                // consumer can clear the store on 401
            }
            return Promise.reject(err);
        }
    );

    return api;
}
