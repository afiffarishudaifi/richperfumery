package com.chickenkids.simparfum.activityStokopname

import com.chickenkids.simparfum.modelStokopname.PersediaanResponse

interface StokOpnameView {
    fun showDataPersediaan(data: PersediaanResponse)
    fun errorDataPersediaan(message:String)
    fun responseSaveStokOpname(code:String,message:String)
    fun showLoading()
    fun hideLoading()
}