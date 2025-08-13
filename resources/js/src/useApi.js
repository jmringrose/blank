import axios from 'axios';
import { useAuthStore } from '@/stores/auth';

export function useApi() {
    const auth = useAuthStore();

    const api = axios.create({
        baseURL: import.meta.env.VITE_API_BASE_URL ?? '/api',
        headers: { Accept: 'application/json' },
    });

    api.interceptors.request.use((config) => {
        const token = auth.token;
        if (token) config.headers.Authorization = `Bearer ${token}`;
        else delete config.headers.Authorization;
        return config;
    });

    return api;
}
