import { apiClient } from '@/Api/client';
import { endpoints } from '@/Api/endpoints';
import type { ApiResponse } from '@/Types/api';
import type { Product } from '@/Types/product';

export interface ProductListParams {
  search?: string;
  status?: string;
  category_id?: string;
  per_page?: number;
  sort?: string;
}

/**
 * Kumpulan fungsi API untuk produk.
 * Tujuannya biar halaman React tinggal panggil fungsi ini, tidak perlu tulis URL API manual di banyak tempat.
 */
export const productApi = {
  /** Ambil daftar produk aktif untuk katalog dan pencarian di menu operasi. */
  list: (params?: ProductListParams) => apiClient.get<ApiResponse<Product[]>>(endpoints.products.list, { params }),

  /** Ambil daftar produk yang sudah masuk trash untuk fitur restore dan purge. */
  trash: (params?: ProductListParams) => apiClient.get<ApiResponse<Product[]>>(endpoints.products.trash, { params }),

  /** Ambil detail satu produk berdasarkan ID. */
  show: (id: string) => apiClient.get<ApiResponse<Product>>(endpoints.products.show(id)),

  /** Buat produk baru lewat REST API. */
  create: (payload: unknown) => apiClient.post<ApiResponse<Product>>(endpoints.products.create, payload),

  /** Ubah data produk lewat REST API. */
  update: (id: string, payload: unknown) => apiClient.put<ApiResponse<Product>>(endpoints.products.update(id), payload),

  /** Hapus produk secara soft delete lewat REST API. */
  delete: (id: string) => apiClient.delete<ApiResponse<null>>(endpoints.products.delete(id)),

  /** Pulihkan produk dari trash dan catat log bisnis ProductRestored. */
  restore: (id: string) => apiClient.post<ApiResponse<Product>>(endpoints.products.restore(id)),

  /** Hapus permanen produk dari trash dan catat log bisnis ProductPurged dulu. */
  purge: (id: string) => apiClient.delete<ApiResponse<null>>(endpoints.products.purge(id)),

  /** Tambah stok produk dan catat log bisnis ProductRestocked. */
  restock: (id: string, quantity: number, reason?: string) =>
    apiClient.post<ApiResponse<Product>>(endpoints.products.restock(id), { quantity, reason }),
};
