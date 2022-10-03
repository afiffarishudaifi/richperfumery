/*
 Navicat Premium Data Transfer

 Source Server         : server_richperfumery_niagahoster
 Source Server Type    : MySQL
 Source Server Version : 100517
 Source Host           : 45.130.231.164:3306
 Source Schema         : u1131016_richperfumery

 Target Server Type    : MySQL
 Target Server Version : 100517
 File Encoding         : 65001

 Date: 03/10/2022 11:36:52
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for log_history
-- ----------------------------
DROP TABLE IF EXISTS `log_history`;
CREATE TABLE `log_history`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_tabel` int NULL DEFAULT NULL,
  `id_user` int NULL DEFAULT NULL,
  `nama_user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `tgl_log` timestamp(0) NULL DEFAULT current_timestamp(0),
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 40739 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for m_barang_closing
-- ----------------------------
DROP TABLE IF EXISTS `m_barang_closing`;
CREATE TABLE `m_barang_closing`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_barang` int NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 27 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for m_detail_produk
-- ----------------------------
DROP TABLE IF EXISTS `m_detail_produk`;
CREATE TABLE `m_detail_produk`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_produk` int NULL DEFAULT NULL,
  `id_barang` int NULL DEFAULT NULL,
  `jumlah` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_produk_relasi`(`id_produk`) USING BTREE,
  CONSTRAINT `id_produk_relasi` FOREIGN KEY (`id_produk`) REFERENCES `m_produk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 36042 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for m_metode
-- ----------------------------
DROP TABLE IF EXISTS `m_metode`;
CREATE TABLE `m_metode`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `urutan` int NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  `status` int NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for m_nominal_poin
-- ----------------------------
DROP TABLE IF EXISTS `m_nominal_poin`;
CREATE TABLE `m_nominal_poin`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nominal` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for m_pelanggan
-- ----------------------------
DROP TABLE IF EXISTS `m_pelanggan`;
CREATE TABLE `m_pelanggan`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `telp` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `alamat` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `status` int NULL DEFAULT NULL COMMENT '1 = pelanggan\r\n2 = member\r\n3 = karyawan\r\n4 = reseller',
  `no_member` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `tempat` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `tanggal_lahir` date NULL DEFAULT NULL,
  `email` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `jenis_kelamin` int NULL DEFAULT NULL,
  `id_gudang` int NULL DEFAULT 9,
  `tanggal_awal` date NULL DEFAULT '2019-12-31',
  `tanggal_akhir` date NULL DEFAULT '2021-12-30',
  `status_aktif` int NULL DEFAULT 1 COMMENT '1 = aktif, 2 = tidak aktif',
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 29940 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for m_pengirim
-- ----------------------------
DROP TABLE IF EXISTS `m_pengirim`;
CREATE TABLE `m_pengirim`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `lokasi` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `no_hp` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for m_produk
-- ----------------------------
DROP TABLE IF EXISTS `m_produk`;
CREATE TABLE `m_produk`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_produk` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nama` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `harga` bigint NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  `id_type_ukuran` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3745 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for m_produk_mapping
-- ----------------------------
DROP TABLE IF EXISTS `m_produk_mapping`;
CREATE TABLE `m_produk_mapping`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_type_ukuran` int NULL DEFAULT NULL,
  `harga` bigint NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for m_produkpoin
-- ----------------------------
DROP TABLE IF EXISTS `m_produkpoin`;
CREATE TABLE `m_produkpoin`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_produk` int NULL DEFAULT NULL,
  `kategori` int NULL DEFAULT NULL,
  `hari` int NULL DEFAULT NULL,
  `tanggal` date NULL DEFAULT NULL,
  `poin` int NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for m_profil
-- ----------------------------
DROP TABLE IF EXISTS `m_profil`;
CREATE TABLE `m_profil`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `inisial` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `telp` varchar(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `jenis_outlet` int NULL DEFAULT NULL,
  `alamat` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for m_rekening
-- ----------------------------
DROP TABLE IF EXISTS `m_rekening`;
CREATE TABLE `m_rekening`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for password_resets
-- ----------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets`  (
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  INDEX `password_resets_email_index`(`email`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for pengiriman
-- ----------------------------
DROP TABLE IF EXISTS `pengiriman`;
CREATE TABLE `pengiriman`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_pengiriman` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `gudang_awal` int NULL DEFAULT NULL,
  `gudang_tujuan` int NULL DEFAULT NULL,
  `id_pengiriman` int NULL DEFAULT NULL,
  `status_pengiriman` int NULL DEFAULT NULL COMMENT '1=perjalan,2=sudah sampai',
  `tanggal_pengiriman` timestamp(0) NULL DEFAULT NULL,
  `tanggal_penerimaan` timestamp(0) NULL DEFAULT NULL,
  `keterangan` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1515 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for pengiriman_detail
-- ----------------------------
DROP TABLE IF EXISTS `pengiriman_detail`;
CREATE TABLE `pengiriman_detail`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_inv_pengiriman` int NULL DEFAULT NULL,
  `id_barang` int NULL DEFAULT NULL,
  `id_log_stok` int NULL DEFAULT NULL,
  `id_log_stok_penerimaan` int NULL DEFAULT NULL,
  `nama` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `jumlah` int NULL DEFAULT NULL,
  `retur` int NULL DEFAULT NULL,
  `diterima` int NULL DEFAULT NULL,
  `status` int NULL DEFAULT NULL,
  `id_satuan` int NULL DEFAULT NULL,
  `harga` double(12, 0) NULL DEFAULT 0,
  `total` double(12, 0) NULL DEFAULT 0,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  `keterangan` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `keterangan_penerimaan` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `pengiriman`(`id_inv_pengiriman`) USING BTREE,
  INDEX `pengiriman_log_stok`(`id_log_stok`) USING BTREE,
  INDEX `barang_peng`(`id_barang`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 18724 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for pengiriman_retur
-- ----------------------------
DROP TABLE IF EXISTS `pengiriman_retur`;
CREATE TABLE `pengiriman_retur`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_barang` int NULL DEFAULT NULL,
  `id_gudang_pusat` int NULL DEFAULT NULL,
  `id_gudang_outlet` int NULL DEFAULT NULL,
  `id_pengiriman` int NULL DEFAULT NULL,
  `kode_retur` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `tanggal_pengiriman` datetime(0) NULL DEFAULT NULL,
  `tanggal_penerimaan` datetime(0) NULL DEFAULT NULL,
  `jumlah` int NULL DEFAULT NULL,
  `jumlah_terima` int NULL DEFAULT NULL,
  `status` int NULL DEFAULT NULL COMMENT '1,2 = 1 untuk dikirim 2 diterima',
  `status_penerimaan` int NULL DEFAULT 1,
  `id_satuan` int NULL DEFAULT NULL,
  `id_log_stok` int NULL DEFAULT NULL,
  `id_log_stok_penerimaan` int NULL DEFAULT NULL,
  `keterangan` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `keterangan_penerimaan` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `pengiriman_detail_relasi`(`id_barang`) USING BTREE,
  INDEX `pengirimand_log_stok`(`id_log_stok`) USING BTREE,
  CONSTRAINT `pengirimand_log_stok` FOREIGN KEY (`id_log_stok`) REFERENCES `tbl_log_stok` (`log_stok_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1362 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for ref_gudang
-- ----------------------------
DROP TABLE IF EXISTS `ref_gudang`;
CREATE TABLE `ref_gudang`  (
  `id` tinyint NOT NULL AUTO_INCREMENT,
  `id_profil` int NULL DEFAULT NULL,
  `nama` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `alamat` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `status` tinyint NULL DEFAULT NULL COMMENT '1=aktif,2=tidak aktif',
  `kode` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for ta_detail_gudang
-- ----------------------------
DROP TABLE IF EXISTS `ta_detail_gudang`;
CREATE TABLE `ta_detail_gudang`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_ta_gudang` int NULL DEFAULT NULL,
  `id_barang` int NULL DEFAULT NULL,
  `jumlah` int NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for ta_gudang
-- ----------------------------
DROP TABLE IF EXISTS `ta_gudang`;
CREATE TABLE `ta_gudang`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_ref_gudang` int NULL DEFAULT NULL,
  `id_inv_pengiriman` int NULL DEFAULT NULL,
  `jumlah` int NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_akun
-- ----------------------------
DROP TABLE IF EXISTS `tbl_akun`;
CREATE TABLE `tbl_akun`  (
  `akun_id` int NOT NULL AUTO_INCREMENT,
  `akun_id_parent` int NULL DEFAULT NULL,
  `akun_kode` char(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `akun_nama` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `akun_saldo_normal` enum('D','K') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`akun_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_barang
-- ----------------------------
DROP TABLE IF EXISTS `tbl_barang`;
CREATE TABLE `tbl_barang`  (
  `barang_id` int NOT NULL AUTO_INCREMENT,
  `satuan_id` int NULL DEFAULT NULL,
  `barang_kode` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `barang_nama` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `barang_id_parent` int NULL DEFAULT NULL,
  `barang_status_bahan` enum('1','2') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `barang_alias` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `paper` enum('1','0') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`barang_id`) USING BTREE,
  INDEX `satuan_relation`(`satuan_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 762 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_detail_harga_barang
-- ----------------------------
DROP TABLE IF EXISTS `tbl_detail_harga_barang`;
CREATE TABLE `tbl_detail_harga_barang`  (
  `detail_harga_barang_id` int NOT NULL AUTO_INCREMENT,
  `barang_id` int NULL DEFAULT NULL,
  `detail_harga_barang_tanggal` date NULL DEFAULT NULL,
  `detail_harga_barang_harga_jual` int NULL DEFAULT NULL,
  `created_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`detail_harga_barang_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_group
-- ----------------------------
DROP TABLE IF EXISTS `tbl_group`;
CREATE TABLE `tbl_group`  (
  `group_id` int NOT NULL AUTO_INCREMENT,
  `group_nama` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `group_aktif` int NULL DEFAULT 2,
  `created_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`group_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_kasir
-- ----------------------------
DROP TABLE IF EXISTS `tbl_kasir`;
CREATE TABLE `tbl_kasir`  (
  `id_kasir` int NOT NULL AUTO_INCREMENT,
  `id_pelanggan` int NULL DEFAULT NULL,
  `no_faktur` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `tanggal` date NULL DEFAULT NULL,
  `tanggal_tempo` date NULL DEFAULT NULL,
  `tanggal_faktur` date NULL DEFAULT NULL,
  `tanggal_bayar` date NULL DEFAULT NULL,
  `uang_muka` int NULL DEFAULT NULL,
  `ongkos_kirim` int NULL DEFAULT NULL,
  `carabayar` int NULL DEFAULT NULL,
  `metodebayar` int NULL DEFAULT NULL,
  `metodebayar2` int NULL DEFAULT NULL,
  `id_rekening` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `no_rekening` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `total_subtotal` int NULL DEFAULT NULL,
  `total_tagihan` int NULL DEFAULT NULL,
  `total_potongan` int NULL DEFAULT NULL,
  `total_metodebayar` int NULL DEFAULT 0,
  `total_metodebayar2` int NULL DEFAULT 0,
  `total_bayar` int NULL DEFAULT NULL,
  `keterangan` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `status` varchar(5) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `jenis_transaksi` int NULL DEFAULT NULL,
  `status_posting` int NULL DEFAULT 1,
  `id_gudang` int NULL DEFAULT NULL,
  `created_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  `total_redeem` int NULL DEFAULT NULL,
  `status_promo` enum('YA','TIDAK') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'TIDAK',
  `created_by` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `created_iduser` int NULL DEFAULT NULL,
  `updated_by` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `updated_iduser` int NULL DEFAULT NULL,
  `metodeongkir` int NULL DEFAULT NULL,
  `metodeongkir2` int NULL DEFAULT NULL,
  `total_metodeongkir` int NULL DEFAULT NULL,
  `total_metodeongkir2` int NULL DEFAULT NULL,
  PRIMARY KEY (`id_kasir`) USING BTREE,
  UNIQUE INDEX `no_faktur`(`no_faktur`, `id_gudang`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 59169 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_kasir_batal
-- ----------------------------
DROP TABLE IF EXISTS `tbl_kasir_batal`;
CREATE TABLE `tbl_kasir_batal`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_kasir` int NOT NULL,
  `id_pelanggan` int NULL DEFAULT NULL,
  `no_faktur` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `tanggal` date NULL DEFAULT NULL,
  `tanggal_tempo` date NULL DEFAULT NULL,
  `tanggal_faktur` date NULL DEFAULT NULL,
  `tanggal_bayar` date NULL DEFAULT NULL,
  `uang_muka` int NULL DEFAULT NULL,
  `ongkos_kirim` int NULL DEFAULT NULL,
  `carabayar` int NULL DEFAULT NULL,
  `metodebayar` int NULL DEFAULT NULL,
  `metodebayar2` int NULL DEFAULT NULL,
  `id_rekening` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `no_rekening` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `total_subtotal` int NULL DEFAULT NULL,
  `total_tagihan` int NULL DEFAULT NULL,
  `total_potongan` int NULL DEFAULT NULL,
  `total_metodebayar` int NULL DEFAULT 0,
  `total_metodebayar2` int NULL DEFAULT 0,
  `total_bayar` int NULL DEFAULT NULL,
  `keterangan` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `status` varchar(5) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `jenis_transaksi` int NULL DEFAULT NULL,
  `status_posting` int NULL DEFAULT 1,
  `id_gudang` int NULL DEFAULT NULL,
  `created_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  `total_redeem` int NULL DEFAULT NULL,
  `status_promo` enum('YA','TIDAK') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'TIDAK',
  `created_by` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `created_iduser` int NULL DEFAULT NULL,
  `updated_by` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `updated_iduser` int NULL DEFAULT NULL,
  `metodeongkir` int NULL DEFAULT NULL,
  `metodeongkir2` int NULL DEFAULT NULL,
  `total_metodeongkir` int NULL DEFAULT NULL,
  `total_metodeongkir2` int NULL DEFAULT NULL,
  `deleted_iduser` int NULL DEFAULT NULL,
  `catatan` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `no_faktur`(`no_faktur`, `id_gudang`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 445 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_kasir_detail
-- ----------------------------
DROP TABLE IF EXISTS `tbl_kasir_detail`;
CREATE TABLE `tbl_kasir_detail`  (
  `id_detail_kasir` int NOT NULL AUTO_INCREMENT,
  `id_kasir` int NULL DEFAULT NULL,
  `id_detail_kasir_produk` int NULL DEFAULT NULL,
  `id_barang` int NULL DEFAULT NULL,
  `id_satuan` int NULL DEFAULT NULL,
  `id_log_stok` int NULL DEFAULT NULL,
  `jumlah` int NULL DEFAULT NULL,
  `harga` int NULL DEFAULT NULL,
  `potongan` int NULL DEFAULT NULL,
  `subtotal` int NULL DEFAULT NULL,
  `total` int NULL DEFAULT NULL,
  `ppn` int NULL DEFAULT 0,
  `cretaed_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id_detail_kasir`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 821330 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_kasir_detail_batal
-- ----------------------------
DROP TABLE IF EXISTS `tbl_kasir_detail_batal`;
CREATE TABLE `tbl_kasir_detail_batal`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_detail_kasir` int NOT NULL,
  `id_kasir` int NULL DEFAULT NULL,
  `id_detail_kasir_produk` int NULL DEFAULT NULL,
  `id_barang` int NULL DEFAULT NULL,
  `id_satuan` int NULL DEFAULT NULL,
  `id_log_stok` int NULL DEFAULT NULL,
  `jumlah` int NULL DEFAULT NULL,
  `harga` int NULL DEFAULT NULL,
  `potongan` int NULL DEFAULT NULL,
  `subtotal` int NULL DEFAULT NULL,
  `total` int NULL DEFAULT NULL,
  `ppn` int NULL DEFAULT 0,
  `cretaed_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7104 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_kasir_detail_produk
-- ----------------------------
DROP TABLE IF EXISTS `tbl_kasir_detail_produk`;
CREATE TABLE `tbl_kasir_detail_produk`  (
  `id_kasir_detail_produk` int NOT NULL AUTO_INCREMENT,
  `id_kasir` int NULL DEFAULT NULL,
  `id_produk` int NULL DEFAULT NULL,
  `jumlah` int NULL DEFAULT NULL,
  `id_satuan` int NULL DEFAULT NULL,
  `harga` int NULL DEFAULT NULL,
  `total` double(12, 0) NULL DEFAULT NULL,
  `id_log_stok` int NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  `status_redeem` int NULL DEFAULT NULL,
  `poin` double(12, 0) NOT NULL,
  `total_poin` double(12, 0) NOT NULL,
  `potongan` int NULL DEFAULT 0,
  `potongan_total` int NULL DEFAULT 0,
  PRIMARY KEY (`id_kasir_detail_produk`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 112022 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_kasir_detail_produk_batal
-- ----------------------------
DROP TABLE IF EXISTS `tbl_kasir_detail_produk_batal`;
CREATE TABLE `tbl_kasir_detail_produk_batal`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_kasir_detail_produk` int NOT NULL,
  `id_kasir` int NULL DEFAULT NULL,
  `id_produk` int NULL DEFAULT NULL,
  `jumlah` int NULL DEFAULT NULL,
  `id_satuan` int NULL DEFAULT NULL,
  `harga` int NULL DEFAULT NULL,
  `total` double(12, 0) NULL DEFAULT NULL,
  `id_log_stok` int NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  `status_redeem` int NULL DEFAULT NULL,
  `poin` double(12, 0) NOT NULL,
  `total_poin` double(12, 0) NOT NULL,
  `potongan` int NULL DEFAULT 0,
  `potongan_total` int NULL DEFAULT 0,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 900 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_kasir_detail_produk_retur
-- ----------------------------
DROP TABLE IF EXISTS `tbl_kasir_detail_produk_retur`;
CREATE TABLE `tbl_kasir_detail_produk_retur`  (
  `id_kasir_detail_produk_retur` int NOT NULL AUTO_INCREMENT,
  `id_kasir_detail_produk` int NULL DEFAULT NULL,
  `id_retur` int NULL DEFAULT NULL,
  `id_produk` int NULL DEFAULT NULL,
  `jumlah` int NULL DEFAULT NULL,
  `id_satuan` int NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id_kasir_detail_produk_retur`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_kasir_detail_retur
-- ----------------------------
DROP TABLE IF EXISTS `tbl_kasir_detail_retur`;
CREATE TABLE `tbl_kasir_detail_retur`  (
  `id_returkasir_detail` int NOT NULL AUTO_INCREMENT,
  `kode_retur` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `tanggal` date NULL DEFAULT NULL,
  `id_gudang` int NULL DEFAULT NULL,
  `id_pelanggan` int NULL DEFAULT NULL,
  `id_barang` int NULL DEFAULT NULL,
  `id_satuan` int NULL DEFAULT NULL,
  `jumlah` int NULL DEFAULT NULL,
  `harga` int NULL DEFAULT NULL,
  `total` int NULL DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `id_log_stok` int NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `update_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  `created_by` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `created_iduser` int NULL DEFAULT NULL,
  `updated_by` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `updated_iduser` int NULL DEFAULT NULL,
  PRIMARY KEY (`id_returkasir_detail`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 241 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_kasir_detail_retur_baru
-- ----------------------------
DROP TABLE IF EXISTS `tbl_kasir_detail_retur_baru`;
CREATE TABLE `tbl_kasir_detail_retur_baru`  (
  `id_detail_kasir_retur` int NOT NULL AUTO_INCREMENT,
  `id_retur` int NULL DEFAULT NULL,
  `id_kasir_detail` int NULL DEFAULT NULL,
  `id_detail_kasir_produk_retur` int NULL DEFAULT NULL,
  `id_barang` int NULL DEFAULT NULL,
  `id_satuan` int NULL DEFAULT NULL,
  `jumlah` int NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id_detail_kasir_retur`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 16 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_kasir_print
-- ----------------------------
DROP TABLE IF EXISTS `tbl_kasir_print`;
CREATE TABLE `tbl_kasir_print`  (
  `id_print` int NOT NULL AUTO_INCREMENT,
  `id_kasir` int NULL DEFAULT NULL,
  `id_user` int NULL DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id_print`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 24988 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_kasir_retur
-- ----------------------------
DROP TABLE IF EXISTS `tbl_kasir_retur`;
CREATE TABLE `tbl_kasir_retur`  (
  `id_retur` int NOT NULL AUTO_INCREMENT,
  `id_kasir` int NULL DEFAULT NULL,
  `id_pelanggan` int NULL DEFAULT NULL,
  `kode_retur` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `no_faktur` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `tanggal` date NULL DEFAULT NULL,
  `tanggal_faktur` date NULL DEFAULT NULL,
  `tanggal_tempo` date NULL DEFAULT NULL,
  `tanggal_retur` date NULL DEFAULT NULL,
  `keterangan` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `id_gudang` int NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id_retur`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_konversipersediaan
-- ----------------------------
DROP TABLE IF EXISTS `tbl_konversipersediaan`;
CREATE TABLE `tbl_konversipersediaan`  (
  `id_konversi` int NOT NULL AUTO_INCREMENT,
  `id_barang` int NULL DEFAULT NULL,
  `id_gudang` int NULL DEFAULT NULL,
  `tanggal` date NULL DEFAULT NULL,
  `id_satuan` int NULL DEFAULT NULL,
  `jumlah` int NULL DEFAULT NULL,
  `id_satuan_konversi` int NULL DEFAULT NULL,
  `jumlah_konversi` int NULL DEFAULT NULL,
  `keterangan` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id_konversi`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_konversipersediaan_detail
-- ----------------------------
DROP TABLE IF EXISTS `tbl_konversipersediaan_detail`;
CREATE TABLE `tbl_konversipersediaan_detail`  (
  `id_konversi_detail` int NOT NULL AUTO_INCREMENT,
  `id_konversi` int NULL DEFAULT NULL,
  `id_barang` int NULL DEFAULT NULL,
  `id_gudang` int NULL DEFAULT NULL,
  `id_satuan` int NULL DEFAULT NULL,
  `jumlah` int NULL DEFAULT NULL,
  `id_log_stok` int NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id_konversi_detail`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_log_stok
-- ----------------------------
DROP TABLE IF EXISTS `tbl_log_stok`;
CREATE TABLE `tbl_log_stok`  (
  `log_stok_id` int NOT NULL AUTO_INCREMENT,
  `id_barang` int NULL DEFAULT NULL,
  `id_ref_gudang` int NOT NULL,
  `id_satuan` int NULL DEFAULT NULL,
  `tanggal` date NULL DEFAULT NULL,
  `unit_masuk` int NOT NULL,
  `unit_keluar` int NOT NULL,
  `status` varchar(11) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT 'berisi ',
  `ket` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`log_stok_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1142819 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for tbl_m_program
-- ----------------------------
DROP TABLE IF EXISTS `tbl_m_program`;
CREATE TABLE `tbl_m_program`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_tr` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nama` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 91 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_mapping_akun
-- ----------------------------
DROP TABLE IF EXISTS `tbl_mapping_akun`;
CREATE TABLE `tbl_mapping_akun`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_akun` int NULL DEFAULT NULL,
  `id_program` int NULL DEFAULT NULL,
  `type` enum('D','K') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_menu
-- ----------------------------
DROP TABLE IF EXISTS `tbl_menu`;
CREATE TABLE `tbl_menu`  (
  `menu_id` int NOT NULL AUTO_INCREMENT,
  `menu_nama` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `menu_link` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `menu_id_parent` int NULL DEFAULT NULL,
  `urutan` int NULL DEFAULT NULL,
  `created_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`menu_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 83 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_pembelian
-- ----------------------------
DROP TABLE IF EXISTS `tbl_pembelian`;
CREATE TABLE `tbl_pembelian`  (
  `id_pembelian` int NOT NULL AUTO_INCREMENT,
  `id_supplier` int NULL DEFAULT NULL,
  `no_faktur` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `tanggal` date NULL DEFAULT NULL,
  `tanggal_tempo` date NULL DEFAULT NULL,
  `tanggal_faktur` date NULL DEFAULT NULL,
  `tanggal_terima` date NULL DEFAULT NULL,
  `tanggal_bayar` date NULL DEFAULT NULL,
  `pajak` int NULL DEFAULT NULL COMMENT '1=ya,0=tidak',
  `uang_muka` int NULL DEFAULT NULL,
  `ongkos_kirim` int NULL DEFAULT NULL,
  `viabayar` int NULL DEFAULT NULL,
  `carabayar` int NULL DEFAULT NULL,
  `id_rekening` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `no_rekening` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `total_subtotal` int NULL DEFAULT NULL,
  `total_tagihan` int NULL DEFAULT NULL,
  `total_potongan` int NULL DEFAULT NULL,
  `total_bayar` int NULL DEFAULT NULL,
  `keterangan` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `status_bayar` int NULL DEFAULT NULL COMMENT '1 = Belum Dibayar, 2 = Sebagian Dibayar, 3 = Sudah Dibayar',
  `status_penerimaan` int NULL DEFAULT NULL COMMENT '1 = Belum Diterima, 2 = Sebagian Diterima, 3 = Sudah Diterima',
  `id_gudang` int NULL DEFAULT NULL,
  `jenis` int NOT NULL DEFAULT 1,
  `status_jenis` int NOT NULL DEFAULT 1,
  `created_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id_pembelian`) USING BTREE,
  INDEX `pembelian_to_suplier_relation`(`id_supplier`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1666 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_pembelian_detail
-- ----------------------------
DROP TABLE IF EXISTS `tbl_pembelian_detail`;
CREATE TABLE `tbl_pembelian_detail`  (
  `id_detail_pembelian` int NOT NULL AUTO_INCREMENT,
  `id_pembelian` int NULL DEFAULT NULL,
  `id_barang` int NULL DEFAULT NULL,
  `id_satuan` int NULL DEFAULT NULL,
  `jumlah` int NULL DEFAULT NULL,
  `jumlah_terima` int NULL DEFAULT 0,
  `harga` int NULL DEFAULT NULL,
  `total` int NULL DEFAULT NULL,
  `id_log_stok` int NULL DEFAULT NULL,
  `cretaed_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id_detail_pembelian`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5553 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_pembelian_retur
-- ----------------------------
DROP TABLE IF EXISTS `tbl_pembelian_retur`;
CREATE TABLE `tbl_pembelian_retur`  (
  `id_retur` int NOT NULL AUTO_INCREMENT,
  `kode_retur` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `tanggal` datetime(0) NULL DEFAULT NULL,
  `id_barang` int NULL DEFAULT NULL,
  `jumlah` int NULL DEFAULT NULL,
  `id_satuan` int NULL DEFAULT NULL,
  `id_gudang` int NULL DEFAULT NULL,
  `id_supplier` int NULL DEFAULT NULL,
  `keterangan` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `id_log_stok` int NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id_retur`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 59 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_saldoawal
-- ----------------------------
DROP TABLE IF EXISTS `tbl_saldoawal`;
CREATE TABLE `tbl_saldoawal`  (
  `id_saldoawal` int NOT NULL AUTO_INCREMENT,
  `id_barang` int NULL DEFAULT NULL,
  `id_gudang` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `jumlah` int NULL DEFAULT NULL,
  `id_satuan` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `tanggal` date NULL DEFAULT NULL,
  `keterangan` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `id_log_stok` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  `created_by` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `created_iduser` int NULL DEFAULT NULL,
  `updated_by` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `updated_iduser` int NULL DEFAULT NULL,
  PRIMARY KEY (`id_saldoawal`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2206 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_satuan
-- ----------------------------
DROP TABLE IF EXISTS `tbl_satuan`;
CREATE TABLE `tbl_satuan`  (
  `satuan_id` int NOT NULL AUTO_INCREMENT,
  `satuan_nama` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `satuan_satuan` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `konversi` int NULL DEFAULT NULL,
  `created_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`satuan_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_satuan_konversi
-- ----------------------------
DROP TABLE IF EXISTS `tbl_satuan_konversi`;
CREATE TABLE `tbl_satuan_konversi`  (
  `id_konversi` int NOT NULL AUTO_INCREMENT,
  `id_satuan_awal` int NULL DEFAULT NULL,
  `jumlah_awal` int NULL DEFAULT NULL,
  `id_satuan_akhir` int NULL DEFAULT NULL,
  `jumlah_akhir` int NULL DEFAULT NULL,
  `jumlah_bagi` double(12, 12) NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id_konversi`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_stok
-- ----------------------------
DROP TABLE IF EXISTS `tbl_stok`;
CREATE TABLE `tbl_stok`  (
  `stok_id` int NOT NULL AUTO_INCREMENT,
  `barang_id` int NULL DEFAULT NULL,
  `stok_jumlah` int NULL DEFAULT NULL,
  `stok_fisik` int NULL DEFAULT NULL,
  `created_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`stok_id`) USING BTREE,
  INDEX `tbl_barang_relation`(`barang_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_stokopname
-- ----------------------------
DROP TABLE IF EXISTS `tbl_stokopname`;
CREATE TABLE `tbl_stokopname`  (
  `id_stokopname` int NOT NULL AUTO_INCREMENT,
  `tanggal` date NULL DEFAULT NULL,
  `id_gudang` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `keterangan` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `status` int NULL DEFAULT 1,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id_stokopname`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 121 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_stokopname_baru
-- ----------------------------
DROP TABLE IF EXISTS `tbl_stokopname_baru`;
CREATE TABLE `tbl_stokopname_baru`  (
  `id_stokopname` int NOT NULL AUTO_INCREMENT,
  `tanggal` date NULL DEFAULT NULL,
  `id_barang` int NULL DEFAULT NULL,
  `id_gudang` int NULL DEFAULT NULL,
  `stok` int NULL DEFAULT NULL,
  `fisik` int NULL DEFAULT NULL,
  `selisih` int NULL DEFAULT NULL,
  `id_satuan` int NULL DEFAULT NULL,
  `keterangan` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `id_log_stok` int NULL DEFAULT NULL,
  `status` int NULL DEFAULT 1,
  `created_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  `created_by` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `created_iduser` int NULL DEFAULT NULL,
  `updated_by` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `updated_iduser` int NULL DEFAULT NULL,
  PRIMARY KEY (`id_stokopname`) USING BTREE,
  INDEX `tbl_barang_relation`(`id_barang`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 265745 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_stokopname_detail
-- ----------------------------
DROP TABLE IF EXISTS `tbl_stokopname_detail`;
CREATE TABLE `tbl_stokopname_detail`  (
  `id_detail_stokopname` int NOT NULL AUTO_INCREMENT,
  `id_stokopname` int NULL DEFAULT NULL,
  `id_barang` int NULL DEFAULT NULL,
  `id_gudang` int NULL DEFAULT NULL,
  `stok` int NULL DEFAULT NULL,
  `fisik` int NULL DEFAULT NULL,
  `selisih` int NULL DEFAULT NULL,
  `id_satuan` int NULL DEFAULT NULL,
  `keterangan` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `id_log_stok` int NULL DEFAULT NULL,
  `created_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id_detail_stokopname`) USING BTREE,
  INDEX `tbl_barang_relation`(`id_barang`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 566 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_supplier
-- ----------------------------
DROP TABLE IF EXISTS `tbl_supplier`;
CREATE TABLE `tbl_supplier`  (
  `supplier_id` int NOT NULL AUTO_INCREMENT,
  `supplier_nama` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `supplier_alamat` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `supplier_telp` char(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `supplier_tempo` int NULL DEFAULT 30,
  `created_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`supplier_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 69 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_t_user
-- ----------------------------
DROP TABLE IF EXISTS `tbl_t_user`;
CREATE TABLE `tbl_t_user`  (
  `t_user_id` int NOT NULL AUTO_INCREMENT,
  `group_id` int NULL DEFAULT NULL,
  `menu_id` int NULL DEFAULT NULL,
  `created_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`t_user_id`) USING BTREE,
  INDEX `t_user__group_relation`(`group_id`) USING BTREE,
  INDEX `t_user__menu_relation`(`menu_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3092 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tbl_transaksi_poin
-- ----------------------------
DROP TABLE IF EXISTS `tbl_transaksi_poin`;
CREATE TABLE `tbl_transaksi_poin`  (
  `id_pelanggan` int NULL DEFAULT NULL,
  `unit_masuk` int NULL DEFAULT 0,
  `unit_keluar` int NULL DEFAULT 0,
  `tanggal` date NULL DEFAULT NULL,
  `status` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `id_kasir` int NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0)
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id` int NULL DEFAULT NULL,
  `id_profil` int NULL DEFAULT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `users_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `users_email_unique`(`email`) USING BTREE,
  INDEX `group_relation`(`group_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 27 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Triggers structure for table tbl_kasir
-- ----------------------------
DROP TRIGGER IF EXISTS `pembatalan_nota`;
delimiter ;;
CREATE TRIGGER `pembatalan_nota` BEFORE DELETE ON `tbl_kasir` FOR EACH ROW BEGIN
    INSERT INTO tbl_kasir_batal (id_kasir, id_pelanggan, no_faktur, tanggal, tanggal_tempo, tanggal_faktur, tanggal_bayar, uang_muka, ongkos_kirim, carabayar, metodebayar, metodebayar2, id_rekening, no_rekening, total_subtotal, total_tagihan, total_potongan, total_metodebayar, total_metodebayar2, total_bayar, keterangan, status, jenis_transaksi, status_posting, id_gudang, created_at, updated_at, total_redeem, status_promo, created_by, created_iduser, updated_by, updated_iduser, metodeongkir, metodeongkir2) VALUES (OLD.id_kasir, OLD.id_pelanggan, OLD.no_faktur, OLD.tanggal, OLD.tanggal_tempo, OLD.tanggal_faktur, OLD.tanggal_bayar, OLD.uang_muka, OLD.ongkos_kirim, OLD.carabayar, OLD.metodebayar, OLD.metodebayar2, OLD.id_rekening, OLD.no_rekening, OLD.total_subtotal, OLD.total_tagihan, OLD.total_potongan, OLD.total_metodebayar, OLD.total_metodebayar2, OLD.total_bayar, OLD.keterangan, OLD.status, OLD.jenis_transaksi, OLD.status_posting, OLD.id_gudang, OLD.created_at, OLD.updated_at, OLD.total_redeem, OLD.status_promo, OLD.created_by, OLD.created_iduser, OLD.updated_by, OLD.updated_iduser, OLD.metodeongkir, OLD.metodeongkir2);
END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table tbl_kasir_detail
-- ----------------------------
DROP TRIGGER IF EXISTS `pembatalan_nota_detail`;
delimiter ;;
CREATE TRIGGER `pembatalan_nota_detail` BEFORE DELETE ON `tbl_kasir_detail` FOR EACH ROW BEGIN
    INSERT INTO tbl_kasir_detail_batal (id_detail_kasir, id_kasir, id_detail_kasir_produk, id_barang, id_satuan, id_log_stok, jumlah, harga, potongan, subtotal, total, ppn, cretaed_at, updated_at) VALUES (OLD.id_detail_kasir, OLD.id_kasir, OLD.id_detail_kasir_produk, OLD.id_barang, OLD.id_satuan, OLD.id_log_stok, OLD.jumlah, OLD.harga, OLD.potongan, OLD.subtotal, OLD.total, OLD.ppn, OLD.cretaed_at, OLD.updated_at);
END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table tbl_kasir_detail_produk
-- ----------------------------
DROP TRIGGER IF EXISTS `pembatalan_nota_detail_produk`;
delimiter ;;
CREATE TRIGGER `pembatalan_nota_detail_produk` BEFORE DELETE ON `tbl_kasir_detail_produk` FOR EACH ROW BEGIN
    INSERT INTO tbl_kasir_detail_produk_batal (id_kasir_detail_produk, id_kasir, id_produk, jumlah, id_satuan, harga, total, id_log_stok, created_at, updated_at, status_redeem, poin, total_poin, potongan, potongan_total) VALUES (OLD.id_kasir_detail_produk, OLD.id_kasir, OLD.id_produk, OLD.jumlah, OLD.id_satuan, OLD.harga, OLD.total, OLD.id_log_stok, OLD.created_at, OLD.updated_at, status_redeem, OLD.poin, OLD.total_poin, OLD.potongan, OLD.potongan_total);
END
;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;
