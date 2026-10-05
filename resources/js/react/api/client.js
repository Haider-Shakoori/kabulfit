import axios from 'axios';

export const api = axios.create({
  baseURL: '/api',
  headers: {
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
  withCredentials: true,
});

export async function ensureCsrfCookie() {
  await axios.get('/sanctum/csrf-cookie', {
    headers: { Accept: 'application/json' },
    withCredentials: true,
  });
}
