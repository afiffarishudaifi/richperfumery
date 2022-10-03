package com.chickenkids.simparfum.modelData

import com.google.gson.annotations.SerializedName


data class DetailClosingItem(

    @field:SerializedName("name")
    val nama: String? = null,

    @field:SerializedName("data")
    val nominal: String? = null

)
