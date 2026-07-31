<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTO\RestockProductDTO;
use Illuminate\Foundation\Http\FormRequest;

final class RestockProductRequest extends FormRequest
{
    /** Izin akses dicek di ProductPolicy, jadi request ini cukup mengizinkan proses validasi. */
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, list<string>> */
    /** Cek jumlah restock dan alasan sebelum stok benar-benar ditambahkan. */
    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'reason' => ['nullable', 'string', 'max:255'],
        ];
    }

    /** Ubah data yang sudah valid jadi DTO supaya lebih rapi saat dipakai service. */
    public function toDto(): RestockProductDTO
    {
        return new RestockProductDTO(
            (int) $this->validated('quantity'),
            $this->validated('reason') === null ? null : (string) $this->validated('reason')
        );
    }
}
