# Dokumentasi API - LSP_backend

Base URL: `http://<host>/api/`

Autentikasi:
- Beberapa endpoint (profile) menggunakan `auth:sanctum` middleware — sertakan header `Authorization: Bearer <token>`.

Ringkasan endpoint (dikelompokkan berdasarkan prefix):

**Asesi**:
- GET `/asesi/`: List all asesi
- POST `/asesi/`: Create asesi
- GET `/asesi/{id}`: Show asesi detail
- PUT `/asesi/{id}`: Update asesi
- DELETE `/asesi/{id}`: Delete asesi
- GET `/asesi/export`: Export data asesi

**Asesor**:
- GET `/asesor/`: List all asesor
- POST `/asesor/`: Create asesor
- GET `/asesor/{id}`: Show asesor
- PUT `/asesor/{id}`: Update asesor
- DELETE `/asesor/{id}`: Delete asesor
- GET `/asesor/export`: Export data asesor

**TUKs**:
- GET `/tuks/`: List all TUK
- POST `/tuks/`: Create TUK
- GET `/tuks/{id}`: Show TUK
- PUT/PATCH `/tuks/{id}`: Update TUK
- DELETE `/tuks/{id}`: Delete TUK

**Skema**:
- GET `/skema/`: List all skema
- POST `/skema/`: Create skema
- GET `/skema/{id}`: Show skema
- PUT/PATCH `/skema/{id}`: Update skema
- DELETE `/skema/{id}`: Delete skema

**Unit**:
- GET `/unit/`: List all unit
- POST `/unit/`: Create unit
- GET `/unit/skema/{skemaId}`: Get units by skema
- GET `/unit/{id}`: Show unit
- PUT/PATCH `/unit/{id}`: Update unit
- DELETE `/unit/{id}`: Delete unit

**Element**:
- GET `/element/`: List all element
- POST `/element/`: Create element
- GET `/element/unit/{unitId}`: Get elements by unit
- GET `/element/{id}`: Show element
- PUT/PATCH `/element/{id}`: Update element
- DELETE `/element/{id}`: Delete element

**Kriteria Kerja**:
- GET `/kriteria-kerja/`: List all kriteria
- POST `/kriteria-kerja/`: Create kriteria
- GET `/kriteria-kerja/element/{elementId}`: Get kriteria by element
- GET `/kriteria-kerja/{id}`: Show kriteria
- PUT/PATCH `/kriteria-kerja/{id}`: Update kriteria
- DELETE `/kriteria-kerja/{id}`: Delete kriteria

**Users & Profile**:
- GET `/users/`: List users
- POST `/users/`: Create user
- GET `/users/{id}`: Show user
- PUT/PATCH `/users/{id}`: Update user
- DELETE `/users/{id}`: Delete user
- POST `/users/{id}/reset-password`: Reset password for user

- (Authenticated) GET `/profile/`: Get current user profile
- (Authenticated) PUT `/profile/`: Update current profile
- (Authenticated) PUT `/profile/password`: Change password
- (Authenticated) GET `/profile/permissions`: Get current user permissions

**MUKs**:
- GET `/muks/`: List MUK (pagination)
- POST `/muks/`: Create MUK
- GET `/muks/skema/{skemaId}`: Get MUK by skema
- GET `/muks/{id}/detail`: Get full detail of MUK
- GET `/muks/{id}/print`: Print single MUK
- GET `/muks/skema/{skemaId}/print`: Print by skema
- GET `/muks/{id}`: Show MUK
- PUT/PATCH `/muks/{id}`: Update MUK
- DELETE `/muks/{id}`: Delete MUK

**Dokumen**:
- GET `/dokumen/`: List dokumen (pagination)
- POST `/dokumen/`: Create dokumen
- GET `/dokumen/muk/{mukId}`: Get dokumen by MUK
- GET `/dokumen/{id}`: Show dokumen
- PUT/PATCH `/dokumen/{id}`: Update dokumen
- DELETE `/dokumen/{id}`: Delete dokumen

**Jadwal Ujikom**:
- GET `/jadwal-ujikom/`: List jadwal (pagination & filters)
- POST `/jadwal-ujikom/`: Create jadwal
- GET `/jadwal-ujikom/metadata`: Get metadata for form
- GET `/jadwal-ujikom/skema/{skemaId}`: Get jadwal by skema
- GET `/jadwal-ujikom/tuk/{tukId}`: Get jadwal by TUK
- GET `/jadwal-ujikom/{id}/kuota`: Get available kuota for jadwal
- GET `/jadwal-ujikom/{id}`: Show jadwal detail
- PUT/PATCH `/jadwal-ujikom/{id}`: Update jadwal
- DELETE `/jadwal-ujikom/{id}`: Delete jadwal

