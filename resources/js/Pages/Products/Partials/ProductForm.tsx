import { useForm } from '@inertiajs/react';
import { Button } from '@/Components/Form/Button';
import { Input } from '@/Components/Form/Input';
import { Select } from '@/Components/Form/Select';
import { Textarea } from '@/Components/Form/Textarea';
import { ValidationMessage } from '@/Components/Feedback/ValidationMessage';
import type { Category, Product } from '@/Types/product';

interface Props {
  categories: Category[];
  product?: Product;
}

/** Form pembuatan dan pengeditan produk dengan label bahasa Indonesia yang jelas dan visual modern. */
export function ProductForm({ categories, product }: Props) {
  const form = useForm({
    category_id: product?.category_id ?? '',
    name: product?.name ?? '',
    sku: product?.sku ?? '',
    description: product?.description ?? '',
    price: product?.price ?? 0,
    stock: product?.stock ?? 0,
    status: product?.status ?? 'draft'
  });

  const submit = () => {
    if (product) {
      form.put(`/products/${product.id}`);
    } else {
      form.post('/products');
    }
  };

  return (
    <form
      onSubmit={(e) => {
        e.preventDefault();
        submit();
      }}
      className="space-y-5 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
    >
      <div>
        <label className="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
          Kategori Produk
        </label>
        <Select
          value={form.data.category_id}
          onChange={(e) => form.setData('category_id', e.target.value)}
          className="w-full"
        >
          <option value="">-- Pilih Kategori --</option>
          {categories.map((c) => (
            <option key={c.id} value={c.id}>
              {c.name} ({c.type})
            </option>
          ))}
        </Select>
        <ValidationMessage message={form.errors.category_id} />
      </div>

      <div>
        <label className="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
          Nama Produk
        </label>
        <Input
          value={form.data.name}
          onChange={(e) => form.setData('name', e.target.value)}
          placeholder="Masukkan nama lengkap produk (contoh: Laptop Lenovo ThinkPad)"
          className="w-full"
        />
        <ValidationMessage message={form.errors.name} />
      </div>

      <div>
        <label className="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
          SKU Produk (Unique Stock Keeping Unit)
        </label>
        <Input
          value={form.data.sku}
          onChange={(e) => form.setData('sku', e.target.value.toUpperCase())}
          placeholder="CONTOH: SKU-LAPTOP-001"
          className="w-full font-mono text-sm uppercase"
        />
        <p className="mt-1 text-[10px] text-slate-400">SKU harus unik dan tidak boleh sama dengan produk lain.</p>
        <ValidationMessage message={form.errors.sku} />
      </div>

      <div>
        <label className="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
          Deskripsi Produk
        </label>
        <Textarea
          value={form.data.description}
          onChange={(e) => form.setData('description', e.target.value)}
          placeholder="Jelaskan spesifikasi detail, kondisi, atau deskripsi produk..."
          className="w-full h-24 resize-none"
        />
        <ValidationMessage message={form.errors.description} />
      </div>

      <div className="grid gap-4 sm:grid-cols-2">
        <div>
          <label className="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
            Harga Produk (IDR)
          </label>
          <Input
            type="number"
            min={0}
            value={form.data.price}
            onChange={(e) => form.setData('price', Number(e.target.value))}
            placeholder="0"
            className="w-full"
          />
          <ValidationMessage message={form.errors.price} />
        </div>

        <div>
          <label className="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
            Stok Barang
          </label>
          <Input
            type="number"
            min={0}
            value={form.data.stock}
            onChange={(e) => form.setData('stock', Number(e.target.value))}
            placeholder="0"
            className="w-full"
            disabled={!!product} // Stok di-disable saat edit karena stok dikelola lewat fitur Restock
          />
          {product && (
            <p className="mt-1 text-[10px] text-slate-400 leading-relaxed">
              * Perubahan stok pada produk yang sudah terdaftar hanya dapat dilakukan melalui menu <strong>Restock Products</strong>.
            </p>
          )}
          <ValidationMessage message={form.errors.stock} />
        </div>
      </div>

      <div>
        <label className="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
          Status Produk
        </label>
        <Select
          value={form.data.status}
          onChange={(e) => form.setData('status', e.target.value)}
          className="w-full"
        >
          <option value="draft">Draft (Persiapan)</option>
          <option value="active">Active (Siap Dijual)</option>
          <option value="archived">Archived (Diarsipkan)</option>
        </Select>
        <ValidationMessage message={form.errors.status} />
      </div>

      <div className="pt-2">
        <Button
          className="w-full bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition"
          disabled={form.processing}
        >
          {form.processing
            ? 'Menyimpan...'
            : product
            ? 'Simpan Perubahan'
            : 'Simpan Produk Baru'}
        </Button>
      </div>
    </form>
  );
}
