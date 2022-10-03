package com.chickenkids.simparfum.modelData

import com.google.gson.annotations.SerializedName

data class ResponseKasir(

    @field:SerializedName("no_member")
	val noMember: String? = null,

    @field:SerializedName("total_qty")
	val totalQty: String? = null,

    @field:SerializedName("detail_produk")
	val detailProduk: List<DetailProdukItem> ? = null,

    @field:SerializedName("tanggal_faktur")
	val tanggalFaktur: String? = null,

    @field:SerializedName("error")
	val error: Int? = null,

    @field:SerializedName("message")
	val message: String? = null,

    @field:SerializedName("no_faktur")
	val noFaktur: String? = null,

    @field:SerializedName("uang_muka")
	val uangMuka: String? = null,

    @field:SerializedName("nama_pelanggan")
	val namaPelanggan: String? = null,

    @field:SerializedName("ongkos_kirim")
	val ongkosKirim: String? = null,

    @field:SerializedName("total_potongan")
	val totalPotongan: String? = null,

    @field:SerializedName("detail_barang")
	val detailBarang: List<DetailBarangItem> ? = null,

    @field:SerializedName("total_tagihan")
	val totalTagihan: String? = null,

    @field:SerializedName("nama_metodebayar")
	val namaMetodebayar: String? = null,

    @field:SerializedName("nama_metodebayar2")
	val namaMetodebayar2: String? = null,

    @field:SerializedName("total_metodebayar")
	val totalMetodebayar: String? = null,

    @field:SerializedName("total_metodebayar2")
	val totalMetodebayar2: String? = null,

    @field:SerializedName("subtotal")
	val totalSubtotal: String? = null,

    @field:SerializedName("status_metode")
	val statusMetodebayar: String? = null

)