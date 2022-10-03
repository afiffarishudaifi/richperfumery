package com.chickenkids.simparfum.modelStokopname

import com.google.gson.annotations.SerializedName

data class PersediaanResponse(

    @field:SerializedName("result")
	val result: PersediaanResponseItem? = null,

    @field:SerializedName("error")
	val error: Int? = null,

    @field:SerializedName("message")
	val message: String? = null
)