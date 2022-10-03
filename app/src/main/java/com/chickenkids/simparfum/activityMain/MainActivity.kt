package com.chickenkids.simparfum.activityMain

import android.app.ProgressDialog
import android.os.Bundle
import android.os.Handler
import android.webkit.WebChromeClient
import android.webkit.WebView
import android.webkit.WebViewClient
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.isVisible
import com.chickenkids.simparfum.BuildConfig
import com.chickenkids.simparfum.R
import com.chickenkids.simparfum.Utils.UtilNet
import com.chickenkids.simparfum.Utils.WebAppInterface
import kotlinx.android.synthetic.main.activity_main.*
import org.jetbrains.anko.toast


class MainActivity : AppCompatActivity() {

    private var urlDocument = String()
    private var utilNet = UtilNet()
    private var codeIntent :String ? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        webview.webViewClient = object : WebViewClient() {
            override fun shouldOverrideUrlLoading(view: WebView?, url: String?): Boolean {
                view?.loadUrl(url)
                return true
            }
        }

        webview.setWebChromeClient(object : WebChromeClient() {
            private var mProgress: ProgressDialog? = null

            override fun onProgressChanged(view: WebView, progress: Int) {
                if (mProgress == null) {
                    mProgress = ProgressDialog(this@MainActivity)
                    mProgress!!.show()
                }
                mProgress!!.setMessage("Loading $progress%")
                mProgress!!.setCancelable(false)
                if (progress == 100) {
                    mProgress!!.dismiss()
                    mProgress = null
                }
            }
        })

        val data = intent
        codeIntent = data.getStringExtra("codeIntent")
        if (codeIntent.equals("1")){
            urlDocument = "${BuildConfig.BASE_URL}kasir"
        }else if (codeIntent.equals("2")){
            urlDocument = "${BuildConfig.BASE_URL}stokopnamebaru"
        }else if (codeIntent.equals("3")){
            urlDocument = "${BuildConfig.BASE_URL}kasirclosing"
        }else if (codeIntent.equals("4")){
            urlDocument = "${BuildConfig.BASE_URL}redeem"
        }else{
            urlDocument = "${BuildConfig.BASE_URL}"
        }

        val webSettings = webview.settings
        webSettings.javaScriptEnabled = true
        webSettings.domStorageEnabled = true
        webSettings.setGeolocationEnabled(true)
        webSettings.javaScriptCanOpenWindowsAutomatically = true
        webview.addJavascriptInterface(
            WebAppInterface(
                this
            ), "Android")

        if(utilNet.isNetworkAvailable(this)){
            webview.loadUrl(urlDocument)
            RL_NO_INTERNET.isVisible = false
        }else{
            RL_NO_INTERNET.isVisible = true
        }

        swMain.isEnabled = false

        /*swMain.setOnRefreshListener {
            if (webview.url == "${BuildConfig.BASE_URL}kasir_tambah"){
                swMain.isEnabled = false
                swMain.isRefreshing = false
            }else{
                refreshAction()
                swMain.isRefreshing = false
//                toast("Bukan Kasir Tambah")
            }
        }*/

        btn_retry.setOnClickListener{
            refreshAction()
        }
    }

    private fun refreshAction() {
        val webUrl = webview.url
        urlDocument = "$webUrl"
        webview.settings.javaScriptEnabled = true
        if(utilNet.isNetworkAvailable(this)){
            webview.loadUrl(urlDocument)
            RL_NO_INTERNET.isVisible = false
        }else{
            RL_NO_INTERNET.isVisible = true
        }
    }

    var doubleBackToExitPressedOnce = false
    override fun onBackPressed() {
        val webUrl = webview.url
//        if (webview.canGoBack()) {
//            webview.goBack()
//        }else {
//
//        }
        if (webUrl == "${BuildConfig.BASE_URL}login") {
            if (doubleBackToExitPressedOnce) {
                finishAffinity()
            }
            this.doubleBackToExitPressedOnce = true
            toast("Double click Back to exit")
            Handler().postDelayed({ doubleBackToExitPressedOnce = false }, 2000)
        }else if (webUrl == "${BuildConfig.BASE_URL}index_admin.html" || webUrl == "${BuildConfig.BASE_URL}" ) {
            if (doubleBackToExitPressedOnce) {
                finishAffinity()
            }
            this.doubleBackToExitPressedOnce = true
            toast("Double click Back to exit")
            Handler().postDelayed({ doubleBackToExitPressedOnce = false }, 2000)
        }else{
            webview.goBack()
        }
    }
}
