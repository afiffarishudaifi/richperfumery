package com.chickenkids.simparfum.modelData

import com.google.gson.annotations.SerializedName


data class ResponseClosing(

    @field:SerializedName("jumlah_nota")
    val jumlah_nota: String? = null,

    @field:SerializedName("status")
    val status: String? = null,

    @field:SerializedName("total")
    val total: String? = null,

    @field:SerializedName("total_ongkir")
    val total_ongkir: String? = null,

    @field:SerializedName("detail_closing")
    val detailClosing: List<DetailClosingItem>,

    @field:SerializedName("detail_tambahan")
    val detailTambahan: List<DetailClosingTambahan>,

    @field:SerializedName("error")
    val error: Int? = null,

    @field:SerializedName("message")
    val message: String? = null

)
