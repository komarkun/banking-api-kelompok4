# kelompok4-banking-api
## TSA IT Perbankan - Homework Week 4
https://www.rakamin.com/

## W4. Homework Back End Development - PHP Fundamentals
https://www.rakamin.com/dashboard/homework-exam/58119

## Kelompok 4
1. Muhamad Komar Hidayat | https://github.com/komarkun |
2. Ayu Permata Widya Nurizma | https://github.com/WidyaNurizma |
3. Kelvin Pahotton Simamora | https://github.com/kelvin77777|
4. Alfan Kasyfil Aziz |https://github.com/Poh4|
5. Matias Ariel Haga Gulo | https://github.com/MatiasGulo |
6. Andrew Jonatan Damanik | https://github.com/drewjd27 |
7. Rizky Hamdani Sakti | https://github.com/Rizkyhamm |
8. Siti Norhalisa | https://github.com/sitinorhalisaa11 |
9. Setiawan | https://github.com/magdubi |
10. Andi Muliawijaya | https://github.com/and1mw |

# 1. Fitur API create account & tambah saldo & delete account & menampilkan saldo
## Langkah Penggunaan API
## saran aja lebih enak pakai postman buat test API (gak wajib)
### 1. Buka post man klik new workspace pilih HTTP
### 2. Masukan link http://localhost/banking-api-kelompok4/api/account.php
### 3. Pilih Body dan x-www-form-urlencoded
### 4.  masukan key dan value

## Untuk Create User & tambah saldo awal pakai POST Method di body lalu centang x-www-form-urlencoded
### |  Key = akun_id |   Value = "nomor akun"      | 
### |  Key = saldo         |   Value = "integer bebas"   | 

## Untuk delete user pakai POST Methot di body lalu centang x-www-form-urlencoded
### |  Key = akun_id |   Value = "nomor akun"      | 
### |  Key = action         |   Value = delete            |

## Untuk menampilkan saldo pakai GET Method di params
### http://localhost/banking-api-kelompok4/api/balance.php
### |  Key = akun_id |   Value = "nomor akun"      |       

# 2. Fitur API transfer dari satu akun ke akun lainnya
## Langkah Penggunaan API
## saran aja lebih enak pakai postman buat test API (gak wajib)
### 1. Buka post man klik new workspace pilih HTTP
### 2. Masukan link http://localhost/banking-api-kelompok4-main/api/transfer.php 
### 3. Pilih Body dan x-www-form-urlencoded
### 4.  masukan key dan value

## Untuk transfer dari satu akun ke akun lainnya pakai POST Method di body lalu centang x-www-form-urlencoded
### |  Key = sender_id |   Value = "dari akun siapa"      | 
### |  Key = receiver_id   |   Value = "ke akun siapa"   | 
### |  Key = saldo   |   Value = "jumlah saldo yang akan ditransfer"   | 

## Untuk cek riwayat transaction menggunakan method GET di param 
### Masukan link http://localhost/banking-api-kelompok4-main/api/transaction.php 
### | Key = akun_id | Value = "akun yg ingin di cek riwayat transaksinya"