**Permohonan Sertifikasi (APL-01, APL-02)**:
- GET `/permohonan/`: List permohonan (dengan filters)
- POST `/permohonan/`: Create permohonan
- GET `/permohonan/summary`: Get summary/statistics
- GET `/permohonan/tipe/{tipeApl}`: Filter by tipe APL (APL-01/APL-02)
- GET `/permohonan/status/{status}`: Filter by status
- GET `/permohonan/asesi/{asesiId}`: Get permohonan by asesi
- GET `/permohonan/{id}`: Show permohonan detail
- PUT/PATCH `/permohonan/{id}`: Update permohonan
- DELETE `/permohonan/{id}`: Delete permohonan

**Tahun Aktif (Pengaturan)**:
- GET `/tahun-aktif/`: List tahun aktif
- POST `/tahun-aktif/`: Create tahun aktif
- GET `/tahun-aktif/active`: Get currently active tahun
- GET `/tahun-aktif/{id}`: Show detail
- PUT/PATCH `/tahun-aktif/{id}`: Update
- PUT `/tahun-aktif/{id}/set-active`: Set tahun aktif
- DELETE `/tahun-aktif/{id}`: Delete

**Kop Surat (Pengaturan)**:
- GET `/kop-surat/`: List kop surat
- POST `/kop-surat/`: Create kop surat
- GET `/kop-surat/config`: Get konfigurasi kop surat (first record)
- GET `/kop-surat/{id}`: Show
- PUT/PATCH `/kop-surat/{id}`: Update
- DELETE `/kop-surat/{id}`: Delete

**Asesor Skema (Penugasan)**:
- GET `/asesor-skema/skema/{skemaId}/asesor`: Get asesor by skema
- GET `/asesor-skema/asesor/{asesorId}/skema`: Get skema by asesor
- POST `/asesor-skema/assign`: Assign single asesor to skema
- POST `/asesor-skema/assign-multiple`: Assign multiple asesor to skema
- DELETE `/asesor-skema/{asesorId}/skema/{skemaId}`: Remove single asesor
- POST `/asesor-skema/remove-multiple`: Remove multiple asesor
- POST `/asesor-skema/sync`: Sync (replace assignments)
- GET `/asesor-skema/check/{asesorId}/skema/{skemaId}`: Check assignment

**Guru (Halaman Guru)**:
- GET `/guru/dashboard`: Dashboard summary

- GET `/guru/jadwal-ujikom/`: List jadwal tugas guru
- GET `/guru/jadwal-ujikom/{id}`: Show jadwal tugas guru

- GET `/guru/penilaian/asesi`: Get daftar asesi untuk penilaian
- GET `/guru/penilaian/asesi/{asesiId}`: Get penilaian by asesi
- POST `/guru/penilaian/`: Input/update nilai
- PATCH `/guru/penilaian/{id}/complete`: Mark penilaian complete

- Referensi:
  - GET `/guru/referensi/skema`
  - GET `/guru/referensi/skema/{skemaId}/detail`
  - GET `/guru/referensi/skema/{skemaId}/download`
  - GET `/guru/referensi/skema/{skemaId}/unit`
  - GET `/guru/referensi/unit/{unitId}/element`
  - GET `/guru/referensi/element/{elementId}/kriteria`
  - GET `/guru/referensi/skema/{skemaId}/muk`

**Admin Monitoring Guru**:
- GET `/admin/guru-monitoring/activity-logs`: Get all activity logs
- GET `/admin/guru-monitoring/activity-logs/summary`: Activity summary
- GET `/admin/guru-monitoring/activity-logs/statistics`: Statistics
- GET `/admin/guru-monitoring/activity-logs/asesor/{asesorId}`: Get logs by guru

Catatan & langkah selanjutnya:
- File ini dibuat secara otomatis berdasarkan isi `routes/api.php`.
- Jika Anda ingin format OpenAPI/Swagger atau contoh request/response, beri tahu saya agar saya tambahkan.

---
Dokumentasi tersimpan di root proyek: [API_DOCUMENTATION.md](API_DOCUMENTATION.md)
