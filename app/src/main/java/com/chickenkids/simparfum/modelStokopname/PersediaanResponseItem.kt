package com.chickenkids.simparfum.modelStokopname

import com.google.gson.annotations.SerializedName

data class PersediaanResponseItem(

	@field:SerializedName("id_satuan")
	val idSatuan: String? = null,

	@field:SerializedName("created_at")
	val createdAt: String? = null,

	@field:SerializedName("unit_masuk")
	val unitMasuk: String? = null,

	@field:SerializedName("id_ref_gudang")
	val idRefGudang: String? = null,

	@field:SerializedName("stok")
	val stok: String? = null,

	@field:SerializedName("updated_at")
	val updatedAt: String? = null,

	@field:SerializedName("id_barang")
	val idBarang: String? = null,

	@field:SerializedName("kode_barang")
	val kodeBarang: String? = null,

	@field:SerializedName("unit_keluar")
	val unitKeluar: String? = null,

	@field:SerializedName("log_stok_id")
	val logStokId: String? = null,

	@field:SerializedName("nama_barang")
	val namaBarang: String? = null,

	@field:SerializedName("tanggal")
	val tanggal: String? = null,

	@field:SerializedName("ket")
	val ket: Any? = null,

	@field:SerializedName("konversi")
	val konversi: Any? = null,

	@field:SerializedName("nama_gudang")
	val namaGudang: String? = null,

	@field:SerializedName("nama_satuan")
	val namaSatuan: String? = null,

	@field:SerializedName("status")
	val status: String? = null
)