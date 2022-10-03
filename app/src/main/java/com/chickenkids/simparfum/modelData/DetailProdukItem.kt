package com.chickenkids.simparfum.modelData

import com.google.gson.annotations.SerializedName

data class DetailProdukItem(

	@field:SerializedName("total")
	val total: String? = null,

	@field:SerializedName("nama")
	val nama: String? = null,

	@field:SerializedName("jumlah")
	val jumlah: String? = null,

	@field:SerializedName("harga")
	val harga: String? = null,

	@field:SerializedName("satuan_satuan")
	val satuanSatuan: String? = null,

	@field:SerializedName("id")
	val id: String? = null
)