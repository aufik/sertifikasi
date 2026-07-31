import { Button } from '@/Components/Form/Button';
export function ConfirmDialog({ message, onConfirm }: { message: string; onConfirm: () => void }) { return <Button className="bg-red-600 hover:bg-red-700" onClick={() => window.confirm(message) && onConfirm()}>Confirm</Button>; }
