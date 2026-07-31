import { productApi } from '@/Api/productApi';
import type { Product } from '@/Types/product';
import { Combobox, ComboboxInput, ComboboxOption, ComboboxOptions } from '@headlessui/react';
import { Search } from 'lucide-react';
import { useEffect, useState } from 'react';
import { toast } from 'sonner';

type ProductSource = 'active' | 'trash';

interface SkuAutocompleteProps {
  value: string;
  onChange: (sku: string) => void;
  onProductSelect?: (product: Product) => void;
  source: ProductSource;
  placeholder?: string;
}

/** Autocomplete SKU agar user bisa cari produk tanpa harus menebak SKU lengkap. */
export function SkuAutocomplete({ value, onChange, onProductSelect, source, placeholder = 'Cari atau ketik SKU produk...' }: SkuAutocompleteProps) {
  const [products, setProducts] = useState<Product[]>([]);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const keyword = value.trim();
    const timeout = window.setTimeout(async () => {
      setLoading(true);
      try {
        const request = source === 'trash'
          ? productApi.trash({ search: keyword, per_page: 8, sort: 'latest' })
          : productApi.list({ search: keyword, per_page: 8, sort: 'latest' });
        const response = await request;
        setProducts(response.data.data);
      } catch (error) {
        toast.error(source === 'trash' ? 'Gagal mengambil SKU produk trash.' : 'Gagal mengambil SKU produk aktif.');
      } finally {
        setLoading(false);
      }
    }, 250);

    return () => window.clearTimeout(timeout);
  }, [source, value]);

  const selectProduct = (product: Product | null) => {
    if (!product) return;
    onChange(product.sku);
    onProductSelect?.(product);
  };

  return (
    <Combobox value={null as Product | null} onChange={selectProduct} immediate>
      <div className="relative">
        <Search className="absolute left-3 top-2.5 h-4 w-4 text-slate-400" />
        <ComboboxInput
          className="w-full rounded-lg border border-slate-200 py-2 pl-9 pr-3 font-mono text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
          displayValue={() => value}
          onChange={(event) => onChange(event.target.value.toUpperCase())}
          placeholder={placeholder}
        />
        <ComboboxOptions className="absolute z-20 mt-1 max-h-64 w-full overflow-auto rounded-lg border border-slate-200 bg-white py-1 text-sm shadow-lg">
          {loading && <div className="px-3 py-2 text-xs text-slate-400">Mencari SKU...</div>}
          {!loading && products.length === 0 && <div className="px-3 py-2 text-xs text-slate-400">SKU tidak ditemukan.</div>}
          {!loading && products.map((product) => (
            <ComboboxOption key={product.id} value={product} className="cursor-pointer px-3 py-2 data-focus:bg-indigo-50">
              <div className="font-semibold text-slate-900">{product.name}</div>
              <div className="font-mono text-[11px] text-slate-500">{product.sku} • Stok: {product.stock}</div>
            </ComboboxOption>
          ))}
        </ComboboxOptions>
      </div>
    </Combobox>
  );
}
