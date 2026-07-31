import { PageHeader } from '@/Components/Common/PageHeader';
import AppLayout from '@/Layouts/AppLayout';
import { ProductForm } from '@/Pages/Products/Partials/ProductForm';
import type { Category } from '@/Types/product';
export default function ProductsCreate({ categories }: { categories: Category[] }) { return <AppLayout><PageHeader title="Create Product" description="Add a new product." /><ProductForm categories={categories} /></AppLayout>; }
