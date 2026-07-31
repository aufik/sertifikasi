import { Head, Link, useForm } from '@inertiajs/react';
import GuestLayout from '@/Layouts/GuestLayout';
import { Input } from '@/Components/Form/Input';
import { Button } from '@/Components/Form/Button';
import { ValidationMessage } from '@/Components/Feedback/ValidationMessage';
import { ShieldCheck, Award, Clock, CheckCircle } from 'lucide-react';

/** Halaman Login untuk Uji Kompetensi Sertifikasi BNSP. */
export default function Login() {
  const form = useForm<{ email: string; password: string }>({
    email: '',
    password: '',
  });

  const submit = (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    form.post('/login', {
      preserveScroll: true,
      onFinish: () => form.reset('password'),
    });
  };

  return (
    <GuestLayout>
      <Head title="Login Uji Kompetensi BNSP" />
      <div className="flex h-screen w-screen items-center justify-center bg-slate-50">
        <div className="flex h-full w-full  overflow-hidden bg-white shadow-2xl">

          {/* Sisi Kiri - Branding & Informasi */}
          <div className="hidden w-1/2 bg-gradient-to-br from-emerald-600 to-teal-700 p-12 text-white md:flex md:flex-col md:justify-between">
            <div>
              <div className="mb-10 flex items-center gap-3">
                <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20">
                  <ShieldCheck className="h-7 w-7 text-white" />
                </div>
                <div>
                  <h1 className="text-xl font-bold tracking-tight">BNSP</h1>
                  <p className="text-xs text-white/80">Badan Nasional Sertifikasi Profesi</p>
                </div>
              </div>

              <div className="space-y-4">
                <h2 className="text-4xl font-bold leading-tight">
                  Selamat Datang,
                  <br />
                  <span className="text-emerald-200">M Taufik Kamil</span>
                </h2>
                <p className="text-sm text-white/80 leading-relaxed max-w-sm">
                  Silakan login untuk melihat system.
                </p>

                <div className="mt-8 space-y-3">
                  <div className="flex items-center gap-3 rounded-lg bg-white/10 p-3">
                    <Award className="h-5 w-5 text-emerald-200" />
                    <div>
                      <p className="text-sm font-medium">Sabtu, 1 Agustus 2026</p>
                      <p className="text-xs text-white/70"></p>
                    </div>
                  </div>
                  <div className="flex items-center gap-3 rounded-lg bg-white/10 p-3">
                    <Clock className="h-5 w-5 text-emerald-200" />
                    <div>
                      <p className="text-sm font-medium">Senior Programmer</p>
                      <p className="text-xs text-white/70"></p>
                    </div>
                  </div>
                  {/* <div className="flex items-center gap-3 rounded-lg bg-white/10 p-3">
                    <CheckCircle className="h-5 w-5 text-emerald-200" />
                    <div>
                      <p className="text-sm font-medium">Hasil Cepat & Akurat</p>
                      <p className="text-xs text-white/70">Dapatkan hasil ujian dalam waktu 3x24 jam</p>
                    </div>
                  </div> */}
                </div>
              </div>
            </div>

            <div className="border-t border-white/20 pt-4">
              <p className="text-xs text-white/60">
                © 2026 Badan Nasional Sertifikasi Profesi (BNSP)
              </p>
            </div>
          </div>

          {/* Sisi Kanan - Form Login */}
          <div className="flex w-full flex-col justify-center p-8 md:w-1/2 md:p-16">
            <div className="mb-8">
              <h3 className="text-2xl font-bold text-slate-800">Login Peserta</h3>
              <p className="mt-1 text-sm text-slate-500">
                Masuk untuk memulai uji kompetensi Anda
              </p>
            </div>

            <form onSubmit={submit} className="space-y-5">
              <div>
                <label className="mb-1.5 block text-sm font-medium text-slate-700">
                  Email Address
                </label>
                <Input
                  autoComplete="email"
                  autoFocus
                  name="email"
                  onChange={(event) => form.setData('email', event.target.value)}
                  placeholder="nama@email.com"
                  type="email"
                  value={form.data.email}
                  className="w-full rounded-lg border-slate-300 px-4 py-2.5 focus:border-emerald-500 focus:ring-emerald-500"
                />
                <ValidationMessage message={form.errors.email} />
              </div>

              <div>
                <div className="mb-1.5 flex items-center justify-between">
                  <label className="text-sm font-medium text-slate-700">
                    Password
                  </label>
                  <Link
                    href="/forgot-password"
                    className="text-sm text-emerald-600 hover:text-emerald-700 hover:underline"
                  >
                    Lupa password?
                  </Link>
                </div>
                <Input
                  autoComplete="current-password"
                  name="password"
                  onChange={(event) => form.setData('password', event.target.value)}
                  placeholder="••••••••"
                  type="password"
                  value={form.data.password}
                  className="w-full rounded-lg border-slate-300 px-4 py-2.5 focus:border-emerald-500 focus:ring-emerald-500"
                />
                <ValidationMessage message={form.errors.password} />
              </div>

              <div className="flex items-center justify-between">
                <label className="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                  <input
                    type="checkbox"
                    className="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                  />
                  <span>Ingat saya</span>
                </label>
              </div>

              <Button
                className="w-full bg-emerald-600 font-semibold hover:bg-emerald-700 transition-colors rounded-lg py-2.5 text-white"
                disabled={form.processing}
                type="submit"
              >
                {form.processing ? 'Memproses...' : 'MASUK'}
              </Button>

              <div className="mt-4 text-center text-sm text-slate-500">
                Belum memiliki akun peserta?{' '}
                <Link href="/register" className="font-semibold text-emerald-600 hover:text-emerald-700 hover:underline">
                  Daftar Sekarang
                </Link>
              </div>
            </form>

            {/* Informasi Akun Demo */}
            <div className="mt-6 rounded-lg bg-slate-50 p-4 border border-slate-200">
              <p className="text-xs font-semibold text-slate-600">🔑 Akun Demo Peserta:</p>
              <div className="mt-1.5 flex flex-wrap items-center gap-4 text-xs text-slate-500">
                <div className="flex items-center gap-1">
                  <span className="font-medium">Email:</span>
                  <span className="font-mono select-all bg-white px-2 py-0.5 rounded border border-slate-200">peserta@bnsp.test</span>
                </div>
                <div className="flex items-center gap-1">
                  <span className="font-medium">Password:</span>
                  <span className="font-mono select-all bg-white px-2 py-0.5 rounded border border-slate-200">password</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </GuestLayout>
  );
}
