import axios from 'axios';

/**
 * Tempat utama untuk semua request API dari halaman React.
 * Jadi base URL, header JSON, cookie login, dan error handling tidak tercecer di banyak file.
 */
export const apiClient = axios.create({
  baseURL: '/api/v1',
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
  withCredentials: true,
});

/** Kalau request API gagal, error dikirim balik supaya halaman bisa tampilkan toast yang sesuai. */
apiClient.interceptors.response.use(
  (response) => response,
  (error) => Promise.reject(error),
);
