package com.chickenkids.simparfum.activityBarcode

import android.Manifest.permission
import android.content.pm.PackageManager
import android.os.Bundle
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.core.app.ActivityCompat
import androidx.core.content.ContextCompat
import com.chickenkids.simparfum.activityMain.MainActivity
import com.chickenkids.simparfum.activityStokopname.StokOpnameActivity
import com.google.zxing.Result
import me.dm7.barcodescanner.zxing.ZXingScannerView
import org.jetbrains.anko.ctx
import org.jetbrains.anko.startActivity
import org.jetbrains.anko.toast


class BarcodeActivity : AppCompatActivity(), ZXingScannerView.ResultHandler {
    override fun handleResult(rawResult: Result) {
//        toast("${rawResult.text}")
        startActivity<StokOpnameActivity>(
            "id_gudang" to "$id_gudang",
            "detail_barang" to "${rawResult.text}"
        )

        mScannerView!!.resumeCameraPreview(this)
    }

    private var mScannerView: ZXingScannerView? = null
    private var id_gudang :String ? = null
    private val MY_PERMISSIONS_REQUEST_CODE = 123

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        checkPermission()
        // Programmatically initialize the scanner view
        mScannerView = ZXingScannerView(this)
        setContentView(mScannerView)
        val data = intent
        id_gudang = data.getStringExtra("id_gudang")
    }

    public override fun onResume() {
        super.onResume()
        mScannerView!!.setResultHandler(this)
        mScannerView!!.startCamera()
    }

    public override fun onPause() {
        super.onPause()
        mScannerView!!.stopCamera()
    }


    private fun checkPermission() {
        if (
            (ContextCompat.checkSelfPermission(ctx, permission.CAMERA)
//                    + ContextCompat.checkSelfPermission( ctx, permission.READ_EXTERNAL_STORAGE) +
//                    ContextCompat.checkSelfPermission(ctx, permission.WRITE_EXTERNAL_STORAGE)
                    ) !== PackageManager.PERMISSION_GRANTED
        ) {

            // Do something, when permissions not granted
            if (ActivityCompat.shouldShowRequestPermissionRationale(
                    this, permission.CAMERA
                )
//                || ActivityCompat.shouldShowRequestPermissionRationale(
//                    mActivity!!, permission.READ_EXTERNAL_STORAGE
//                )
//                || ActivityCompat.shouldShowRequestPermissionRationale(
//                    mActivity!!, permission.WRITE_EXTERNAL_STORAGE
//                )
            ) {
                // If we should give explanation of requested permissions

                // Show an alert dialog here with request explanation
                val builder = AlertDialog.Builder(ctx)
                builder.setMessage("Camera permissions are required to do the task.")
                builder.setTitle("Please grant those permissions")
                builder.setPositiveButton(
                    "OK"
                ) { _, i ->
                    ActivityCompat.requestPermissions(
                        this,
                        arrayOf(
                            permission.CAMERA
//                            ,permission.READ_EXTERNAL_STORAGE,
//                            permission.WRITE_EXTERNAL_STORAGE
                        ),
                        MY_PERMISSIONS_REQUEST_CODE
                    )
                }
//                builder.setNeutralButton("Cancel", null)
                val dialog = builder.create()
                dialog.show()
            } else {
                // Directly request for required permissions, without explanation
                ActivityCompat.requestPermissions(
                    this,
                    arrayOf(
                        permission.CAMERA
//                        ,permission.READ_EXTERNAL_STORAGE,
//                        permission.WRITE_EXTERNAL_STORAGE
                    ),
                    MY_PERMISSIONS_REQUEST_CODE
                )
            }
        } else {
            // Do something, when permissions are already granted
            //            Toast.makeText(mContext,"Permissions already granted",Toast.LENGTH_SHORT).show();
        }
    }

    override fun onRequestPermissionsResult(
        requestCode: Int,
        permissions: Array<String>,
        grantResults: IntArray
    ) {
        when (requestCode) {
            MY_PERMISSIONS_REQUEST_CODE -> {
                // When request is cancelled, the results array are empty
                if (grantResults.size > 0 && (grantResults[0]
//                            + grantResults[1]
//                            + grantResults[2]
                            ) == PackageManager.PERMISSION_GRANTED
                ) {
                    // Permissions are granted
                    toast("Permissions granted")
                } else {
                    // Permissions are denied
                    toast("Permissions denied")
                }
                return
            }
        }
    }



    override fun onBackPressed() {
        startActivity<MainActivity>(
            "codeIntent" to "2"
        )
        super.onBackPressed()
    }
}
