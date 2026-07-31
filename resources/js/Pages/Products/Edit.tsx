import { PageHeader } from '@/Components/Common/PageHeader';
import AppLayout from '@/Layouts/AppLayout';
import { ProductForm } from '@/Pages/Products/Partials/ProductForm';
import type { Category, Product } from '@/Types/product';
export default function ProductsEdit({ product, categories }: { product: Product; categories: Category[] }) { return <AppLayout><PageHeader title="Edit Product" description={product.sku} /><ProductForm product={product} categories={categories} /></AppLayout>; }
