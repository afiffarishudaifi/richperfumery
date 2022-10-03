package com.chickenkids.simparfum.Utils

import android.content.Context
import android.webkit.JavascriptInterface
import android.widget.Toast
import com.chickenkids.simparfum.ClosingCetakActivity
import com.chickenkids.simparfum.activityBarcode.BarcodeActivity
import com.chickenkids.simparfum.activityPrint.CetakActivity
import com.chickenkids.simparfum.activityPrint.CetakRedeemActivity
import org.jetbrains.anko.startActivity

class WebAppInterface(private val mContext: Context) {
    @JavascriptInterface
    fun showToast(toast: String) {
        Toast.makeText(mContext, toast, Toast.LENGTH_SHORT).show()
    }
    @JavascriptInterface
    fun movePrinter(data_kasir: String,detail_kasir: String) {
        mContext.startActivity<CetakActivity>(
            "data_kasir" to data_kasir,
            "detail_kasir" to detail_kasir
        )
    }
    @JavascriptInterface
    fun moveCetak(id_kasir: String,nama_kasir: String,nama_outlet: String,alamat_outlet: String,telp_outlet: String,status:String) {
        mContext.startActivity<CetakActivity>(
            "id_kasir" to id_kasir,
            "nama_outlet" to nama_outlet,
            "alamat_outlet" to alamat_outlet,
            "telp_outlet" to telp_outlet,
            "nama_kasir" to nama_kasir,
            "status" to status
        )
    }
    @JavascriptInterface
    fun moveCetakRedeem(id_kasir: String,nama_kasir: String,nama_outlet: String,alamat_outlet: String,telp_outlet: String) {
        mContext.startActivity<CetakRedeemActivity>(
            "id_kasir" to id_kasir,
            "nama_outlet" to nama_outlet,
            "alamat_outlet" to alamat_outlet,
            "telp_outlet" to telp_outlet,
            "nama_kasir" to nama_kasir
        )
    }
    @JavascriptInterface
    fun moveCetakClosing(id_gudang: String,tanggal: String,nama_kasir: String,nama_outlet: String,alamat_outlet: String,telp_outlet: String) {
        mContext.startActivity<ClosingCetakActivity>(
            "id_gudang" to id_gudang,
            "tanggal" to tanggal,
            "nama_outlet" to nama_outlet,
            "alamat_outlet" to alamat_outlet,
            "telp_outlet" to telp_outlet,
            "nama_kasir" to nama_kasir
        )
    }
    @JavascriptInterface
    fun moveBarcode(id_gudang: String) {
        mContext.startActivity<BarcodeActivity>(
            "id_gudang" to id_gudang
        )
    }

    @JavascriptInterface
    fun refreshPage() {

    }
}