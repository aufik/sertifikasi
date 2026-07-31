import { productApi } from '@/Api/productApi';
import AppLayout from '@/Layouts/AppLayout';
import { formatCurrency } from '@/Lib/formatCurrency';
import type { Product } from '@/Types/product';
import { Copy, PackagePlus, Search, ShieldCheck } from 'lucide-react';
import { FormEvent, useEffect, useState } from 'react';
import { toast } from 'sonner';

/** Halaman restock: cari produk, pilih produk, lalu tambah stok lewat API terpusat. */
export default function ProductRestock() {
  const [search, setSearch] = useState('');
  const [products, setProducts] = useState<Product[]>([]);
  const [selectedProduct, setSelectedProduct] = useState<Product | null>(null);
  const [quantity, setQuantity] = useState(1);
  const [reason, setReason] = useState('Operational restock');
  const [loading, setLoading] = useState(false);
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    const timeout = window.setTimeout(async () => {
      setLoading(true);
      try {
        const response = await productApi.list({ search, per_page: 8, sort: 'latest' });
        setProducts(response.data.data);
      } catch (error) {
        toast.error('Gagal mengambil daftar produk.');
      } finally {
        setLoading(false);
      }
    }, 300);

    return () => window.clearTimeout(timeout);
  }, [search]);

  /** Copy SKU supaya user bisa pakai di tempat lain tanpa mengetik ulang. */
  const copySku = async (sku: string) => {
    await navigator.clipboard.writeText(sku);
    toast.success(`SKU ${sku} berhasil dicopy.`);
  };

  /** Cek produk yang dipilih, lalu kirim jumlah restock dan alasan ke API backend. */
  const submit = async (event: FormEvent) => {
    event.preventDefault();
    if (!selectedProduct) {
      toast.error('Pilih produk terlebih dahulu.');
      return;
    }

    setSubmitting(true);
    try {
      const response = await productApi.restock(selectedProduct.id, quantity, reason);
      const updatedProduct = response.data.data;
      toast.success(`Stok ${selectedProduct.name} berhasil ditambah.`);
      setSelectedProduct(updatedProduct);
      setProducts((currentProducts) => currentProducts.map((product) => product.id === updatedProduct.id ? updatedProduct : product));
      setQuantity(1);
    } catch (error) {
      toast.error('Restock gagal. Pastikan jumlah valid dan role Anda diizinkan.');
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <AppLayout>
      <div className="max-w-6xl space-y-4">
        <div className="flex items-center justify-between border-b border-slate-200 pb-3">
          <div>
            <h1 className="text-xl font-bold text-slate-900">Restock Products</h1>
            <p className="text-xs text-slate-500">Operasi restock via REST API dengan audit log bisnis otomatis.</p>
          </div>
          <span className="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-600/20">
            <ShieldCheck className="h-3.5 w-3.5" /> API Driven
          </span>
        </div>

        <div className="grid gap-4 lg:grid-cols-12">
          <div className="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-900/5 lg:col-span-7">
            <label className="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-500">Cari Produk</label>
            <div className="relative">
              <Search className="absolute left-3 top-2.5 h-4 w-4 text-slate-400" />
              <input className="w-full rounded-lg border border-slate-200 py-2 pl-9 pr-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" value={search} onChange={(e) => setSearch(e.target.value)} placeholder="Cari nama produk atau SKU..." />
            </div>

            <div className="mt-3 divide-y divide-slate-100 rounded-lg border border-slate-100">
              {loading ? <div className="p-4 text-center text-xs text-slate-400">Loading products...</div> : products.map((product) => (
                <div key={product.id} className={`flex items-center justify-between gap-2 px-3 py-2.5 transition hover:bg-slate-50 ${selectedProduct?.id === product.id ? 'bg-indigo-50' : ''}`}>
                  <button type="button" onClick={() => setSelectedProduct(product)} className="flex min-w-0 flex-1 items-center justify-between text-left">
                    <div className="min-w-0">
                      <p className="truncate text-sm font-semibold text-slate-900">{product.name}</p>
                      <p className="text-[11px] font-mono text-slate-500">{product.sku} • Stok: {product.stock}</p>
                    </div>
                    <span className="ml-3 shrink-0 text-xs font-semibold text-slate-500">{formatCurrency(product.price)}</span>
                  </button>
                  <button type="button" onClick={() => copySku(product.sku)} className="inline-flex shrink-0 items-center gap-1 rounded-md bg-white px-2 py-1 text-[11px] font-semibold text-slate-600 ring-1 ring-slate-200 transition hover:bg-slate-50"><Copy className="h-3 w-3" /> Copy SKU</button>
                </div>
              ))}
            </div>
          </div>

          <form onSubmit={submit} className="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-900/5 lg:col-span-5">
            <h2 className="mb-3 flex items-center gap-2 text-sm font-bold text-slate-900"><PackagePlus className="h-4 w-4 text-indigo-600" /> Form Restock</h2>
            <div className="rounded-lg bg-slate-50 p-3 text-xs text-slate-600">
              <p className="font-semibold text-slate-900">{selectedProduct?.name ?? 'Belum ada produk dipilih'}</p>
              <p className="font-mono text-slate-500">{selectedProduct ? `${selectedProduct.sku} • Current Stock: ${selectedProduct.stock}` : 'Pilih produk dari daftar kiri.'}</p>
            </div>

            <div className="mt-3 space-y-3">
              <div>
                <label className="mb-1 block text-xs font-semibold text-slate-700">Jumlah Restock</label>
                <input type="number" min={1} max={100000} value={quantity} onChange={(e) => setQuantity(Number(e.target.value))} className="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" />
              </div>
              <div>
                <label className="mb-1 block text-xs font-semibold text-slate-700">Alasan</label>
                <textarea value={reason} onChange={(e) => setReason(e.target.value)} className="h-20 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" />
              </div>
              <button disabled={submitting || !selectedProduct} className="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50">
                {submitting ? 'Processing...' : 'Submit Restock via API'}
              </button>
            </div>
          </form>
        </div>
      </div>
    </AppLayout>
  );
}
