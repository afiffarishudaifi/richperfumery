package com.chickenkids.simparfum.modelData

import com.google.gson.annotations.SerializedName

data class DetailBarangItem(

	@field:SerializedName("total_barang")
	val totalBarang: String? = null,

	@field:SerializedName("kode_barang")
	val kodeBarang: String? = null,

	@field:SerializedName("satuan_barang")
	val satuanBarang: String? = null,

	@field:SerializedName("nama_barang")
	val namaBarang: String? = null,

	@field:SerializedName("harga_barang")
	val hargaBarang: String? = null,

	@field:SerializedName("jml_barang")
	val jmlBarang: String? = null
)