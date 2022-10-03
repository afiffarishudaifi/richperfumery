package com.chickenkids.simparfum.modelData

import com.google.gson.annotations.SerializedName



data class DetailClosingTambahan(

    @field:SerializedName("nama_barang")
    val nama: String? = null,

    @field:SerializedName("kode_barang")
    val kode: String? = null,

    @field:SerializedName("jumlah")
    val jumlah: String? = null

)
