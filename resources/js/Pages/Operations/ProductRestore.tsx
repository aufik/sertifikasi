import { productApi } from '@/Api/productApi';
import { SkuAutocomplete } from '@/Components/Forms/SkuAutocomplete';
import AppLayout from '@/Layouts/AppLayout';
import type { Product } from '@/Types/product';
import { FormEvent, useEffect, useState } from 'react';
import { Copy, RotateCcw, Search, ShieldCheck } from 'lucide-react';
import { toast } from 'sonner';

/** Halaman restore: tetap tampilkan list trash, lalu restore dari form SKU dan catatan. */
export default function ProductRestore() {
  const [search, setSearch] = useState('');
  const [products, setProducts] = useState<Product[]>([]);
  const [sku, setSku] = useState('');
  const [selectedProduct, setSelectedProduct] = useState<Product | null>(null);
  const [operatorNote, setOperatorNote] = useState('Produk masih diperlukan dan akan dipakai kembali.');
  const [loading, setLoading] = useState(false);
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    const timeout = window.setTimeout(async () => {
      setLoading(true);
      try {
        const response = await productApi.trash({ search, per_page: 8, sort: 'latest' });
        setProducts(response.data.data);
      } catch (error) {
        toast.error('Gagal mengambil daftar produk trash.');
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

  /** Cari produk dari list trash berdasarkan SKU yang diketik, lalu restore setelah user konfirmasi. */
  const submitRestore = async (event: FormEvent) => {
    event.preventDefault();
    const normalizedSku = sku.trim();
    if (!normalizedSku) {
      toast.error('Isi SKU produk terlebih dahulu.');
      return;
    }

    const product = selectedProduct?.sku.toLowerCase() === normalizedSku.toLowerCase()
      ? selectedProduct
      : products.find((item) => item.sku.toLowerCase() === normalizedSku.toLowerCase());
    if (!product) {
      toast.error('SKU tidak ditemukan di list trash saat ini. Cari dulu SKU tersebut di kolom kiri, lalu submit lagi.');
      return;
    }

    if (!operatorNote.trim()) {
      toast.error('Isi penjelasan restore terlebih dahulu.');
      return;
    }

    if (!window.confirm(`Restore produk ${product.name} dengan SKU ${product.sku}?`)) return;

    setSubmitting(true);
    try {
      await productApi.restore(product.id);
      toast.success(`${product.name} berhasil direstore.`);
      setProducts((currentProducts) => currentProducts.filter((item) => item.id !== product.id));
      setSelectedProduct(null);
      setSku('');
      setOperatorNote('Produk masih diperlukan dan akan dipakai kembali.');
    } catch (error) {
      toast.error('Restore gagal. Operasi ini hanya untuk Admin.');
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <AppLayout>
      <div className="max-w-6xl space-y-4">
        <div className="flex items-center justify-between border-b border-slate-200 pb-3">
          <div>
            <h1 className="text-xl font-bold text-slate-900">Restore Products</h1>
            <p className="text-xs text-slate-500">Lihat list produk trash, isi SKU dan penjelasan, lalu restore setelah konfirmasi.</p>
          </div>
          <span className="inline-flex items-center gap-1 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 ring-1 ring-blue-600/20">
            <ShieldCheck className="h-3.5 w-3.5" /> Admin Operation
          </span>
        </div>

        <div className="grid gap-4 lg:grid-cols-12">
          <div className="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-900/5 lg:col-span-7">
            <label className="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-500">List Produk di Trash</label>
            <div className="relative">
              <Search className="absolute left-3 top-2.5 h-4 w-4 text-slate-400" />
              <input className="w-full rounded-lg border border-slate-200 py-2 pl-9 pr-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" value={search} onChange={(e) => setSearch(e.target.value)} placeholder="Cari nama produk atau SKU di trash..." />
            </div>

            <div className="mt-3 divide-y divide-slate-100 rounded-lg border border-slate-100">
              {loading ? <div className="p-4 text-center text-xs text-slate-400">Loading trash products...</div> : products.map((product) => (
                <div key={product.id} className="flex items-center justify-between gap-2 px-3 py-2.5 transition hover:bg-blue-50">
                  <button type="button" onClick={() => { setSku(product.sku); setSelectedProduct(product); }} className="min-w-0 flex-1 text-left">
                    <p className="truncate text-sm font-semibold text-slate-900">{product.name}</p>
                    <p className="text-[11px] font-mono text-slate-500">{product.sku} • Stok: {product.stock}</p>
                  </button>
                  <div className="flex shrink-0 items-center gap-2">
                    <span className="text-xs font-semibold text-blue-600">Pakai SKU</span>
                    <button type="button" onClick={() => copySku(product.sku)} className="inline-flex items-center gap-1 rounded-md bg-white px-2 py-1 text-[11px] font-semibold text-slate-600 ring-1 ring-slate-200 transition hover:bg-slate-50"><Copy className="h-3 w-3" /> Copy SKU</button>
                  </div>
                </div>
              ))}
              {!loading && products.length === 0 && <div className="p-4 text-center text-xs text-slate-400">Tidak ada produk trash yang cocok.</div>}
            </div>
          </div>

          <form onSubmit={submitRestore} className="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-900/5 lg:col-span-5">
            <h2 className="mb-3 flex items-center gap-2 text-sm font-bold text-slate-900"><RotateCcw className="h-4 w-4 text-blue-600" /> Form Restore</h2>
            <div className="rounded-lg bg-slate-50 p-3 text-xs text-slate-600">
              <p className="font-semibold text-slate-900">Restore berdasarkan SKU</p>
              <p className="text-slate-500">Ketik SKU manual, atau klik produk di list kiri untuk mengisi SKU.</p>
            </div>

            <div className="mt-3 space-y-3">
              <div>
                <label className="mb-1 block text-xs font-semibold text-slate-700">SKU Produk</label>
                <SkuAutocomplete value={sku} onChange={(nextSku) => { setSku(nextSku); setSelectedProduct(null); }} onProductSelect={setSelectedProduct} source="trash" placeholder="Cari atau ketik SKU produk trash..." />
              </div>
              <div>
                <label className="mb-1 block text-xs font-semibold text-slate-700">Penjelasan Restore</label>
                <textarea value={operatorNote} onChange={(e) => setOperatorNote(e.target.value)} className="h-24 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" placeholder="Contoh: produk masih dibutuhkan untuk operasional." />
              </div>
              <button disabled={submitting} className="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50">
                {submitting ? 'Restoring...' : 'Submit Restore via API'}
              </button>
            </div>
          </form>
        </div>
      </div>
    </AppLayout>
  );
}
