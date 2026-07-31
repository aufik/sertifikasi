import { productApi } from '@/Api/productApi';
import type { Product } from '@/Types/product';
import { useCallback, useState } from 'react';
import { toast } from 'sonner';

/**
 * Hook kecil untuk operasi produk.
 * Isinya state loading, panggil API, dan toast supaya halaman restock/restore/purge tidak terlalu penuh logic.
 */
export function useProductOperations() {
  const [processing, setProcessing] = useState(false);

  /** Tambah stok produk, lalu balikin data produk terbaru kalau berhasil. */
  const restock = useCallback(async (product: Product, quantity: number, reason?: string): Promise<Product | null> => {
    setProcessing(true);
    try {
      const response = await productApi.restock(product.id, quantity, reason);
      toast.success(`${product.name} berhasil direstock.`);
      return response.data.data;
    } catch (error) {
      toast.error('Restock produk gagal. Periksa input dan hak akses Anda.');
      return null;
    } finally {
      setProcessing(false);
    }
  }, []);

  /** Pulihkan produk dari trash, lalu balikin datanya kalau berhasil. */
  const restore = useCallback(async (product: Product): Promise<Product | null> => {
    setProcessing(true);
    try {
      const response = await productApi.restore(product.id);
      toast.success(`${product.name} berhasil direstore.`);
      return response.data.data;
    } catch (error) {
      toast.error('Restore produk gagal. Pastikan Anda memiliki hak akses Admin.');
      return null;
    } finally {
      setProcessing(false);
    }
  }, []);

  /** Hapus permanen produk dari trash, lalu balikin true kalau proses berhasil. */
  const purge = useCallback(async (product: Product): Promise<boolean> => {
    setProcessing(true);
    try {
      await productApi.purge(product.id);
      toast.success(`${product.name} berhasil dihapus permanen.`);
      return true;
    } catch (error) {
      toast.error('Purge produk gagal. Pastikan Anda memiliki hak akses Admin.');
      return false;
    } finally {
      setProcessing(false);
    }
  }, []);

  return { processing, restock, restore, purge };
}
