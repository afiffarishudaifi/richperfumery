package com.chickenkids.simparfum.activityPrint

import com.chickenkids.simparfum.modelData.ResponseRedeem


interface PrintRedeemView {
    fun showDataList(data: ResponseRedeem)
    fun errorDataList(message:String)
    fun setToolbar()
    fun showLoading()
    fun hideLoading()
}