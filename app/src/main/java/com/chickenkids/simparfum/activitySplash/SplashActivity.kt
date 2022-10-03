package com.chickenkids.simparfum.activitySplash

import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity
import com.bumptech.glide.Glide
import com.chickenkids.simparfum.R
import com.chickenkids.simparfum.Utils.UtilNet
import com.chickenkids.simparfum.activityMain.MainActivity
import kotlinx.android.synthetic.main.activity_splash.*
import org.jetbrains.anko.startActivity

class SplashActivity : AppCompatActivity(),
    SplashView {
    private val TIME_TUNGGU:Long = 1500
    private lateinit var presenter: SplashPresenter
    val utilnet = UtilNet()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_splash)
        Glide.with(this).load(utilnet.getImage(this,"logo_rich")).into(iv_logo)
        presenter =
            SplashPresenter(this)
        presenter.getLoading(TIME_TUNGGU)
    }
    override fun runLoading(waktu: Long) {
        val timer = object : Thread() {
            override fun run() {
                try {
                    sleep(waktu)
                    checkSession()
                    super.run()
                } catch (e: InterruptedException) {
                    e.printStackTrace()
                }
            }
        }
        timer.start()
    }
    private fun checkSession() {
        startActivity<MainActivity>(
            "codeIntent" to "0"
        )
        finish()
    }
}
