package com.chickenkids.simparfum.modelStokopname

import com.google.gson.annotations.SerializedName

data class ResponseStokOpname(

	@field:SerializedName("error")
	val error: Boolean? = null,

	@field:SerializedName("message")
	val message: String? = null
)