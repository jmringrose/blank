// resources/js/src/auth.js
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';

const base = import.meta.env.VITE_API_BASE_URL ?? '/api';

export async function login(email, password, deviceName = 'web') {
    const auth = useAuthStore();

    // 1) hit login
    const res = await axios.post(`${base}/login`, {
        email,
        password,
        device_name: deviceName,
    }, { headers: { Accept: 'application/json' } });

    const token = res.data?.token;
    if (!token) throw new Error('No token returned from /api/login');

    // 2) persist token into Pinia + localStorage
    auth.setToken(token);

    // 3) optional: fetch user and persist
    try {
        const me = await axios.get(`${base}/me`, {
            headers: {
                Accept: 'application/json',
                Authorization: `Bearer ${token}`,
            },
        }).then(r => r.data);
        if (me) auth.setUser(me);
    } catch {
        // ignore
    }

    return token;
}

export async function logout() {
    const auth = useAuthStore();
    const token = auth.token;

    try {
        await axios.post(`${base}/logout`, null, {
            headers: {
                Accept: 'application/json',
                ...(token ? { Authorization: `Bearer ${token}` } : {}),
            },
        });
    } catch {
        // ignore network errors on logout
    }

    auth.logout(); // clears store + localStorage
}
