# MelatiChain - Global Supply Chain Risk Intelligence 🌍⚓

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)
![Chart.js](https://img.shields.io/badge/Chart.js-FF6384?style=for-the-badge&logo=chartdotjs&logoColor=white)
![Leaflet](https://img.shields.io/badge/Leaflet-199900?style=for-the-badge&logo=leaflet&logoColor=white)

**MelatiChain** adalah sebuah platform intelijen risiko rantai pasok global (Global Supply Chain Risk Intelligence Platform). Aplikasi web ini dirancang untuk memantau, menganalisis, dan memvisualisasikan faktor risiko operasional, ekonomi, cuaca, dan sentimen intelijen berita di berbagai negara di seluruh dunia secara *real-time*.

## 👨‍💻 Dikembangkan Oleh
- **Nama** : Melati Nur Sabil
- **NIM**  : 240180044

---

## 🌟 Fitur Utama
1. **Global Country Dashboard**: Dasbor sentral untuk memantau negara-negara secara spesifik, lengkap dengan metrik cuaca (*suhu & kecepatan angin*) dan ekonomi (*GDP & inflasi*).
2. **Interactive Supply Chain Risk Map**: Pemetaan geografis menggunakan *Leaflet.js* untuk melihat secara visual distribusi negara dan lokasi pelabuhan aktif dunia.
3. **Country Risk Comparison Engine**: Mesin analitik untuk membandingkan tingkat risiko (*weather*, *economic*, *sentiment*) antara dua negara untuk menentukan rute/mitra logistik terbaik, yang dilengkapi dengan saran berbasis AI.
4. **Data Visualization Dashboard**: Visualisasi analitik komprehensif menggunakan *Chart.js*, meliputi *Global Risk Distribution*, *Economic Impact vs Risk Score*, dan tren makroekonomi (Inflasi & Nilai Tukar Mata Uang).
5. **Intelligence Feed (Sentiment Analysis)**: Pengumpulan berita lokal secara *real-time* dengan pelabelan sentimen (Positif/Netral/Negatif) otomatis menggunakan algoritma *Lexicon-based Analysis*.
6. **Country Watchlist System**: Memungkinkan pengguna menyimpan dan memantau secara intensif negara-negara tertentu.

## ⚙️ Integrasi API
Platform ini ditenagai oleh 7 layanan API Eksternal untuk menjamin akurasi data *real-time*:
- **Open-Meteo API**: Pengambilan data iklim dan cuaca.
- **World Bank API**: Indikator makroekonomi (GDP, Populasi, Inflasi).
- **REST Countries API**: Referensi data dasar dan koordinat negara.
- **ExchangeRate-API**: Fluktuasi nilai tukar mata uang global ke USD.
- **World Port Index API**: Lokasi pelabuhan maritim internasional.
- **GNews API**: Umpan berita lokal intelijen.
- **OpenStreetMap**: Ubin (*tiles*) peta dasar gratis untuk visualisasi geografis.

## 🛠️ Panduan Instalasi (Development)
Jika Anda ingin menjalankan proyek ini secara lokal:

1. Kloning repositori ini:
   ```bash
   git clone https://github.com/MelatiNurr/Project-Final.git
   ```
2. Masuk ke dalam folder proyek:
   ```bash
   cd MelatiChain
   ```
3. Instal semua dependensi PHP (Composer) dan Node.js:
   ```bash
   composer install
   npm install
   ```
4. Salin file `.env.example` menjadi `.env` lalu sesuaikan konfigurasi *database* (MySQL disarankan):
   ```bash
   cp .env.example .env
   ```
5. Bangkitkan *App Key* Laravel:
   ```bash
   php artisan key:generate
   ```
6. Jalankan *migrasi* database:
   ```bash
   php artisan migrate
   ```
7. Ambil dan sinkronisasikan data API secara keseluruhan (perlu koneksi internet stabil):
   ```bash
   php artisan api:fetch --type=all
   ```
8. Jalankan *server*:
   ```bash
   php artisan serve
   ```
9. Buka di browser: `http://localhost:8000`

---
*Proyek ini dikembangkan sebagai bagian dari tugas akhir / evaluasi akademik.*
