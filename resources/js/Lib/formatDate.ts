import { format } from 'date-fns';
export function formatDate(value: string | null): string { return value ? format(new Date(value), 'dd MMM yyyy HH:mm') : '-'; }
