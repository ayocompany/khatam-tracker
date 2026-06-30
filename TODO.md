# TODO - Fitur Utama Quran Layout & Master Data

- [x] Review model & migration terkait user, surah, verse, page mappings
- [x] Tambah migration master `quran_layouts`
- [x] Tambah kolom preferensi layout di `users` (`quran_layout_code`)
- [x] Buat model `QuranLayout`
- [x] Update `User` model relation + fillable
- [x] Buat seeder master data layout Quran
- [x] Buat controller dashboard untuk kirim summary dinamis
- [x] Tambah route update pilihan layout user
- [x] Update `Dashboard.vue` agar:
  - [x] bisa pilih jenis Quran/layout
  - [x] menampilkan total halaman, total ayat, total surat, baris per halaman (dinamis)
- [x] Jalankan verifikasi minimal (syntax/build)

---

## Catatan sebelumnya: Hard Remove Email (OTP-only Auth)

- [ ] Update users base migration to remove `email` and `email_verified_at`
- [x] Add migration for existing DB to drop `email`, `email_verified_at`, and `password_reset_tokens` table
- [x] Update `OtpAuthController` user creation to remove `email` assignment
- [x] Update `User` model fillable/casts to remove email-related fields
- [x] Update `UserFactory` to remove email fields and unverified state dependency
- [x] Run migration and run tests
- [x] Report testing status (critical-path done, remaining coverage if any)
