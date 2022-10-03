package com.chickenkids.simparfum.modelData

import com.google.gson.annotations.SerializedName

data class ResponseRedeem(

    @field:SerializedName("no_member")
	val noMember: String? = null,

    @field:SerializedName("total_qty")
	val totalQty: String? = null,

    @field:SerializedName("detail_produk")
	val detailProduk: List<DetailProdukItemRedeem> ? = null,

    @field:SerializedName("tanggal_faktur")
	val tanggalFaktur: String? = null,

    @field:SerializedName("error")
	val error: Int? = null,

    @field:SerializedName("message")
	val message: String? = null,

    @field:SerializedName("no_faktur")
	val noFaktur: String? = null,

    @field:SerializedName("nama_pelanggan")
	val namaPelanggan: String? = null,

    @field:SerializedName("total_poin")
	val totalPoin: String? = null,

    @field:SerializedName("nama_metodebayar")
	val namaMetodebayar: String? = null,

    @field:SerializedName("total_metodebayar")
	val totalMetodebayar: String? = null,

    @field:SerializedName("status_metode")
	val statusMetodebayar: String? = null
)